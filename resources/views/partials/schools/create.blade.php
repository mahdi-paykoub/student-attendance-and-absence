@extends('layouts.app')

@section('title', 'افزودن مدرسه جدید')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">افزودن مدرسه</div>
    <div class="card-body">

        <form action="{{ route('schools.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">نام مدرسه</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="مثلاً مدرسه امام رضا" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-success">ذخیره</button>
            <a href="{{ route('schools.index') }}" class="btn btn-secondary">بازگشت</a>
        </form>

    </div>
</div>
@endsection
