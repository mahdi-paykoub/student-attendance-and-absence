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


        return view('students.assign-products', [
            'student' => $student,
            'grade' => $student->grade?->name,
            'major' => $student->major?->name,
            'products' => $products,
            'assignedProducts' => $assignedProducts,

            'paymentCards' => $paymentCards,
            'existingPayments' => $existingPayments,
            'existingChecks' => $existingChecks,


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
        $submittedPayments = collect($request->payments ?? []);
        $submittedChecks = collect($request->checks ?? []);

        // حذف پرداخت‌هایی که دیگر وجود ندارند
        $student->payments()->whereNotIn('id', $submittedPayments->pluck('id')->filter())->delete();
        $student->checks()->whereNotIn('id', $submittedChecks->pluck('id')->filter())->delete();

        // ذخیره پرداخت‌ها
        foreach ($submittedPayments as $p) {
            $payment = $student->payments()->updateOrCreate(
                ['id' => $p['id'] ?? null],
                [
                    'date' => $p['date'],
                    'amount' => $p['amount'],
                    'ref' => $p['ref'] ?? null,
                    'card_id' => $p['card_id'] ?? null,
                ]
            );
            if (isset($p['image'])) {
                $path = $p['image']->store('private/payments');
                $payment->update(['image' => $path]);
            }
        }

        // ذخیره چک‌ها
        foreach ($submittedChecks as $c) {
            $check = $student->checks()->updateOrCreate(
                ['id' => $c['id'] ?? null],
                [
                    'date' => $c['date'],
                    'amount' => $c['amount'],
                    'serial' => $c['serial'] ?? null,
                    'code' => $c['code'] ?? null,
                    'owner_name' => $c['owner_name'] ?? null,
                    'owner_national' => $c['owner_national'] ?? null,
                    'owner_phone' => $c['owner_phone'] ?? null,
                ]
            );
            if (isset($c['image'])) {
                $path = $c['image']->store('private/checks');
                $check->update(['image' => $path]);
            }
        }

        return redirect()->back()->with('success', 'پرداخت‌ها با موفقیت بروزرسانی شدند.');
    }
}
