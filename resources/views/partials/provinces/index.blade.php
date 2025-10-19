@extends('layouts.app')

@section('title', 'مدیریت استان‌ها')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold fs18">مدیریت استان‌ها</h4>
    <a href="{{ route('provinces.create') }}" class="btn btn-success bg-admin-green">افزودن استان جدید</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>ردیف</th>
            <th>نام استان</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($provinces as $key => $province)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $province->name }}</td>
                <td>
                    <a href="{{ route('provinces.edit', $province) }}" class="btn btn-sm btn-success bg-admin-green">ویرایش</a>
                    <form action="{{ route('provinces.destroy', $province) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-secondary" onclick="return confirm('حذف شود؟')">حذف</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
