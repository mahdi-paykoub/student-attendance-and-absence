@extends('layouts.app')

@section('title', 'ویرایش رشته')

@section('content')
<div class="card">
    <div class="card-header bg-admin-green text-white">ویرایش رشته تحصیلی</div>
    <div class="card-body">

        <form action="{{ route('majors.update', $major->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">نام رشته</label>
                <input type="text" id="name" name="name" class="form-control" 
                       value="{{ old('name', $major->name) }}" placeholder="مثلاً ریاضی فیزیک" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-success bg-admin-green btn-sm">بروزرسانی</button>
            <a href="{{ route('majors.index') }}" class="btn btn-secondary btn-sm">بازگشت</a>
        </form>

    </div>
</div>
@endsection
