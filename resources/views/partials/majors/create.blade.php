@extends('layouts.app')

@section('title', 'افزودن رشته جدید')

@section('content')
<div class="card">
    <div class="card-header bg-admin-green text-white">افزودن رشته تحصیلی</div>
    <div class="card-body">

        <form action="{{ route('majors.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">نام رشته</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="مثلاً ریاضی فیزیک" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-success bg-admin-green btn-sm">ذخیره</button>
            <a href="{{ route('majors.index') }}" class="btn btn-secondary btn-sm">بازگشت</a>
        </form>

    </div>
</div>
@endsection
