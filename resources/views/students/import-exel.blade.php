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
                <div class="d-md-flex align-items-center justify-content-between">
                    <input class="form-control w-75" type="file" name="file" accept=".xlsx,.xls" required>
                    <button class="btn btn-success bg-admin-green mt-2 mt-md-0" type="submit">آپلود اکسل</button>
                </div>
            </form>



            <form action="{{ route('students.photos.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label class="mt-4"> ZIP تصاویر :</label>

                <div class="d-md-flex align-items-center justify-content-between mt-1">
                    <input type="file" class="form-control w-75" name="photos_zip" accept=".zip" required>
                    <button type="submit" class="btn btn-primary btn btn-success bg-admin-green mt-2 mt-md-0">آپلود تصاویر</button>
                </div>
            </form>

            @if(session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger mt-3">{{ session('error') }}</div>
            @endif


        </div>
    </div>
</div>
@endsection