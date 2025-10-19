@extends('layouts.app')

@section('title', 'تخصیص محصول به دانش‌آموز')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm p-3">

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('student-products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- دانش‌آموز --}}
            <div class="mb-3">
                <label class="form-label">دانش‌آموز</label>
                <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                    <option value="">انتخاب کنید...</option>
                    @foreach($students as $student)
                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                        {{ $student->first_name }} {{ $student->last_name }} - {{ $student->national_code }}
                    </option>
                    @endforeach
                </select>
                @error('student_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- محصول --}}
            <div class="mb-3">
                <label class="form-label">محصول</label>
                <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                    <option value="">انتخاب کنید...</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->title }}
                    </option>
                    @endforeach
                </select>
                @error('product_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- نوع پرداخت --}}
            <div class="mb-3">
                <label class="form-label">نوع پرداخت</label>
                <select name="payment_type" id="payment_type" class="form-select @error('payment_type') is-invalid @enderror" required>
                    <option value="">انتخاب کنید...</option>
                    <option value="cash" {{ old('payment_type')=='cash' ? 'selected' : '' }}>نقدی</option>
                    <option value="installment" {{ old('payment_type')=='installment' ? 'selected' : '' }}>اقساطی</option>
                </select>
                @error('payment_type')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- بخش نقدی --}}
            <div id="cash_fields" style="display:none;">
                <div class="mb-3">
                    <label class="form-label">نوع دستگاه پوز</label>
                    <input type="text" name="pos_type" value="{{ old('pos_type') }}" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">نوع کارت</label>
                    <input type="text" name="card_type" value="{{ old('card_type') }}" class="form-control">
                </div>
            </div>

            {{-- بخش اقساطی --}}
            <div id="installment_fields" style="display:none;">
                <div id="checks_container">
                    {{-- چک‌ها اضافه می‌شوند --}}
                </div>
                <button type="button" class="btn btn-secondary mb-2" id="add_check">افزودن چک</button>
            </div>

            <button type="submit" class="btn btn-success bg-admin-green">تخصیص محصول</button>
        </form>

    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentType = document.getElementById('payment_type');
        const cashFields = document.getElementById('cash_fields');
        const installmentFields = document.getElementById('installment_fields');
        const addCheckBtn = document.getElementById('add_check');
        const checksContainer = document.getElementById('checks_container');

        function togglePaymentFields() {
            if (paymentType.value === 'cash') {
                cashFields.style.display = 'block';
                installmentFields.style.display = 'none';
            } else if (paymentType.value === 'installment') {
                cashFields.style.display = 'none';
                installmentFields.style.display = 'block';
            } else {
                cashFields.style.display = 'none';
                installmentFields.style.display = 'none';
            }
        }

        paymentType.addEventListener('change', togglePaymentFields);
        togglePaymentFields();

        let checkIndex = 0;
        addCheckBtn.addEventListener('click', function() {
            const html = `
            <div class="border p-3 mb-2">
                <div class="mb-2">
                    <label>نام صاحب چک</label>
                    <input type="text" name="checks[${checkIndex}][owner]" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>شماره موبایل صاحب چک</label>
                    <input type="text" name="checks[${checkIndex}][phone]" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>عکس چک</label>
                    <input type="file" name="checks[${checkIndex}][image]" class="form-control">
                </div>
            </div>
        `;
            checksContainer.insertAdjacentHTML('beforeend', html);
            checkIndex++;
        });
    });
</script>
@endsection