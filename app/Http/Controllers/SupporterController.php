<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Supporter;
use Illuminate\Http\Request;

class SupporterController extends Controller
{
    public function index()
    {
        $supporters = Supporter::all();
        return view('supporters.index', compact('supporters'));
    }

    public function create()
    {
        return view('supporters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Supporter::create($validated);

        return redirect()->route('supporters.index')->with('success', 'پشتبان با موفقیت اضافه شد.');
    }

    public function destroy(Supporter $supporter)
    {
        $supporter->delete();
        return redirect()->route('supporters.index')->with('success', 'پشتبان با موفقیت حذف شد.');
    }



    public function assignStudentsForm(Supporter $supporter)
    {
        $students = Student::whereDoesntHave('supporters')->get();
        return view('supporters.assign_students', compact('supporter', 'students'));
    }

    public function assignStudents(Request $request, Supporter $supporter)
    {
        $validated = $request->validate([
            'students' => 'required|array',
            'students.*' => 'exists:students,id',
        ]);

        // اینجا از sync استفاده نمی‌کنیم چون می‌خواهیم مدیریت ارجاع داشته باشیم
        foreach ($validated['students'] as $studentId) {
            $supporter->students()->syncWithoutDetaching([$studentId]);
        }

        return redirect()->route('supporters.index')->with('success', 'دانش‌آموزان با موفقیت ارجاع داده شدند.');
    }

    public function showStudents(Supporter $supporter)
    {
        // دانش‌آموزانی که این پشتیبان دارد
        $students = $supporter->students()->get();

        return view('supporters.students_list', compact('supporter', 'students'));
    }


    public function removeStudent(Supporter $supporter, Student $student)
    {
        // حذف از pivot
        $supporter->students()->detach($student->id);

        return back()->with('success', 'دانش‌آموز با موفقیت از پشتیبان حذف شد.');
    }
}
