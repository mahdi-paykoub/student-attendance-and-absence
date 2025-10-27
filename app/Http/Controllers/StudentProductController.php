<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Major;
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

        $paymentCards = PaymentCard::all();

        // محصولاتی که قبلاً تخصیص داده شده + payments و checks
        $assignedProducts = ProductStudent::with(['payments', 'checks', 'product'])
            ->where('student_id', $student->id)
            ->get();

        foreach ($assignedProducts as $ap) {
            foreach ($ap->payments as $p) {
                // مطمئن شو date یک Carbon instance باشه
                $carbonDate = Carbon::parse($p->date);
                $p->date_shamsi = Jalalian::fromCarbon($carbonDate)->format('Y/m/d');
            }
            foreach ($ap->checks as $c) {
                $carbonDate = Carbon::parse($c->date);
                $c->date_shamsi = Jalalian::fromCarbon($carbonDate)->format('Y/m/d');
            }
        }


        return view('students.assign-products', [
            'student' => $student,
            'grade' => $student->grade?->name,
            'major' => $student->major?->name,
            'products' => $products,
            'paymentCards' => $paymentCards,
            'assignedProducts' => $assignedProducts,
        ]);
    }












    // public function storeAssign(Request $request, Student $student)
    // {
    //     $studentProducts = [];

    //     // ایجاد ProductStudent‌ها
    //     // foreach ($request->products ?? [] as $productId) {
    //     //     $exists = ProductStudent::where('student_id', $student->id)
    //     //         ->where('product_id', $productId)
    //     //         ->exists();

    //     //     if (!$exists) {
    //     //         $studentProducts[] = ProductStudent::create([
    //     //             'student_id'   => $student->id,
    //     //             'product_id'   => $productId,
    //     //             'payment_type' => $request->payment_type,
    //     //         ]);
    //     //     }
    //     // }
    //     foreach ($request->products ?? [] as $productId) {
    //         $ps = ProductStudent::updateOrCreate(
    //             ['student_id' => $student->id, 'product_id' => $productId],
    //             ['payment_type' => $request->payment_type]
    //         );

    //         $studentProducts[] = $ps;
    //     }

    //     if (!empty($studentProducts)) {
    //         $mainProduct = $studentProducts[0];

    //         // 🟢 پرداخت نقدی
    //         if ($request->payment_type === 'cash') {
    //             foreach ($request->cash_date ?? [] as $i => $dateTime) {
    //                 // مثال مقدار ورودی: "1403/08/04 14:30"
    //                 [$shamsiDate, $time] = explode(' ', $dateTime . ' '); // جدا کردن تاریخ و ساعت

    //                 $gregorianDate = Jalalian::fromFormat('Y/m/d', trim($shamsiDate))
    //                     ->toCarbon()->format('Y-m-d');

    //                 $mainProduct->payments()->create([
    //                     'date' => Jalalian::fromFormat('Y/m/d', trim($shamsiDate))->toCarbon()->format('Y-m-d'),
    //                     'time' => $time ? trim($time) : '00:00',
    //                     'amount'        => $request->cash_amount[$i],
    //                     'voucher_number' => $request->cash_voucher[$i] ?? null,
    //                     'payment_card_id' => $request->cash_card[$i] ?? null,
    //                     'receipt_image' => isset($request->file('cash_image')[$i])
    //                         ? $request->file('cash_image')[$i]->store('payments', 'private')
    //                         : null,
    //                 ]);
    //             }
    //         }

    //         // 🟠 پرداخت اقساط (پیش‌پرداخت و چک)
    //         if ($request->payment_type === 'installment') {
    //             // پیش‌پرداخت‌ها
    //             foreach ($request->pre_date ?? [] as $i => $dateTime) {
    //                 [$shamsiDate, $time] = explode(' ', $dateTime . ' ');
    //                 $gregorianDate = Jalalian::fromFormat('Y/m/d', trim($shamsiDate))
    //                     ->toCarbon()->format('Y-m-d');

    //                 $mainProduct->payments()->create([
    //                     'date' => Jalalian::fromFormat('Y/m/d', trim($shamsiDate))->toCarbon()->format('Y-m-d'),
    //                     'time' => $time ? trim($time) : '00:00',
    //                     'amount'         => $request->pre_amount[$i],
    //                     'voucher_number' => $request->pre_voucher[$i] ?? null,
    //                     'payment_card_id' => $request->pre_card[$i] ?? null,
    //                     'receipt_image'  => isset($request->file('cash_image')[$i])
    //                         ? $request->file('cash_image')[$i]->store('payments', 'private')
    //                         : null,
    //                 ]);
    //             }

    //             // چک‌ها
    //             foreach ($request->check_date ?? [] as $i => $dateTime) {
    //                 [$shamsiDate, $time] = explode(' ', $dateTime . ' ');
    //                 $gregorianDate = Jalalian::fromFormat('Y/m/d', trim($shamsiDate))
    //                     ->toCarbon()->format('Y-m-d');

    //                 $mainProduct->checks()->create([
    //                     'date'                  => $gregorianDate,
    //                     'amount'                => $request->check_amount[$i],
    //                     'serial'                => $request->check_serial[$i],
    //                     'sayad_code'            => $request->check_sayad[$i],
    //                     'owner_name'            => $request->check_owner[$i],
    //                     'owner_national_code'   => $request->check_national[$i],
    //                     'owner_phone'           => $request->check_phone[$i],
    //                     'check_image'           => $request->file('check_image')[$i]?->store('checks', 'private'),
    //                 ]);
    //             }
    //         }
    //     }

    //     // ✅ بقیه منطق مربوط به صندلی و امتحان اجباری مثل قبل
    //     // ...

    //     return redirect()->route('students.index')->with('success', 'تخصیص، پرداخت و شماره صندلی‌ها با موفقیت ثبت شدند.');
    // }


public function storeAssign(Request $request, Student $student)
{
    // لیست محصولاتی که کاربر ارسال کرده
    $requestedProductIds = $request->products ?? [];

    // همه محصولاتی که همین الان به این دانش‌آموز تخصیص داده شده‌اند
    $existingAssignments = ProductStudent::where('student_id', $student->id)->get();

    // 1) حذف محصولاتی که در فرم نیستند (یعنی کاربر تیک رو برداشته)
    foreach ($existingAssignments as $existing) {
        if (!in_array($existing->product_id, $requestedProductIds)) {
            // پاک کردن پرداخت‌ها و چک‌های مربوط به این تخصیص
            $existing->payments()->delete();
            $existing->checks()->delete();
            // سپس خود تخصیص را پاک کن
            $existing->delete();
        }
    }

    // 2) برای هر محصولی که ارسال شده -> updateOrCreate
    $studentProducts = [];
    foreach ($requestedProductIds as $productId) {
        $ps = ProductStudent::updateOrCreate(
            ['student_id' => $student->id, 'product_id' => $productId],
            ['payment_type' => $request->payment_type] // اگر بخواهی می‌تونی payment_type را وابسته به هر محصول بفرستی
        );
        $studentProducts[$productId] = $ps; // نگه‌دار با کلید product_id برای دسترسی راحت‌تر بعداً
    }

    // اگر هیچ محصولی ارسال نشده بود، کاری برای پرداخت‌ها نداریم
    if (!empty($studentProducts)) {

        // کمک: تابع کمکی برای تبدیل تاریخ شمسی( Y/m/d [H:i] ) به gregorian date & time
        $parseShamsiDateTime = function($dateTime) {
            // مطمئن شو همیشه دو قسمت داشته باشیم: تاریخ و اختیاری زمان
            [$shamsiDate, $time] = array_pad(explode(' ', trim($dateTime)), 2, null);

            // تاریخ شمسی -> میلادی (Y-m-d)
            $gregorianDate = null;
            try {
                $gregorianDate = Jalalian::fromFormat('Y/m/d', trim($shamsiDate))->toCarbon()->format('Y-m-d');
            } catch (\Throwable $e) {
                // اگر فرمت اشتباه بود، تلاش کن با Carbon.parse (fallback)
                try {
                    $carbon = Carbon::parse($shamsiDate);
                    $gregorianDate = $carbon->format('Y-m-d');
                } catch (\Throwable $e2) {
                    $gregorianDate = null;
                }
            }

            // time: اگر وجود نداشت مقدار پیش‌فرض 00:00 قرار بده
            $timeValue = $time ? trim($time) : '00:00';

            return [$gregorianDate, $timeValue];
        };

        /*
         IMPORTANT:
         برای اینکه پرداخت‌ها/چک‌ها به محصولِ درست متصل شوند باید در فرم (Blade) 
         هر ردیف پرداخت شامل یک فیلد پنهان باشد که مشخص کند آن پرداخت برای کدام product_id (یا product_student_id) است.
         مثال فیلد در فرم:
         <input type="hidden" name="cash_product_id[]" value="{{ $product->id }}">
         یا اگر بخواهی وصل به ProductStudent استفاده کنی: value="{{ $productStudent->id }}"
         کد پایین در صورت نبودن این mapping، پرداخت‌ها را به اولین محصول اختصاص می‌دهد (fallback).
        */

        // MAPPING: اگر فرم شامل arrays مرتبط با product باشه (مثلاً cash_product_id[])
        $cashProductMap = $request->cash_product_id ?? []; // index aligned with cash_date[], cash_amount[] ...
        $preProductMap  = $request->pre_product_id ?? [];  // for prepayments
        $checkProductMap= $request->check_product_id ?? []; // for checks

        // ---------- پردازش پرداخت‌های نقدی ----------
        foreach ($request->cash_date ?? [] as $i => $dateTime) {
            [$gregorianDate, $timeValue] = $parseShamsiDateTime($dateTime);

            // تعیین محصول هدف برای این پرداخت
            $targetProductId = $cashProductMap[$i] ?? null;
            // اگر mapping product_id بود و ما studentProducts داریم، سعی کن ProductStudent پیدا کنی
            if ($targetProductId && isset($studentProducts[$targetProductId])) {
                $targetPs = $studentProducts[$targetProductId];
            } else {
                // fallback: محصول اول
                $first = reset($studentProducts);
                $targetPs = $first ?: null;
            }

            if (!$targetPs) continue; // اگر هیچ محصولی پیدا نشد، رد کن

            $targetPs->payments()->create([
                'date' => $gregorianDate,
                'time' => $timeValue ?: '00:00',
                'amount' => $request->cash_amount[$i] ?? 0,
                'voucher_number' => $request->cash_voucher[$i] ?? null,
                'payment_card_id' => $request->cash_card[$i] ?? null,
                'receipt_image' => isset($request->file('cash_image')[$i]) 
                                    ? $request->file('cash_image')[$i]->store('payments', 'private')
                                    : null,
            ]);
        }

        // ---------- پردازش پیش‌پرداخت‌ها ----------
        foreach ($request->pre_date ?? [] as $i => $dateTime) {
            [$gregorianDate, $timeValue] = $parseShamsiDateTime($dateTime);

            $targetProductId = $preProductMap[$i] ?? null;
            if ($targetProductId && isset($studentProducts[$targetProductId])) {
                $targetPs = $studentProducts[$targetProductId];
            } else {
                $first = reset($studentProducts);
                $targetPs = $first ?: null;
            }

            if (!$targetPs) continue;

            $targetPs->payments()->create([
                'date' => $gregorianDate,
                'time' => $timeValue ?: '00:00',
                'amount' => $request->pre_amount[$i] ?? 0,
                'voucher_number' => $request->pre_voucher[$i] ?? null,
                'payment_card_id' => $request->pre_card[$i] ?? null,
                'receipt_image' => isset($request->file('pre_image')[$i])
                                    ? $request->file('pre_image')[$i]->store('payments', 'private')
                                    : null,
            ]);
        }

        // ---------- پردازش چک‌ها ----------
        foreach ($request->check_date ?? [] as $i => $dateTime) {
            [$gregorianDate, $timeValue] = $parseShamsiDateTime($dateTime);

            $targetProductId = $checkProductMap[$i] ?? null;
            if ($targetProductId && isset($studentProducts[$targetProductId])) {
                $targetPs = $studentProducts[$targetProductId];
            } else {
                $first = reset($studentProducts);
                $targetPs = $first ?: null;
            }

            if (!$targetPs) continue;

            $targetPs->checks()->create([
                'date' => $gregorianDate,
                'amount' => $request->check_amount[$i] ?? 0,
                'serial' => $request->check_serial[$i] ?? null,
                'sayad_code' => $request->check_sayad[$i] ?? null,
                'owner_name' => $request->check_owner[$i] ?? null,
                'owner_national_code' => $request->check_national[$i] ?? null,
                'owner_phone' => $request->check_phone[$i] ?? null,
                'check_image' => isset($request->file('check_image')[$i])
                                    ? $request->file('check_image')[$i]->store('checks', 'private')
                                    : null,
            ]);
        }
    }

    // ... هر منطق دیگر (صندلی، امتحان اجباری و غیره)

    return redirect()->route('students.index')->with('success', 'تخصیص، پرداخت و شماره صندلی‌ها با موفقیت ثبت شدند.');
}

}
