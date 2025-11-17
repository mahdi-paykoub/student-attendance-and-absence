<?php

namespace App\Http\Controllers;

use App\Models\smsReport;
use Illuminate\Support\Facades\Http;

use App\Models\SmsTemplate;
use App\Models\Student;
use App\Services\SmsService;
use Illuminate\Http\Request;

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
        return view('sms.send', compact('students', 'templates'));
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

        // جایگذاری placeholderها
        if ($request->placeholders) {
            foreach ($request->placeholders as $key => $value) {
                $body = str_replace('{' . $key . '}', $value, $body);
            }
        }

        // انتخاب شماره بر اساس receiver_type موجود در قالب
        switch ($template->receiver_type) {
            case 'father':
                $to = $student->mobile_father;
                break;
            case 'mother':
                $to = $student->mobile_mother;
                break;
            default:
                $to = $student->mobile_student;
                break;
        }

        if (!$to) {
            return back()->with('error', 'شماره گیرنده موجود نیست.');
        }

        // ارسال پیامک
        SmsService::send($to, $body .  "11\nلغو", $template);


        smsReport::create([
            'student_id'  => $student->id,
            'template_id' => $template->id,
            'to'          => $to,
            'body'        => $body,
        ]);


        return back()->with('success', "پیامک با موفقیت به {$to} ارسال شد.");
    }
}
