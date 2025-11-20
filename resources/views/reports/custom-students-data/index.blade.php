@extends('layouts.app')

@section('title', 'لیست دانش‌آموزان')
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/data-picker.css') }}">
@endsection

@section('content')
<div class="mt-4 table-wrap">

    {{-- پیام موفقیت --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="fw-bold fs18 mb-1 pb-2 border-bottom">
        انتخاب فیلد
    </div>

    <form method="GET" class="mb-3">
        <div class="row">
            @php
            $fields = [
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
            'referrer_id' => 'معرف',
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

            {{-- فیلدهای اصلی --}}
            @foreach($fields as $key => $label)
            <div class="col-md-3 border-start mt-3">
                <div class="form-check">
                    <input type="checkbox" name="columns[]" value="{{ $key }}" id="{{ $key }}"
                        class="form-check-input"
                        {{ in_array($key, request('columns', [])) ? 'checked' : '' }}>
                    <label for="{{ $key }}" class="form-check-label">{{ $label }}</label>
                </div>
            </div>
            @endforeach

            {{-- گزینه وضعیت پرداخت --}}
            <div class="col-md-3 border-start mt-3">
                <div class="form-check">
                    <input type="checkbox" name="columns[]" value="payment_status" id="payment_status"
                        class="form-check-input"
                        {{ in_array('payment_status', request('columns', [])) ? 'checked' : '' }}>
                    <label for="payment_status" class="form-check-label">وضعیت پرداخت</label>
                </div>
            </div>
        </div>

        <button type="submit" formaction="{{ route('report.student.custom.data.view') }}"
            class="btn btn-success bg-admin-green mt-4">نمایش گزارش</button>

        <button type="submit" formaction="{{ route('report.student.custom.data.pdf') }}"
            class="btn btn-success bg-admin-green mt-4">خروجی PDF</button>
    </form>

    <br><br>

    <div class="mt-4">
        <h4 class="fw-bold fs18">گزارش دانش‌آموزان</h4>

        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead class="table-success">
                    <tr>
                        @foreach($columns as $col)
                        @if($col === 'payment_status')
                        <th>جمع پرداخت‌ها</th>
                        <th>جمع محصولات</th>
                        <th>بدهکاری</th>
                        @else
                        <th>{{ $fields[$col] ?? $col }}</th>
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
                        // پرداخت‌های نقدی
                        $totalPayments = $student->payments()->where('payment_type', 'cash')->sum('amount');

                        // پرداخت‌های قسطی
                        $totalPrepayments = $student->payments()->where('payment_type', 'installment')->sum('amount');

                        // چک‌های وصول شده
                        $totalClearedChecks = $student->checks()->where('is_cleared', true)->sum('amount');

                        // مجموع پرداختی‌ها (نقدی + قسط + چک وصول شده)
                        $totalPaid = $totalPayments + $totalPrepayments + $totalClearedChecks;

                        // مجموع مبلغ محصولات + مالیات
                        $totalProducts = $student->products->sum(function ($product) {
                        $taxAmount = $product->price * ($product->tax_percent / 100);
                        return $product->price + $taxAmount;
                        });

                        // بدهی
                        $debt = max($totalProducts - $totalPaid, 0);
                        @endphp
                        <td>{{ number_format($totalPaid) }}</td>
                        <td>{{ number_format($totalProducts) }}</td>
                        <td>{{ number_format($debt) }}</td>

                        @elseif(in_array($col, ['created_at', 'birthday', 'custom_date']) && $student->$col)
                        <td>
                            {{ \Morilog\Jalali\Jalalian::forge($student->$col)->format('Y/m/d - H:i') }}
                        </td>
                        @elseif($col === 'gender')
                        <td>
                            {{ $student->gender === 'male' ? 'پسر' : ($student->gender === 'female' ? 'دختر' : '-') }}
                        </td>

                        @elseif($col === 'photo')
                        @php
                        $photoPath = ($student->photo == null)
                        ? asset('download.jpg')
                        : route('students.photo', ['filename' => basename($student->photo)]);
                        @endphp
                        <td>
                            <img src="{{ $photoPath }}" alt="عکس دانش‌آموز" width="60" height="60"
                                style="object-fit: cover; border-radius: 5px;">
                        </td>

                        @else
                        <td>{{ $student->$col }}</td>
                        @endif
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection