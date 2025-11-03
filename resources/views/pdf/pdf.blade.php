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
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-after: always;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            /* فاصله بین ردیف‌ها */
        }

        .card {
            width: 48%;
            /* دو کارت در یک ردیف */
            border: 1px solid #ddd;
            /* رنگ ملایم */
            border-radius: 10px;
            /* گوشه‌های گرد */
            padding: 15px;
            box-sizing: border-box;
            text-align: right;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* سایه ملایم */
            background-color: #fff;
            margin-bottom: 10px;
            /* فاصله بین کارت‌ها اگر لازم شد */
            display: flex ;
            justify-content: space-between;
            align-items: center;
        }

        .card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 10px;
            border-radius: 50%;
            /* تصویر گرد */
        }

        .card p {
            margin: 4px 0;
        }
    </style>
</head>

<body>
    @php $counter = 0; @endphp

    @foreach($students as $student)
    @if($counter % 12 == 0 && $counter != 0)
    <div class="page-break"></div>
    @endif

    @if($counter % 2 == 0)
    <div class="row">
        @endif

        <div class="card">

            <div>
                @if($student->photo_path)
                <img src="{{ $student->photo_path }}" style="width:80px; height:80px; border-radius:10px;">
                @endif
            </div>
            <div>
                <p><strong>نام:</strong> {{ $student->first_name }}</p>
                <p><strong>نام خانوادگی:</strong> {{ $student->last_name }}</p>
                <p><strong>پایه:</strong> {{ $student->grade->name ?? '' }}</p>
                <p><strong>رشته:</strong> {{ $student->major->name ?? '' }}</p>
                <p><strong>شماره صندلی:</strong> {{ $student->seat_number ?? '' }}</p>
            </div>
        </div>


        @if($counter % 2 == 1)
    </div> {{-- بسته شدن row --}}
    @endif

    @php $counter++; @endphp
    @endforeach

    @if($counter % 2 != 0)
    </div>
    @endif
</body>

</html>