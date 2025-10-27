@extends('layouts.app')
@section('styles')
<link rel="stylesheet" href="{{asset('assets/css/data-picker.css')}}">
@endsection
@section('content')
<div class=" mt-4">

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


        <h3>تخصیص محصولات به {{ $student->name }}</h3>

        <form action="{{ route('student-products.storeAssign.product', $student->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                @foreach($products as $product)
                @php
                $assigned = $assignedProducts->contains($product->id);
                $finalPrice = $product->price + ($product->price * $product->tax_percent / 100);
                @endphp

                <div class="col-md-6 mb-2">
                    <div class="form-check border p-2 rounded {{ $assigned ? 'bg-light' : '' }}">
                        <input type="checkbox" id="id-{{$product->id}}"
                            name="products[]"
                            value="{{ $product->id }}"
                            class="product-checkbox form-check-input"
                            data-price="{{ $product->price }}"
                            data-tax="{{ $product->tax_percent }}"
                            {{ $assigned ? 'checked' : '' }}>
                        <label class="form-check-label" for="id-{{$product->id}}">
                            {{ $product->title }} - {{ $finalPrice }} تومان
                            <!-- (مالیات: {{ $product->tax_percent }}%) -->
                        </label>
                    </div>
                </div>
                @endforeach
            </div>

            <h4>هزینه نهایی: <span id="totalPrice">0</span> تومان</h4>

            <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
        </form>







        <br><br><br><br><br><br>
        <div class="mb-3">
            <label for="payment_type" class="form-label">نوع پرداخت</label>
            <select id="payment_type" class="form-select">
                <option value="">انتخاب کنید</option>
                <option value="cash">نقدی</option>
                <option value="installment">اقساطی</option>
                <option value="scholarship">بورسیه</option>
            </select>
        </div>

        <div class="mb-3" id="payment_buttons"></div>

        <div id="payments_container"></div>
        <div id="checks_container"></div>




      <form action="{{ route('student-products.storePayments', $student->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <h4>پرداخت‌ها</h4>

    <div id="payments_container">
        @foreach($existingPayments as $payment)
        <div class="payment-item border p-3 mb-2 position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 remove-btn"></button>

            <input type="hidden" name="payments[{{ $loop->index }}][id]" value="{{ $payment->id }}">

            <div class="mb-2">
                <label>تاریخ</label>
                <input type="date" name="payments[{{ $loop->index }}][date]" class="form-control" value="{{ $payment->date->format('Y-m-d') }}" required>
            </div>
            <div class="mb-2">
                <label>مبلغ</label>
                <input type="number" name="payments[{{ $loop->index }}][amount]" class="form-control" value="{{ $payment->amount }}" required>
            </div>
            <div class="mb-2">
                <label>شماره فیش</label>
                <input type="text" name="payments[{{ $loop->index }}][ref]" class="form-control" value="{{ $payment->ref }}">
            </div>
            <div class="mb-2">
                <label>پوز</label>
                <select name="payments[{{ $loop->index }}][card_id]" class="form-select">
                    @foreach($paymentCards as $card)
                        <option value="{{ $card->id }}" {{ $payment->card_id == $card->id ? 'selected' : '' }}>{{ $card->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <label>تصویر پرداخت</label>
                <input type="file" name="payments[{{ $loop->index }}][image]" class="form-control">
                @if($payment->image)
                    <small>تصویر قبلی: {{ basename($payment->image) }}</small>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div id="checks_container">
        @foreach($existingChecks as $check)
        <div class="check-item border p-3 mb-2 position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 remove-btn"></button>

            <input type="hidden" name="checks[{{ $loop->index }}][id]" value="{{ $check->id }}">

            <div class="mb-2"><label>تاریخ</label><input type="date" name="checks[{{ $loop->index }}][date]" class="form-control" value="{{ $check->date->format('Y-m-d') }}" required></div>
            <div class="mb-2"><label>مبلغ</label><input type="number" name="checks[{{ $loop->index }}][amount]" class="form-control" value="{{ $check->amount }}" required></div>
            <div class="mb-2"><label>سریال چک</label><input type="text" name="checks[{{ $loop->index }}][serial]" class="form-control" value="{{ $check->serial }}"></div>
            <div class="mb-2"><label>کد صیاد</label><input type="text" name="checks[{{ $loop->index }}][code]" class="form-control" value="{{ $check->code }}"></div>
            <div class="mb-2"><label>نام صاحب چک</label><input type="text" name="checks[{{ $loop->index }}][owner_name]" class="form-control" value="{{ $check->owner_name }}"></div>
            <div class="mb-2"><label>کد ملی صاحب چک</label><input type="text" name="checks[{{ $loop->index }}][owner_national]" class="form-control" value="{{ $check->owner_national }}"></div>
            <div class="mb-2"><label>موبایل صاحب چک</label><input type="text" name="checks[{{ $loop->index }}][owner_phone]" class="form-control" value="{{ $check->owner_phone }}"></div>
            <div class="mb-2"><label>تصویر چک</label><input type="file" name="checks[{{ $loop->index }}][image]" class="form-control">
                @if($check->image)
                    <small>تصویر قبلی: {{ basename($check->image) }}</small>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <button type="submit" class="btn btn-success mt-3">ذخیره تغییرات</button>
</form>


    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/js/data-picker.js')}}"></script>

<script>
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const totalPriceEl = document.getElementById('totalPrice');

    function calculateTotal() {
        let total = 0;
        checkboxes.forEach(chk => {
            if (chk.checked) {
                let price = parseFloat(chk.dataset.price);
                let tax = parseFloat(chk.dataset.tax);
                total += price + (price * tax / 100);
            }
        });
        totalPriceEl.textContent = total.toLocaleString();
    }

    checkboxes.forEach(chk => chk.addEventListener('change', calculateTotal));
    calculateTotal(); // وقتی صفحه لود شد هم محاسبه شود
</script>


<script>
    const paymentTypeSelect = document.getElementById('payment_type');
    const paymentButtons = document.getElementById('payment_buttons');
    const paymentsContainer = document.getElementById('payments_container');
    const checksContainer = document.getElementById('checks_container');

    paymentTypeSelect.addEventListener('change', function() {
        paymentButtons.innerHTML = '';
        paymentsContainer.innerHTML = '';
        checksContainer.innerHTML = '';

        if (this.value === 'cash') {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-success mb-2';
            btn.textContent = 'افزودن پرداخت';
            btn.addEventListener('click', addPayment);
            paymentButtons.appendChild(btn);
        }
        if (this.value === 'installment') {
            const btn1 = document.createElement('button');
            btn1.type = 'button';
            btn1.className = 'btn btn-primary me-2';
            btn1.textContent = 'افزودن پیشپرداخت';
            btn1.addEventListener('click', addPayment);
            const btn2 = document.createElement('button');
            btn2.type = 'button';
            btn2.className = 'btn btn-warning';
            btn2.textContent = 'افزودن چک';
            btn2.addEventListener('click', addCheck);
            paymentButtons.appendChild(btn1);
            paymentButtons.appendChild(btn2);
        }
    });

    function addPayment() {
        const template = document.getElementById('payment_template');
        const clone = template.content.cloneNode(true);
        clone.querySelector('.remove-btn').addEventListener('click', function() {
            this.parentElement.remove();
        });
        paymentsContainer.appendChild(clone);
    }

    function addCheck() {
        const template = document.getElementById('check_template');
        const clone = template.content.cloneNode(true);
        clone.querySelector('.remove-btn').addEventListener('click', function() {
            this.parentElement.remove();
        });
        checksContainer.appendChild(clone);
    }

    // ✅ اگر دیتای قبلی داشتیم، میتونیم اینجا با JS اضافه کنیم
    // existingPayments و existingChecks از Controller فرستاده میشن
</script>
@endsection