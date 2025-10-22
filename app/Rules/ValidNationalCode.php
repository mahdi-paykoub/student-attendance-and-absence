<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidNationalCode implements Rule
{
    public function passes($attribute, $value)
    {
        $code = preg_replace('/[^0-9]/', '', $value); // حذف فاصله و کاراکترهای غیر عددی

        if (!preg_match('/^\d{10}$/', $code)) return false;
        if (preg_match('/^(\\d)\\1{9}$/', $code)) return false;

        $check = (int) substr($code, 9, 1);
        $sum = 0;

        for ($i = 0; $i < 9; $i++) {
            $sum += ((int) substr($code, $i, 1)) * (10 - $i);
        }

        $remainder = $sum % 11;

        return ($remainder < 2 && $check == $remainder) ||
               ($remainder >= 2 && $check + $remainder == 11);
    }

    public function message()
    {
        return 'کد ملی وارد شده معتبر نیست.';
    }
}
