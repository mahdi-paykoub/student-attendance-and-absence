<?php

namespace App\Http\Controllers;

use App\Models\Advisor;
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
        $advisors = Advisor::all(); // همه مراقبین
        return view('exams.create', compact('advisors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'domain_manager' => 'required|string|max:255',
            'exam_datetime' => 'required|date',
            'supervisors' => 'required|array|min:1',
        ]);

        $exam = Exam::create([
            'name' => $request->name,
            'domain' => $request->domain,
            'domain_manager' => $request->domain_manager,
            'exam_datetime' => $request->exam_datetime,
        ]);

        $exam->supervisors()->attach($request->supervisors);
        return redirect()->route('exams.index')->with('success', 'آزمون با موفقیت اضافه شد.');
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
