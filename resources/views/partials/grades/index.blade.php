@extends('layouts.app')

@section('title', 'مدیریت پایه‌ها')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold fs18">مدیریت پایه‌های تحصیلی</h4>
    <a href="{{ route('grades.create') }}" class="btn btn-success bg-admin-green">افزودن پایه جدید</a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-wrap">
    <table class="table">
        <thead class="table-light">
            <tr>
                <th>ردیف</th>
                <th>عنوان پایه</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grades as $key => $grade)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $grade->name }}</td>
                <td>
                    <a href="{{ route('grades.edit', $grade) }}" class="btn btn-sm btn-success bg-admin-green">ویرایش</a>
                    <form action="{{ route('grades.destroy', $grade) }}" method="POST" class="d-inline">
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