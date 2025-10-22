@extends('layouts.app')
@section('title','افزودن مشاور')
@section('content')
<div class=" mt-4">
    <h4 class="fw-bold fs18">افزودن مشاور جدید</h4>

    <div class="table-wrap mt-4">
        <form action="{{ route('advisors.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">نام مشاور</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                @error('name')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">شماره تماس</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                @error('phone')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-success bg-admin-green">ثبت</button>
        </form>
    </div>
</div>
@endsection