<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AdvisorController,
    AttendanceController,
    StudentController,
    GradeController,
    MajorController,
    SchoolController,
    ProvinceController,
    CityController,
    ExamController,
    StudentProductController
};
use App\Http\Controllers\ProductController;




// صفحه اصلی → می‌تونه لیست دانش‌آموزان باشه
Route::get('/', [StudentController::class, 'index'])->name('home');

// ---------------------
// 🧑‍🎓 بخش دانش‌آموزان
// 🧑‍🎓 بخش محصولات
// ---------------------
Route::resource('students', StudentController::class);
Route::resource('products', ProductController::class);
Route::resource('exams', ExamController::class);

// ---------------------
// ⚙️ مدیریت گزینه‌های انتخابی
// ---------------------
Route::resource('grades', GradeController::class)->except(['show']);
Route::resource('majors', MajorController::class)->except(['show']);
Route::resource('schools', SchoolController::class)->except(['show']);
Route::resource('provinces', ProvinceController::class)->except(['show']);
Route::resource('cities', CityController::class)->except(['show']);
Route::resource('advisors', AdvisorController::class);

// ---------------------
// 🌍ajax for city
// ---------------------
Route::get('/cities/{province}', [CityController::class, 'getByProvince'])->name('cities.byProvince');



// add product to student
Route::get('student-products/create', [StudentProductController::class, 'create'])->name('student-products.create');
Route::post('student-products', [StudentProductController::class, 'store'])->name('student-products.store');



// get student image
Route::get('/student/photo/{filename}', [StudentController::class, 'showPhoto'])
    ->name('students.photo');



// دکمه حضور و غیاب خارج از ریسورس
Route::get('exams/{exam}/attendance', [ExamController::class, 'attendance'])->name('exams.attendance');







Route::get('/attendances/{exam}', [AttendanceController::class, 'create'])->name('attendances.create');
Route::post('/attendances/store', [AttendanceController::class, 'store'])->name('attendances.store');
Route::get('/student-info/by-national-code', [AttendanceController::class, 'findStudent'])->name('students.byNationalCode');
Route::get('/exams/{exam}/attendance', [ExamController::class, 'attendance'])->name('exams.attendance');

// دسترسی به امضا
Route::get('/signatures/{attendance}', [AttendanceController::class, 'showSignature'])->name('signatures.show'); 
