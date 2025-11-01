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
                    <th>درصد آبونمان</th>
                    <th> قیمت کل</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="{{ !$product->is_active ? 'table-secondary' : '' }}">
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->title }}</td>
                    <td>{{ number_format($product->price) }}</td>
                    <td>{{ $product->tax_percent }}%</td>
                    <td>{{ number_format(($product->price * $product->tax_percent / 100) +$product->price )  }}</td>
                    <td>
                        <a href="{{ route('products.students', $product->id) }}" class="btn btn-sm btn-success bg-admin-green mt-1 mt-md-0">
                            دانش‌آموزان
                        </a>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-success bg-admin-green mt-1 mt-md-0">
                            ویرایش
                        </a>

                        <button class="btn btn-sm btn-dark btn-toggle-status mt-1 mt-md-0" data-id="{{ $product->id }}">
                            {{ $product->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}
                        </button>


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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.btn-toggle-status');

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = button.getAttribute('data-id');

                fetch(`/products/${productId}/toggle`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // تغییر متن و کلاس دکمه
                            if (data.is_active) {
                                button.textContent = 'غیرفعال کردن';
                                button.classList.remove('btn-danger');
                                button.classList.add('btn-success');
                                button.closest('tr').classList.remove('table-secondary');
                            } else {
                                button.textContent = 'فعال کردن';
                                button.classList.remove('btn-success');
                                button.classList.add('btn-danger');
                                button.closest('tr').classList.add('table-secondary');
                            }

                            // نمایش پیام با SweetAlert2
                            Swal.fire({
                                icon: 'success',
                                title: 'موفقیت!',
                                text: `محصول اکنون ${data.is_active ? 'فعال' : 'غیرفعال'} است.`,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'خطا!',
                            text: 'تغییر وضعیت انجام نشد.'
                        });
                    });
            });
        });
    });
</script>

@endsection