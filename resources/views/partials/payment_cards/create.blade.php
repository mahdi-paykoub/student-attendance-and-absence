@extends('layouts.app')

@section('title', 'افزودن کارت جدید')

@section('content')
<div class="card">
    <div class="card-header bg-admin-green text-white">افزودن کارت جدید</div>
    <div class="card-body">

        <div class="table-wrap">
            <form action="{{ route('payment-cards.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">نام حساب</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <button class="btn btn-success bg-admin-green">ذخیره</button>
            </form>
        </div>

    </div>
</div>
@endsection