<?php

namespace App\Http\Controllers;

use App\Models\SmsTemplate;
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
        ]);

        SmsTemplate::create($validated);

        return redirect()->route('sms.createor.view')
            ->with('success', 'قالب پیامک با موفقیت ایجاد شد.');
    }
}
