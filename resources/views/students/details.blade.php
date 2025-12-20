@extends('layouts.app')

@section('content')
<h3 class="mb-4 fw-bold fs18">جزئیات دانش‌آموز: {{ $student->first_name }} {{ $student->last_name }}</h3>

<div class="table-wrap">
    {{-- خلاصه مالی --}}
    <div class="card mb-4">
        <div class="card-header bg-admin-green text-white">📊 خلاصه مالی</div>
        <div class="card-body text-center row justify-content-center">
            <div class="col-md-2"><strong>💰 جمع کل محصولات:</strong>
                <p>{{ number_format($totalProducts) }} تومان</p>
            </div>
            <div class="col-md-2"><strong>💵 پرداخت نقدی:</strong>
                <p>{{ number_format($totalPayments) }} تومان</p>
            </div>
            <div class="col-md-2"><strong>💳 پیش‌پرداخت‌ها:</strong>
                <p>{{ number_format($totalPrepayments) }} تومان</p>
            </div>
            <div class="col-md-2"><strong>🧾 چک‌ها:</strong>
                <p>{{ number_format($totalChecks) }} تومان</p>
            </div>
            <div class="col-md-2"><strong>🧾 تخفیف:</strong>
                <p>{{ number_format($student->discounts()->first()?->amount) }} تومان</p>
            </div>
        </div>
        <hr>
        <p class="fw-bold text-center">
            @if($debt > 0)
            <span class="text-danger">🔻 بدهکار: {{ number_format($debt) }} تومان</span>
            @elseif($credit > 0)
            <span class="text-success">✅ بستانکار: {{ number_format($credit) }} تومان</span>
            @else
            <span class="text-secondary">تسویه‌شده ✅</span>
            @endif
        </p>
    </div>

    {{-- پرداخت‌ها --}}
    @if($student->payments->count())
    <div class="card mb-4">
        <div class="card-header bg-light fw-bold">💵 پرداخت‌های نقدی و پیش‌پرداخت</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>نوع پرداخت</th>
                        <th>تاریخ</th>
                        <th>مبلغ</th>
                        <th>شماره فیش</th>
                        <th>کارت</th>
                        <th>رسید</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($student->payments as $pay)
                    <tr>
                        <td>{{ $pay->payment_type == 'cash' ? 'نقدی' : 'پیش‌پرداخت' }}</td>
                        <td>{{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($pay->date))->format('Y/m/d H:i') }}</td>
                        <td>{{ number_format($pay->amount) }} تومان</td>
                        <td>{{ $pay->voucher_number ?? '-' }}</td>
                        <td>{{ $pay->paymentCard->name ?? '-' }}</td>
                        <td>
                            @if($pay->receipt_image)
                            <a href="{{ route('payments.receipt', $pay->id) }}" target="_blank" class="btn btn-success btn-sm">مشاهده</a>
                            @else
                            -
                            @endif
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- چک‌ها --}}
    @if($student->checks->count())
    <div class="card mb-4">
        <div class="card-header bg-light fw-bold">🧾 چک‌ها</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>تاریخ</th>
                        <th>مبلغ</th>
                        <th>سریال</th>
                        <th>کد صیاد</th>
                        <th>صاحب چک</th>
                        <th>کد ملی</th>
                        <th>تلفن</th>
                        <th>وضعیت</th>
                        <th>تصویر</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($student->checks as $check)
                    <tr>
                        <td>{{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($check->date))->format('Y/m/d') }}</td>
                        <td>{{ number_format($check->amount) }} </td>
                        <td>{{ $check->serial }}</td>
                        <td>{{ $check->sayad_code }}</td>
                        <td>{{ $check->owner_name }}</td>
                        <td>{{ $check->owner_national_code }}</td>
                        <td>{{ $check->owner_phone }}</td>
                        <td>
                            @if($check->is_cleared)
                            <span class="badge bg-admin-green">وصول شده</span>
                            @else
                            <span class="badge bg-danger">وصول نشده</span>
                            @endif
                        </td>
                        <td>
                            @if($check->check_image)
                            <a href="{{ route('checks.image', $check->id) }}" target="_blank" class="btn btn-success btn-sm">مشاهده</a>
                            @else
                            -
                            @endif
                        </td>
                        <td class="d-flex align-items-center">

                            @if(!$check->is_cleared)
                            <form action="{{ route('checks.clear', ['check' => $check->id, 'student' =>$student->id ]) }}" method="POST" onsubmit="return confirm('آیا از وصول این چک مطمئن هستید؟')">
                                @csrf
                                <button class="btn btn-success bg-admin-green me-1 btn-sm">
                                    وصول شود
                                </button>
                            </form>
                            @endif


                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="text-start">
        <a href="{{ route('students.index') }}" class="btn btn-secondary">بازگشت</a>
    </div>
</div>

@endsection

@section('scripts')

{{-- اسکریپت حذف با SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-item').forEach(btn => {
        btn.addEventListener('click', e => {
            const id = btn.dataset.id;
            const type = btn.dataset.type;

            Swal.fire({
                title: 'حذف رکورد؟',
                text: 'آیا از حذف این مورد اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'بله، حذف شود',
                cancelButtonText: 'انصراف'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/${type}s/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    }).then(res => location.reload());
                }
            });
        });
    });
</script>
@endsection