<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Major;
use App\Models\PaymentCard;
use App\Models\Product;
use App\Models\ProductStudent;
use App\Models\Student;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;



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
            'grade' => $student->grade()->first()->name,
            'major' => $student->major()->first()->name,
            'products' => $products,
            'paymentCards' => $paymentCards,
        ]);
    }



    // public function storeAssign(Request $request, Student $student)
    // {
    //     $studentProducts = [];

    //     foreach ($request->products as $productId) {
    //         // بررسی وجود قبلی
    //         $exists = ProductStudent::where('student_id', $student->id)
    //             ->where('product_id', $productId)
    //             ->exists();

    //         if (!$exists) {
    //             // فقط اگر وجود ندارد، ایجادش کن
    //             $studentProducts[] = ProductStudent::create([
    //                 'student_id' => $student->id,
    //                 'product_id' => $productId,
    //                 'payment_type' => $request->payment_type,
    //             ]);
    //         }
    //     }

    //     if (!empty($studentProducts)) {
    //         $mainProduct = $studentProducts[0];

    //         // --- پرداخت نقدی ---
    //         if ($request->payment_type === 'cash') {
    //             foreach ($request->cash_date ?? [] as $i => $shamsiDate) {
    //                 $gregorianDate = Jalalian::fromFormat('Y/m/d', $shamsiDate)
    //                     ->toCarbon()->format('Y-m-d');

    //                 $mainProduct->payments()->create([
    //                     'date' => $gregorianDate,
    //                     'time' => $request->cash_time[$i],
    //                     'amount' => $request->cash_amount[$i],
    //                     'voucher_number' => $request->cash_voucher[$i] ?? null,
    //                     'payment_card_id' => $request->cash_card[$i] ?? null,
    //                     'receipt_image' => $request->file('cash_image')[$i]?->store('payments', 'private'),
    //                 ]);
    //             }
    //         }

    //         // --- پرداخت اقساط ---
    //         if ($request->payment_type === 'installment') {
    //             // پیش‌پرداخت‌ها
    //             foreach ($request->pre_date ?? [] as $i => $shamsiDate) {
    //                 $gregorianDate = Jalalian::fromFormat('Y/m/d', $shamsiDate)
    //                     ->toCarbon()->format('Y-m-d');

    //                 $mainProduct->payments()->create([
    //                     'date' => $gregorianDate,
    //                     'time' => $request->pre_time[$i],
    //                     'amount' => $request->pre_amount[$i],
    //                     'voucher_number' => $request->pre_voucher[$i] ?? null,
    //                     'payment_card_id' => $request->pre_card[$i] ?? null,
    //                     'receipt_image' => $request->file('pre_image')[$i]?->store('payments', 'private'),
    //                 ]);
    //             }

    //             // چک‌ها
    //             foreach ($request->check_date ?? [] as $i => $shamsiDate) {
    //                 $gregorianDate = Jalalian::fromFormat('Y/m/d', $shamsiDate)
    //                     ->toCarbon()->format('Y-m-d');

    //                 $mainProduct->checks()->create([
    //                     'date' => $gregorianDate,
    //                     'amount' => $request->check_amount[$i],
    //                     'serial' => $request->check_serial[$i],
    //                     'sayad_code' => $request->check_sayad[$i],
    //                     'owner_name' => $request->check_owner[$i],
    //                     'owner_national_code' => $request->check_national[$i],
    //                     'owner_phone' => $request->check_phone[$i],
    //                     'check_image' => $request->file('check_image')[$i]?->store('checks', 'private'),
    //                 ]);
    //             }
    //         }
    //     }

    //     return redirect()->route('students.index')->with('success', 'تخصیص و پرداخت‌ها ثبت شد.');
    // }

    public function storeAssign(Request $request, Student $student)
    {
        $studentProducts = [];

        // --- ایجاد ProductStudent ها ---
        foreach ($request->products ?? [] as $productId) {
            $exists = ProductStudent::where('student_id', $student->id)
                ->where('product_id', $productId)
                ->exists();

            if (!$exists) {
                $studentProducts[] = ProductStudent::create([
                    'student_id' => $student->id,
                    'product_id' => $productId,
                    'payment_type' => $request->payment_type,
                ]);
            }
        }

        if (!empty($studentProducts)) {
            $mainProduct = $studentProducts[0];

            // --- پرداخت نقدی ---
            if ($request->payment_type === 'cash') {
                foreach ($request->cash_date ?? [] as $i => $shamsiDate) {
                    $gregorianDate = Jalalian::fromFormat('Y/m/d', $shamsiDate)
                        ->toCarbon()->format('Y-m-d');

                    $mainProduct->payments()->create([
                        'date' => $gregorianDate,
                        'time' => $request->cash_time[$i],
                        'amount' => $request->cash_amount[$i],
                        'voucher_number' => $request->cash_voucher[$i] ?? null,
                        'payment_card_id' => $request->cash_card[$i] ?? null,
                        'receipt_image' => $request->file('cash_image')[$i]?->store('payments', 'private'),
                    ]);
                }
            }

            // --- پرداخت اقساط ---
            if ($request->payment_type === 'installment') {
                // پیش‌پرداخت‌ها
                foreach ($request->pre_date ?? [] as $i => $shamsiDate) {
                    $gregorianDate = Jalalian::fromFormat('Y/m/d', $shamsiDate)
                        ->toCarbon()->format('Y-m-d');

                    $mainProduct->payments()->create([
                        'date' => $gregorianDate,
                        'time' => $request->pre_time[$i],
                        'amount' => $request->pre_amount[$i],
                        'voucher_number' => $request->pre_voucher[$i] ?? null,
                        'payment_card_id' => $request->pre_card[$i] ?? null,
                        'receipt_image' => $request->file('pre_image')[$i]?->store('payments', 'private'),
                    ]);
                }

                // چک‌ها
                foreach ($request->check_date ?? [] as $i => $shamsiDate) {
                    $gregorianDate = Jalalian::fromFormat('Y/m/d', $shamsiDate)
                        ->toCarbon()->format('Y-m-d');

                    $mainProduct->checks()->create([
                        'date' => $gregorianDate,
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

        // --- بررسی محصول اجباری و بازسازی شماره صندلی ---
        $mandatoryExamId = setting('mandatory_exam_product_id'); // خواندن ID محصول اجباری از تنظیمات
        $assignedMandatory = in_array($mandatoryExamId, $request->products ?? []);

        if ($assignedMandatory) {
            $genders = ['male', 'female']; // یا ['پسر','دختر'] بسته به دیتابیس

            foreach ($genders as $gender) {
                $seatNumber = 1;

                $bases = Grade::orderBy('id')->get();
                foreach ($bases as $base) {
                    $majors = Major::orderBy('id')->get();

                    foreach ($majors as $major) {
                        $students = Student::where('gender', $gender)
                            ->where('base_id', $base->id)
                            ->where('major_id', $major->id)
                            ->orderBy('id')
                            ->get();

                        foreach ($students as $s) {
                            $s->seat_number = $seatNumber++;
                            $s->save();
                        }
                    }
                }
            }
        }

        return redirect()->route('students.index')->with('success', 'تخصیص، پرداخت و شماره صندلی‌ها با موفقیت ثبت شدند.');
    }
}
