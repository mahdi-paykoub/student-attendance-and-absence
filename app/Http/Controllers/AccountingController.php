<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    public function registerPercantageView()
    {
        $students = Student::with('percentages.account')->get();

        $accounts = Account::all(); // اضافه کردن همه حساب‌ها

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
        $wallet->update(['balance' => $totalBalance]);

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
}
