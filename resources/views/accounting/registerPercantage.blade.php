@extends('layouts.app')

@section('title', ' ')

@section('content')
<div class=" mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fs18 fw-bold">افزودن درصد طرف حساب ها</h4>

    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif



    <div class="table-wrap mt-3">
        <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>نام</th>
                    <th>نام خانوادگی</th>

                    <th>کد ملی</th>
                    <th>پایه</th>
                    <th>رشته</th>

                    <th>لیست محصولات</th>
                    <th>درصد مرکزی</th>
                    <th>درصد نمایندگی</th>
                    <th>سهم مرکزی</th>
                    <th>سهم نمایندگی</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>

                    <td>{{ $student->first_name }}</td>
                    <td>{{ $student->last_name }}</td>
                    <td>{{ $student->national_code }}</td>
                    <td>{{ optional($student->grade)->name }}</td>
                    <td>{{ optional($student->major)->name }}</td>
                    <td>لیست محصول</td>


                    <td>
                        <form id="central_form_{{ $student->id }}"
                            class="central_form"
                            action="{{ route('accounting.register.centarl.percentage', $student->id) }}"
                            method="post">
                            @csrf
                            <input type="text"
                                name="percatege"
                                class="percantage_input form-control text-center"
                                value="{{ optional($student->percentages->firstWhere('account.type', 'center'))->percentage ?? '' }}">
                        </form>
                    </td>

                    <td>
                        <form id="agency_form_{{ $student->id }}"
                            class="central_form"
                            action="{{ route('accounting.register.agency.percentage', $student->id) }}"
                            method="post">
                            @csrf
                            <input type="text"
                                class="percantage_input form-control text-center agency_percentage"
                                data-student="{{ $student->id }}"
                                value="{{ optional($student->percentages->firstWhere('account.type','agency'))->percentage ?? '' }}">
                        </form>

                    </td>
                    <td>
                        <div dir="ltr" class="central_value text-end" id="central_value_{{ $student->id }}">
                            @php
                            $percentage = optional($student->percentages->firstWhere('account.type', 'center'))->percentage ?? 0;

                            // فقط محصولاتی که is_share = true هستند
                            $sharedProducts = $student->products->where('is_shared', true);

                            $totalPrice = $sharedProducts->sum('price');

                            $central_share = $totalPrice * ($percentage / 100);

                            $totalTax = 0;
                            if ($percentage) {
                            $totalTax = $sharedProducts->sum(function ($product) {
                            return $product->price * ($product->tax_percent / 100);
                            });
                            }

                            $final = $central_share + $totalTax;
                            @endphp

                            {{number_format($final)}}
                        </div>
                    </td>
                    <td>
                        <div class="agency_share text-end" dir="ltr" id="agency_share_{{ $student->id }}">
                            @php
                            $percentage =optional($student->percentages->firstWhere('account.type', 'agency'))->percentage ?? 0;
                            $baseShare=0;
                            $totalDue=0;
                            if($percentage){
                            $totalPayments = \App\Models\Payment::where('student_id', $student->id)->sum('amount');

                            $totalDue = ($totalPrice + $totalTax) - $totalPayments;
                            $baseShare = $totalPrice * ($percentage / 100);

                            }
                            $agencyShare = $baseShare - $totalDue;

                            @endphp

                            @if($agencyShare > 0)
                            <span class="">{{number_format($agencyShare)}}</span>
                            @else
                            <span class="text-danger">{{number_format($agencyShare)}}</span>

                            @endif
                        </div>
                    </td>


                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted">هیچ دانش‌آموزی  محصول دریافت  نکرده است.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // پیدا کردن همه فرم‌هایی که id شون با central_form_ شروع میشه
        const forms = document.querySelectorAll('form[id^="central_form_"]');

        forms.forEach(form => {
            const input = form.querySelector('input[name="percatege"]');

            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // جلوگیری از submit معمولی

                    // ساخت FormData
                    const formData = new FormData(form);

                    // ارسال AJAX
                    fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            const studentId = form.id.split('_')[2];
                            const div = document.getElementById('central_value_' + studentId);

                            if (data.status === 'success') {
                                div.innerHTML = `${Number(data.final).toLocaleString()}`;
                            }
                        })
                        .catch(error => {
                            console.error('خطا در ارسال AJAX:', error);
                        });
                }
            });
        });

    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // انتخاب تمام input هایی که درصد نمایندگی دارند
        const inputs = document.querySelectorAll('.agency_percentage');

        inputs.forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // جلوگیری از submit معمولی فرم

                    const studentId = this.dataset.student; // id دانش‌آموز
                    const percentage = this.value;

                    fetch(`/accounting/register/agency/percentage/${studentId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}' // توکن csrf لاراول
                            },
                            body: JSON.stringify({
                                percentage: percentage
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // بروزرسانی نمایش سهم نمایندگی
                                const shareDiv = document.getElementById(`agency_share_${studentId}`);
                                if (shareDiv) {
                                    shareDiv.textContent = Number(data.agency_share).toLocaleString();
                                    // اگر منفی است رنگ آن قرمز شود
                                    shareDiv.style.color = data.agency_share < 0 ? '#ed1c1c' : 'black';
                                }
                            } else {
                                alert('خطا در ثبت درصد نمایندگی');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('خطا در ارتباط با سرور');
                        });
                }
            });
        });
    });
</script>

@endsection