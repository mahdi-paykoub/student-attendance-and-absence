<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Supporter;
use App\Models\User;
use Illuminate\Http\Request;

class SupporterController extends Controller
{
    public function index()
    {
        $supporters = User::where('role', 'suporter')->get();
        return view('supporters.index', compact('supporters'));
    }



    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('supporters.index')->with('success', 'پشتبان با موفقیت حذف شد.');
    }



    public function assignStudentsForm(User $user)
    {
        $students = Student::whereDoesntHave('supporters')->get();
        return view('supporters.assign_students', compact('user', 'students'));
    }

    public function assignStudents(Request $request, User $user)
    {
        $validated = $request->validate([
            'students' => 'required|array',
            'students.*' => 'exists:students,id',
        ]);

        // اینجا از sync استفاده نمی‌کنیم چون می‌خواهیم مدیریت ارجاع داشته باشیم
        foreach ($validated['students'] as $studentId) {
            $user->students()->syncWithoutDetaching([$studentId]);
        }

        return redirect()->route('supporters.index')->with('success', 'دانش‌آموزان با موفقیت ارجاع داده شدند.');
    }

    public function showStudents(User $user)
    {
        // دانش‌آموزانی که این پشتیبان دارد
        $students = $user->students()->get();

        return view('supporters.students_list', compact('user', 'students'));
    }


    public function removeStudent(User $user, Student $student)
    {
        // حذف از pivot
        $user->students()->detach($student->id);

        return back()->with('success', 'دانش‌آموز با موفقیت از پشتیبان حذف شد.');
    }
}
