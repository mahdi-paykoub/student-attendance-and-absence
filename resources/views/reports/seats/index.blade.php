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

    <div class="row">
        <div class="text-start w-100">
            <a href="{{ route('students.pdf.generate') }}" class="btn btn-success bg-admin-green me-auto mb-3">
                چاپ شماره صندلی
            </a>
        </div>
    </div>
    <div class="table-wrap table-responsive-xl students-list-table">

        <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>عکس</th>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>پایه</th>
                    <th>رشته</th>
                    <th>شماره صندلی</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    {{-- عکس --}}
                    <td>
                        @if($student->photo)
                        <img src="{{ route('students.photo', basename($student->photo)) }}"
                            alt="photo" width="40" height="40" class="rounded-circle">
                        @else
                        <img src="{{ asset('images/no-photo.png') }}" width="50" height="50" class="rounded-circle">
                        @endif
                    </td>

                    <td>{{ $student->first_name }}</td>
                    <td>{{ $student->last_name }}</td>
                    <td>{{ optional($student->grade)->name }}</td>
                    <td>{{ optional($student->major)->name }}</td>
                    <td>{{ $student->seat_number }}</td>


                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted">هیچ دانش‌آموزی ثبت نشده است.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection