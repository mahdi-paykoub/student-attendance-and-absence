<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function registerPercantageView()
    {
        $students = Student::all();
        return view('accounting.registerPercantage', compact('students'));
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










        // ===== افزودن یا بروزرسانی کیف پول بخش مرکزی =====
        $centralAccountId = 2; // id بخش مرکزی در جدول accounts

        // پیدا کردن یا ساختن کیف پول بخش مرکزی
        $wallet = Wallet::firstOrCreate(
            ['account_id' => $centralAccountId],
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
}
