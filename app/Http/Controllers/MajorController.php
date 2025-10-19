<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    public function index()
    {
        $majors = Major::latest()->get();
        return view('partials.majors.index', compact('majors'));
    }

    public function create()
    {
        return view('partials.majors.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        Major::create(['name' => $request->name]);
        return redirect()->route('majors.index')->with('success', 'رشته جدید افزوده شد.');
    }

    public function edit(Major $major)
    {
        return view('partials.majors.edit', compact('major'));
    }

    public function update(Request $request, Major $major)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $major->update(['name' => $request->name]);
        return redirect()->route('majors.index')->with('success', 'رشته ویرایش شد.');
    }

    public function destroy(Major $major)
    {
        $major->delete();
        return back()->with('success', 'رشته حذف شد.');
    }
}
