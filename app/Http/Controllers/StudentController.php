<?php

namespace App\Http\Controllers;

use App\Models\Advisor;
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
    public function index(Request $request)
    {
        $filter = $request->get('filter'); // all | with | without

        $students = Student::with(['grade', 'major', 'school', 'province', 'city', 'products'])
            ->when($filter === 'with', function ($query) {
                $query->whereHas('products');
            })
            ->when($filter === 'without', function ($query) {
                $query->whereDoesntHave('products');
            })
            ->paginate(10);

        return view('students.index', compact('students', 'filter'));
    }

    /**
     * فرم ایجاد دانش‌آموز جدید
     */
    public function create()
    {
        $advisors = Advisor::all();
        $grades = Grade::all();
        $majors = Major::all();
        $schools = School::all();
        $provinces = Province::all();

        return view('students.create', compact('grades', 'majors', 'schools', 'provinces', 'advisors'));
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
            'gender'          => 'nullable|in:male,female',
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
    public function show($id) {}
    /**
     * نمایش عکس دانش‌آموز از مسیر private
     */
    public function showPhoto($filename)
    {
        $path = 'students/' . $filename;

        if (!Storage::disk('private')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('private')->get($path);
        $type = Storage::disk('private')->mimeType($path);

        return response($file, 200)
            ->header('Content-Type', $type);
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
