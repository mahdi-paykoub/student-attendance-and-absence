<?php

namespace App\Http\Controllers;

use App\Models\PaymentCard;
use App\Models\Product;
use App\Models\ProductStudent;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentProductController extends Controller
{
    public function assignForm(Student $student)
    {
        // گرفتن تمام محصولات موجود
        $products = Product::all();

        // گرفتن کارت‌های پرداخت برای سلکت‌باکس
        $paymentCards = PaymentCard::all();

        return view('students.assign-products', [
            'student' => $student,
            'products' => $products,
            'paymentCards' => $paymentCards,
        ]);
    }

    public function storeAssign(Request $request, Student $student)
    {
        // ابتدا ProductStudentها رو ایجاد می‌کنیم
        $studentProducts = [];
        foreach ($request->products as $productId) {
            $studentProducts[] = ProductStudent::create([
                'student_id' => $student->id,
                'product_id' => $productId,
                'payment_type' => $request->payment_type,
            ]);
        }

        // اگر حداقل یک محصول داریم، پرداخت‌ها را فقط برای اولین محصول ثبت می‌کنیم
        if (!empty($studentProducts)) {
            $mainProduct = $studentProducts[0];

            // پرداخت نقدی
            if ($request->payment_type === 'cash') {
                foreach ($request->cash_date ?? [] as $i => $date) {
                    $mainProduct->payments()->create([
                        'date' => $date,
                        'time' => $request->cash_time[$i],
                        'amount' => $request->cash_amount[$i],
                        'voucher_number' => $request->cash_voucher[$i] ?? null,
                        'payment_card_id' => $request->cash_card[$i] ?? null,
                        'receipt_image' => $request->file('cash_image')[$i]?->store('payments', 'private'),
                    ]);
                }
            }

            // پرداخت اقساط
            if ($request->payment_type === 'installment') {
                // پیش‌پرداخت‌ها
                foreach ($request->pre_date ?? [] as $i => $date) {
                    $mainProduct->payments()->create([
                        'date' => $date,
                        'time' => $request->pre_time[$i],
                        'amount' => $request->pre_amount[$i],
                        'voucher_number' => $request->pre_voucher[$i] ?? null,
                        'payment_card_id' => $request->pre_card[$i] ?? null,
                        'receipt_image' => $request->file('pre_image')[$i]?->store('payments', 'private'),
                    ]);
                }

                // چک‌ها
                foreach ($request->check_date ?? [] as $i => $date) {
                    $mainProduct->checks()->create([
                        'date' => $date,
                        'amount' => $request->check_amount[$i],
                        'serial' => $request->check_serial[$i],
                        'sayad_code' => $request->check_sayad[$i],
                        'owner_name' => $request->check_owner[$i],
                        'owner_national_code' => $request->check_national[$i],
                        'owner_phone' => $request->check_phone[$i],
                        'check_image' => $request->file('check_image')[$i]?->store('checks', 'private'),
                    ]);
                }
            }
        }

        return redirect()->route('students.index')->with('success', 'تخصیص و پرداخت‌ها ثبت شد.');
    }
}
