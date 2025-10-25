@extends('layouts.app')

@section('title', 'محصولات')

@section('content')
<div class=" mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h4 class="fw-bold fs18">لیست محصولات</h4>
        <a href="{{ route('products.create') }}" class="btn btn-success bg-admin-green">افزودن محصول جدید</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="table-wrap table-responsive">
        <table class="table">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>عنوان محصول</th>
                    <th>قیمت</th>
                    <th>درصد مالیات</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->title }}</td>
                    <td>{{ number_format($product->price) }}</td>
                    <td>{{ $product->tax_percent }}%</td>
                    <td>
                        <a href="{{ route('products.students', $product->id) }}" class="btn btn-sm btn-success bg-admin-green mt-1 mt-md-0">
                            دانش‌آموزان
                        </a>

                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('آیا مطمئن هستید؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-secondary   mt-1 mt-md-0">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">هیچ محصولی ثبت نشده است.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection