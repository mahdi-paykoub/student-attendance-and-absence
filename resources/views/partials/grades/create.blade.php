@extends('layouts.app')

@section('title', 'افزودن پایه جدید')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">افزودن پایه تحصیلی</div>
    <div class="card-body">

        <form action="{{ route('grades.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">نام پایه</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="مثلاً پایه دهم" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-success">ذخیره</button>
            <a href="{{ route('grades.index') }}" class="btn btn-secondary">بازگشت</a>
        </form>

    </div>
</div>
@endsection
