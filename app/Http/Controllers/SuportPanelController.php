<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuportPanelController extends Controller
{

    public function students(Request $request)
    {
        $user = auth()->user();

        $students = $user->students()
            ->wherePivot('relation_type' , '!=' , 'referred')
            ->with(['grade', 'major', 'products']);

        // فیلتر progress_status
        if ($request->has('progress_status') && $request->progress_status != '') {
            $students->wherePivot('progress_status', $request->progress_status);
        }

        $students = $students->get();

        return view('suporter-panel.students', compact('students'));
    }

    public function filterStudents(Request $request)
    {
        $user = auth()->user();

        $query = $user->students()->with('products', 'grade', 'major');

        // فیلتر نوع ارتباط
        if ($request->relation_type) {
            $query->wherePivot('relation_type', $request->relation_type);
        }

        // فیلتر وضعیت رسیدگی
        if ($request->progress_status) {
            $query->wherePivot('progress_status', $request->progress_status);
        }

        $students = $query->get();

        return view('suporter-panel.index', [
            'students' => $students,
            'relationType' => $request->relation_type,
            'progressStatus' => $request->progress_status,
        ]);
    }


    public function showStudent(Student $student)
    {
        // پشتیبان‌های فعلی این دانش‌آموز (کاربران)
        $currentSupporters = $student->supporters()->get();

        // شناسه پشتیبان‌های فعلی
        $currentIds = $currentSupporters->pluck('id');

        // پشتیبان‌های دیگر (فقط کاربرانی که نقش supporter دارند)
        $otherSupporters = User::where('role', 'suporter')
            ->whereNotIn('id', $currentIds)
            ->get();

        $userId = auth()->id();

        // همه یادداشت‌های shared + یادداشت‌های خود پشتیبان
        $notes = $student->notes()
            ->where(function ($q) use ($userId) {
                $q->where('is_shared', true)
                    ->orWhere('user_id', $userId);
            })
            ->orderByDesc('created_at')
            ->get();
        return view('suporter-panel.single-student', compact(
            'student',
            'currentSupporters',
            'otherSupporters',
            'notes'
        ));
    }

    public function referStudent(Student $student, Request $request)
    {
        $request->validate([
            'supporter_id' => 'required|exists:users,id'
        ]);

        $newSupporter = $request->supporter_id;
        $currentSupporter = auth()->id(); // پشتیبان فعلی

        // اتصال پشتیبان جدید
        $student->supporters()->attach($newSupporter, [
            'relation_type' => 'referred',
            'previous_supporter_id' => $currentSupporter, // پشتیبان قبل
            'progress_status'        => 'pending' // شروع کار برای پشتیبان جدید
        ]);

        return back()->with('success', 'دانش‌آموز با موفقیت به پشتیبان جدید ارجاع داده شد.');
    }



    public function referentialSudents(Request $request)
    {
        
         $students = auth()->user()
        ->students()
        ->wherePivot('relation_type', 'referred');

    // فیلتر وضعیت رسیدگی روی pivot
    if ($request->has('progress_status') && $request->progress_status != '') {
        $students->wherePivot('progress_status', $request->progress_status);
    }

    $students = $students->with(['grade', 'major', 'products'])->get();

    return view('suporter-panel.students', compact('students'));
    }

    public function updateStatus(Student $student)
    {
        $user = auth()->user();
        $pivot = $student->supporters()->where('user_id', $user->id)->first()->pivot;

        // اگر این دانش‌آموز ارجاعی بوده و کار کامل شد
        if ($pivot->relation_type === 'referred' && request('progress_status') == 'done') {

            // پشتیبان قبلی را پیدا کن
            if ($pivot->previous_supporter_id) {

                // رکورد pivot پشتیبان قبلی را بروز کن (is_returned = true)
                DB::table('student_supporter')
                    ->where('student_id', $student->id)
                    ->where('user_id', $pivot->previous_supporter_id)
                    ->update([
                        'is_returned' => true,
                    ]);
            }
        }

        // آپدیت وضعیت فعلی
        $pivot->progress_status = request('progress_status');
        $pivot->save();

        return back()->with('success', 'وضعیت بروزرسانی شد');
    }




    public function storeNote(Request $request, Student $student)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_shared' => 'nullable'
        ]);
        $isShared = boolval($request->input('is_shared', false));

        $student->notes()->create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'is_shared' => $isShared
        ]);

        return back()->with('success', 'یادداشت با موفقیت ثبت شد.');
    }




    public function returnedList()
    {
        $user = auth()->user();

        $students = $user->students()
            ->wherePivot('is_returned', true)
            ->orderByPivot('updated_at', 'desc')
            ->get();

        return view('suporter-panel.students', compact('students'));
    }


    public function referredList(Request $request)
    {
        $user = auth()->user();

        // گام 1: گرفتن رکوردهای pivot با فیلتر وضعیت
        $pivotRecords = DB::table('student_supporter')
            ->where('previous_supporter_id', $user->id)
            ->where('relation_type', 'referred');

        if ($request->has('progress_status') && $request->progress_status != '') {
            $pivotRecords->where('progress_status', $request->progress_status);
        }

        $pivotRecords = $pivotRecords->orderBy('updated_at', 'desc')->get();

        // گام 2: گرفتن Student های مربوطه
        $students = Student::with(['grade', 'major', 'products'])
            ->whereIn('id', $pivotRecords->pluck('student_id'))
            ->get();

        // گام 3: attach کردن pivot info برای Blade
        // اینطوری می‌تونی وضعیت و پشتیبان فعلی رو در Blade نمایش بدی
        $students = $students->map(function ($student) use ($pivotRecords) {
            $pivot = $pivotRecords->firstWhere('student_id', $student->id);
            $student->pivot = (object) $pivot; // شبیه سازی pivot
            return $student;
        });

        return view('suporter-panel.students', compact('students'));
    }
}
