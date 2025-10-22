@extends('layouts.app')

@section('title', 'افزودن محصول جدید')

@section('content')
<div class=" mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-admin-green text-white">
            افزودن محصول جدید
        </div>
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">عنوان محصول</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">قیمت</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price') }}" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">درصد مالیات</label>
                    <input type="number" name="tax_percent" class="form-control" value="{{ old('tax_percent', 0) }}" step="0.01" min="0" max="100" required>
                </div>
                <button type="submit" class="btn btn-success bg-admin-green">ثبت محصول</button>
            </form>

        </div>
    </div>
</div>
@endsection
