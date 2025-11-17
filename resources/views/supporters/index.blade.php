@extends('layouts.app')

@section('title', 'لیست پشتبان‌ها')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold fs18">لیست پشتبان‌ها</h4>
        <a href="{{ route('supporters.create') }}" class="btn btn-success bg-admin-green">ایجاد پشتبان جدید</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="table-wrap">
        @if($supporters->count())
        <table class="table table-striped">
            <thead class="table-light">
                <tr>
                    <th>نام</th>
                    <th>ایمیل</th>
                    <th>شماره تماس</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($supporters as $supporter)
                <tr>
                    <td>{{ $supporter->name }}</td>
                    <td>{{ $supporter->phone ?? '-' }}</td>
                    <td>{{ \Morilog\Jalali\Jalalian::fromDateTime($supporter->created_at)->format('Y/m/d') }}</td>
                    <td>

                        <div class="d-flex align-items-center">
                            <form action="{{ route('supporters.destroy', $supporter->id) }}" method="POST" onsubmit="return confirm('آیا از حذف این پشتبان مطمئن هستید؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                            </form>
                            <a href="{{ route('supporters.assign.form', $supporter->id) }}" class="btn me-1 btn-sm btn-success bg-admin-green">
                                ارجاع دانش‌آموز
                            </a>
                            <a href="{{ route('supporters.show_students', $supporter->id) }}" class="btn btn-info btn-sm">
                                لیست دانش‌آموزان
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p>هیچ پشتبانی ثبت نشده است.</p>
        @endif
    </div>

</div>
@endsection