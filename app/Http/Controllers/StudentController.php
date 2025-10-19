<?php

namespace App\Http\Controllers;

use App\Models\{Student, Grade, Major, School, Province, City};

use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('students.create', [
            'grades' => Grade::all(),
            'majors' => Major::all(),
            'schools' => School::all(),
            'provinces' => Province::all(),
            'cities' => City::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'father_name' => ['required', 'string', 'max:100'],
            'national_code' => ['required', 'digits:10', 'unique:students,national_code'],
            'mobile_student' => ['required', 'regex:/^09\d{9}$/'],
            'grade_id' => ['nullable', 'exists:grades,id'],
            'major_id' => ['nullable', 'exists:majors,id'],
            'school_id' => ['nullable', 'exists:schools,id'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'consultant_name' => ['nullable', 'string', 'max:100'],
            'referrer_name' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile_father' => ['nullable', 'regex:/^09\d{9}$/'],
            'mobile_mother' => ['nullable', 'regex:/^09\d{9}$/'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        Student::create($validated);

        return redirect()->route('students.index')->with('success', 'دانش‌آموز با موفقیت ثبت شد.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
