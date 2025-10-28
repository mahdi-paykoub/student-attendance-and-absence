@extends('layouts.app')

@section('title', 'ویرایش محصول')

@section('content')
<div class=" mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-admin-green text-white">
            ویرایش محصول
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

            <form action="{{ route('products.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">عنوان محصول</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $product->title) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">قیمت</label>
                    <input type="text" name="price" class="form-control price-input" value="{{ old('price', $product->price) }}" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">درصد آبونمان</label>
                    <input type="number" name="tax_percent" class="form-control" value="{{ old('tax_percent', $product->tax_percent) }}" step="0.01" min="0" max="100" required>
                </div>

                {{-- پایه تحصیلی --}}
                <div class="mb-3">
                    <label class="form-label">پایه تحصیلی</label>
                    <select name="grade_id" class="form-select" required>
                        <option value="">انتخاب پایه...</option>
                        @foreach ($grades as $grade)
                        <option value="{{ $grade->id }}" {{ old('grade_id', $product->grade_id) == $grade->id ? 'selected' : '' }}>
                            {{ $grade->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- رشته تحصیلی --}}
                <div class="mb-3">
                    <label class="form-label">رشته تحصیلی</label>
                    <select name="major_id" class="form-select" required>
                        <option value="">انتخاب رشته...</option>
                        @foreach ($majors as $major)
                        <option value="{{ $major->id }}" {{ old('major_id', $product->major_id) == $major->id ? 'selected' : '' }}>
                            {{ $major->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-success bg-admin-green">بروزرسانی محصول</button>
            </form>

        </div>
    </div>
</div>
@endsection
