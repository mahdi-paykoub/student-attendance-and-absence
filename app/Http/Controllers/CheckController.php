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
    public function clear(Check $check, Student $student)
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
                    'description' => "Agency share from payment ID: {$check->id} for student ID: {$student->id}",
                    'for' => "وصول چک دانش‌آموزِ : {$student->first_name} {$student->last_name}",
                ]),
                'status' => 'success'
            ]);

            // بروزرسانی موجودی کیف پول
            $newBalance = WalletTransaction::where('wallet_id', $wallet->id)->sum('amount');
            $wallet->balance = $newBalance;
            $wallet->save();

            // partners
            // ======================================

            // مبلغی که الان به کیف پول نمایندگی اضافه شده
            $agencyDeltaAmount = $check->amount;

            $partners = Account::where('type', 'person')
                ->orderBy('id')
                ->limit(3)
                ->get();

            foreach ($partners as $partner) {

                if (!$partner->percentage || $agencyDeltaAmount == 0) {
                    continue;
                }

                // 1️⃣ محاسبه سهم شریک از همین چک وصول‌شده
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
                    'type'      => 'deposit', // افزایش سهم شریک
                    'amount'    => $partnerShare,
                    'meta'      => json_encode([
                        'description' => 'Partner share from cleared check',
                        'check_id'    => $check->id,
                        'student_id'  => $student->id,
                        'agency_id'   => $agencyAccount->id,
                        'for' => "وصول چک دانش‌آموزِ : {$student->first_name} {$student->last_name}",
                    ]),
                    'status'    => 'success'
                ]);

                // 4️⃣ محاسبه موجودی جدید کیف پول شریک
                $newBalance = WalletTransaction::where('wallet_id', $partnerWallet->id)
                    ->sum('amount');

                // 5️⃣ بروزرسانی موجودی
                $partnerWallet->update([
                    'balance' => $newBalance
                ]);
            }
            // ======================================

        }

        return back()->with('success', 'چک با موفقیت وصول شد');
    }
}
