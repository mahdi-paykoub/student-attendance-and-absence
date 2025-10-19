<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Grade;
use App\Models\Major;
use App\Models\School;
use App\Models\Province;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * لیست دانش‌آموزان
     */
    public function index()
    {
        $students = Student::with(['grade', 'major', 'school', 'province', 'city'])
            ->latest()
            ->paginate(10);

        return view('students.index', compact('students'));
    }

    /**
     * فرم ایجاد دانش‌آموز جدید
     */
    public function create()
    {
        $grades = Grade::all();
        $majors = Major::all();
        $schools = School::all();
        $provinces = Province::all();

        return view('students.create', compact('grades', 'majors', 'schools', 'provinces'));
    }

    /**
     * ذخیره دانش‌آموز جدید
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'father_name'     => 'required|string|max:255',
            'national_code'   => 'required|digits:10|unique:students,national_code',
            'mobile_student'  => 'required|string|max:15',
            'grade_id'        => 'required|exists:grades,id',
            'major_id'        => 'nullable|exists:majors,id',
            'school_id'       => 'nullable|exists:schools,id',
            'province_id'     => 'nullable|exists:provinces,id',
            'city_id'         => 'nullable|exists:cities,id',
            'photo'           => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // 🔹 ذخیره عکس در مسیر private/students با نام امن
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('students', $filename, 'private');
            $validated['photo'] = $path;
        }

        Student::create($validated);

        return redirect()->route('students.index')->with('success', 'دانش‌آموز با موفقیت ثبت شد.');
    }

    /**
     * نمایش عکس دانش‌آموز از مسیر private
     */
    public function showPhoto($id)
    {
        $student = Student::findOrFail($id);

        if (!$student->photo || !Storage::disk('private')->exists($student->photo)) {
            abort(404);
        }

        // ⚠️ اینجا می‌تونی کنترل دسترسی بذاری (مثلاً فقط ادمین یا معلم‌ها)
        // if (!auth()->check() || !auth()->user()->isAdmin()) abort(403);

        return response()->file(Storage::disk('private')->path($student->photo));
    }

    /**
     * حذف دانش‌آموز
     */
    public function destroy(Student $student)
    {
        // حذف عکس از storage
        if ($student->photo && Storage::disk('private')->exists($student->photo)) {
            Storage::disk('private')->delete($student->photo);
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'دانش‌آموز حذف شد.');
    }
}
