@extends('layouts.app')

@section('title', 'ثبت نام دانش‌آموز جدید')

@section('content')
<div class=" mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-admin-green text-white">
            <h5 class="mb-0">فرم ثبت‌نام دانش‌آموز با exel</h5>
        </div>
        <div class="card-body">

            <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="d-flex align-items-center justify-content-between">
                    <input class="form-control" type="file" name="file" accept=".xlsx,.xls" required>
                    <button class="btn btn-success bg-admin-green me-3" type="submit">آپلود اکسل</button>
                </div>
            </form>


        </div>
    </div>
</div>
@endsection