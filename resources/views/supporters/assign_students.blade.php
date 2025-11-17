@extends('layouts.app')

@section('title', 'ارجاع دانش‌آموزان به ' . $user->name)

@section('content')
<div class="container mt-4">

    <h4 class="fw-bold fs18">ارجاع دانش‌آموز به پشتیبان: {{ $user->name }}</h4>

    <div class="table-wrap mt-2">
        <form action="{{ route('supporters.assign.store', $user->id) }}" method="POST">
            @csrf

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>انتخاب</th>
                        <th>نام</th>
                        <th>نام‌خانوادگی</th>
                        <th>کدملی</th>
                        <th>پایه</th>
                        <th>رشته</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input" name="students[]" value="{{ $student->id }}">
                        </td>
                        <td>{{ $student->first_name }}</td>
                        <td>{{ $student->last_name }}</td>
                        <td>{{ $student->national_code }}</td>
                        <td>{{ $student->grade->name }}</td>
                        <td>{{ $student->major->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <button class="btn btn-success bg-admin-green btn-sm" type="submit">ثبت ارجاع</button>
            <a href="{{ route('supporters.index') }}" class="btn btn-sm btn-secondary">بازگشت</a>
        </form>
    </div>
</div>
@endsection