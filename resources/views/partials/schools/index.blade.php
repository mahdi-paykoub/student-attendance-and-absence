@extends('layouts.app')

@section('title', 'مدیریت مدارس')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fs18 fw-bold">مدیریت مدارس</h4>
    <a href="{{ route('schools.create') }}" class="btn btn-success bg-admin-green">افزودن مدرسه جدید</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>ردیف</th>
            <th>نام مدرسه</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($schools as $key => $school)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $school->name }}</td>
                <td>
                    <a href="{{ route('schools.edit', $school) }}" class="btn btn-sm btn-success bg-admin-green">ویرایش</a>
                    <form action="{{ route('schools.destroy', $school) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-secondary" onclick="return confirm('حذف شود؟')">حذف</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
