<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::latest()->get();
        return view('partials.grades.index', compact('grades'));
    }

    public function create()
    {
        return view('partials.grades.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:200']);
        Grade::create(['name' => $request->name]);
        return redirect()->route('grades.index')->with('success', 'پایه جدید افزوده شد.');
    }

    public function edit(Grade $grade)
    {
        return view('partials.grades.edit', compact('grade'));
    }

    public function update(Request $request, Grade $grade)
    {
        $request->validate([
            'name' => 'required|string|max:200'
        ]);

        $grade->update([
            'name' => $request->name
        ]);

        return redirect()->route('grades.index')->with('success', 'پایه با موفقیت ویرایش شد.');

      
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();
        return back()->with('success', 'پایه حذف شد.');
    }
}
