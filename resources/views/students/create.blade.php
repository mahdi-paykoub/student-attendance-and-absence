@extends('layouts.app')
@section('styles')
<link rel="stylesheet" href="{{asset('assets/css/data-picker.css')}}">
@endsection
@section('title', 'ثبت نام دانش‌آموز جدید')

@section('content')
<div class=" mt-4">
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
                        <input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}">
                        @error('father_name')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                {{-- کد ملی و موبایل --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">جنسیت</label>
                        <select name="gender" class="form-select">
                            <option value="">انتخاب کنید</option>
                            <option value="male">پسر</option>
                            <option value="female">دختر</option>
                        </select>
                        @error('gender')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">کد ملی</label>
                        <input type="text" name="national_code" class="form-control" value="{{ old('national_code') }}" required>
                        @error('national_code')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
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



                {{-- بقیه فیلدها --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="consultant_id" class="form-label">مشاور</label>
                        <select name="consultant_id" id="consultant_id" class="form-select">
                            <option value="">انتخاب مشاور</option>
                            @foreach($advisors as $advisor)
                            <option value="{{ $advisor->id }}" {{ old('consultant_id', $student->consultant_id ?? '') == $advisor->id ? 'selected' : '' }}>
                                {{ $advisor->name }} - {{ $advisor->phone }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="referrer_id" class="form-label">معرف</label>
                        <select name="referrer_id" id="referrer_id" class="form-select">
                            <option value="">انتخاب معرف</option>
                            @foreach($advisors as $referrer)
                            <option value="{{ $referrer->id }}" {{ old('referrer_id', $student->referrer_id ?? '') == $referrer->id ? 'selected' : '' }}>
                                {{ $referrer->name }} - {{ $referrer->phone }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">شماره ثابت</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                        @error('home_phone')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ تولد</label>
                        <input type="text" name="birthday" class="form-control" value="{{ old('birthday') }}" data-jdp>
                        @error('birthday')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">شماره موبایل پدر</label>
                        <input type="text" name="mobile_father" class="form-control" value="{{ old('father_mobile') }}">
                        @error('father_mobile')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">شماره موبایل مادر</label>
                        <input type="text" name="mobile_mother" class="form-control" value="{{ old('mother_mobile') }}">
                        @error('mother_mobile')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- استان و شهر --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="province_id" class="form-label">استان</label>
                        <select id="province" name="province" class="form-select">
                            <option value="">انتخاب استان</option>
                        </select>
                        @error('province')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="city" class="form-label">شهرستان</label>
                        <select name="city" id="city" class="form-select">
                            <option value="">ابتدا استان را انتخاب کنید</option>
                        </select>
                        @error('city')
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
                    <textarea name="notes" class="form-control" rows="2">{{ old('description') }}</textarea>
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
<script src="{{asset('assets/js/data-picker.js')}}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let provinces = [];
        let cities = [];

        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');

        // لود استان‌ها
        fetch('/assets/js/provinces.json')
            .then(response => response.json())
            .then(data => {
                provinces = data;
                provinces.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.name; // ✅ مقدار برابر با نام استان
                    option.textContent = province.name;
                    option.dataset.id = province.id; // در صورت نیاز برای فیلتر شهرها
                    provinceSelect.appendChild(option);
                });
            })
            .catch(() => {
                provinceSelect.innerHTML = '<option>خطا در بارگذاری استان‌ها</option>';
            });

        // لود شهرها
        fetch('/assets/js/cities.json')
            .then(response => response.json())
            .then(data => {
                cities = data;
            })
            .catch(() => {
                citySelect.innerHTML = '<option>خطا در بارگذاری شهرها</option>';
            });

        // تغییر استان
        provinceSelect.addEventListener('change', function() {
            const selectedProvinceName = this.value;
            const selectedProvince = provinces.find(p => p.name === selectedProvinceName);
            citySelect.innerHTML = ''; // پاک کردن لیست قبلی

            if (selectedProvince) {
                const filteredCities = cities.filter(city => city.province_id == selectedProvince.id);

                if (filteredCities.length > 0) {
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'انتخاب کنید';
                    citySelect.appendChild(defaultOption);

                    filteredCities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.name; // ✅ مقدار برابر با نام شهر
                        option.textContent = city.name;
                        citySelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.textContent = 'هیچ شهری یافت نشد';
                    citySelect.appendChild(option);
                }
            } else {
                const option = document.createElement('option');
                option.textContent = 'ابتدا استان را انتخاب کنید';
                citySelect.appendChild(option);
            }
        });
    });
    jalaliDatepicker.startWatch();
</script>


@endsection