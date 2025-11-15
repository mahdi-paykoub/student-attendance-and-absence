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
                        <form id="central_form_{{ $student->id }}" class="central_form" action="{{ route('accounting.register.centarl.percentage', $student->id) }}" method="post">
                            @csrf
                            <input type="text" name="percatege" class="percantage_input form-control text-center">
                        </form>

                    </td>
                    <td>
                        <form>
                            @csrf
                            <input type="text" class="percantage_input form-control text-center">
                        </form>
                    </td>
                    <td>
                        <div class="central_value" id="central_value_{{ $student->id }}"></div>
                    </td>
                    <td>
                        <div class="representation_value">

                        </div>
                    </td>


                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted">هیچ دانش‌آموزی ثبت نشده است.</td>
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
                                div.innerHTML = `<div class="text-success text-center">
                                            ${Number(data.final).toLocaleString()}
                                         </div>`;
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
@endsection