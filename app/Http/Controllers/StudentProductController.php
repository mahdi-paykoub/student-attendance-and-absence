<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Check;
use App\Models\Discount;
use App\Models\Grade;
use App\Models\Major;
use App\Models\Payment;
use App\Models\PaymentCard;
use App\Models\Product;
use App\Models\ProductStudent;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentAccountPercentage;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Facades\DB;



class StudentProductController extends Controller
{


    public function assignForm(Student $student)
    {


        $products = Product::where(function ($query) use ($student) {
            if ($student->grade_id !== null) {
                $query->where(function ($q) use ($student) {
                    $q->where('grade_id', $student->grade_id)
                        ->orWhereNull('grade_id');
                });
            }
            // اگر grade_id دانش آموز null بود، همه رکوردها می‌گیرن
        })
            ->where(function ($query) use ($student) {
                if ($student->major_id !== null) {
                    $query->where(function ($q) use ($student) {
                        $q->where('major_id', $student->major_id)
                            ->orWhereNull('major_id');
                    });
                }
            })
            ->where(function ($query) use ($student) {
                $query->where('is_active', true)
                    ->orWhereHas('students', function ($q) use ($student) {
                        $q->where('student_id', $student->id);
                    });
            })
            ->get();




        $assignedProducts = $student->products;


        $paymentCards = PaymentCard::all();
        $existingPayments = $student->payments()->get(); // نقدی و پیش‌پرداخت
        $existingChecks = $student->checks()->get();     // چک‌ها









        // گرفتن همه پرداخت‌ها
        $cashPayments = Payment::where('student_id', $student->id)
            ->where('payment_type', 'cash')
            ->get();

        $prepayments = Payment::where('student_id', $student->id)
            ->where('payment_type', 'installment')
            ->get();

        $checks = Check::where('student_id', $student->id)->get();

        return view('students.assign-products', [
            'student' => $student,
            'grade' => $student->grade?->name,
            'major' => $student->major?->name,
            'products' => $products,
            'assignedProducts' => $assignedProducts,

            'paymentCards' => $paymentCards,
            'existingPayments' => $existingPayments,
            'existingChecks' => $existingChecks,

            'cashPayments' => $cashPayments,
            'prepayments' => $prepayments,
            'checks' => $checks,



        ]);
    }


    public function updateAssignedProducts(Request $request, Student $student)
    {

        // ثبت کد تخفیف
        if ($request->has('amount') && $request->amount !== null) {
            $amount = (int) $request->amount;

            // updateOrCreate
            Discount::updateOrCreate(
                ['student_id' => $student->id],
                ['amount' => $amount]
            );
        }

        // آرایه محصولاتی که انتخاب شده (اگر چیزی انتخاب نشده باشه، آرایه خالی)
        $selectedProducts = $request->input('products', []);

        // sync خودش مدیریت میکنه: حذف قبلی، اضافه جدید
        $student->products()->sync($selectedProducts);

        // ================= بروزرسانی سهم مرکزی =================
        $centralAccount = \App\Models\Account::where('type', 'center')->first();
        $centralPercentage = \App\Models\StudentAccountPercentage::where('student_id', $student->id)
            ->where('account_id', $centralAccount->id)
            ->first();

        if ($centralPercentage) {
            $percent = $centralPercentage->percentage;

            $totalPrice = $student->products->sum('price');
            $central_share = $totalPrice * ($percent / 100);

            $totalTax = $student->products->sum(function ($product) {
                return $product->price * ($product->tax_percent / 100);
            });

            $final = $central_share + $totalTax;

            $wallet = \App\Models\Wallet::firstOrCreate(
                ['account_id' => $centralAccount->id],
                ['balance' => 0]
            );

            \App\Models\WalletTransaction::where('wallet_id', $wallet->id)
                ->whereJsonContains('meta->description', "Central contribution of the student: {$student->id}")
                ->delete();

            \App\Models\WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $final,
                'meta' => json_encode([
                    'description' => "Central contribution of the student: {$student->id}",
                    'for' => "ثبت محصول جدید برای دانش‌آموزِ: {$student->first_name} {$student->last_name}",
                ]),
                'status' => 'success'
            ]);

            $totalCentralBalance = \App\Models\WalletTransaction::where('wallet_id', $wallet->id)->sum('amount');
            $wallet->update(['balance' => $totalCentralBalance]);
        }
        // ========================================================

        // ================= بروزرسانی سهم نمایندگی =================
        $agencyAccount = \App\Models\Account::where('type', 'agency')->first();
        $agencyPercentage = \App\Models\StudentAccountPercentage::where('student_id', $student->id)
            ->where('account_id', $agencyAccount->id)
            ->first();

        if ($agencyPercentage) {
            $percent = $agencyPercentage->percentage;

            $totalProducts = $student->products->sum('price');
            $totalTax = $student->products->sum(function ($product) {
                return $product->price * ($product->tax_percent / 100);
            });
            $totalPayments = $student->payments()->sum('amount');

            $totalDue = ($totalProducts + $totalTax) - $totalPayments;

            $baseShare = $totalProducts * ($percent / 100);
            $agencyShare = $baseShare - $totalDue;

            $wallet = \App\Models\Wallet::firstOrCreate(
                ['account_id' => $agencyAccount->id],
                ['balance' => 0]
            );

            \App\Models\WalletTransaction::where('wallet_id', $wallet->id)
                ->whereJsonContains('meta->description', "Agency contribution of student: {$student->id}")
                ->delete();

            \App\Models\WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $agencyShare,
                'meta' => json_encode([
                    'description' => "Agency contribution of student: {$student->id}",
                    'for' => "ثبت محصول جدید برای دانش‌آموزِ: {$student->first_name} {$student->last_name}",
                ]),
                'status' => 'success'
            ]);

            $totalBalance = \App\Models\WalletTransaction::where('wallet_id', $wallet->id)->sum('amount');
            $wallet->balance = $totalBalance;
            $wallet->save();

            // partners
            // ======================================
            // مبلغی که الان برای نمایندگی ثبت شده (اثر خالص این دانش‌آموز)
            $agencyDeltaAmount = $agencyShare;

            $partners = Account::where('type', 'person')
                ->orderBy('id')
                ->limit(3)
                ->get();

            foreach ($partners as $partner) {

                if (!$partner->percentage || $agencyDeltaAmount == 0) {
                    continue;
                }

                // 1️⃣ محاسبه سهم شریک از همین دانش‌آموز
                $partnerShare = $agencyDeltaAmount * ($partner->percentage / 100);

                if ($partnerShare == 0) {
                    continue;
                }

                // 2️⃣ گرفتن یا ساخت کیف پول شریک
                $partnerWallet = Wallet::firstOrCreate(
                    ['account_id' => $partner->id],
                    ['balance' => 0]
                );

                // 3️⃣ حذف تراکنش قبلی این دانش‌آموز برای این شریک
                WalletTransaction::where('wallet_id', $partnerWallet->id)
                    ->whereJsonContains(
                        'meta->description',
                        "Partner share from student: {$student->id}"
                    )
                    ->delete();

                // 4️⃣ ثبت تراکنش جدید سهم شریک
                WalletTransaction::create([
                    'wallet_id' => $partnerWallet->id,
                    'type'      => 'deposit', // یا partner_share اگر enum جدا داری
                    'amount'    => $partnerShare,
                    'meta'      => json_encode([
                        'description' => "Partner share from student: {$student->id}",
                        'student_id'  => $student->id,
                        'agency_id'   => $agencyAccount->id,
                        'for' => "ثبت محصول جدید برای دانش‌آموزِ: {$student->first_name} {$student->last_name}",
                    ]),
                    'status'    => 'success'
                ]);

                // 5️⃣ محاسبه و بروزرسانی موجودی کیف پول شریک
                $newBalance = WalletTransaction::where('wallet_id', $partnerWallet->id)
                    ->sum('amount');

                $partnerWallet->update([
                    'balance' => $newBalance
                ]);
            }
            // ======================================

        }
        // ============================================================

        // گرفتن ID محصول اجباری از تنظیمات
        $mandatoryExamId = Setting::where('key', 'mandatory_exam_product_id')->value('value');

        // اگر محصول اجباری جزو محصولات انتخاب شده باشه، شماره صندلی تولید کن
        if (in_array($mandatoryExamId, $selectedProducts)) {
            DB::transaction(function () use ($mandatoryExamId) {
                $genders = ['male', 'female'];
                foreach ($genders as $gender) {
                    $seatNumber = ($gender === 'female') ? 1000 : 2000;
                    $grades = Grade::orderBy('id')->get();

                    foreach ($grades as $grade) {
                        $majors = Major::orderBy('id')->get();

                        foreach ($majors as $major) {
                            $students = Student::where('gender', $gender)
                                ->where('grade_id', $grade->id)
                                ->where('major_id', $major->id)
                                ->orderBy('id')
                                ->get();

                            foreach ($students as $s) {
                                $hasMandatory = $s->products()->where('product_id', $mandatoryExamId)->exists();
                                if ($hasMandatory) {
                                    $s->update(['seat_number' => $seatNumber++]);
                                }
                            }
                        }
                    }
                }
            });
        }

        return redirect()->back()->with('success', 'محصولات دانش‌آموز با موفقیت بروزرسانی شد.');
    }







    public function storePayments(Request $request, Student $student)
    {
        $paymentType = $request->input('payment_type');

        // 🔹 پرداخت‌های نقدی و پیش‌پرداخت‌ها
        if ($request->has('cash_amount')) {



            foreach ($request->cash_amount as $index => $amount) {

                $jalaliDateTime = $request->cash_date[$index] ?? '';

                if (empty($jalaliDateTime)) continue;

                // جایگزینی - با / اگر لازم بود
                $jalaliDateTime = str_replace('-', '/', $jalaliDateTime);

                // تبدیل به Carbon میلادی
                try {
                    $gregorianDateTime = Jalalian::fromFormat('Y/m/d H:i:s', $jalaliDateTime)->toCarbon();
                } catch (\Exception $e1) {
                    try {
                        $gregorianDateTime = Jalalian::fromFormat('Y/m/d H:i', $jalaliDateTime)->toCarbon();
                    } catch (\Exception $e2) {
                        try {
                            $gregorianDateTime = Jalalian::fromFormat('Y/m/d', $jalaliDateTime)->toCarbon();
                        } catch (\Exception $e3) {
                            $gregorianDateTime = now(); // مقدار پیش‌فرض
                        }
                    }
                }

                $payment = new Payment();
                $payment->student_id = $student->id;
                $payment->payment_type = $paymentType;
                $payment->date = $gregorianDateTime; // فقط یک ستون datetime
                $payment->amount = $amount;
                $payment->voucher_number = $request->cash_receipt[$index] ?? null;
                $payment->payment_card_id = $request->cash_card_id[$index] ?? null;

                // آپلود تصویر

                if ($request->hasFile('cash_image.' . $index)) {
                    $file = $request->file('cash_image.' . $index);
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('payments', $filename, 'private');
                    $payment->receipt_image = $path;
                }

                $payment->save();

                // === شارژ کیف پول نمایندگی فقط اگر درصد نمایندگی تعیین شده باشد ===
                $agencyPercentage = StudentAccountPercentage::where('student_id', $student->id)
                    ->whereHas('account', function ($q) {
                        $q->where('type', 'agency');
                    })
                    ->first();

                if ($agencyPercentage) {

                    $percent = $agencyPercentage->percentage;

                    // گرفتن طرف حساب نمایندگی
                    $agencyAccount = $agencyPercentage->account;

                    // کیف پول نمایندگی (اگر نبود ایجاد می‌شود)
                    $wallet = Wallet::firstOrCreate(
                        ['account_id' => $agencyAccount->id],
                        ['balance' => 0]
                    );

                    // سهم نمایندگی از همین پرداخت جدید
                    $agencyShare = $payment->amount;

                    // ثبت تراکنش در کیف پول
                    WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'type' => 'deposit',
                        'amount' => $agencyShare,
                        'meta' => json_encode([
                            'description' => "Agency share from payment ID: {$payment->id} for student ID: {$student->id}",
                            'for' => "ثبت پرداخت جدید برای دانش‌آموزِ: {$student->first_name} {$student->last_name}",
                        ]),
                        'status' => 'success'
                    ]);

                    // بروزرسانی موجودی کیف پول
                    $newBalance = WalletTransaction::where('wallet_id', $wallet->id)->sum('amount');
                    $wallet->balance = $newBalance;
                    $wallet->save();

                    // partners
                    // ======================================

                    // مبلغی که الان از این پرداخت وارد کیف پول نمایندگی شده
                    $agencyDeltaAmount = $payment->amount;

                    $partners = Account::where('type', 'person')
                        ->orderBy('id')
                        ->limit(3)
                        ->get();

                    foreach ($partners as $partner) {

                        if (!$partner->percentage || $agencyDeltaAmount == 0) {
                            continue;
                        }

                        // 1️⃣ سهم شریک فقط از همین پرداخت
                        $partnerShare = $agencyDeltaAmount * ($partner->percentage / 100);

                        if ($partnerShare == 0) {
                            continue;
                        }

                        // 2️⃣ گرفتن یا ساخت کیف پول شریک
                        $partnerWallet = Wallet::firstOrCreate(
                            ['account_id' => $partner->id],
                            ['balance' => 0]
                        );

                        // 3️⃣ ثبت تراکنش سهم شریک
                        WalletTransaction::create([
                            'wallet_id' => $partnerWallet->id,
                            'type'      => 'deposit',
                            'amount'    => $partnerShare,
                            'meta'      => json_encode([
                                'description' => 'Partner share from payment',
                                'payment_id'  => $payment->id,
                                'student_id'  => $student->id,
                                'agency_id'   => $agencyAccount->id,
                                'for' => "ثبت پرداخت جدید برای دانش‌آموزِ: {$student->first_name} {$student->last_name}",
                            ]),
                            'status'    => 'success'
                        ]);

                        // 4️⃣ محاسبه موجودی جدید شریک
                        $newBalance = WalletTransaction::where('wallet_id', $partnerWallet->id)
                            ->sum('amount');

                        // 5️⃣ بروزرسانی موجودی
                        $partnerWallet->update([
                            'balance' => $newBalance
                        ]);
                    }
                    // ======================================



                }
            }
        }
        // 🔹 پیش‌پرداخت‌ها
        if ($request->has('pre_amount')) {
            foreach ($request->pre_amount as $index => $amount) {

                $jalaliDateTime_pre = $request->pre_date[$index] ?? '';

                if (empty($jalaliDateTime_pre)) continue;

                $jalaliDateTime_pre = str_replace('-', '/', $jalaliDateTime_pre);

                try {
                    $gregorianDateTime_pre = Jalalian::fromFormat('Y/m/d H:i:s', $jalaliDateTime_pre)->toCarbon();
                } catch (\Exception $e1) {
                    try {
                        $gregorianDateTime_pre = Jalalian::fromFormat('Y/m/d H:i', $jalaliDateTime_pre)->toCarbon();
                    } catch (\Exception $e2) {
                        try {
                            $gregorianDateTime_pre = Jalalian::fromFormat('Y/m/d', $jalaliDateTime_pre)->toCarbon();
                        } catch (\Exception $e3) {
                            $gregorianDateTime_pre = now();
                        }
                    }
                }

                $payment = new Payment();
                $payment->student_id = $student->id;
                $payment->payment_type = 'installment'; // چون پیش‌پرداخت
                $payment->date = $gregorianDateTime_pre;
                $payment->amount = $amount;
                $payment->voucher_number = $request->pre_receipt[$index] ?? null;
                $payment->payment_card_id = $request->pre_card_id[$index] ?? null;

                if ($request->hasFile('pre_image.' . $index)) {
                    $file = $request->file('pre_image.' . $index);
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('payments', $filename, 'private');
                    $payment->receipt_image = $path;
                }

                $payment->save();

                // === شارژ کیف پول نمایندگی فقط اگر درصد نمایندگی تعیین شده باشد ===
                $agencyPercentage = StudentAccountPercentage::where('student_id', $student->id)
                    ->whereHas('account', function ($q) {
                        $q->where('type', 'agency');
                    })
                    ->first();

                if ($agencyPercentage) {

                    $percent = $agencyPercentage->percentage;

                    // گرفتن طرف حساب نمایندگی
                    $agencyAccount = $agencyPercentage->account;

                    // کیف پول نمایندگی (اگر نبود ایجاد می‌شود)
                    $wallet = Wallet::firstOrCreate(
                        ['account_id' => $agencyAccount->id],
                        ['balance' => 0]
                    );

                    // سهم نمایندگی از همین پرداخت جدید
                    $agencyShare = $payment->amount;

                    // ثبت تراکنش در کیف پول
                    WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'type' => 'deposit',
                        'amount' => $agencyShare,
                        'meta' => json_encode([
                            'description' => "Agency share from payment ID: {$payment->id} for student ID: {$student->id}",
                            'for' => "ثبت پرداخت جدید برای دانش‌آموزِ: {$student->first_name} {$student->last_name}",
                        ]),
                        'status' => 'success'
                    ]);

                    // بروزرسانی موجودی کیف پول
                    $newBalance = WalletTransaction::where('wallet_id', $wallet->id)->sum('amount');
                    $wallet->balance = $newBalance;
                    $wallet->save();

                    // partners
                    // ======================================

                    // مبلغی که الان از این پرداخت وارد کیف پول نمایندگی شده
                    $agencyDeltaAmount = $payment->amount;

                    $partners = Account::where('type', 'person')
                        ->orderBy('id')
                        ->limit(3)
                        ->get();

                    foreach ($partners as $partner) {

                        if (!$partner->percentage || $agencyDeltaAmount == 0) {
                            continue;
                        }

                        // سهم شریک فقط از همین پرداخت
                        $partnerShare = $agencyDeltaAmount * ($partner->percentage / 100);

                        if ($partnerShare == 0) {
                            continue;
                        }

                        // گرفتن یا ساخت کیف پول شریک
                        $partnerWallet = Wallet::firstOrCreate(
                            ['account_id' => $partner->id],
                            ['balance' => 0]
                        );

                        // ثبت تراکنش سهم شریک
                        WalletTransaction::create([
                            'wallet_id' => $partnerWallet->id,
                            'type'      => 'deposit',
                            'amount'    => $partnerShare,
                            'meta'      => json_encode([
                                'description' => 'Partner share from prepayment',
                                'payment_id'  => $payment->id,
                                'student_id'  => $student->id,
                                'agency_id'   => $agencyAccount->id,
                                'for' => "ثبت پرداخت جدید برای دانش‌آموزِ: {$student->first_name} {$student->last_name}",
                            ]),
                            'status'    => 'success'
                        ]);

                        // محاسبه موجودی جدید شریک
                        $newBalance = WalletTransaction::where('wallet_id', $partnerWallet->id)
                            ->sum('amount');

                        // بروزرسانی موجودی
                        $partnerWallet->update([
                            'balance' => $newBalance
                        ]);
                    }
                    // ======================================

                }
            }
        }

        // 🔹 چک‌ها
        if ($request->has('check_amount')) {
            foreach ($request->check_amount as $index => $amount) {

                $jalaliDateTime_checks = $request->check_date[$index] ?? '';

                if (empty($jalaliDateTime_checks)) continue;

                // جایگزینی - با / اگر لازم بود
                $jalaliDateTime_checks = str_replace('-', '/', $jalaliDateTime_checks);

                // تبدیل به Carbon میلادی
                try {
                    $gregorianDateTime_ck = Jalalian::fromFormat('Y/m/d H:i:s', $jalaliDateTime_checks)->toCarbon();
                } catch (\Exception $e1) {
                    try {
                        $gregorianDateTime_ck = Jalalian::fromFormat('Y/m/d H:i', $jalaliDateTime_checks)->toCarbon();
                    } catch (\Exception $e2) {
                        try {
                            $gregorianDateTime_ck = Jalalian::fromFormat('Y/m/d', $jalaliDateTime_checks)->toCarbon();
                        } catch (\Exception $e3) {
                            $gregorianDateTime_ck = now(); // مقدار پیش‌فرض
                        }
                    }
                }




                $check = new Check();
                $check->student_id = $student->id;
                $check->date = $gregorianDateTime_ck; // datetime
                $check->amount = $amount;
                $check->serial = $request->check_serial[$index];
                $check->sayad_code = $request->check_sayad[$index];
                $check->owner_name = $request->check_owner_name[$index];
                $check->owner_national_code = $request->check_owner_national[$index];
                $check->owner_phone = $request->check_owner_phone[$index];

                // آپلود تصویر
                if ($request->hasFile('check_image.' . $index)) {
                    $file = $request->file('check_image.' . $index);
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('checks', $filename, 'private');
                    $check->check_image = $path;
                }


                $check->save();
            }
        }

        return back()->with('success', 'پرداخت‌ها با موفقیت ذخیره شدند.');
    }


    public function deletePayment($type, $id)
    {
        if ($type == 'payment') {
            $payment = Payment::findOrFail($id);

            // گرفتن student
            $student = $payment->student;

            // گرفتن درصد نمایندگی
            $agencyPercentage = StudentAccountPercentage::where('student_id', $student->id)
                ->whereHas('account', function ($q) {
                    $q->where('type', 'agency');
                })
                ->first();

            if ($agencyPercentage) {

                $agencyAccount = $agencyPercentage->account;

                // گرفتن کیف پول نمایندگی
                $wallet = Wallet::firstOrCreate(
                    ['account_id' => $agencyAccount->id],
                    ['balance' => 0]
                );

                // سهم نمایندگی همان مبلغ پرداخت است 
                $agencyShare = $payment->amount;

                // ثبت تراکنش برداشت هنگام حذف پرداخت
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'withdraw',
                    'amount' => - ($agencyShare),
                    'meta' => json_encode([
                        'description' => "Revert agency share due to payment deletion. Payment ID: {$payment->id}",
                        'for' => "حذف پرداخت برای دانش‌آموزِ: {$student->first_name} {$student->last_name}",
                    ]),
                    'status' => 'success'
                ]);


                $newBalance = WalletTransaction::where('wallet_id', $wallet->id)->sum('amount');
                $wallet->balance = $newBalance;
                $wallet->save();

                // partners (revert)
                // ======================================

                // فقط اثر همین پرداخت باید برگردد
                $agencyDeltaAmount = $payment->amount;

                $partners = Account::where('type', 'person')
                    ->orderBy('id')
                    ->limit(3)
                    ->get();

                foreach ($partners as $partner) {

                    if (!$partner->percentage || $agencyDeltaAmount == 0) {
                        continue;
                    }

                    // سهم شریک از همین پرداخت
                    $partnerShare = $agencyDeltaAmount * ($partner->percentage / 100);

                    if ($partnerShare == 0) {
                        continue;
                    }

                    // گرفتن یا ساخت کیف پول شریک
                    $partnerWallet = Wallet::firstOrCreate(
                        ['account_id' => $partner->id],
                        ['balance' => 0]
                    );

                    // ثبت تراکنش برداشت (برگشت سهم)
                    WalletTransaction::create([
                        'wallet_id' => $partnerWallet->id,
                        'type'      => 'withdraw',
                        'amount'    => -$partnerShare,
                        'meta'      => json_encode([
                            'description' => 'Revert partner share due to payment deletion',
                            'payment_id'  => $payment->id,
                            'student_id'  => $student->id,
                            'agency_id'   => $agencyAccount->id,
                            'for' => "حذف پرداخت برای دانش‌آموزِ: {$student->first_name} {$student->last_name}",
                        ]),
                        'status'    => 'success'
                    ]);

                    // محاسبه موجودی جدید شریک
                    $newBalance = WalletTransaction::where('wallet_id', $partnerWallet->id)
                        ->sum('amount');

                    // بروزرسانی موجودی
                    $partnerWallet->update([
                        'balance' => $newBalance
                    ]);
                }
                // ======================================


            }

            // در آخر حذف پرداخت
            $payment->delete();
        } elseif ($type == 'check') {
            Check::findOrFail($id)->delete();
        }

        return response()->json(['success' => true, 'message' => 'با موفقیت حذف شد']);
    }
}
