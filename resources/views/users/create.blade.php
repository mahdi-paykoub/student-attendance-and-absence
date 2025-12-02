@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fs-18 font-bold">افزودن کاربر جدید</h2>

    <div class="table-wrap">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="mb-3">
                <label>نام</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>ایمیل</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>رمز عبور</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>تایید رمز عبور</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>


            <button type="submit" class="btn btn-success bg-admin-green">ثبت</button>
        </form>
    </div>
</div>
@endsection