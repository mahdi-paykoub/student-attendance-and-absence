@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 fw-bold fs18">تنظیمات محصول الزامی برای آزمون حضوری</h3>

    <div class="table-wrap">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('settings.updateExamProduct') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                @foreach($products as $product)
                <div class="form-check mt-3">
                    <input class="form-check-input" type="radio" name="product_id" id="product{{ $product->id }}" value="{{ $product->id }}"
                        {{ $mandatoryProductId == $product->id ? 'checked' : '' }}>
                    <label class="form-check-label" for="product{{ $product->id }}">
                        {{ $product->title }} - {{ number_format($product->price) }} تومان
                    </label>
                </div>
                @endforeach
            </div>

            <div class="text-start">
                <button type="submit" class="btn btn-success bg-admin-green">ذخیره تنظیمات</button>
            </div>
        </form>
    </div>
</div>
@endsection