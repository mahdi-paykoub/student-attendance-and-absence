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
                    <td>{{ $student->grade->name }}</td>
                    <td>{{ $student->major->name }}</td>
                    <td>{{ $student->seat_number }}</td>
                    <td>
                        @if($student->custom_date)
                        {{ \Morilog\Jalali\Jalalian::fromDateTime($student->custom_date)->format('Y/m/d') }}
                        @else
                        <span class="text-muted">-</span>
                        @endif
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
                            <a href="{{ route('students.details', $student->id) }}" class="btn btn-success bg-admin-green btn-sm mt-1 me-1">مالی</a>

                            {{-- دکمه تاریخ --}}
                            <button type="button"
                                class="btn btn-success bg-admin-green btn-sm mt-1  me-1"
                                data-bs-toggle="modal"
                                data-bs-target="#updateDateModal"
                                data-id="{{ $student->id }}"
                                data-date="{{ $student->custom_date ? \Morilog\Jalali\Jalalian::fromDateTime($student->custom_date)->format('Y/m/d') : '' }}">
                                تاریخ
                            </button>

                            {{-- حذف --}}
                            <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline me-1"
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

{{-- 🔵 مودال مشترک برای تمام دانش‌آموزان --}}
<div class="modal fade" id="updateDateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="updateDateForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fs15">ثبت تاریخ</h5>
                    <button type="button" class="btn-close me-auto ms-0" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>
                <div class="modal-body position-relative">
                    <label class="form-label">تاریخ شمسی:</label>
                    <input type="text" name="custom_date" id="modal_custom_date"
                        class="form-control"
                        data-jdp>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-success bg-admin-green">ثبت</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{asset('assets/js/data-picker.js')}}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('updateDateModal');
        const form = document.getElementById('updateDateForm');
        const inputDate = document.getElementById('modal_custom_date');

        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const studentId = button.getAttribute('data-id');
            const currentDate = button.getAttribute('data-date') || '';

            // مقداردهی فیلد تاریخ
            inputDate.value = currentDate;

            // تنظیم آدرس اکشن فرم
            form.action = `/students/${studentId}/update-date`;
        });
    });
    jalaliDatepicker.startWatch();
</script>
@endsection