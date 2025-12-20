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

    <table class="table table-striped">
        <thead class="table-light">
            <tr class="text-center">
                <th>#</th>
                <th>دانش‌آموز</th>
                <th>کدملی</th>
                <th>مبلغ</th>
                <th>نحوه واریز</th>

                <th>تاریخ واریز</th>
            </tr>
        </thead>

        <tbody>
            @foreach($payments as $index => $payment)
            <tr class="text-center">
                <td>{{ $index + 1 }}</td>
                <td>{{ $payment->student->first_name }} {{ $payment->student->last_name }}</td>
                <td>{{ $payment->student->national_code }}</td>
                <td>{{ number_format($payment->amount) }}</td>
                <td>
                    @if($payment->payment_type == 'cash')
                    نقدی
                    @elseif($payment->payment_type == 'installment')
                    پیش‌پرداخت قسط
                    @endif
                </td>
                <td>{{ \Morilog\Jalali\Jalalian::forge($payment->date)->format('Y/m/d H:i') }}</td>




            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>