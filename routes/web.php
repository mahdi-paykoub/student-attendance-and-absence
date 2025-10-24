<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AdvisorController,
    AttendanceController,
    CheckController,
    StudentController,
    GradeController,
    MajorController,
    SchoolController,
    ExamController,
    PaymentCardController,
    SettingsController,
    StudentProductController,
    SeatNumberController,
    ProductController,
    UserController
};
use Illuminate\Support\Facades\Auth;
use Mews\Captcha\Facades\Captcha;





Route::middleware(['auth', 'is_admin'])->group(function () {

    // صفحه اصلی → می‌تونه لیست دانش‌آموزان باشه
    Route::get('/', [StudentController::class, 'index'])->name('home');

    // ---------------------
    // 🧑‍🎓 بخش دانش‌آموزان
    // 🧑‍🎓 بخش محصولات
    // ---------------------
    Route::resource('users', UserController::class);
    Route::resource('students', StudentController::class);
    Route::resource('products', ProductController::class);
    Route::resource('exams', ExamController::class);

    // ---------------------
    // ⚙️ مدیریت گزینه‌های انتخابی
    // ---------------------
    Route::resource('grades', GradeController::class)->except(['show']);
    Route::resource('majors', MajorController::class)->except(['show']);
    Route::resource('schools', SchoolController::class)->except(['show']);
    Route::resource('advisors', AdvisorController::class);
    Route::resource('payment-cards', PaymentCardController::class)->except(['show', 'edit', 'update']);



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





    // Route::get('/students/{student}/assign-products', [ProductAssignmentController::class, 'create'])->name('students.assign-products');
    // Route::post('/students/{student}/assign-products', [ProductAssignmentController::class, 'store'])->name('students.assign-products.store');


    Route::prefix('student-products')->name('student-products.')->group(function () {
        Route::get('/assign/{student}', [StudentProductController::class, 'assignForm'])->name('assign');
        Route::post('/assign/{student}', [StudentProductController::class, 'storeAssign'])->name('storeAssign');
    });

    // Route::get('/private/{path}', [PrivateFileController::class, 'show'])->where('path', '.*')->name('private.file');



    Route::get('/products/{product}/students', [ProductController::class, 'students'])
        ->name('products.students');


    Route::get('/students/{student}/details', [StudentController::class, 'details'])->name('students.details');


    Route::get('/payments/{payment}/receipt', [PaymentCardController::class, 'showReceipt'])
        ->name('payments.receipt');
    Route::get('/checks/{check}/image', [CheckController::class, 'showImage'])
        ->name('checks.image');



    Route::get('settings/exam-product', [SettingsController::class, 'editExamProduct'])->name('settings.editExamProduct');
    Route::put('settings/exam-product', [SettingsController::class, 'updateExamProduct'])->name('settings.updateExamProduct');


    Route::get('/seat-numbers', [SeatNumberController::class, 'index'])->name('seats.index');
    Route::post('/seat-numbers/generate', [SeatNumberController::class, 'generate'])->name('seats.generate');


    Route::post('/students-import', [StudentController::class, 'import'])->name('students.import');
    Route::get('/students-import-date', [StudentController::class, 'showImport'])->name('show.students.import');
    Route::post('/students-import-image/photos/upload', [StudentController::class, 'uploadImagesZip'])->name('students.photos.upload');



    Route::post('users/{user}/make-admin', [UserController::class, 'makeAdmin'])->name('users.makeAdmin');


   
});






Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/reload-captcha', function () {
    return captcha_img();
});
