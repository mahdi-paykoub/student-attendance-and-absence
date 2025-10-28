@extends('layouts.app')

@section('title', 'ویرایش پایه')

@section('content')
<div class="card">
    <div class="card-header bg-admin-green text-white">ویرایش پایه تحصیلی</div>
    <div class="card-body">

        <form action="{{ route('grades.update', $grade->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">نام پایه</label>
                <input type="text" id="name" name="name" class="form-control" 
                       value="{{ old('name', $grade->name) }}" placeholder="مثلاً پایه دهم" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-success bg-admin-green btn-sm">بروزرسانی</button>
            <a href="{{ route('grades.index') }}" class="btn btn-secondary btn-sm">بازگشت</a>
        </form>

    </div>
</div>
@endsection
