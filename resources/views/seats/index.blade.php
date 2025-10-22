@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="fw-bold mb-4">مدیریت شماره صندلی‌ها</h3>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-wrap">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p style="font-size: 14px;">با زدن دکمه روبرو، شماره صندلی‌ها طبق پایه، رشته و جنسیت از ابتدا محاسبه می‌شوند.</p>
            <form action="{{ route('seats.generate') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success bg-admin-green px-4">
                    <i class="bi bi-grid-fill"></i> تولید شماره صندلی‌ها
                </button>
            </form>
        </div>

        <div>
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>عکس</th>
                        <th>نام</th>
                        <th>نام خانوادگی</th>
                        <th>شماره صندلی</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <!-- <td>{{ $loop->iteration }}</td> -->

                        {{-- عکس --}}
                        <td>
                            @if($student->photo)
                            <img src="{{ route('students.photo', basename($student->photo)) }}"
                                alt="photo" width="50" height="50" class="rounded-circle">
                            @else
                            <img src="{{ asset('images/no-photo.png') }}" width="50" height="50" class="rounded-circle">
                            @endif
                        </td>

                        <td>{{ $student->first_name }}</td>
                        <td>{{ $student->last_name }}</td>
                        <td>
                            @if($student->seat_number)
                            {{$student->seat_number}}
                            @endif
                        </td>


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
</div>
@endsection