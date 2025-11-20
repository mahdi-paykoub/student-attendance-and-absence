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
                    <div class="col-md-4 mt-3"><strong>شماره همراه:</strong> {{$student->mobile_student}}</div>
                </div>
            </div>
        </div>


        <h3 class="fw-bold fs18">تخصیص محصولات : </h3>

        <form class="border-bottom pb-5" action="{{ route('student-products.storeAssign.product', $student->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                @foreach($products as $product)
                @php
                $assigned = $assignedProducts->contains($product->id);
                $finalPrice = $product->price + ($product->price * $product->tax_percent / 100);
                $isInactiveAssigned = !$product->is_active && $assigned;
                @endphp

                <div class="col-md-6 mb-2">
                    <div class="form-check border p-2 rounded {{ $isInactiveAssigned ? 'bg-secondary text-white' : ($assigned ? 'bg-light' : '') }}">
                        <input type="checkbox" id="id-{{$product->id}}"
                            name="products[]"
                            value="{{ $product->id }}"
                            class="product-checkbox form-check-input"
                            data-price="{{ $product->price }}"
                            data-tax="{{ $product->tax_percent }}"
                            {{ $assigned ? 'checked' : '' }}
                            {{ $isInactiveAssigned ? 'disabled' : '' }}>
                        <label class="form-check-label" for="id-{{$product->id}}">
                            {{ $product->title }} - {{ number_format($finalPrice) }} تومان
                            @if($isInactiveAssigned)
                            (غیرفعال)
                            @endif
                        </label>
                    </div>
                </div>
                @endforeach

            </div>

            <div class="row mt-5 align-items-center">
                <div class="col-lg-6">
                    <div>
                        <h4 class="fw-bold fs18">هزینه نهایی: <span id="totalPrice">0</span> تومان</h4>
                    </div>

                </div>
                <div class="col-lg-6 ">
                    <div class="d-flex align-items-center justify-content-end">
                        <input
                            type="number"
                            name="amount"
                            id="amount"
                            class="form-control w-25 "
                            placeholder="مقدار تخفیف"
                            value="{{optional($student->discounts->first())->amount ?? ''}}">
                        <button type="submit" class="btn btn-success bg-admin-green me-2">ذخیره تغییرات</button>
                    </div>
                </div>
            </div>


        </form>



        <h4 class="fw-bold fs18 mt-5">افزودن پرداخت : </h4>

        <form class="bg-body-secondary mt-4 p-4 rounded" action="{{ route('student-products.storePayments', $student->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- نوع پرداخت --}}
            <div class="mb-3 row align-items-center">
                <div class="col-lg-6">
                    <label class="form-label">نوع پرداخت:</label>
                    <select id="paymentType" name="payment_type" class="form-select w-100" required>
                        <option value="">انتخاب کنید...</option>
                        <option value="installment">اقساطی</option>
                        <option value="cash">نقدی</option>
                        <option value="scholarship">بورسیه</option>
                    </select>
                </div>
                <div class="col-lg-6 text-start">
                    <button type="submit" class="btn btn-success bg-admin-green mt-3">ذخیره پرداخت‌ها</button>
                </div>

            </div>

            {{-- دکمه‌های پرداخت نقدی --}}
            <div id="cashBtnContainer" class="mb-3 d-none">
                <button id="addCashPaymentBtn" type="button" class="btn btn-dark">
                    افزودن پرداخت نقدی
                </button>
            </div>

            {{-- دکمه‌های اقساطی --}}
            <div id="installmentBtnContainer" class="mb-3 d-none">
                <button id="addPrepaymentBtn" type="button" class="btn btn-success bg-admin-green me-2">
                    افزودن پیش‌پرداخت
                </button>
                <button id="addCheckBtn" type="button" class="btn btn-dark">
                    افزودن چک
                </button>
            </div>

            {{-- بخش‌ها --}}
            <div id="cashPaymentsContainer"></div>
            <div id="installmentContainer"></div>


        </form>

        <br> <br><br>
        <h4 class="fs18 fw-blod">پرداخت‌ها</h4>
        <table class="table table-bordered">
            <thead class="table-success">
                <tr>
                    <th>نوع</th>
                    <th>تاریخ و ساعت</th>
                    <th>مبلغ</th>
                    <th>شماره فیش / سریال</th>
                    <th>تصویر</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                {{-- پرداخت نقدی --}}
                @foreach($cashPayments as $payment)
                <tr>
                    <td>نقدی</td>
                    <td>
                        {{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($payment->date))->format('Y/m/d H:i') }}

                    </td>
                    <td>{{number_format($payment->amount) }}</td>
                    <td>{{ $payment->voucher_number }}</td>
                    <td>
                        @if($payment->receipt_image)
                        <a href="{{ route('payments.receipt', $payment->id) }}" target="_blank">
                            مشاهده تصویر
                        </a>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-payment" data-id="{{ $payment->id }}" data-type="payment">حذف</button>
                    </td>
                </tr>
                @endforeach

                {{-- پیش‌پرداخت --}}
                @foreach($prepayments as $pre)
                <tr>
                    <td>پیش‌پرداخت</td>
                    <td>
                        {{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($pre->date))->format('Y/m/d H:i') }}
                    </td>

                    <td>{{ number_format($pre->amount) }}</td>
                    <td>{{ $pre->voucher_number }}</td>
                    <td>
                        @if($pre->receipt_image)
                        <a href="{{ route('payments.receipt', $pre->id) }}" target="_blank">
                            مشاهده تصویر
                        </a>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-payment" data-id="{{ $pre->id }}" data-type="payment">حذف</button>
                    </td>
                </tr>
                @endforeach

                {{-- چک‌ها --}}
                @foreach($checks as $check)
                <tr>
                    <td>چک</td>


                    <td>{{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($check->date))->format('Y/m/d H:i') }}</td>
                    <td>{{ number_format($check->amount) }}</td>
                    <td>{{ $check->serial }}</td>
                    <td>
                        @if($check->check_image)
                        <a href="{{ route('checks.image', $check->id) }}" target="_blank">
                            مشاهده تصویر
                        </a>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-payment" data-id="{{ $check->id }}" data-type="check">حذف</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>


    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/js/data-picker.js')}}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const totalPriceEl = document.getElementById('totalPrice');
    const discountInput = document.getElementById('amount'); // فیلد تخفیف

    function calculateTotal() {
        let total = 0;

        checkboxes.forEach(chk => {
            if (chk.checked) {
                let price = parseFloat(chk.dataset.price);
                let tax = parseFloat(chk.dataset.tax);
                total += price + (price * tax / 100);
            }
        });

        // اعمال تخفیف اگر وارد شده باشد
        let discount = parseFloat(discountInput.value) || 0;
        let finalTotal = total - discount;

        if (finalTotal < 0) finalTotal = 0; // جلوگیری از عدد منفی

        totalPriceEl.textContent = finalTotal.toLocaleString();
    }

    // وقتی تغییرات روی محصولات یا تخفیف اعمال شد، دوباره محاسبه کن
    checkboxes.forEach(chk => chk.addEventListener('change', calculateTotal));
    discountInput.addEventListener('input', calculateTotal);

    // محاسبه اولیه هنگام لود صفحه
    calculateTotal();
</script>



<script>
    jalaliDatepicker.startWatch({
        'time': true
    });
    document.addEventListener('DOMContentLoaded', () => {
        const paymentType = document.getElementById('paymentType');

        const cashBtnContainer = document.getElementById('cashBtnContainer');
        const addCashPaymentBtn = document.getElementById('addCashPaymentBtn');
        const cashPaymentsContainer = document.getElementById('cashPaymentsContainer');

        const installmentBtnContainer = document.getElementById('installmentBtnContainer');
        const addPrepaymentBtn = document.getElementById('addPrepaymentBtn');
        const addCheckBtn = document.getElementById('addCheckBtn');
        const installmentContainer = document.getElementById('installmentContainer');

        // تغییر نوع پرداخت
        paymentType.addEventListener('change', function() {
            cashBtnContainer.classList.add('d-none');
            installmentBtnContainer.classList.add('d-none');
            cashPaymentsContainer.innerHTML = '';
            installmentContainer.innerHTML = '';

            if (this.value === 'cash') {
                cashBtnContainer.classList.remove('d-none');
            } else if (this.value === 'installment') {
                installmentBtnContainer.classList.remove('d-none');
            }
        });

        // آپشن‌های نوع کارت
        const cardOptions = `
        @foreach($paymentCards as $card)
            <option value="{{ $card->id }}">{{ $card->name }}</option>
        @endforeach
    `;

        // تابع تولید ردیف پرداخت (با دکمه حذف)
        function createRow(html) {
            const wrapper = document.createElement('div');
            wrapper.classList.add('payment-row', 'border', 'p-3', 'rounded', 'mb-3', 'bg-light', 'position-relative');
            wrapper.innerHTML = `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-payment-btn" aria-label="حذف"></button>
            ${html}
        `;
            return wrapper;
        }

        addCashPaymentBtn.addEventListener('click', e => {
            e.preventDefault();
            const html = `
        <div class="row g-3 align-items-center mt-2">
            <div class="col-md-2">
                <label>تاریخ:</label>
                <input type="text" name="cash_date[]" data-jdp class="form-control" required>
            </div>
            <div class="col-md-2">
                <label>مبلغ:</label>
                <input type="text" name="cash_amount[]" class="form-control price-input" required>
            </div>
            <div class="col-md-2">
                <label>شماره فیش:</label>
                <input type="text" name="cash_receipt[]" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>نوع کارت:</label>
                <select name="cash_card_id[]" class="form-select">
                    <option value="">انتخاب کارت...</option>
                    ${cardOptions}
                </select>
            </div>
            <div class="col-md-3">
                <label>تصویر پرداخت:</label>
                <input type="file" name="cash_image[]" class="form-control" accept="image/*" >
            </div>
        </div>
    `;

            const newRow = createRow(html);
            cashPaymentsContainer.appendChild(newRow);

            // ✅ این خط رو اضافه کن تا price-input جدید هم فرمت سه‌رقمی بگیره
            initPriceInputs(newRow.querySelectorAll('.price-input'));
        });


        // افزودن پیش‌پرداخت
        addPrepaymentBtn.addEventListener('click', e => {
            e.preventDefault();
            const html = `
        <div class="row g-3 align-items-center mt-2">
            <div class="col-md-2">
                <label>تاریخ:</label>
                <input type="text" name="pre_date[]" data-jdp class="form-control" required>
            </div>
            <div class="col-md-2">
                <label>مبلغ:</label>
                <input type="text" name="pre_amount[]" class="form-control price-input" required>
            </div>
            <div class="col-md-2">
                <label>شماره فیش:</label>
                <input type="text" name="pre_receipt[]" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>نوع کارت:</label>
                <select name="pre_card_id[]" class="form-select">
                    <option value="">انتخاب کارت...</option>
                    ${cardOptions}
                </select>
            </div>
            <div class="col-md-3">
                <label>تصویر پرداخت:</label>
                <input type="file" name="pre_image[]" class="form-control" accept="image/*" >
            </div>
        </div>
    `;
            const newRow = createRow(html);
            installmentContainer.appendChild(newRow);

            // فعال کردن سه‌رقمی شدن مبلغ جدید
            initPriceInputs(newRow.querySelectorAll('.price-input'));
        });


        // افزودن چک
        addCheckBtn.addEventListener('click', e => {
            e.preventDefault();
            const html = `
        <div class="row g-3 align-items-center mt-2">
            <div class="col-md-2">
                <label>تاریخ چک:</label>
                <input type="text" name="check_date[]" data-jdp class="form-control" required>
            </div>
            <div class="col-md-2">
                <label>مبلغ:</label>
                <input type="text" name="check_amount[]" class="form-control price-input" required>
            </div>
            <div class="col-md-2">
                <label>سریال چک:</label>
                <input type="text" name="check_serial[]" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label>کد صیاد:</label>
                <input type="text" name="check_sayad[]" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label>نام صاحب چک:</label>
                <input type="text" name="check_owner_name[]" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label>کد ملی صاحب چک:</label>
                <input type="text" name="check_owner_national[]" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label>موبایل صاحب چک:</label>
                <input type="text" name="check_owner_phone[]" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>تصویر چک:</label>
                <input type="file" name="check_image[]" class="form-control" accept="image/*">
            </div>
        </div>
    `;
            const newRow = createRow(html);
            installmentContainer.appendChild(newRow);

            // فعال کردن سه‌رقمی شدن مبلغ چک جدید
            initPriceInputs(newRow.querySelectorAll('.price-input'));
        });


        // حذف هر ردیف با delegation
        document.addEventListener('click', e => {
            if (e.target.classList.contains('remove-payment-btn')) {
                e.target.closest('.payment-row').remove();
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.delete-payment');

        buttons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const type = this.dataset.type;

                Swal.fire({
                    title: 'آیا مطمئن هستید؟',
                    text: "این عملیات غیرقابل بازگشت است!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'بله حذف کن',
                    cancelButtonText: 'لغو'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/student-products/delete-payment/${type}/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('حذف شد!', data.message, 'success');
                                    location.reload(); // یا حذف ردیف از جدول بدون ریلود
                                }
                            })
                    }
                })
            });
        });
    });
</script>
@endsection