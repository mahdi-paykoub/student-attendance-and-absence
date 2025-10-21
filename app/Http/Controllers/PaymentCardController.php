<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentCardController extends Controller
{
    public function index()
    {
        $cards = PaymentCard::latest()->get();
        return view('partials.payment_cards.index', compact('cards'));
    }

    public function create()
    {
        return view('partials.payment_cards.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:payment_cards,name',
        ]);

        PaymentCard::create($request->only('name'));

        return redirect()->route('payment-cards.index')->with('success', 'کارت با موفقیت افزوده شد.');
    }

    public function destroy(PaymentCard $paymentCard)
    {
        $paymentCard->delete();
        return back()->with('success', 'کارت حذف شد.');
    }


    public function showReceipt(Payment $payment)
    {
        // چک کن فایل موجود باشه
        if (!$payment->receipt_image || !Storage::disk('private')->exists($payment->receipt_image)) {
            abort(404, 'رسید یافت نشد.');
        }

        // برگردوندن فایل
        return response()->file(
            Storage::disk('private')->path($payment->receipt_image)
        );
    }
}
