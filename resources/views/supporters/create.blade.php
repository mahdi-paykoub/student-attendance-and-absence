@extends('layouts.app')

@section('title', 'ایجاد پشتبان جدید')

@section('content')
<div class="container mt-4">

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h4 class="fw-bold fs18">ایجاد پشتبان جدید</h4>

    <div class="table-wrap">
        <form action="{{ route('supporters.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">نام</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}">
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>



            <div class="mb-3">
                <label for="phone" class="form-label">شماره تماس</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                @error('phone') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-success bg-admin-green">ثبت پشتبان</button>
        </form>
    </div>
</div>
@endsection