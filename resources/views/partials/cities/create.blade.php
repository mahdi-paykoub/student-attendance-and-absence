@extends('layouts.app')

@section('title', 'افزودن شهرستان جدید')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">افزودن شهرستان</div>
    <div class="card-body">

        <form action="{{ route('cities.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">نام شهرستان</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="مثلاً مشهد" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="province_id" class="form-label">استان مربوطه</label>
                <select id="province_id" name="province_id" class="form-select" required>
                    <option value="">انتخاب کنید</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                    @endforeach
                </select>
                @error('province_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-success">ذخیره</button>
            <a href="{{ route('cities.index') }}" class="btn btn-secondary">بازگشت</a>
        </form>

    </div>
</div>
@endsection
