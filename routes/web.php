<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    StudentController,
    GradeController,
    MajorController,
    SchoolController,
    ProvinceController,
    CityController,
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


// ---------------------
// ⚙️ مدیریت گزینه‌های انتخابی
// ---------------------
Route::resource('grades', GradeController::class)->except(['show']);
Route::resource('majors', MajorController::class)->except(['show']);
Route::resource('schools', SchoolController::class)->except(['show']);
Route::resource('provinces', ProvinceController::class)->except(['show']);
Route::resource('cities', CityController::class)->except(['show']);

// ---------------------
// 🌍ajax for city
// ---------------------
Route::get('/cities/{province}', [CityController::class, 'getByProvince'])->name('cities.byProvince');



// add product to student
Route::get('student-products/create', [StudentProductController::class, 'create'])->name('student-products.create');
Route::post('student-products', [StudentProductController::class, 'store'])->name('student-products.store');