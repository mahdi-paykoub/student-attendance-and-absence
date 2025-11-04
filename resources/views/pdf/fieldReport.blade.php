<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>گزارش دانش‌آموزان</title>
    <style>
        body {
            font-family: 'fa', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 10px;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 5px;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>

<body>

    @php
    $labels = [
    'photo' => 'عکس',
    'first_name' => 'نام',
    'last_name' => 'نام خانوادگی',
    'gender' => 'جنسیت',
    'father_name' => 'نام پدر',
    'national_code' => 'کد ملی',
    'mobile_student' => 'موبایل دانش‌آموز',
    'grade_id' => 'پایه',
    'major_id' => 'رشته',
    'school_id' => 'مدرسه',
    'province' => 'استان',
    'city' => 'شهر',
    'consultant_id' => 'مشاور',
    'referrer_id' => 'ارجاع‌دهنده',
    'custom_date' => 'تاریخ سفارشی',
    'birthday' => 'تاریخ تولد',
    'address' => 'آدرس',
    'phone' => 'تلفن منزل',
    'mobile_father' => 'موبایل پدر',
    'mobile_mother' => 'موبایل مادر',
    'notes' => 'یادداشت',
    'seat_number' => 'شماره صندلی',
    'created_at' => 'تاریخ ایجاد',
    ];
    @endphp

    <h3 style="text-align: center;">گزارش دانش‌آموزان</h3>

    <table>
        <thead>
            <tr>
                @foreach($columns as $col)
                <th>{{ $labels[$col] ?? $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                @foreach($columns as $col)
                <td>
                    @if(in_array($col, ['created_at', 'birthday', 'custom_date']) && $student->$col)
                    {{ \Morilog\Jalali\Jalalian::forge($student->$col)->format('Y/m/d - H:i') }}
                    @elseif($col === 'gender')
                    {{ $student->gender === 'male' ? 'پسر' : ($student->gender === 'female' ? 'دختر' : '-') }}
                    @else
                    {{ $student->$col }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>