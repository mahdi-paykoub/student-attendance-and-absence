<?php

namespace App\Http\Controllers;

use App\Models\PaymentCard;
use Illuminate\Http\Request;

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
}
