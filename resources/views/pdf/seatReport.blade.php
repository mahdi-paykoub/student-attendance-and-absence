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

    <table>
        
        <tbody>
            @foreach($students as $student)
            <tr>
                <td>
                    @if($student->photo_path)
                    <img src="{{ $student->photo_path }}">
                    @endif
                </td>
                <td>
                    <div style="font-size: 14px;">
                        نام:
                    </div>
                    <div>
                        {{ $student->first_name }}
                    </div>
                </td>
                <td>
                    <div style="font-size: 14px;">
                        نام خانوادگی:
                    </div>
                    {{ $student->last_name }}

                </td>
                <td>
                    <div style="font-size: 14px;">
                        پایه:
                    </div>
                    {{ $student->grade->name ?? '' }}
                </td>
                <td>
                    <div style="font-size: 14px;">
                        رشته:
                    </div>
                    {{ $student->major->name ?? '' }}
                </td>
                <td>
                    <div style="font-size: 14px;">
                        شماره صندلی:
                    </div>
                    {{ $student->seat_number ?? '' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>