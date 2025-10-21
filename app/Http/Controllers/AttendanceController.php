<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Exam;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function create(Exam $exam)
    {
        return view('attendances.create', compact('exam'));
    }


    public function findStudent(Request $request)
    {
        $national = $request->query('national_code');
        if (!$national) {
            return response()->json(['success' => false]);
        }

        $student = Student::where('national_code', $national)->first();

        if (!$student) {
            return response()->json(['success' => false]);
        }




        $mandatoryProductId = Setting::get('mandatory_exam_product_id');
        $hasMandatoryProduct = $mandatoryProductId
            ? $student->productStudents()->where('product_id', $mandatoryProductId)->exists()
            : true; // اگر محصول الزامی تعریف نشده باشد، اجازه شرکت بده

        if (!$hasMandatoryProduct) {
            return response()->json([
                'success' => false,
                'message' => 'دانش‌آموز محصول الزامی برای آزمون را ندارد و نمی‌تواند در آزمون شرکت کند.'
            ]);
        }





        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'grade' => $student->grade?->name,
                'major' => $student->major?->name,
                'photo' => $student->photo
                    ? route('students.photo', ['filename' => basename($student->photo)])
                    : null,
            ]
        ]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'student_id' => 'required|exists:students,id',
            'signature' => 'nullable|file|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $signaturePath = null;

        // ذخیره امضا در فضای خصوصی
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('signatures', 'private');
        }

        // ثبت حضور
        // بررسی اینکه آیا دانش‌آموز قبلا برای این آزمون ثبت شده
        $exists = Attendance::where('exam_id', $request->exam_id)
            ->where('student_id', $request->student_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'این دانش‌آموز قبلا در این آزمون ثبت شده است.'
            ], 422); // یا هر کد وضعیت مناسب
        }

        // اگر وجود نداشت، رکورد جدید ایجاد شود
        $attendance = Attendance::create([
            'exam_id' => $request->exam_id,
            'student_id' => $request->student_id,
            'signature' => $signaturePath,
            'is_present' => true,
        ]);


        return response()->json([
            'success' => true,
            'message' => 'حضور با موفقیت ثبت شد.',
            'attendance' => $attendance
        ]);
    }


    public function showSignature(Attendance $attendance)
    {
        if (!$attendance->signature || !Storage::disk('private')->exists($attendance->signature)) {
            abort(404);
        }

        return response()->file(
            Storage::disk('private')->path($attendance->signature)
        );
    }
}
