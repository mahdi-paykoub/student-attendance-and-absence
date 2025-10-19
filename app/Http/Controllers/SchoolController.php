<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
     public function index()
    {
        $schools = School::latest()->get();
        return view('partials.schools.index', compact('schools'));
    }

    public function create()
    {
        return view('partials.schools.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        School::create(['name' => $request->name]);
        return redirect()->route('schools.index')->with('success', 'مدرسه جدید افزوده شد.');
    }

    public function edit(School $school)
    {
        return view('partials.schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $school->update(['name' => $request->name]);
        return redirect()->route('schools.index')->with('success', 'مدرسه ویرایش شد.');
    }

    public function destroy(School $school)
    {
        $school->delete();
        return back()->with('success', 'مدرسه حذف شد.');
    }
}
