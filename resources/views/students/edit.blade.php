@extends('layouts.app')

@section('title', 'ویرایش اطلاعات دانش‌آموز')

@section('content')
<div class=" mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-admin-green text-white">
            <h5 class="mb-0">ویرایش اطلاعات دانش‌آموز</h5>
        </div>
        <div class="card-body">

            {{-- پیام خطا --}}
            @if ($errors->any())
            <div class="alert alert-danger">
                لطفا خطاهای زیر را بررسی کنید:
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-lg-6">
                        {{-- عکس --}}
                        <div class="mb-3">
                            <label class="form-label">عکس 3x4</label>
                            @if($student->photo)
                            <div class="mb-2">
                                <img src="{{ route('students.photo', basename($student->photo)) }}"
                                    alt="عکس دانش‌آموز"
                                    width="100"
                                    class="rounded">
                            </div>
                            @endif

                            <input type="file" name="photo" class="form-control">
                            @error('photo')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror

                        </div>



                    </div>
                    <div class="col-lg-6">
                        {{-- عکس 2 --}}
                        <div class="mb-3">
                            <label class="form-label"> تصویر</label>

                            @if($student->photo_2)
                            <div class="mb-2">
                                <img src="{{ route('students.photo', basename($student->photo_2)) }}"
                                    alt="عکس دوم دانش‌آموز"
                                    width="100"
                                    class="rounded">
                            </div>
                            @endif

                            <input type="file" name="photo_2" class="form-control">

                            @error('photo_2')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- نام و نام خانوادگی --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نام</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $student->first_name) }}" required>
                        @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">نام خانوادگی</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $student->last_name) }}" required>
                        @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">نام پدر</label>
                        <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $student->father_name) }}">
                        @error('father_name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                {{-- جنسیت و کد ملی و موبایل --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">جنسیت</label>
                        <select name="gender" class="form-select" required>
                            <option value="">انتخاب کنید</option>
                            <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>پسر</option>
                            <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>دختر</option>
                        </select>
                        @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">کد ملی</label>
                        <input type="text" name="national_code" class="form-control" value="{{ old('national_code', $student->national_code) }}" required>
                        @error('national_code') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">شماره موبایل دانش‌آموز</label>
                        <input type="text" name="mobile_student" class="form-control" value="{{ old('mobile_student', $student->mobile_student) }}" required>
                        @error('mobile_student') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                {{-- سلکت باکس‌ها --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">پایه تحصیلی</label>
                        <select name="grade_id" class="form-select">
                            <option value="">انتخاب کنید...</option>
                            @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ old('grade_id', $student->grade_id) == $grade->id ? 'selected' : '' }}>
                                {{ $grade->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('grade_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">رشته تحصیلی</label>
                        <select name="major_id" class="form-select">
                            <option value="">انتخاب کنید...</option>
                            @foreach($majors as $major)
                            <option value="{{ $major->id }}" {{ old('major_id', $student->major_id) == $major->id ? 'selected' : '' }}>
                                {{ $major->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('major_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">نام مدرسه</label>
                        <select name="school_id" class="form-select">
                            <option value="">انتخاب کنید...</option>
                            @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id', $student->school_id) == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('school_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>



                {{-- بقیه فیلدها --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مشاور</label>
                        <select name="advisor_id" class="form-select">
                            <option value="">انتخاب مشاور</option>
                            @foreach($advisors as $advisor)
                            <option value="{{ $advisor->id }}" {{ old('advisor_id', $student->advisor_id) == $advisor->id ? 'selected' : '' }}>
                                {{ $advisor->name }} - {{ $advisor->phone }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">معرف</label>
                        <select name="referrer_id" class="form-select">
                            <option value="">انتخاب معرف</option>
                            @foreach($advisors as $referrer)
                            <option value="{{ $referrer->id }}" {{ old('referrer_id', $student->referrer_id) == $referrer->id ? 'selected' : '' }}>
                                {{ $referrer->name }} - {{ $referrer->phone }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">شماره ثابت</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone) }}">
                        @error('home_phone') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ تولد</label>
                        <input type="text" name="birthday" class="form-control" value="{{ old('birthday', $birthdayShamsi) }}">
                        @error('birthday') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">شماره موبایل پدر</label>
                        <input type="text" name="mobile_father" class="form-control" value="{{ old('mobile_father', $student->mobile_father) }}">
                        @error('father_mobile') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">شماره موبایل مادر</label>
                        <input type="text" name="mobile_mother" class="form-control" value="{{ old('mobile_mother', $student->mobile_mother) }}">
                        @error('mother_mobile') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                {{-- استان و شهر --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="province" class="form-label">استان</label>
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
                    <textarea name="address" class="form-control" rows="2">{{ old('address', $student->address) }}</textarea>
                    @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">توضیحات</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $student->notes) }}</textarea>
                    @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <button type="submit" class="btn btn-success bg-admin-green">ذخیره تغییرات</button>
                <a href="{{ route('students.index') }}" class="btn btn-secondary">بازگشت</a>
            </form>

        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let provinces = [];
        let cities = [];

        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');

        // 🟢 مقدارهای قبلی (old یا student)
        const selectedProvince = "{{ old('province', $student->province ?? '') }}";
        const selectedCity = "{{ old('city', $student->city ?? '') }}";

        // 🟢 لود استان‌ها
        fetch('/assets/js/provinces.json')
            .then(response => response.json())
            .then(data => {
                provinces = data;

                provinces.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.name;
                    option.textContent = province.name;
                    option.dataset.id = province.id;

                    if (province.name === selectedProvince) {
                        option.selected = true;
                    }

                    provinceSelect.appendChild(option);
                });

                // 🟢 بعد از لود استان‌ها، شهرها رو لود کن
                return fetch('/assets/js/cities.json');
            })
            .then(response => response.json())
            .then(data => {
                cities = data;

                // اگر استانی انتخاب شده (از old یا student)
                if (selectedProvince) {
                    const province = provinces.find(p => p.name === selectedProvince);

                    if (province) {
                        const filteredCities = cities.filter(c => c.province_id == province.id);
                        citySelect.innerHTML = '<option value="">انتخاب کنید</option>';

                        filteredCities.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.name;
                            option.textContent = city.name;
                            if (city.name === selectedCity) {
                                option.selected = true; // انتخاب شهر قبلی
                            }
                            citySelect.appendChild(option);
                        });
                    }
                }
            })
            .catch(() => {
                provinceSelect.innerHTML = '<option>خطا در بارگذاری استان‌ها</option>';
            });

        // 🟢 وقتی استان تغییر کرد، شهرها رو آپدیت کن
        provinceSelect.addEventListener('change', function() {
            const selectedProvinceName = this.value;
            const province = provinces.find(p => p.name === selectedProvinceName);
            citySelect.innerHTML = '';

            if (province) {
                const filteredCities = cities.filter(c => c.province_id == province.id);
                citySelect.innerHTML = '<option value="">انتخاب کنید</option>';
                filteredCities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.name;
                    option.textContent = city.name;
                    citySelect.appendChild(option);
                });
            } else {
                citySelect.innerHTML = '<option>ابتدا استان را انتخاب کنید</option>';
            }
        });
    });
</script>

@endsection