<?php

namespace App\Imports;

use App\Models\{
    Student,
    Grade,
    Major,
    School,
    Province,
    City,
    Advisor
};
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Morilog\Jalali\Jalalian;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {




        // 🟦 تبدیل تاریخ تولد → شمسی → میلادی برای ذخیره در DB
        $birthday = null;

        if (!empty($row['birthday'])) {

            $value = trim($row['birthday']);

            // اگر تاریخ اکسل عددی باشد
            if (is_numeric($value)) {
                // اکسل تاریخ را از 1900/01/01 شروع می‌کند
                $carbonDate = Carbon::createFromTimestamp(($value - 25569) * 86400);
                $birthday = Jalalian::fromCarbon($carbonDate)->toCarbon(); // تبدیل به میلادی
            }

            // اگر به‌صورت شمسی باشد مثل: 1402/05/12
            elseif (preg_match('/\d{4}\/\d{1,2}\/\d{1,2}/', $value)) {
                try {
                    $birthday = Jalalian::fromFormat('Y/m/d', $value)->toCarbon();
                } catch (\Exception $e) {
                }
            }

            // اگر میلادی متن باشد: 2023-01-04
            elseif (preg_match('/\d{4}\-\d{1,2}\-\d{1,2}/', $value)) {
                try {
                    $birthday = Carbon::parse($value);
                } catch (\Exception $e) {
                }
            }
        }


        // 🟩 1. تبدیل جنسیت
        $gender = null;
        if (isset($row['gender'])) {
            $value = trim(mb_strtolower($row['gender']));
            if (in_array($value, ['پسر', 'male', 'm'])) {
                $gender = 'male';
            } elseif (in_array($value, ['دختر', 'female', 'f'])) {
                $gender = 'female';
            }
        }

        // 🟩 2. پیدا کردن ID از مدل‌های مرتبط با name
        $grade_id = Grade::where('name', $row['grade_id'] ?? null)->value('id');
        $major_id = Major::where('name', $row['major_id'] ?? null)->value('id');
        $school_id = School::where('name', $row['school_id'] ?? null)->value('id');

        // 🟩 3. مشاور و معرف از مدل Advisor (هر دو با name)
        $consultant_id = Advisor::where('name', $row['consultant_id'] ?? null)->value('id');
        $referrer_id   = Advisor::where('name', $row['referrer_id'] ?? null)->value('id');

        // 🟩 4. ساخت دانش‌آموز جدید
        return new Student([
            'photo'           => $row['photo'] ?? null,
            'first_name'      => $row['first_name'] ?? null,
            'last_name'       => $row['last_name'] ?? null,
            'gender'          => $gender,
            'father_name'     => $row['father_name'] ?? null,
            'national_code'   => $row['national_code'] ?? null,
            'mobile_student'  => $row['mobile_student'] ?? null,
            'grade_id'        => $grade_id,
            'major_id'        => $major_id,
            'school_id'       => $school_id,
            'province'        => $row['province'] ?? null,
            'consultant_id'   => $consultant_id,
            'referrer_id'     => $referrer_id,
            'city'            => $row['city'] ?? null,
            'address'         => $row['address'] ?? null,
            'phone'           => $row['phone'] ?? null,
            'mobile_father'   => $row['mobile_father'] ?? null,
            'mobile_mother'   => $row['mobile_mother'] ?? null,
            'notes'           => $row['notes'] ?? null,
            'birthday'        => $birthday,   

            // 'seat_number'     => $row['seat_number'] ?? null,
        ]);
    }
}
