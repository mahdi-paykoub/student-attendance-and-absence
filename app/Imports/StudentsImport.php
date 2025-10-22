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
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
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
        $province_id = Province::where('name', $row['province_id'] ?? null)->value('id');
        $city_id = City::where('name', $row['city_id'] ?? null)->value('id');

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
            'province_id'     => $province_id,
            'consultant_id'   => $consultant_id,
            'referrer_id'     => $referrer_id,
            'city_id'         => $city_id,
            'address'         => $row['address'] ?? null,
            'phone'           => $row['phone'] ?? null,
            'mobile_father'   => $row['mobile_father'] ?? null,
            'mobile_mother'   => $row['mobile_mother'] ?? null,
            'notes'           => $row['notes'] ?? null,
            // 'seat_number'     => $row['seat_number'] ?? null,
        ]);
    }
}
