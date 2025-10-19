@extends('layouts.app')

@section('title', 'افزودن آزمون')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-admin-green text-white">
            <h5 class="mb-0">افزودن آزمون جدید</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('exams.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">نام آزمون</label>
                    <input type="text" name="name" class="form-control" required>
                    @error('name')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-success bg-admin-green">ثبت آزمون</button>
            </form>
        </div>
    </div>
</div>
@endsection
