@extends('layouts.app')

@section('title', 'مدیریت شهرستان‌ها')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold fs18
    ">مدیریت شهرستان‌ها</h4>
    <a href="{{ route('cities.create') }}" class="btn btn-success bg-admin-green">افزودن شهرستان جدید</a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-wrap">
    <table class="table">
        <thead class="table-light">
            <tr>
                <th>ردیف</th>
                <th>نام شهرستان</th>
                <th>استان مربوطه</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cities as $key => $city)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $city->name }}</td>
                <td>{{ $city->province->name ?? '-' }}</td>
                <td>
                    <a href="{{ route('cities.edit', $city) }}" class="btn btn-sm btn-success bg-admin-green">ویرایش</a>
                    <form action="{{ route('cities.destroy', $city) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-secondary" onclick="return confirm('حذف شود؟')">حذف</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection