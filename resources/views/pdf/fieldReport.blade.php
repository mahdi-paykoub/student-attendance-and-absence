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
        }

        th,
        td {
            border: 1px solid #666;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #e3f2fd;
        }

        img {
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>

<body>

    <h3 style="text-align: center; margin-bottom: 15px;">گزارش دانش‌آموزان</h3>

    <table>
        <thead>
            <tr>
                @foreach($columns as $col)
                @if($col === 'payment_status')
                <th>جمع پرداخت‌ها</th>
                <th>جمع محصولات</th>
                <th>بدهکاری</th>
                @else
                <th>
                    @switch($col)
                    @case('photo') عکس @break
                    @case('first_name') نام @break
                    @case('last_name') نام خانوادگی @break
                    @case('gender') جنسیت @break
                    @case('father_name') نام پدر @break
                    @case('national_code') کد ملی @break
                    @case('mobile_student') موبایل دانش‌آموز @break
                    @case('grade_id') پایه @break
                    @case('major_id') رشته @break
                    @case('school_id') مدرسه @break
                    @case('province') استان @break
                    @case('city') شهر @break
                    @case('consultant_id') مشاور @break
                    @case('referrer_id') معرف @break
                    @case('custom_date') تاریخ سفارشی @break
                    @case('birthday') تاریخ تولد @break
                    @case('address') آدرس @break
                    @case('phone') تلفن منزل @break
                    @case('mobile_father') موبایل پدر @break
                    @case('mobile_mother') موبایل مادر @break
                    @case('notes') یادداشت @break
                    @case('seat_number') شماره صندلی @break
                    @case('created_at') تاریخ ایجاد @break
                    @default {{ $col }}
                    @endswitch
                </th>
                @endif
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach($students as $student)
            <tr>
                @foreach($columns as $col)
                @if($col === 'payment_status')
                @php
                $totalPayments = $student->payments()->where('payment_type', 'cash')->sum('amount');
                $totalPrepayments = $student->payments()->where('payment_type', 'installment')->sum('amount');
                $totalChecks = $student->checks()->sum('amount');
                $totalPaid = $totalPayments + $totalPrepayments + $totalChecks;
                $totalProducts = $student->products->sum(function ($product) {
                $taxAmount = $product->price * ($product->tax_percent / 100);
                return $product->price + $taxAmount;
                });
                $debt = max($totalProducts - $totalPaid, 0);
                @endphp
                <td>{{ number_format($totalPaid) }}</td>
                <td>{{ number_format($totalProducts) }}</td>
                <td>{{ number_format($debt) }}</td>

                @elseif(in_array($col, ['created_at', 'birthday', 'custom_date']) && $student->$col)
                <td>{{ \Morilog\Jalali\Jalalian::forge($student->$col)->format('Y/m/d - H:i') }}</td>

                @elseif($col === 'gender')
                <td>{{ $student->gender === 'male' ? 'پسر' : ($student->gender === 'female' ? 'دختر' : '-') }}</td>

                @elseif($col === 'photo')
                @php
                $photoPath = $student->photo
                ? public_path('storage/' . basename($student->photo))
                : public_path('download.jpg');
                @endphp
                <td>
                    <img src="{{ $photoPath }}" width="50" height="50">
                </td>

                @else
                <td>{{ $student->$col }}</td>
                @endif
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>