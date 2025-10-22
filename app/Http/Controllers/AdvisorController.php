<?php

namespace App\Http\Controllers;

use App\Models\Advisor;
use Illuminate\Http\Request;

class AdvisorController extends Controller
{
    public function index()
    {
        $advisors = Advisor::latest()->get();
        return view('partials.advisors.index', compact('advisors'));
    }

    public function create()
    {
        return view('partials.advisors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Advisor::create($request->only('name','phone'));

        return redirect()->route('advisors.index')->with('success', 'مشاور با موفقیت اضافه شد.');
    }

    public function edit(Advisor $advisor)
    {
        return view('partials.advisors.edit', compact('advisor'));
    }

    public function update(Request $request, Advisor $advisor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $advisor->update($request->only('name','phone'));

        return redirect()->route('advisors.index')->with('success', 'مشاور با موفقیت ویرایش شد.');
    }

    public function destroy(Advisor $advisor)
    {
        $advisor->delete();
        return redirect()->route('advisors.index')->with('success', 'مشاور حذف شد.');
    }
}
