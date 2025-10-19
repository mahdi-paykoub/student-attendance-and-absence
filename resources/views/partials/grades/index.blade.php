@extends('layouts.app')

@section('title', 'مدیریت پایه‌ها')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>مدیریت پایه‌های تحصیلی</h4>
    <a href="{{ route('grades.create') }}" class="btn btn-primary">افزودن پایه جدید</a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-hover">
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
                <a href="{{ route('grades.edit', $grade) }}" class="btn btn-sm btn-warning">ویرایش</a>
                <form action="{{ route('grades.destroy', $grade) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('حذف شود؟')">حذف</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection