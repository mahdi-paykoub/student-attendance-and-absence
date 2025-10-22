@extends('layouts.app')
@section('title','لیست مشاوران')
@section('content')
<div class=" mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold fs18">لیست مشاوران</h4>
        <a href="{{ route('advisors.create') }}" class="btn btn-success bg-admin-green">+ افزودن مشاور</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-wrap mt-4">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>نام</th>
                    <th>شماره تماس</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($advisors as $advisor)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $advisor->name }}</td>
                    <td>{{ $advisor->phone ?? '-' }}</td>
                    <td>
                        <a href="{{ route('advisors.edit', $advisor) }}" class="btn btn-sm btn-success bg-admin-green">ویرایش</a>
                        <form action="{{ route('advisors.destroy', $advisor) }}" method="POST" class="d-inline" onsubmit="return confirm('آیا مطمئن هستید؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-secondary">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">مشاوری ثبت نشده است.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $advisors->links('pagination::bootstrap-5') }}
</div>
@endsection