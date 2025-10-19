@extends('layouts.app')

@section('title', 'مدیریت رشته‌ها')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fs18 fw-bold">مدیریت رشته‌های تحصیلی</h4>
    <a href="{{ route('majors.create') }}" class="btn btn-success bg-admin-green">افزودن رشته جدید</a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-wrap">
    <table class="table">
        <thead class="table-light">
            <tr>
                <th>ردیف</th>
                <th>نام رشته</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($majors as $key => $major)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $major->name }}</td>
                <td>
                    <a href="{{ route('majors.edit', $major) }}" class="btn btn-sm btn-success bg-admin-green">ویرایش</a>
                    <form action="{{ route('majors.destroy', $major) }}" method="POST" class="d-inline">
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