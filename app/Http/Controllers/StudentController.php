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
            'major_id'        => 'required|exists:majors,id',
            'school_id'       => 'required|exists:schools,id',
            'province_id'     => 'required|exists:provinces,id',
            'city_id'         => 'required|exists:cities,id',
            'photo'           => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'gender'          => 'required|in:male,female',
            'consultant_id'   => 'required',
            'referrer_id'     => 'required',
            'address'         => 'required|string|max:500',
            'mobile_mother'   => 'required|string|max:15',
            'mobile_father'   => 'required|string|max:15',
            'notes'           => 'nullable|string|max:1000',
            'phone'           => 'required|string|max:20',

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


    public function edit(Student $student)
    {
        // 🔹 دریافت لیست‌ها برای dropdownها
        $grades     = Grade::all();
        $majors     = Major::all();
        $schools    = School::all();
        $provinces  = Province::all();
        $cities     = City::where('province_id', $student->province_id)->get();
        $advisors = Advisor::all();

        // 🔹 ساخت مسیر تصویر (در صورت وجود)
        $photoUrl = null;
        if ($student->photo) {
            $filename = basename($student->photo); // فقط نام فایل بدون مسیر
            $photoUrl = route('students.photo', $filename);
        }

        return view('students.edit', compact(
            'student',
            'grades',
            'majors',
            'schools',
            'provinces',
            'cities',
            'photoUrl',
            'advisors'
        ));
    }




    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'father_name'     => 'required|string|max:255',
            'national_code'   => 'required|digits:10|unique:students,national_code,' . $student->id,
            'mobile_student'  => 'required|string|max:15',
            'grade_id'        => 'required|exists:grades,id',
            'major_id'        => 'nullable|exists:majors,id',
            'school_id'       => 'nullable|exists:schools,id',
            'province_id'     => 'nullable|exists:provinces,id',
            'city_id'         => 'nullable|exists:cities,id',
            'photo'           => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'gender'          => 'nullable|in:male,female',

            'consultant_name' => 'nullable|string|max:255',
            'referrer_name'   => 'nullable|string|max:255',
            'address'         => 'nullable|string|max:500',
            'mobile_mother'   => 'nullable|string|max:15',
            'mobile_father'   => 'nullable|string|max:15',
            'notes'           => 'nullable|string|max:1000',
            'phone'           => 'nullable|string|max:20',
        ]);

        if ($request->hasFile('photo')) {
            if ($student->photo && Storage::disk('private')->exists($student->photo)) {
                Storage::disk('private')->delete($student->photo);
            }

            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('students', $filename, 'private');
            $validated['photo'] = $path;
        } else {
            $validated['photo'] = $student->photo;
        }

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'اطلاعات دانش‌آموز با موفقیت ویرایش شد.');
    }


    public function destroy(Student $student)
    {
        if ($student->photo && Storage::disk('private')->exists($student->photo)) {
            Storage::disk('private')->delete($student->photo);
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'دانش‌آموز حذف شد.');
    }


    public function details(Student $student)
    {
        $student->load([
            'productStudents.product',
            'productStudents.payments',
            'productStudents.checks',
        ]);

        // مجموع مبلغ محصولات (با مالیات)
        $totalProducts = $student->productStudents->sum(function ($ps) {
            if (!$ps->product) return 0;

            $price = $ps->product->price;
            $taxPercent = $ps->product->tax_percent ?? 0;

            // اضافه کردن مالیات به قیمت
            $finalPrice = $price + ($price * ($taxPercent / 100));

            return $finalPrice;
        });

        // مجموع پرداخت‌های نقدی
        $totalPayments = $student->productStudents->sum(function ($ps) {
            return $ps->payments->sum('amount');
        });

        // مجموع چک‌ها
        $totalChecks = $student->productStudents->sum(function ($ps) {
            return $ps->checks->sum('amount');
        });

        // مجموع پرداختی کل (پرداخت نقدی + چک‌ها)
        $totalPaid = $totalPayments + $totalChecks;

        // بدهکاری (اگر پرداختی کمتر از محصولات بود)
        $debt = max(0, $totalProducts - $totalPaid);

        // بستانکاری (اگر پرداختی بیشتر از محصولات بود)
        $credit = max(0, $totalPaid - $totalProducts);

        return view('students.details', compact(
            'student',
            'totalProducts',
            'totalPayments',
            'totalChecks',
            'totalPaid',
            'debt',
            'credit'
        ));
    }

   
}
