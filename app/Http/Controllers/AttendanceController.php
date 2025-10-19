<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Exam;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function create(Exam $exam)
    {
        return view('attendances.create', compact('exam'));
    }


    public function findStudent(Request $request)
    {
        // بررسی اینکه چنین کد ملی محصول مورد نظر را دارد یا نع؟

        $student = Student::where('national_code', $request->national_code)->first();

        if (!$student) {
            return response()->json(['success' => false]);
        }

        return response()->json([
            'success' => true,
            'student' => [
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'grade' => $student->grade?->name,
                'major' => $student->major?->name,
                'photo' => asset('storage/' . $student->photo),
            ]
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'student_id' => 'required|exists:students,id',
            'signature' => 'required|file|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $path = $request->file('signature')->store('signatures', 'public');

        Attendance::create([
            'exam_id' => $request->exam_id,
            'student_id' => $request->student_id,
            'signature' => $path,
            'is_present' => true,
        ]);

        return back()->with('success', 'حضور با موفقیت ثبت شد.');
    }
}
