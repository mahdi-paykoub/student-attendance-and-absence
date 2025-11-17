@extends('layouts.app')

@section('title', 'لیست دانش‌آموزان')
@section('styles')
<link rel="stylesheet" href="{{asset('assets/css/data-picker.css')}}">
@endsection

@section('content')
<div class="mt-4">

    {{-- پیام موفقیت --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif


    <div class="table-wrap table-responsive-xl students-list-table">

        <div class="row justify-content-end">
            <div class="col-4">
                <form action="" method="GET" class="mb-3 d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="جستجوی نام یا کد ملی" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-success btn-sm bg-admin-green me-2">جستجو</button>

                </form>
            </div>
        </div>

        <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>دانش آموز</th>
                    <th>کدملی</th>
                    <th>قالب پیامک</th>
                    <th>خط ارسال</th>
                    <th>فرد دریافت کننده</th>
                    <th>شماره دریافت کننده</th>
                    <th>متن پیامک</th>
                    <th>زمان ارسال</th>

                </tr>
            </thead>
            <tbody>
                @forelse($smsReports as $report)
                <tr>
                    <td>{{ $report->student->first_name ?? '-' }} {{ $report->student->last_name ?? '-' }}</td>
                    <td>{{ $report->student->national_code ?? '-' }}</td>
                    <td>{{ $report->template->title ?? '-' }}</td>
                    <td>{{ $report->template->gateway ?? '-' }}</td>
                    <td> @php
                        $receiver = $report->template->receiver_type ?? '-';
                        if ($receiver === 'father') $receiver = 'پدر دانش‌آموز';
                        elseif ($receiver === 'mother') $receiver = 'مادر دانش‌آموز';
                        elseif ($receiver === 'student') $receiver = 'دانش‌آموز';
                        @endphp
                        {{ $receiver }}
                    </td>
                    <td>{{ $report->to }}</td>
                    <td style="white-space: pre-line;">{{ $report->body }}</td>
                    <td dir="ltr" class="text-end">
                        {{ \Morilog\Jalali\Jalalian::fromDateTime($report->created_at)->format('Y/m/d (H:i)') }}
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted">هیچ پیامکی ثبت نشده است.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection