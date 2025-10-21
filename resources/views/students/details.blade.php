@extends('layouts.app')

@section('content')
<h3 class="mb-4 fw-blod fs18">جزئیات دانش‌آموز: {{ $student->first_name }} {{ $student->last_name }}</h3>

<div class="table-wrap">
    {{-- خلاصه مالی دانش‌آموز --}}
    <div class="card mb-4">
        <div class="card-header bg-admin-green text-white">📊 خلاصه مالی دانش‌آموز</div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <strong>💰 جمع کل محصولات:</strong>
                    <p>{{ number_format($totalProducts) }} تومان</p>
                </div>
                <div class="col-md-3">
                    <strong>💵 پرداخت نقدی:</strong>
                    <p>{{ number_format($totalPayments) }} تومان</p>
                </div>
                <div class="col-md-3">
                    <strong>🧾 پرداخت با چک:</strong>
                    <p>{{ number_format($totalChecks) }} تومان</p>
                </div>
                <div class="col-md-3">
                    <strong>📈 مجموع پرداختی:</strong>
                    <p>{{ number_format($totalPaid) }} تومان</p>
                </div>
            </div>

            <hr>

            @if($debt > 0)
            <p class="text-danger fw-bold"><strong>🔻 بدهکار:</strong> {{ number_format($debt) }} تومان</p>
            @elseif($credit > 0)
            <p class="text-success fw-bold"><strong>✅ بستانکار:</strong> {{ number_format($credit) }} تومان</p>
            @else
            <p class="text-secondary fw-bold">تسویه‌شده ✅</p>
            @endif
        </div>
    </div>

    {{-- نمایش محصولات --}}
    <h5 class="mb-3 fw-bold fs18">محصولات تخصیص داده‌شده</h5>

    @foreach($student->productStudents as $index => $ps)
    {{-- فقط اگر محصول پرداخت یا چک داشته باشد جدول را نمایش بده --}}
    @if($ps->payments->count() || $ps->checks->count())
    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong>
                {{$ps->product->name}}
            </strong>
            <span class="badge bg-secondary">
                @if($ps->payment_type == 'cash')
                نقدی
                @elseif($ps->payment_type == 'installment')
                اقساطی
                @endif
            </span>
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
                            <a href="{{ route('payments.receipt', $pay->id) }}" target="_blank" class="btn btn-success bg-admin-green btn-sm">مشاهده</a>
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
            <h6 class="text-success mt-4">چک‌ها</h6>
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
                        <td>
                            @if($check->check_image)
                            <a href="{{ route('checks.image', $check->id) }}" target="_blank" class="btn btn-success bg-admin-green btn-sm">مشاهده</a>
                            @else
                            -
                            @endif
                        </td>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif



        </div>
    </div>
    @endif
    @endforeach


    <div class="text-start">
        <a href="{{ route('students.index') }}" class="btn btn-secondary">بازگشت</a>
    </div>
</div>
@endsection