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

     <table class="table mt-4 table-striped">
            <thead class="table-light">
                <tr>
                    <th>عنوان</th>
                    <th>حساب</th>
                    <th>مبلغ</th>
                    <th>تاریخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deposits as $deposit)
                <tr>
                    <td>{{ $deposit->title }}</td>
                    <td>{{ $deposit->account->name }}</td>
                    <td>{{ number_format($deposit->amount) }}</td>
                    <td>{{ $deposit->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">واریزی‌ وجود ندارد</td>
                </tr>
                @endforelse
            </tbody>
        </table>

</body>

</html>