<?php

    public function updateAssignedProducts(Request $request, Student $student)
    {
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
                    'description' => "Central contribution of the student: {$student->id}"
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
                    'description' => "Agency contribution of student: {$student->id}"
                ]),
                'status' => 'success'
            ]);

            $totalBalance = \App\Models\WalletTransaction::where('wallet_id', $wallet->id)->sum('amount');
            $wallet->balance = $totalBalance;
            $wallet->save();
            // partners
            // ======================================
            $totalAmount = $wallet->balance;
            $partners = Account::where('type', 'person')
                ->orderBy('id')
                ->limit(3)
                ->get();
            foreach ($partners as $partner) {
                if ($partner->percentage) {
                    // 3) محاسبه سهم شریک
                    $partnerShare = $totalAmount * ($partner->percentage / 100);
                    // 4) گرفتن کیف پول شریک
                    $partnerWallet = Wallet::where('account_id', $partner->id)->first();
                    // اگر کیف پول شریک هنوز وجود ندارد → بساز
                    if (!$partnerWallet) {
                        $partnerWallet = Wallet::create([
                            'account_id' => $partner->id,
                            'balance' => 0
                        ]);
                    }

                    // 5) بروزرسانی مبلغ کیف پول شریک
                    $partnerWallet->update([
                        'balance' => $partnerShare
                    ]);
                }
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
                            'description' => "Agency share from payment ID: {$payment->id} for student ID: {$student->id}"
                        ]),
                        'status' => 'success'
                    ]);

                    // بروزرسانی موجودی کیف پول
                    $newBalance = WalletTransaction::where('wallet_id', $wallet->id)->sum('amount');
                    $wallet->balance = $newBalance;
                    $wallet->save();

                    // partners
                    // ======================================
                    $totalAmount = $wallet->balance;
                    $partners = Account::where('type', 'person')
                        ->orderBy('id')
                        ->limit(3)
                        ->get();
                    foreach ($partners as $partner) {
                        if ($partner->percentage) {
                            // 3) محاسبه سهم شریک
                            $partnerShare = $totalAmount * ($partner->percentage / 100);
                            // 4) گرفتن کیف پول شریک
                            $partnerWallet = Wallet::where('account_id', $partner->id)->first();
                            // اگر کیف پول شریک هنوز وجود ندارد → بساز
                            if (!$partnerWallet) {
                                $partnerWallet = Wallet::create([
                                    'account_id' => $partner->id,
                                    'balance' => 0
                                ]);
                            }

                            // 5) بروزرسانی مبلغ کیف پول شریک
                            $partnerWallet->update([
                                'balance' => $partnerShare
                            ]);
                        }
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
                            'description' => "Agency share from payment ID: {$payment->id} for student ID: {$student->id}"
                        ]),
                        'status' => 'success'
                    ]);

                    // بروزرسانی موجودی کیف پول
                    $newBalance = WalletTransaction::where('wallet_id', $wallet->id)->sum('amount');
                    $wallet->balance = $newBalance;
                    $wallet->save();

                    // partners
                    // ======================================
                    $totalAmount = $wallet->balance;
                    $partners = Account::where('type', 'person')
                        ->orderBy('id')
                        ->limit(3)
                        ->get();
                    foreach ($partners as $partner) {
                        if ($partner->percentage) {
                            // 3) محاسبه سهم شریک
                            $partnerShare = $totalAmount * ($partner->percentage / 100);
                            // 4) گرفتن کیف پول شریک
                            $partnerWallet = Wallet::where('account_id', $partner->id)->first();
                            // اگر کیف پول شریک هنوز وجود ندارد → بساز
                            if (!$partnerWallet) {
                                $partnerWallet = Wallet::create([
                                    'account_id' => $partner->id,
                                    'balance' => 0
                                ]);
                            }

                            // 5) بروزرسانی مبلغ کیف پول شریک
                            $partnerWallet->update([
                                'balance' => $partnerShare
                            ]);
                        }
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

                // سهم نمایندگی همان مبلغ پرداخت است (طبق کد شما)
                $agencyShare = $payment->amount;

                // ثبت تراکنش برداشت هنگام حذف پرداخت
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'withdraw',
                    'amount' => $agencyShare,
                    'meta' => json_encode([
                        'description' => "Revert agency share due to payment deletion. Payment ID: {$payment->id}"
                    ]),
                    'status' => 'success'
                ]);

                $deposits = WalletTransaction::where('wallet_id', $wallet->id)
                    ->where('type', 'deposit')
                    ->sum('amount');

                $withdraws = WalletTransaction::where('wallet_id', $wallet->id)
                    ->where('type', 'withdraw')
                    ->sum('amount');

                $newBalance = $deposits - $withdraws;

                $wallet->balance = $newBalance;
                $wallet->save();

                // partners
                // ======================================
                $totalAmount = $wallet->balance;
                $partners = Account::where('type', 'person')
                    ->orderBy('id')
                    ->limit(3)
                    ->get();
                foreach ($partners as $partner) {
                    if ($partner->percentage) {
                        // 3) محاسبه سهم شریک
                        $partnerShare = $totalAmount * ($partner->percentage / 100);
                        // 4) گرفتن کیف پول شریک
                        $partnerWallet = Wallet::where('account_id', $partner->id)->first();
                        // اگر کیف پول شریک هنوز وجود ندارد → بساز
                        if (!$partnerWallet) {
                            $partnerWallet = Wallet::create([
                                'account_id' => $partner->id,
                                'balance' => 0
                            ]);
                        }

                        // 5) بروزرسانی مبلغ کیف پول شریک
                        $partnerWallet->update([
                            'balance' => $partnerShare
                        ]);
                    }
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



























     public function accountsProfits(Request $request)
    {
        $start = $request->start_date
            ? Jalalian::fromFormat('Y/m/d', $request->start_date)->toCarbon()->startOfDay()
            : null;

        $end = $request->end_date
            ? Jalalian::fromFormat('Y/m/d', $request->end_date)->toCarbon()->endOfDay()
            : null;


        $students = Student::with('products', 'percentages.account')->get();

        // محاسبه سود هر دانش‌آموز
        foreach ($students as $student) {
            $profits = $this->calculateStudentProfits($student);
            $student->central_profit = $profits['central_profit'];
            $student->agency_profit  = $profits['agency_profit'];
        }

        $centralTotal = 0;
        $agencyTotal  = 0;

        foreach ($students as $student) {
            // درصدهای ثبت‌شده برای این دانش‌آموز
            $centralPercentage = optional($student->percentages->firstWhere('account.type', 'center'))->percentage ?? 0;
            $agencyPercentage  = optional($student->percentages->firstWhere('account.type', 'agency'))->percentage ?? 0;

            foreach ($student->products as $product) {

                $price = $product->price;
                $tax   = $price * ($product->tax_percent / 100);

                if (!$product->is_shared) {
                    // بررسی تاریخ تخصیص از جدول واسط
                    $allocationExists = DB::table('product_student')
                        ->where('student_id', $student->id)
                        ->where('product_id', $product->id)
                        ->when($start, fn($q) => $q->where('created_at', '>=', $start))
                        ->when($end, fn($q) => $q->where('created_at', '<=', $end))
                        ->exists();

                    if (!$allocationExists) {
                        continue; // اگر تخصیص محصول در بازه نبود، از سود حذف می‌کنیم
                    }
                    // محصول غیر اشتراکی → همه‌اش برای نمایندگی

                    $agencyTotal += $price;
                } else {
                    // محصول اشتراکی → فقط اگر درصد ثبت شده باشد در بازه تاریخ
                    $centralPercentage = 0;
                    $agencyPercentage  = 0;

                    // درصد مرکزی
                    $centralRecord = DB::table('student_account_percentages')
                        ->where('student_id', $student->id)
                        ->where('account_id', optional($student->percentages->firstWhere('account.type', 'center'))->account_id)
                        ->when($start, fn($q) => $q->where('created_at', '>=', $start))
                        ->when($end, fn($q) => $q->where('created_at', '<=', $end))
                        ->latest('created_at')
                        ->first();

                    if ($centralRecord) {
                        $centralPercentage = $centralRecord->percentage;
                    }

                    // درصد نمایندگی
                    $agencyRecord = DB::table('student_account_percentages')
                        ->where('student_id', $student->id)
                        ->where('account_id', optional($student->percentages->firstWhere('account.type', 'agency'))->account_id)
                        ->when($start, fn($q) => $q->where('created_at', '>=', $start))
                        ->when($end, fn($q) => $q->where('created_at', '<=', $end))
                        ->latest('created_at')
                        ->first();

                    if ($agencyRecord) {
                        $agencyPercentage = $agencyRecord->percentage;
                    }

                    $hasCentralPercentage = $centralPercentage > 0;
                    $hasAgencyPercentage  = $agencyPercentage > 0;

                    if ($hasCentralPercentage || $hasAgencyPercentage) {
                        $centralShareFromPrice = $price * ($centralPercentage / 100);
                        $agencyShareFromPrice  = $price * ($agencyPercentage / 100);

                        // مالیات 100٪ برای مرکزی
                        $centralTotal += ($centralShareFromPrice + $tax);
                        $agencyTotal  += $agencyShareFromPrice;
                    }
                    // در غیر این صورت، این محصول از سود حذف می‌شود
                }
            }
        }

        // سهم هر شریک از سود نمایندگی
        $agencyPartners = Account::where('type', 'person')->get(); // تمام شرکای نمایندگی
        $totalPercent   = $agencyPartners->sum('percentage'); // مجموع درصد شرکا

        $partnersProfits = [];

        foreach ($agencyPartners as $partner) {
            if ($totalPercent > 0) {
                $partnersProfits[$partner->name] = $agencyTotal * ($partner->percentage / $totalPercent);
            } else {
                // اگر درصدی ثبت نشده بود، سهم صفر بده
                $partnersProfits[$partner->name] = 0;
            }
        }

        return view('accounting.profits', compact('centralTotal', 'agencyTotal', 'students', 'partnersProfits'));
    }

    private function calculateStudentProfits(Student $student)
    {
        $centralPercentage = optional(
            $student->percentages->firstWhere('account.type', 'central')
        )->percentage ?? 0;

        $agencyPercentage = optional(
            $student->percentages->firstWhere('account.type', 'agency')
        )->percentage ?? 0;

        // هندل درصدها (مثل قبل)
        if ($centralPercentage == 0 && $agencyPercentage > 0) {
            $centralPercentage = 100 - $agencyPercentage;
        }

        if ($agencyPercentage == 0 && $centralPercentage > 0) {
            $agencyPercentage = 100 - $centralPercentage;
        }

        $central = 0;
        $agency = 0;

        foreach ($student->products as $product) {

            $price = $product->price;
            $tax   = $price * ($product->tax_percent / 100);

            if (!$product->is_shared) {
                // غیر اشتراکی → همیشه برای نمایندگی
                $agency += $price;
            } else {
                // مشترک → فقط اگر درصد ثبت شده باشد
                if ($centralPercentage > 0 || $agencyPercentage > 0) {
                    $centralShare = $price * ($centralPercentage / 100);
                    $agencyShare  = $price * ($agencyPercentage / 100);

                    $central += $centralShare + $tax;
                    $agency  += $agencyShare;
                }
                // اگر درصد ثبت نشده، این محصول از سود حذف می‌شود
            }
        }



        // ----------- 👇 اعمال تخفیف اینجاست 👇 ------------
        $discount = $student->discounts()->first()?->amount ?? 0;

        // تقسیم تخفیف بین مرکز و نمایندگی
        // (اگر تخفیف فقط از سود مرکز کم می‌شود، این را تغییر بده)
        $agency -= $discount;

        return [
            'central_profit' => $central,
            'agency_profit'  => $agency,
        ];
    }