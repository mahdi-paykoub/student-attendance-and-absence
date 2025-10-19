@extends('layouts.app')

@section('title', 'لیست آزمون‌ها')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-bold">لیست آزمون‌ها</h4>
        <a href="{{ route('exams.create') }}" class="btn btn-success bg-admin-green">+ افزودن آزمون</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif



    <div class="table-wrap">
        <table class="table">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>نام آزمون</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exams as $exam)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $exam->name }}</td>
                    <td>
                        <a href="{{ route('exams.attendance', $exam) }}" class="btn btn-sm btn-success bg-admin-green">
                            حضور و غیاب
                        </a>
                        <a href="{{ route('exams.edit', $exam) }}" class="btn btn-sm btn-success bg-admin-green">ویرایش</a>
                        <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('آیا از حذف این آزمون مطمئن هستید؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-secondary">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">هیچ آزمونی ثبت نشده است.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $exams->links('pagination::bootstrap-5') }}
    </div>

</div>
@endsection