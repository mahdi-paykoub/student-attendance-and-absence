@extends('layouts.app')

@section('title', 'لیست دانش‌آموزان')

@section('content')
<div class="container mt-4">

    {{-- هدر صفحه --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-bold fs18">لیست دانش‌آموزان</h4>
        <a href="{{ route('students.create') }}" class="btn btn-success bg-admin-green">
            + افزودن دانش‌آموز جدید
        </a>
    </div>

    {{-- پیام موفقیت --}}
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    {{-- فیلتر دانش‌آموزان --}}
    <form method="GET" action="{{ route('students.index') }}" class="mb-3 d-flex align-items-center">
        <label for="filter" class="me-2 fw-bold">فیلتر:</label>
        <select name="filter" id="filter" class="form-select w-auto me-2" onchange="this.form.submit()">
            <option value="">همه دانش‌آموزان</option>
            <option value="with" {{ $filter === 'with' ? 'selected' : '' }}>دارای محصول</option>
            <option value="without" {{ $filter === 'without' ? 'selected' : '' }}>بدون محصول</option>
        </select>
    </form>
    {{-- جدول --}}
    <div class="table-wrap">
        <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>عکس</th>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>پایه تحصیلی</th>
                    <th>رشته</th>
                    <th>مدرسه</th>
                    <th>استان</th>
                    <th>شهر</th>
                    <th>موبایل</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    {{-- عکس --}}
                    <td>
                        @if($student->photo)
                        <img src="{{ asset('storage/'.$student->photo) }}" alt="photo"
                            width="50" height="50" class="rounded-circle">
                        @else
                        <img src="{{ asset('images/no-photo.png') }}" width="50" height="50" class="rounded-circle">
                        @endif
                    </td>

                    <td>{{ $student->first_name }}</td>
                    <td>{{ $student->last_name }}</td>
                    <td>{{ $student->grade->name ?? '-' }}</td>
                    <td>{{ $student->major->name ?? '-' }}</td>
                    <td>{{ $student->school->name ?? '-' }}</td>
                    <td>{{ $student->province->name ?? '-' }}</td>
                    <td>{{ $student->city->name ?? '-' }}</td>
                    <td>{{ $student->mobile_student }}</td>

                    <td>
                        <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-success bg-admin-green">ویرایش</a>

                        <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('آیا از حذف این دانش‌آموز مطمئن هستید؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-secondary">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted">هیچ دانش‌آموزی ثبت نشده است.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- صفحه‌بندی --}}
    <div class="mt-3">
        {{ $students->links('pagination::bootstrap-5') }}
    </div>

</div>
@endsection