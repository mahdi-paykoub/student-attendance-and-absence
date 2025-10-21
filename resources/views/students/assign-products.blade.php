@extends('layouts.app')
@section('styles')
<link rel="stylesheet" href="{{asset('assets/css/data-picker.css')}}">
@endsection
@section('content')
<div class="container mt-4">

    <h4 class="mb-4 fw-bold fs18">تخصیص محصول به: <span class="text-success">{{ $student->first_name }} {{ $student->last_name }}</span></h4>


    <div class="table-wrap">
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="mb-3 fw-bold">اطلاعات دانش‌آموز</h6>
                <div class="row">
                    <div class="col-md-4 mt-3"><strong>کد ملی:</strong> {{ $student->national_code }}</div>
                    <div class="col-md-4 mt-3"><strong>شماره موبایل:</strong> {{ $student->phone }}</div>
                    <div class="col-md-4 mt-3"><strong>نام پدر:</strong> {{ $student->father_name }}</div>
                    <div class="col-md-4 mt-3"><strong>پایه:</strong> {{$grade}}</div>
                    <div class="col-md-4 mt-3"><strong>رشته:</strong> {{$major}}</div>
                </div>
            </div>
        </div>


        <form method="POST" action="{{ route('student-products.storeAssign', $student->id) }}" enctype="multipart/form-data">
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
                                $finalPrice = $product->price + ($product->price * ($product->tax_percent / 100));
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
                                            {{ $product->title }}
                                            <small class="text-muted d-block">قیمت نهایی: {{ number_format($finalPrice) }} تومان</small>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- محصولات انتخاب‌شده --}}
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">محصولات انتخاب‌شده</div>
                        <div class="card-body">
                            <ul id="selectedProducts" class="list-group mb-3"></ul>
                            <h6 class="text-end">جمع کل: <span id="totalPrice">0</span> تومان</h6>
                        </div>
                    </div>
                </div>
            </div>

            {{-- روش پرداخت --}}
            <div class="card mt-4">
                <div class="card-header">روش پرداخت</div>
                <div class="card-body">
                    <div class="mb-3">
                        <select name="payment_type" id="payment_type" class="form-control">
                            <option value="cash">نقدی</option>
                            <option value="installment">اقساط</option>
                            <option value="scholarship">بورسیه</option>
                        </select>
                    </div>
                    <div id="payment-fields"></div>
                </div>
            </div>

            <button type="submit" class="btn btn-success bg-admin-green w-100 mt-3">ثبت تخصیص</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/js/data-picker.js')}}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        jalaliDatepicker.startWatch();
        // ---------- محصولات ----------
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

        // ---------- روش پرداخت ----------
        const paymentType = document.getElementById('payment_type');
        const paymentFields = document.getElementById('payment-fields');
        const paymentCards = @json($paymentCards); // آرایه کارت‌های پرداخت از کنترلر

        function createCashFields() {
            paymentFields.innerHTML = `
            <h6 class="fw-bold mt-3">پرداخت نقدی</h6>
            <button type="button" id="add-cash" class="btn btn-success bg-admin-green btn-sm mb-3 mt-3">افزودن پرداخت</button>
            <div id="cash-container"></div>
        `;
            document.getElementById('add-cash').addEventListener('click', () => {
                const html = `<div class="row mb-2 border p-2 rounded">
                                    <div class="col-4 mt-3">
                                        <input type="text" data-jdp class="form-control" name="cash_date[]" required>
                                    </div>
                                    <div class="col-4 mt-3">
                                        <input type="time" name="cash_time[]" class="form-control" required>
                                    </div>
                                    <div class="col-4 mt-3">
                                        <input type="number" name="cash_amount[]" class="form-control" placeholder="مبلغ" required>
                                    </div>
                                    <div class="col-4 mt-3">
                                        <input type="text" name="cash_voucher[]" class="form-control" placeholder="شماره فیش">
                                    </div>
                                    <div class="col-4 mt-3">
                                        <select name="cash_card[]" class="form-control">
                                            ${paymentCards.map(c=>`<option value="${c.id}">${c.name}</option>`).join('')}
                                        </select>
                                    </div>
                                    <div class="col-4 mt-3">
                                        <input type="file" name="cash_image[]" class="form-control">
                                    </div>
                                </div>`;
                document.getElementById('cash-container').insertAdjacentHTML('beforeend', html);
            });
        }

        function createInstallmentFields() {
            paymentFields.innerHTML = `
            <h6 class="fw-bold mt-3">پرداخت اقساط</h6>
            <button type="button" id="add-prepayment" class="btn btn-sm bg-admin-green btn-success mb-3 mt-3">افزودن پیش پرداخت</button>
            <button type="button" id="add-check" class="btn btn-sm bg-admin-green btn-success mb-3 mt-3">افزودن چک</button>
            <div id="prepayment-container"></div>
            <div id="check-container"></div>
        `;

            // پیش پرداخت
            document.getElementById('add-prepayment').addEventListener('click', () => {
                const html = `<div class="row mb-2 border p-2 rounded">
                                <div class="col-4 mt-3">
                                    <input type="text" data-jdp name="pre_date[]" class="form-control" required>
                                </div>
                                <div class="col-4 mt-3">
                                    <input type="time" name="pre_time[]" class="form-control" required>
                                </div>
                                <div class="col-4 mt-3">
                                    <input type="number" name="pre_amount[]" class="form-control" placeholder="مبلغ" required>
                                </div>
                                <div class="col-4 mt-3">
                                    <input type="text" name="pre_voucher[]" class="form-control" placeholder="شماره فیش">
                                </div>
                                <div class="col-4 mt-3">
                                    <select name="pre_card[]" class="form-control">
                                        ${paymentCards.map(c=>`<option value="${c.id}">${c.name}</option>`).join('')}
                                    </select>
                                </div>
                                <div class="col-4 mt-3">
                                    <input type="file" name="pre_image[]" class="form-control">
                                </div>
                            </div>`;
                document.getElementById('prepayment-container').insertAdjacentHTML('beforeend', html);
            });

            // چک
            document.getElementById('add-check').addEventListener('click', () => {
                const html = `<div class="row mb-2 border p-2 rounded">
                                    <div class="col-4 mt-3">
                                        <input type="text" data-jdp name="check_date[]" class="form-control" required>
                                    </div>
                                    <div class="col-4 mt-3">
                                        <input type="number" name="check_amount[]" class="form-control" placeholder="مبلغ" required>
                                    </div>
                                    <div class="col-4 mt-3">
                                        <input type="text" name="check_serial[]" class="form-control" placeholder="سریال چک" required>
                                    </div>
                                    <div class="col-4 mt-3">
                                        <input type="text" name="check_sayad[]" class="form-control" placeholder="کد صیاد" required>
                                    </div>
                                    <div class="col-4 mt-3">
                                        <input type="text" name="check_owner[]" class="form-control" placeholder="نام صاحب چک" required>
                                    </div>
                                    <div class="col-4 mt-3">
                                        <input type="text" name="check_national[]" class="form-control" placeholder="کد ملی صاحب چک" required>
                                    </div>
                                    <div class="col-4 mt-3">
                                        <input type="text" name="check_phone[]" class="form-control" placeholder="موبایل صاحب چک" required>
                                    </div>
                                    <div class="col-4 mt-3">
                                        <input type="file" name="check_image[]" class="form-control">
                                    </div>
                                </div>`;
                document.getElementById('check-container').insertAdjacentHTML('beforeend', html);
            });
        }

        function createFields() {
            if (paymentType.value === 'cash') createCashFields();
            else if (paymentType.value === 'installment') createInstallmentFields();
            else paymentFields.innerHTML = ''; // بورسیه
        }

        paymentType.addEventListener('change', createFields);
        createFields();
    });
</script>
@endsection