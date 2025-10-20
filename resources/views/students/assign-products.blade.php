@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h4 class="mb-4">تخصیص محصول به {{ $student->first_name }} {{ $student->last_name }}</h4>

    {{-- اطلاعات دانش‌آموز --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="mb-3">اطلاعات دانش‌آموز</h6>
            <div class="row">
                <div class="col-md-4"><strong>کد ملی:</strong> {{ $student->national_code }}</div>
                <div class="col-md-4"><strong>شماره موبایل:</strong> {{ $student->phone }}</div>
                <div class="col-md-4"><strong>نام پدر:</strong> {{ $student->father_name }}</div>
                <div class="col-md-4"><strong>پایه:</strong> {{ $student->grade }}</div>
                <div class="col-md-4"><strong>رشته:</strong> {{ $student->major }}</div>
            </div>
        </div>
    </div>

    {{-- فرم انتخاب محصولات --}}
    <form method="POST" action="{{ route('students.assign-products.store', $student->id) }}">
        @csrf

        <div class="row">
            {{-- لیست محصولات --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">انتخاب محصولات</div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($products as $product)
                                @php
                                    $finalPrice = $product->price - ($product->price * ($product->tax_percent / 100));
                                @endphp
                                <div class="col-md-6 mb-2">
                                    <div class="form-check border p-2 rounded">
                                        <input type="checkbox" class="form-check-input product-checkbox"
                                               id="product_{{ $product->id }}"
                                               name="products[]"
                                               value="{{ $product->id }}"
                                               data-name="{{ $product->name }}"
                                               data-price="{{ $finalPrice }}">
                                        <label class="form-check-label" for="product_{{ $product->id }}">
                                            {{ $product->name }}  
                                            <small class="text-muted d-block">قیمت نهایی: {{ number_format($finalPrice) }} تومان</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- لیست انتخاب‌ها --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">محصولات انتخاب‌شده</div>
                    <div class="card-body">
                        <ul id="selectedProducts" class="list-group mb-3"></ul>
                        <h6 class="text-end">جمع کل: <span id="totalPrice">0</span> تومان</h6>
                        <button type="submit" class="btn btn-success w-100 mt-3">ثبت تخصیص</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- اسکریپت محاسبه لحظه‌ای --}}
<script>
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const selectedList = document.getElementById('selectedProducts');
    const totalPriceEl = document.getElementById('totalPrice');
    let total = 0;

    checkboxes.forEach(chk => {
        chk.addEventListener('change', () => {
            const price = parseFloat(chk.dataset.price);
            const name = chk.dataset.name;

            if (chk.checked) {
                const li = document.createElement('li');
                li.classList.add('list-group-item');
                li.setAttribute('data-id', chk.value);
                li.textContent = `${name} — ${price.toLocaleString()} تومان`;
                selectedList.appendChild(li);
                total += price;
            } else {
                const li = selectedList.querySelector(`[data-id="${chk.value}"]`);
                if (li) li.remove();
                total -= price;
            }

            totalPriceEl.textContent = total.toLocaleString();
        });
    });
</script>
@endsection
