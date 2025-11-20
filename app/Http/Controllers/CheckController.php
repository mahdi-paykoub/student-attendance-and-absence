<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Check;
use App\Models\Student;
use App\Models\StudentAccountPercentage;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class CheckController extends Controller
{
    public function showImage(Check $check)
    {
        if (!$check->check_image || !Storage::disk('private')->exists($check->check_image)) {
            abort(404, 'عکس چک یافت نشد.');
        }

        return response()->file(
            Storage::disk('private')->path($check->check_image)
        );
    }
    public function clear(Check $check , Student $student)
    {
        $check->update([
            'is_cleared' => 1
        ]);














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
            $agencyShare = $check->amount;

            // ثبت تراکنش در کیف پول
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $agencyShare,
                'meta' => json_encode([
                    'description' => "Agency share from payment ID: {$check->id} for student ID: {$student->id}"
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






























        return back()->with('success', 'چک با موفقیت وصول شد');
    }
}
