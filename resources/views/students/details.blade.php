@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">جزئیات دانش‌آموز: {{ $student->first_name }} {{ $student->last_name }}</h3>

    <div class="card mb-4">
        <div class="card-body">
            <h5>اطلاعات پایه</h5>
            <p><strong>کد ملی:</strong> {{ $student->national_code }}</p>
            <p><strong>تلفن:</strong> {{ $student->phone }}</p>
            <p><strong>پایه تحصیلی:</strong> {{ $student->grade->name ?? '-' }}</p>
        </div>
    </div>

    <h5 class="mb-3">محصولات تخصیص داده‌شده</h5>

    @foreach($student->productStudents as $ps)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <strong>{{ $ps->product->name }}</strong> 
                <span class="badge bg-secondary">{{ $ps->payment_type }}</span>
            </div>
            <div class="card-body">

                {{-- پرداخت‌ها --}}
                @if($ps->payments->count())
                    <h6 class="text-success">پرداخت‌ها</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>تاریخ</th>
                                <th>ساعت</th>
                                <th>مبلغ</th>
                                <th>شماره فیش</th>
                                <th>کارت</th>
                                <th>رسید</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ps->payments as $pay)
                                <tr>
                                    <td>{{ \Morilog\Jalali\Jalalian::fromDateTime($pay->date)->format('Y/m/d') }}</td>
                                    <td>{{ $pay->time }}</td>
                                    <td>{{ number_format($pay->amount) }} تومان</td>
                                    <td>{{ $pay->voucher_number ?? '-' }}</td>
                                    <td>{{ $pay->paymentCard->name ?? '-' }}</td>
                                    <td>
                                        @if($pay->receipt_image)
                                            <a href="{{ Storage::url($pay->receipt_image) }}" target="_blank" class="btn btn-outline-primary btn-sm">مشاهده</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                {{-- چک‌ها --}}
                @if($ps->checks->count())
                    <h6 class="text-warning mt-4">چک‌ها</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>تاریخ</th>
                                <th>مبلغ</th>
                                <th>سریال</th>
                                <th>کد صیاد</th>
                                <th>صاحب چک</th>
                                <th>کد ملی</th>
                                <th>تلفن</th>
                                <th>عکس</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ps->checks as $check)
                                <tr>
                                    <td>{{ \Morilog\Jalali\Jalalian::fromDateTime($check->date)->format('Y/m/d') }}</td>
                                    <td>{{ number_format($check->amount) }} تومان</td>
                                    <td>{{ $check->serial }}</td>
                                    <td>{{ $check->sayad_code }}</td>
                                    <td>{{ $check->owner_name }}</td>
                                    <td>{{ $check->owner_national_code }}</td>
                                    <td>{{ $check->owner_phone }}</td>
                                    <td>
                                        @if($check->check_image)
                                            <a href="{{ Storage::url($check->check_image) }}" target="_blank" class="btn btn-outline-primary btn-sm">مشاهده</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            </div>
        </div>
    @endforeach

    <a href="{{ route('students.index') }}" class="btn btn-secondary mt-3">بازگشت</a>
</div>
@endsection
