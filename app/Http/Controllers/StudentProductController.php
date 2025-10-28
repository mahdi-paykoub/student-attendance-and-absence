<?php

namespace App\Http\Controllers;

use App\Models\Check;
use App\Models\Grade;
use App\Models\Major;
use App\Models\Payment;
use App\Models\PaymentCard;
use App\Models\Product;
use App\Models\ProductStudent;
use App\Models\Setting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;



class StudentProductController extends Controller
{


    public function assignForm(Student $student)
    {
        $products = Product::where('grade_id', $student->grade_id)
            ->where('major_id', $student->major_id)
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




    // ثبت یا آپدیت محصولات برای دانش‌آموز
    public function updateAssignedProducts(Request $request, Student $student)
    {
        // آرایه محصولاتی که انتخاب شده (اگر چیزی انتخاب نشده باشه، آرایه خالی)
        $selectedProducts = $request->input('products', []);

        // sync خودش مدیریت میکنه: حذف قبلی، اضافه جدید
        $student->products()->sync($selectedProducts);

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
            Payment::findOrFail($id)->delete();
        } elseif ($type == 'check') {
            Check::findOrFail($id)->delete();
        }

        return response()->json(['success' => true, 'message' => 'با موفقیت حذف شد']);
    }
}
