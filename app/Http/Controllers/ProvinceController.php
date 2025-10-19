<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function index()
    {
        $provinces = Province::latest()->get();
        return view('partials.provinces.index', compact('provinces'));
    }

    public function create()
    {
        return view('partials.provinces.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        Province::create(['name' => $request->name]);
        return redirect()->route('provinces.index')->with('success', 'استان جدید افزوده شد.');
    }

    public function edit(Province $province)
    {
        return view('partials.provinces.edit', compact('province'));
    }

    public function update(Request $request, Province $province)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $province->update(['name' => $request->name]);
        return redirect()->route('provinces.index')->with('success', 'استان ویرایش شد.');
    }

    public function destroy(Province $province)
    {
        $province->delete();
        return back()->with('success', 'استان حذف شد.');
    }
}
