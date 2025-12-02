<?php

namespace App\Http\Controllers;

use App\Models\SmsReport;
use App\Models\SmsTemplate;
use App\Models\Student;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class SmsTemplateController extends Controller
{
    public function smsCreateorView()
    {
        $templates = SmsTemplate::latest()->get();
        return view('sms.create', compact('templates'));
    }

    public function storeSmsTemplate(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'receiver_type' => 'required|in:father,mother,student',
            'gateway' => 'required',
        ]);

        SmsTemplate::create($validated);

        return redirect()->route('sms.createor.view')
            ->with('success', 'قالب پیامک با موفقیت ایجاد شد.');
    }


    public function sendSmsView()
    {
        $students = Student::all();
        $templates = SmsTemplate::all();

        $knownPlaceholders = [
            'first_name',
            'last_name',
            'full_name',
            'mobile',
            'father_name',
            'national_code',
            'grade',
            'major',
            'mobile_student',
            'phone',
            'seat_number',
            'province',
            'city',
            'address',

            'total_products_price',
            'debt',
            'totalPayments',
        ];
        $knownPlaceholders = ($knownPlaceholders);
        return view('sms.send', compact('students', 'templates', 'knownPlaceholders'));
    }


    public function sendSms(Request $request, Student $student)
    {
        $request->validate([
            'student_id'  => 'required|exists:students,id',
            'template_id' => 'required|exists:sms_templates,id',
        ]);

        $student = Student::find($request->student_id);
        $template = SmsTemplate::find($request->template_id);

        // متن قالب
        $body = $template->content;



        $totalProducts = $student->total_product_cost ?? ($student->product_total ?? 0);
        $totalPayments = $student->total_payments ?? ($student->payment_total ?? 0);
        $debt = $totalProducts - $totalPayments;
        // لیست placeholderهای شناخته‌شده و مقدارشان از دیتابیس
        $knownPlaceholders = [
            'first_name' => $student->first_name,
            'last_name'  => $student->last_name,
            'full_name'    => $student->first_name . ' ' . $student->last_name,
            'mobile'     => $student->mobile_student,
            'father_name' => $student->father_name,
            'national_code' => $student->national_code,
            'grade' => $student->grade()->first()->name,
            'major' => $student->major()->first()->name,
            'mobile_student' => $student->mobile_student,
            'phone' => $student->phone,
            'seat_number' => $student->seat_number,
            'province' => $student->province,
            'city' => $student->city,
            'address' => $student->address,

            'total_products_price' => number_format($totalProducts),
            'debt' => number_format($debt),
            'totalPayments' => number_format($totalPayments),
        ];

        // تمام placeholderهای موجود در متن مثل {first_name}
        preg_match_all('/{(.*?)}/', $body, $matches);

        foreach ($matches[1] as $placeholder) {

            if (array_key_exists($placeholder, $knownPlaceholders)) {
                // اگر placeholder شناخته‌شده بود → از دیتابیس جایگزین کن
                $value = $knownPlaceholders[$placeholder];
            } else {
                // اگر ناشناخته بود → از ورودی کاربر بخوان
                $value = $request->placeholders[$placeholder] ?? '';
            }

            // جایگزینی
            $body = str_replace("{" . $placeholder . "}", $value, $body);
        }

        // تعیین شماره
        switch ($template->receiver_type) {
            case 'father':
                $to = $student->mobile_father;
                break;
            case 'mother':
                $to = $student->mobile_mother;
                break;
            default:
                $to = $student->mobile_student;
        }

        if (!$to) {
            return back()->with('error', 'شماره گیرنده موجود نیست.');
        }

        $body = $body . "\nلغو 11";
        // ارسال پیامک
        SmsService::send($to, $body, $template->gateway);

        SmsReport::create([
            'student_id'  => $student->id,
            'template_id' => $template->id,
            'to'          => $to,
            'body'        => $body,
        ]);

        return back()->with('success', "پیامک با موفقیت به {$to} ارسال شد.");
    }


    public function sendDelete(SmsTemplate $smsTemplate)
    {
        $smsTemplate->delete();

        return redirect()->back()->with('success', 'قالب پیامک با موفقیت حذف شد.');
    }

    public function previousSms($studentId)
    {
        $sms = \App\Models\SmsReport::where('student_id', $studentId)->with('template')
            ->orderByDesc('created_at')
            ->get();

        // تبدیل تاریخ‌ها به شمسی
        $sms->transform(function ($item) {
            $item->created_at_sh = Jalalian::fromDateTime($item->created_at)->format('Y/m/d H:i');
            $item->updated_at_sh = Jalalian::fromDateTime($item->updated_at)->format('Y/m/d H:i');
            return $item;
        });

        return response()->json($sms);
    }
}
