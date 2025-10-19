@extends('layouts.app')

@section('title', 'ثبت نام دانش‌آموز جدید')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-admin-green text-white">
            <h5 class="mb-0">فرم ثبت‌نام دانش‌آموز</h5>
        </div>
        <div class="card-body">

            {{-- نمایش پیام کلی خطا --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    لطفا خطاهای زیر را اصلاح کنید:
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- عکس --}}
                <div class="mb-3">
                    <label class="form-label">عکس 3x4</label>
                    <input type="file" name="photo" class="form-control" required>
                    @error('photo')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- نام و نام خانوادگی --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نام</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                        @error('first_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نام خانوادگی</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                        @error('last_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نام پدر</label>
                        <input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}" required>
                        @error('father_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- کد ملی و موبایل --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">کد ملی</label>
                        <input type="text" name="national_code" class="form-control" value="{{ old('national_code') }}" required>
                        @error('national_code')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">شماره موبایل دانش‌آموز</label>
                        <input type="text" name="mobile_student" class="form-control" value="{{ old('mobile_student') }}" required>
                        @error('mobile_student')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- سلکت باکس‌ها --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">پایه تحصیلی</label>
                        <select name="grade_id" class="form-select" required>
                            <option value="">انتخاب کنید...</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                            @endforeach
                        </select>
                        @error('grade_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">رشته تحصیلی</label>
                        <select name="major_id" class="form-select">
                            <option value="">انتخاب کنید...</option>
                            @foreach($majors as $major)
                                <option value="{{ $major->id }}" {{ old('major_id') == $major->id ? 'selected' : '' }}>{{ $major->name }}</option>
                            @endforeach
                        </select>
                        @error('major_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">نام مدرسه</label>
                        <select name="school_id" class="form-select">
                            <option value="">انتخاب کنید...</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                            @endforeach
                        </select>
                        @error('school_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- استان و شهر --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="province_id" class="form-label">استان</label>
                        <select name="province_id" id="province_id" class="form-select">
                            <option value="">انتخاب کنید</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                            @endforeach
                        </select>
                        @error('province_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="city_id" class="form-label">شهرستان</label>
                        <select name="city_id" id="city_id" class="form-select">
                            <option value="">ابتدا استان را انتخاب کنید</option>
                        </select>
                        @error('city_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- بقیه فیلدها --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نام مشاور</label>
                        <input type="text" name="advisor_name" class="form-control" value="{{ old('advisor_name') }}">
                        @error('advisor_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نام معرف</label>
                        <input type="text" name="referrer_name" class="form-control" value="{{ old('referrer_name') }}">
                        @error('referrer_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">شماره ثابت</label>
                        <input type="text" name="home_phone" class="form-control" value="{{ old('home_phone') }}">
                        @error('home_phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">شماره موبایل پدر</label>
                        <input type="text" name="father_mobile" class="form-control" value="{{ old('father_mobile') }}">
                        @error('father_mobile')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">شماره موبایل مادر</label>
                        <input type="text" name="mother_mobile" class="form-control" value="{{ old('mother_mobile') }}">
                        @error('mother_mobile')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">آدرس</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    @error('address')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">توضیحات</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    @error('description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success bg-admin-green">ثبت دانش‌آموز</button>
            </form>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var provinceSelect = document.getElementById('province_id');
    var citySelect = document.getElementById('city_id');

    provinceSelect.addEventListener('change', function() {
        var provinceId = this.value;
        citySelect.innerHTML = '<option value="">در حال بارگذاری...</option>';

        if (provinceId) {
            fetch('/cities/' + provinceId)
                .then(response => response.json())
                .then(data => {
                    citySelect.innerHTML = '<option value="">انتخاب کنید</option>';
                    data.forEach(function(city) {
                        var option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        citySelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error(error);
                    citySelect.innerHTML = '<option value="">خطا در دریافت داده‌ها</option>';
                });
        } else {
            citySelect.innerHTML = '<option value="">ابتدا استان را انتخاب کنید</option>';
        }
    });
});
</script>
@endsection
