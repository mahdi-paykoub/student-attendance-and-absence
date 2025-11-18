<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class SuportPanelController extends Controller
{
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
    public function students()
    {
        $students = auth()->user()
            ->students()
            ->wherePivot('relation_type', '!=', 'referred')
            ->orWherePivot('relation_type', null)
            ->with('products')
            ->get();
        return view('suporter-panel.students', compact('students'));
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

        // اتصال پشتیبان جدید (بدون حذف پشتیبان‌های قبلی)
        $student->supporters()->attach($newSupporter, [
            'relation_type' => 'referred',
        ]);

        return back()->with('success', 'دانش‌آموز با موفقیت به پشتیبان جدید ارجاع داده شد.');
    }


    public function referentialSudents()
    {
        $students = auth()->user()
            ->students()
            ->wherePivot('relation_type', 'referred')
            ->get();
        return view('suporter-panel.students', compact('students'));
    }

    public function updateStatus(Request $request, Student $student)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,done',
        ]);

        $userId = auth()->id(); // پشتیبان فعلی که می‌خواهد وضعیت را تغییر دهد

        // آپدیت pivot table برای این دانش‌آموز و پشتیبان
        $student->supporters()->updateExistingPivot($userId, [
            'progress_status' => $request->status,
        ]);

        return back()->with('success', 'وضعیت رسیدگی با موفقیت بروزرسانی شد.');
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
}
