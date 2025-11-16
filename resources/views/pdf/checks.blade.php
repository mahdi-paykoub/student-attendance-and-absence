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
                <th>تاریخ</th>
                <th>مبلغ</th>
                <th>سریال</th>
                <th>شناسه صیاد</th>
                <th>نام صاحب چک</th>
                <th>وضعیت وصول</th>
            </tr>
        </thead>

        <tbody>
            @foreach($checks as $index => $check)
            <tr class="text-center">
                <td>{{ $index + 1 }}</td>
                <td>{{ $check->student->first_name }} {{ $check->student->last_name }}</td>
                <td>{{ \Morilog\Jalali\Jalalian::forge($check->date)->format('Y/m/d') }}</td>
                <td>{{ number_format($check->amount) }} تومان</td>
                <td>{{ $check->serial }}</td>
                <td>{{ $check->sayad_code }}</td>
                <td>{{ $check->owner_name }}</td>

                <td>
                    @if($check->is_cleared)
                    <span class="badge bg-admin-green">وصول شده</span>
                    @else
                    <span class="badge bg-danger">وصول نشده</span>
                    @endif
                </td>


            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>