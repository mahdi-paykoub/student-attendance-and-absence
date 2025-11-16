@extends('layouts.app')



@section('content')
<div class="mt-4">

    {{-- پیام موفقیت --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif


    <div class="mb-3 text-start">
        <form action="{{route('report.get.debtor.students.pdf')}}">
            @csrf
            <button class="btn btn-success bg-admin-green">
                دریافت pdf
            </button>
        </form>
    </div>
    <div class="table-wrap table-responsive-xl students-list-table">

        <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>کد ملی</th>
                    <th>جنسیت</th>
                    <th>پایه</th>
                    <th>رشته</th>

                    {{-- ستون‌های جدید --}}
                    <th>جمع محصولات (با مالیات)</th>
                    <th>جمع پرداختی‌ها</th>
                    <th>بدهی</th>
                </tr>
            </thead>
            <tbody>
                @forelse($debtors as $student)
                @php
                // اگر از مدل مقدارها بیاد:
                $totalProducts = $student->total_product_cost ?? ($student->product_total ?? 0);
                $totalPayments = $student->total_payments ?? ($student->payment_total ?? 0);
                $debt = $totalProducts - $totalPayments;
                @endphp

                <tr>
                    <td>{{ $student->first_name }}</td>
                    <td>{{ $student->last_name }}</td>
                    <td>{{ $student->national_code }}</td>

                    <td>
                        @if($student->gender == 'male')
                        <span>پسر</span>
                        @else
                        <span>دختر</span>
                        @endif
                    </td>

                    <td>{{ optional($student->grade)->name }}</td>
                    <td>{{ optional($student->major)->name }}</td>

                    {{-- ستون‌های جدید --}}
                    <td>{{ number_format($totalProducts) }} تومان</td>
                    <td>{{ number_format($totalPayments) }} تومان</td>

                    <td class="{{ $debt > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                        {{ number_format($debt) }} تومان
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted">هیچ دانش‌آموزی یافت نشد.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection