@extends('layouts.app')

@section('title', 'لیست دانش‌آموزان')
@section('styles')
<link rel="stylesheet" href="{{asset('assets/css/data-picker.css')}}">
@endsection

@section('content')
<div class="mt-4">

    {{-- هدر صفحه --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-bold fs18">لیست دانش‌آموزان</h4>
        <a href="{{ route('students.create') }}" class="btn btn-success bg-admin-green">
            + افزودن دانش‌آموز جدید
        </a>
    </div>

    {{-- پیام موفقیت --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="table-wrap table-responsive-xl students-list-table">
        <div class="row">
            <div class="col-lg-3">
                <form method="GET" action="{{ route('students.index') }}" class="">
                    <div class="d-flex align-items-center mb-4">
                        <label class="" for="filter">فیلتر</label>
                        <select class="form-control me-2" name="filter" id="filter" onchange="this.form.submit()">
                            <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>همه</option>
                            <option value="with" {{ $filter === 'with' ? 'selected' : '' }}>با محصول</option>
                            <option value="without" {{ $filter === 'without' ? 'selected' : '' }}>بدون محصول</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>عکس</th>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>نام پدر</th>
                    <th>پایه</th>
                    <th>رشته</th>
                    <th>شماره صندلی</th>
                    <th>تاریخ ثبت شده</th>
                    <th>تاریخ عضویت</th>
                    <th>محصول؟</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    {{-- عکس --}}
                    <td>
                        @if($student->photo)
                        <img src="{{ route('students.photo', basename($student->photo)) }}"
                            alt="photo" width="40" height="40" class="rounded-circle">
                        @else
                        <img src="{{ asset('images/no-photo.png') }}" width="50" height="50" class="rounded-circle">
                        @endif
                    </td>

                    <td>{{ $student->first_name }}</td>
                    <td>{{ $student->last_name }}</td>
                    <td>{{ $student->father_name }}</td>
                    <td>{{ optional($student->grade)->name }}</td>
                    <td>{{ optional($student->major)->name }}</td>
                    <td>{{ $student->seat_number }}</td>
                    <td>
                        <form id="updateDateForm" method="POST" action="{{route('students.updateDate' , $student->id)}}" class="d-flex align-items-center">
                            @csrf
                            @method('PUT')

                            <input style="width: 90px; height: 35px;font-size: 13px;" data-jdp type="text" name="custom_date" id="modal_custom_date" value=" @if($student->custom_date){{ \Morilog\Jalali\Jalalian::fromDateTime($student->custom_date)->format('Y/m/d') }}@endif"
                                class="form-control p-1"
                                data-jdp>
                            <button type="submit" class="btn btn-sm btn-success bg-admin-green">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="none" d="M0 0h24v24H0z"></path>
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75zM20.71 5.63l-2.34-2.34a.996.996 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83a.996.996 0 0 0 0-1.41z"></path>
                                </svg>
                            </button>
                        </form>

                    </td>
                    <td dir="ltr">{{ \Morilog\Jalali\Jalalian::fromDateTime($student->created_at)->format('Y/m/d H:i') }}</td>
                    <td>
                        @if($student->products->count() > 0)
                        <span class="badge bg-admin-green">دارد</span>
                        @else
                        <span class="badge bg-danger">ندارد</span>
                        @endif
                    </td>

                    <td>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-success bg-admin-green mt-1 me-1">ویرایش</a>
                            <a href="{{ route('student-products.assign', $student->id) }}" class="btn btn-success bg-admin-green btn-sm mt-1 me-1">تخصیص</a>

                        </div>
                        <div>
                            <a href="{{ route('students.details', $student->id) }}" class="btn btn-success bg-admin-green btn-sm mt-1 me-1">مالی</a>


                            {{-- حذف --}}
                            <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline "
                                onsubmit="return confirm('آیا از حذف این دانش‌آموز مطمئن هستید؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-secondary mt-1">حذف</button>
                            </form>
                        </div>
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
</div>

@endsection

@section('scripts')
<script src="{{asset('assets/js/data-picker.js')}}"></script>
<script>
    jalaliDatepicker.startWatch();
</script>

@endsection