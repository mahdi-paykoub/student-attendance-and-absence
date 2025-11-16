<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>لیست دانش‌آموزان</title>
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
            border: 1px solid #dfdfdf;
            padding: 8px;
            text-align: right;
            font-size: 16px;
        }

        th {
            background-color: #f0f0f0;
        }

        img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    <table class="">
        <thead>
            <tr>
                <th>#</th>
                <th>نام دانش‌آموز</th>
                <th>مجموع محصولات (با مالیات)</th>
                <th>جمع پرداختی‌ها</th>
                <th>بدهی</th>
                <th>وضعیت</th>
            </tr>
        </thead>
        <tbody>
            @forelse($debtors as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $student->first_name }} - {{ $student->last_name }}</td>
                <td>{{ number_format($student->total_product_cost ?? ($student->product_total ?? 0)) }} تومان</td>
                <td>{{ number_format($student->total_payments ?? ($student->payment_total ?? 0)) }} تومان</td>
                <td>
                    {{ number_format( 
                        ($student->total_product_cost ?? ($student->product_total ?? 0))
                        - ($student->total_payments ?? ($student->payment_total ?? 0))
                    ) }} تومان
                </td>
                <td>
                    @if(
                    (($student->total_product_cost ?? ($student->product_total ?? 0))
                    - ($student->total_payments ?? ($student->payment_total ?? 0))) > 0
                    )
                    <span class="badge bg-danger">بدهکار</span>
                    @else
                    <span class="badge bg-success">بی‌بدهی</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">هیچ دانش‌آموز بدهکاری یافت نشد.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>