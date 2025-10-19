@extends('layouts.app')

@section('title', 'افزودن استان جدید')

@section('content')
<div class="card">
    <div class="card-header bg-admin-green text-white">افزودن استان</div>
    <div class="card-body">

        <form action="{{ route('provinces.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">نام استان</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="مثلاً خراسان رضوی" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-success bg-admin-green btn-sm">ذخیره</button>
            <a href="{{ route('provinces.index') }}" class="btn btn-secondary btn-sm">بازگشت</a>
        </form>

    </div>
</div>
@endsection
