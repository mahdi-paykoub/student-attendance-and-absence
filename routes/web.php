<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AccountingController,
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
    ReportController,
    SmsTemplateController,
    SupporterController,
    UserController
};
use Illuminate\Support\Facades\Auth;





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
        Route::put('/assign/{student}', [StudentProductController::class, 'updateAssignedProducts'])->name('storeAssign.product');
        Route::post('/assign/{student}/payments', [StudentProductController::class, 'storePayments'])->name('storePayments');
        Route::delete('/delete-payment/{type}/{id}', [StudentProductController::class, 'deletePayment'])
            ->name('deletePayment');
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



    Route::put('/students/{student}/update-date', [StudentController::class, 'updateDate'])
        ->name('students.updateDate');


    Route::post('/products/{product}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');



    // reports
    Route::get('reports/seats', [ReportController::class, 'seatNumberView'])->name('report.seatsNumber.view');
    Route::get('reports/get/students/pdf', [ReportController::class, 'generatePdf'])->name('report.students.pdf.generate');
    Route::get('reports/get/students/custom-data', [ReportController::class, 'customDataView'])->name('report.student.custom.data.view');
    Route::get('reports/get/students/custom-data/pdf', [ReportController::class, 'generateStudentsCustomFielsPdf'])->name('report.student.custom.data.pdf');

    Route::get('reports/get/debtor/students/view', [ReportController::class, 'getDebtorStudemtsView'])->name('report.get.debtor.students.view');
    Route::get('reports/get/debtor/students/pdf', [ReportController::class, 'getDebtorStudemtsPdf'])->name('report.get.debtor.students.pdf');


    Route::get('reports/get/deposits/view', [ReportController::class, 'getDepositssView'])->name('report.get.deposits.view');
    Route::get('reports/get/deposits/pdf', [ReportController::class, 'getDdepositsPdf'])->name('report.get.deposits.pdf');



    Route::get('reports/get/checks/view', [ReportController::class, 'getChecksView'])->name('report.get.checks.view');
    Route::get('reports/get/checks/pdf', [ReportController::class, 'getChecksPdf'])->name('report.get.checks.pdf');

    Route::get('reports/sms', [ReportController::class, 'smsReportsView'])->name('report.sms');





    // Accounting
    Route::get('accounting/register/percentage/view', [AccountingController::class, 'registerPercantageView'])->name('accounting.register.percentage.view');
    Route::post('accounting/register/centarl/percentage/{student}', [AccountingController::class, 'registerCentralPercantage'])->name('accounting.register.centarl.percentage');
    Route::post('accounting/register/agency/percentage/{student}', [AccountingController::class, 'registerAgencyPercentage'])->name('accounting.register.agency.percentage');

    // partners
    Route::get('accounting/partners/view', [AccountingController::class, 'partnersView'])->name('accounting.partners.view');
    Route::post('accounting/partners/create', [AccountingController::class, 'createPartners'])->name('accounting.partners.create');


    //costs
    Route::get('accounting/costs/view', [AccountingController::class, 'costsView'])->name('accounting.costs.view');
    Route::post('accounting/costs/create', [AccountingController::class, 'costsCreate'])->name('accounting.costs.create');
    Route::get('accounting/get/costs/image/{filename}', [AccountingController::class, 'getImageCosts'])->name('get.image.costs');

    //deposits
    Route::get('accounting/deposits/view', [AccountingController::class, 'deposistView'])->name('accounting.deposits.view');
    Route::post('accounting/deposits/create', [AccountingController::class, 'deposistCreate'])->name('accounting.deposits.create');
    Route::get('accounting/get/deposits/image/{filename}', [AccountingController::class, 'getImageDeposits'])->name('get.image.deposits');


    // sms panel
    Route::get('sms/createor/view', [SmsTemplateController::class, 'smsCreateorView'])->name('sms.createor.view');
    Route::post('sms/store/sms-template', [SmsTemplateController::class, 'storeSmsTemplate'])->name('sms.store.sms.template');
    Route::get('sms/send/view', [SmsTemplateController::class, 'sendSmsView'])->name('sms.send.view');
    Route::post('/sms/send', [SmsTemplateController::class, 'sendSms'])->name('sms.send');


    // suporters
    Route::get('/supporters', [SupporterController::class, 'index'])->name('supporters.index');
    Route::get('/supporters/{user}/assign-students', [SupporterController::class, 'assignStudentsForm'])->name('supporters.assign.form');
    Route::post('/supporters/{user}/assign-students', [SupporterController::class, 'assignStudents'])->name('supporters.assign.store');
    Route::delete('/supporters/delete/{user}', [SupporterController::class, 'destroy'])->name('supporters.destroy');

    Route::get('/supporters/{user}/students', [SupporterController::class, 'showStudents'])->name('supporters.show_students');


    Route::delete('/supporters/{user}/students/{student}', [SupporterController::class, 'removeStudent'])->name('supporters.remove_student');
});






Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/reload-captcha', function () {
    return captcha_img();
});
