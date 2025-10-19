<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Grade;
use App\Models\Province;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::with('province')->latest()->get();
        return view('partials.cities.index', compact('cities'));
    }

    public function create()
    {
        $provinces = Province::all();
        return view('partials.cities.create', compact('provinces'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'province_id' => 'required|exists:provinces,id',
        ]);

        City::create([
            'name' => $request->name,
            'province_id' => $request->province_id,
        ]);

        return redirect()->route('cities.index')->with('success', 'شهر جدید افزوده شد.');
    }

    public function edit(City $city)
    {
        $provinces = Province::all();
        return view('partials.cities.edit', compact('city', 'provinces'));
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'province_id' => 'required|exists:provinces,id',
        ]);

        $city->update([
            'name' => $request->name,
            'province_id' => $request->province_id,
        ]);

        return redirect()->route('cities.index')->with('success', 'شهر ویرایش شد.');
    }

    public function destroy(City $city)
    {
        $city->delete();
        return back()->with('success', 'شهر حذف شد.');
    }
}
