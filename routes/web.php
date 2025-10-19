<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    StudentController,
    GradeController,
    MajorController,
    SchoolController,
    ProvinceController,
    CityController
};



// صفحه اصلی → می‌تونه لیست دانش‌آموزان باشه
Route::get('/', [StudentController::class, 'index'])->name('home');

// ---------------------
// 🧑‍🎓 بخش دانش‌آموزان
// ---------------------
Route::resource('students', StudentController::class);

// ---------------------
// ⚙️ مدیریت گزینه‌های انتخابی
// ---------------------
Route::resource('grades', GradeController::class)->except(['show']);
Route::resource('majors', MajorController::class)->except(['show']);
Route::resource('schools', SchoolController::class)->except(['show']);
Route::resource('provinces', ProvinceController::class)->except(['show']);
Route::resource('cities', CityController::class)->except(['show']);

// ---------------------
// 🌍 برای AJAX وابستگی استان ← شهر
// ---------------------
Route::get('/get-cities/{province}', [CityController::class, 'getCities'])->name('get.cities');
