<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    protected static $apiKey = 'sa346962026:30VS1kkn0icUcAreALcKZ41mcPrkt0xEwMvS';

    /**
     * ارسال پیامک
     *
     * @param string $to شماره گیرنده (0912xxxxxxx)
     * @param string $message متن پیامک
     * @return array
     * @throws \Exception
     */
    public static function send(string $to, string $message, $gateway)
    {
        // URL کامل

        $url = "https://api.sabanovin.com/v1/sa346962026:30VS1kkn0icUcAreALcKZ41mcPrkt0xEwMvS/sms/send.json?gateway=" . $gateway . " &to=" . $to . "&text=" . $message . "";

        // GET request با query
        $response = Http::get($url);
        // dd($response->body());
        if ($response->failed()) {
            throw new \Exception("ارسال پیامک موفق نبود: " . $response->body());
        }

        return $response->json();
    }
}
