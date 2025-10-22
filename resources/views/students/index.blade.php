@extends('layouts.app')

@section('title', 'لیست دانش‌آموزان')

@section('content')
<div class=" mt-4">

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

    {{-- جدول --}}
    <div class="table-wrap">
        <div class="text-start mb-4">
            {{-- فیلتر دانش‌آموزان --}}
            <form method="GET" action="{{ route('students.index') }}" class="mb-3 d-flex align-items-center">
                <label for="filter" class="me-2 fw-bold">فیلتر:</label>
                <select name="filter" id="filter" class="form-select w-auto me-2" onchange="this.form.submit()">
                    <option value="">همه دانش‌آموزان</option>
                    <option value="with" {{ $filter === 'with' ? 'selected' : '' }}>دارای محصول</option>
                    <option value="without" {{ $filter === 'without' ? 'selected' : '' }}>بدون محصول</option>
                </select>
            </form>
        </div>
        <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>عکس</th>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>شماره صندلی</th>
                    <th>تاریخ عضویت</th>
                    <th>محصول؟</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <!-- <td>{{ $loop->iteration }}</td> -->

                    {{-- عکس --}}
                    <td>
                        @if($student->photo)
                        <img src="{{ route('students.photo', basename($student->photo)) }}"
                            alt="photo" width="50" height="50" class="rounded-circle">
                        @else
                        <img src="{{ asset('images/no-photo.png') }}" width="50" height="50" class="rounded-circle">
                        @endif
                    </td>

                    <td>{{ $student->first_name }}</td>
                    <td>{{ $student->last_name }}</td>
                    <td>
                        @if($student->seat_number)
                        {{$student->seat_number}}
                        @endif
                    </td>
                    <td class="fs14 text-end" dir="ltr">{{ \Morilog\Jalali\Jalalian::fromDateTime($student->created_at)->format('Y/m/d H:i') }}</td>
                    <td>
                        @if($student->products->count() > 0)
                        <span class="badge bg-admin-green">دارد</span>
                        @else
                        <span class="badge bg-danger">ندارد</span>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-success bg-admin-green">ویرایش</a>

                        <a href="{{ route('student-products.assign', $student->id) }}" class="btn btn-success bg-admin-green btn-sm">تخصیص</a>
                        <a href="{{ route('students.details', $student->id) }}" class="btn btn-success bg-admin-green btn-sm">
                            مشاهده مالی
                        </a>

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