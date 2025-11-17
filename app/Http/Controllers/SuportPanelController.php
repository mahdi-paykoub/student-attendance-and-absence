<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class SuportPanelController extends Controller
{
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

        return view('suporter-panel.single-student', compact(
            'student',
            'currentSupporters',
            'otherSupporters'
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
}
