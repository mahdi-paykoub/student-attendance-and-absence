@extends('layouts.app')
@section('title','ویرایش مشاور')
@section('content')
<div class="mt-4">
    <h4 class="fw-bold fs18">ویرایش مشاور</h4>

    <div class="table-wrap mt-4">
        <form action="{{ route('advisors.update', $advisor->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">نام مشاور</label>
                <input type="text" name="name" class="form-control" 
                       value="{{ old('name', $advisor->name) }}">
                @error('name')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">شماره تماس</label>
                <input type="text" name="phone" class="form-control" 
                       value="{{ old('phone', $advisor->phone) }}">
                @error('phone')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-success bg-admin-green">بروزرسانی</button>
            <a href="{{ route('advisors.index') }}" class="btn btn-secondary">بازگشت</a>
        </form>
    </div>
</div>
@endsection
