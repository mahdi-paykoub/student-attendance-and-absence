<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    // لیست آزمون‌ها
    public function index()
    {
        $exams = Exam::latest()->paginate(10);
        return view('exams.index', compact('exams'));
    }

    // فرم ایجاد آزمون
    public function create()
    {
        return view('exams.create');
    }

    // ذخیره آزمون جدید
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Exam::create($request->all());

        return redirect()->route('exams.index')->with('success', 'آزمون با موفقیت ثبت شد.');
    }

    // نمایش جزئیات آزمون
    public function show(Exam $exam)
    {
        return view('exams.show', compact('exam'));
    }

    // فرم ویرایش آزمون
    public function edit(Exam $exam)
    {
        return view('exams.edit', compact('exam'));
    }

    // بروزرسانی آزمون
    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $exam->update($request->all());

        return redirect()->route('exams.index')->with('success', 'آزمون با موفقیت بروزرسانی شد.');
    }

    // حذف آزمون
    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('exams.index')->with('success', 'آزمون با موفقیت حذف شد.');
    }



    public function attendance(Exam $exam)
    {
        // گرفتن همه حضورهای این آزمون همراه با اطلاعات دانش‌آموز
        $attendances = $exam->attendances()->with('student')->get();

        return view('exams.attendance', compact('exam', 'attendances'));
    }
}
