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
        <form method="GET" class="mb-3">
            <div class="row align-items-center">
                <div class="mb-3 col-lg-3">
                    <label for="gender">جنسیت</label>
                    <select name="gender" id="gender" class="form-control">
                        <option value="">همه</option>
                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>مرد</option>
                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>زن</option>
                    </select>
                </div>

                <div class="mb-3 col-lg-3">
                    <label for="grade_id">پایه</label>
                    <select name="grade_id" id="grade_id" class="form-control">
                        <option value="">همه</option>
                        @foreach($grades as $grade)
                        <option value="{{ $grade->id }}" {{ request('grade_id') == $grade->id ? 'selected' : '' }}>
                            {{ $grade->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 col-lg-3">
                    <label for="major_id">رشته</label>
                    <select name="major_id" id="major_id" class="form-control">
                        <option value="">همه</option>
                        @foreach($majors as $major)
                        <option value="{{ $major->id }}" {{ request('major_id') == $major->id ? 'selected' : '' }}>
                            {{ $major->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 col-lg-3 mt-4 d-flex gap-2">
                    <!-- دکمه نمایش -->
                    <button type="submit" formaction="{{ route('seatsNumber.view') }}" class="btn btn-success bg-admin-green">
                        نمایش
                    </button>

                    <!-- دکمه PDF -->
                    <button type="submit" formaction="{{ route('students.pdf.generate') }}" class="btn btn-success bg-admin-green">
                        چاپ PDF
                    </button>
                </div>
            </div>
        </form>

       
        <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>عکس</th>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>جنسیت</th>
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
                    <td>
                        @if($student->gender == 'male')
                        <span>پسر</span>
                        @else
                        <span>دختر</span>
                        @endif
                    </td>
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