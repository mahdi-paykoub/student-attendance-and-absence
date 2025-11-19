<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Deposit;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class AccountingController extends Controller
{
    public function registerPercantageView()
    {
        $students = Student::with('percentages.account')
            ->withMax('products as last_assigned_at', 'product_student.created_at')
            ->orderByDesc('last_assigned_at')
            ->get();

        $accounts = Account::all();


        return view('accounting.registerPercantage', compact('students', 'accounts'));
    }


    public function registerCentralPercantage(Request $request, Student $student)
    {
        $validated = $request->validate([
            'percatege' => 'required|numeric|min:0|max:100'
        ]);

        $totalPrice = $student->products->sum('price');
        $central_share = $totalPrice * ($validated['percatege'] / 100);

        $totalTax = $student->products->sum(function ($product) {
            return $product->price * ($product->tax_percent / 100);
        });

        $final = $central_share + $totalTax;

        // ===== ثبت یا بروزرسانی درصد در جدول واسط =====
        $centralAccount = Account::where('type', 'center')->first();

        \App\Models\StudentAccountPercentage::updateOrCreate(
            [
                'student_id' => $student->id,
                'account_id' => $centralAccount->id
            ],
            [
                'percentage' => $validated['percatege']
            ]
        );
        // ============================================

        // ===== افزودن یا بروزرسانی کیف پول بخش مرکزی =====
        $wallet = Wallet::firstOrCreate(
            ['account_id' => $centralAccount->id],
            ['balance' => 0]
        );

        // حذف تراکنش قبلی این دانش‌آموز (اگر وجود داشت)
        WalletTransaction::where('wallet_id', $wallet->id)
            ->whereJsonContains('meta->description', "Central contribution of the student: {$student->id}")
            ->delete();

        // ثبت تراکنش جدید برای دانش‌آموز
        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'deposit',
            'amount' => $final,
            'meta' => json_encode([
                'description' => "Central contribution of the student: {$student->id}"
            ]),
            'status' => 'success'
        ]);

        // محاسبه موجودی کل بخش مرکزی بر اساس همه تراکنش‌ها
        $totalCentralBalance = WalletTransaction::where('wallet_id', $wallet->id)->sum('amount');

        // آپدیت موجودی کیف پول
        $wallet->update(['balance' => $totalCentralBalance]);
        // ======================================

        return response()->json([
            'status' => 'success',
            'final' => $final,
            'central_share' => $central_share,
            'tax_total' => $totalTax
        ]);
    }


    public function registerAgencyPercentage(Request $request, Student $student)
    {
        $validated = $request->validate([
            'percentage' => 'required|numeric|min:0|max:100'
        ]);

        $agencyAccount = Account::where('type', 'agency')->first();

        // ===== جمع پرداختی‌های دانش‌آموز =====
        $totalPayments = Payment::where('student_id', $student->id)->sum('amount');

        // ===== محاسبه جمع کل محصولات + مالیات =====
        $totalProducts = $student->products->sum('price');
        $totalTax = $student->products->sum(function ($product) {
            return $product->price * ($product->tax_percent / 100);
        });

        $totalDue = ($totalProducts + $totalTax) - $totalPayments;

        // ===== سهم نمایندگی =====
        $baseShare = $totalProducts * ($validated['percentage'] / 100);
        $agencyShare = $baseShare - $totalDue;

        // ===== ثبت یا بروزرسانی درصد در جدول واسط =====
        \App\Models\StudentAccountPercentage::updateOrCreate(
            [
                'student_id' => $student->id,
                'account_id' => $agencyAccount->id
            ],
            [
                'percentage' => $validated['percentage']
            ]
        );

        // ===== بروزرسانی کیف پول نمایندگی =====
        $wallet = Wallet::firstOrCreate(
            ['account_id' => $agencyAccount->id],
            ['balance' => 0]
        );

        // حذف تراکنش قبلی این دانش‌آموز برای نمایندگی
        WalletTransaction::where('wallet_id', $wallet->id)
            ->whereJsonContains('meta->description', "Agency contribution of student: {$student->id}")
            ->delete();

        // ثبت تراکنش جدید
        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'deposit',
            'amount' => $agencyShare,
            'meta' => json_encode([
                'description' => "Agency contribution of student: {$student->id}"
            ]),
            'status' => 'success'
        ]);

        // بروزرسانی موجودی کیف پول
        $totalBalance = WalletTransaction::where('wallet_id', $wallet->id)->sum('amount');
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
        return response()->json([
            'status' => 'success',
            'agency_share' => $agencyShare,
            'base_share' => $baseShare,
            'total_due' => $totalDue,
            'total_payments' => $totalPayments
        ]);
    }


    public function partnersView()
    {
        $partners = Account::where('type', 'person')
            ->orderBy('id')
            ->limit(3)
            ->get();
        $wallet = Wallet::whereHas('account', function ($q) {
            $q->where('type', 'agency');
        })->first();


        return view('accounting.partners', compact('partners', 'wallet'));
    }
    public function createPartners(Request $request)
    {
        $validated = $request->validate([
            'partners' => 'required|array',
            'partners.*.name' => 'nullable|string',
            'partners.*.percent' => 'nullable|numeric|min:0|max:100',
        ]);








        foreach ($validated['partners'] as $partner) {

            // اگر هر دو مقدار خالی باشد، رد شو
            if (empty($partner['name']) && empty($partner['percent'])) {
                continue;
            }



            $account = Account::updateOrCreate(
                [
                    'name' => $partner['name'],
                    'type' => 'person'
                ],
                [
                    'percentage' => $partner['percent']
                ]
            );








            // 2) گرفتن کل مبلغ از کیف پول مرکزی (type = agency)
            $centralWallet = Wallet::whereHas('account', function ($q) {
                $q->where('type', 'agency');
            })->first();

            $totalAmount = $centralWallet->balance;

            // 3) محاسبه سهم شریک
            $partnerShare = $totalAmount * ($partner['percent'] / 100);

            // 4) گرفتن کیف پول شریک
            $partnerWallet = Wallet::where('account_id', $account->id)->first();

            // اگر کیف پول شریک هنوز وجود ندارد → بساز
            if (!$partnerWallet) {
                $partnerWallet = Wallet::create([
                    'account_id' => $account->id,
                    'balance' => 0
                ]);
            }

            // 5) بروزرسانی مبلغ کیف پول شریک
            $partnerWallet->update([
                'balance' => $partnerShare
            ]);
        }



        return redirect()->back()->with('success', 'شریک‌ها با موفقیت ثبت شدند.');
    }


    public function costsView()
    {
        $expenses = Expense::all();
        return view('accounting.costs', compact('expenses'));
    }

    public function costsCreate(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:consumable,capital,gift',
            'title' => 'required|string|max:255',
            'receipt_image' => 'nullable|image|max:2048',
            'expense_date' => 'required|string',
            'amount' => 'required|string',
        ]);

        // تبدیل تاریخ و ساعت شمسی به میلادی
        $jalaliDateTime = Jalalian::fromFormat('Y/m/d H:i:s', $validated['expense_date']);
        $validated['expense_datetime'] = $jalaliDateTime->toCarbon()->format('Y-m-d H:i:s');

        // ذخیره تصویر در storage/private
        if ($request->hasFile('receipt_image')) {
            $validated['receipt_image'] = $request->file('receipt_image')
                ->store('receipts', 'private');
        }

        // ذخیره هزینه
        $expense = Expense::create($validated);




        // ======================================
        // کسر از کیف پول نمایندگی
        DB::transaction(function () use ($expense) {

            // 1. گرفتن حساب نمایندگی
            $agencyAccount = Account::where('type', 'agency')->first();

            // 2. گرفتن یا ایجاد کیف پول
            $wallet = Wallet::firstOrCreate(
                ['account_id' => $agencyAccount->id],
                ['balance' => 0]
            );

            // 3. ثبت تراکنش کاهش موجودی
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'withdraw',
                'amount' => - ($expense->amount),
                'meta' => json_encode([
                    'description' => "Deduction due to expense recording"
                ]),
                'status' => 'success'
            ]);

            // 4. محاسبه موجودی کیف پول: جمع تراکنش‌ها با در نظر گرفتن نوع تراکنش
            $newBalance = WalletTransaction::where('wallet_id', $wallet->id)->sum('amount');

            // 5. آپدیت موجودی کیف پول
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
        });
        // ======================================





        return redirect()->back()->with('success', 'هزینه با موفقیت ثبت شد.');
    }



    public  function getImageCosts($filename)
    {
        $path = storage_path('app/private/receipts/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }


    public function deposistView()
    {
        $accounts = Account::all();
        $deposits = Deposit::with('account')->latest()->get();
        return view('accounting.deposits', compact('accounts', 'deposits'));
    }

    public function deposistCreate(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'image'        => 'nullable|image|max:4096',
            'amount'       => 'required|numeric|min:0',
            'account_id'   => 'required|exists:accounts,id',
            'paid_at'      => 'required|string', // تاریخ شمسی
        ]);

        // ===== تبدیل تاریخ شمسی به میلادی =====
        // ورودی مثل: 1403/08/15 14:30:00
        $jalaliDateTime = Jalalian::fromFormat('Y/m/d H:i:s', $validated['paid_at']);
        $validated['paid_at'] = $jalaliDateTime->toCarbon()->format('Y-m-d H:i:s');

        // ===== ذخیره تصویر در storage/private =====
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('deposits', 'private'); // مسیر: storage/app/private/deposits
        }

        // ===== ذخیره رکورد =====
        $deposit = Deposit::create($validated);



        // ======================================
        // کسر از کیف پول نمایندگی
        DB::transaction(function () use ($deposit) {


            // 2. گرفتن یا ایجاد کیف پول
            $wallet = Wallet::firstOrCreate(
                ['account_id' => $deposit->account_id],
                ['balance' => 0]
            );

            // 3. ثبت تراکنش کاهش موجودی
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'withdraw',
                'amount' => $deposit->amount,
                'meta' => json_encode([
                    'description' => "Deposit registration"
                ]),
                'status' => 'success'
            ]);

            // 4. محاسبه موجودی کیف پول: جمع تراکنش‌ها با در نظر گرفتن نوع تراکنش
            $newBalance = WalletTransaction::where('wallet_id', $wallet->id)->sum('amount');

            // 5. آپدیت موجودی کیف پول
            $wallet->balance = $newBalance;
            $wallet->save();

            // partners
            // ======================================
            if ($deposit->account->type === 'agency') {
            }
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
        });
        // ======================================



        return redirect()->back()->with('success', 'واریزی با موفقیت ثبت شد.');
    }

    public  function getImageDeposits($filename)
    {
        $path = storage_path('app/private/deposits/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    // wallets
    public function walletsView(Request $request)
    {
        // فیلترها
        $accountId = $request->account_id;
        $type = $request->type;

        $wallets = Wallet::all();
        $accounts = Account::all(); // برای فرم فیلتر

        $transactions = WalletTransaction::query()
            ->when($accountId, function ($q) use ($accountId) {
                $q->whereHas('wallet', function ($q) use ($accountId) {
                    $q->where('account_id', $accountId);
                });
            })
            ->when($type, function ($q) use ($type) {
                if ($type == 'deposit') { // واریزی
                    $q->where('amount', '>', 0);
                } elseif ($type == 'withdraw') { // برداشت
                    $q->where('amount', '<', 0);
                }
            })
            ->latest()
            ->get();
        return view('accounting.wallets', compact('wallets', 'transactions', 'accounts'));
    }

    public function accountsProfits(){
        
    }
}
