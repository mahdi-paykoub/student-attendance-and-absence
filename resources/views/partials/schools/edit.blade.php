@extends('layouts.app')

@section('title', 'ویرایش مدرسه')

@section('content')
<div class="card">
    <div class="card-header bg-admin-green text-white">ویرایش مدرسه</div>
    <div class="card-body">

        <form action="{{ route('schools.update', $school->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">نام مدرسه</label>
                <input type="text" id="name" name="name" class="form-control" 
                       value="{{ old('name', $school->name) }}" placeholder="مثلاً مدرسه امام رضا" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-success bg-admin-green btn-sm">بروزرسانی</button>
            <a href="{{ route('schools.index') }}" class="btn btn-secondary btn-sm">بازگشت</a>
        </form>

    </div>
</div>
@endsection
