@extends('layouts.app')


@section('content')
<div class=" mt-4">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 class="fs18 fw-bold"> ارسال پیامک </h4>

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

    <div class="table-wrap">
        <table class="table mt-3">
            <thead class="table-light">
                <tr>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>کد ملی</th>
                    <th> پایه</th>
                    <th> رشته</th>
                    <th> عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td>
                        {{$student->first_name}}
                    </td>
                    <td>
                        {{$student->last_name}}
                    </td>
                    <td>{{$student->national_code}}</td>
                    <td>
                        {{$student->grade?->name}}
                    </td>
                    <td>
                        {{$student->major?->name}}
                    </td>
                    <td>
                        <button
                            class="btn btn-sm btn-success bg-admin-green"
                            data-bs-toggle="modal"
                            data-bs-target="#sendSmsModal"
                            data-student-id="{{ $student->id }}"
                            data-student-name="{{ $student->name }}">
                            ارسال پیامک
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">هیچ دانش اموزی ثبت نشده است.</td>
                </tr>
                @endforelse



            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="sendSmsModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('sms.send') }}">
            @csrf
            <input type="hidden" name="student_id" id="modal-student-id">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ارسال پیامک</h5>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>انتخاب قالب پیامک</label>
                        <select id="template-selector" name="template_id" class="form-control mt-1">
                            <option value="">انتخاب کنید </option>
                            @foreach($templates as $tpl)
                            <option
                                value="{{ $tpl->id }}"
                                data-body="{{ $tpl->content }}">
                                {{ $tpl->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="placeholders-area"></div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success bg-admin-green">ارسال</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection


@section('scripts')
<script>
    // لیست placeholderهای شناخته‌شده از PHP
    const knownPlaceholders = @json($knownPlaceholders);

    document.addEventListener("DOMContentLoaded", function() {

        // پر کردن student_id داخل مدال
        const modal = document.getElementById('sendSmsModal');
        modal.addEventListener('show.bs.modal', function(event) {
            let btn = event.relatedTarget;
            document.getElementById('modal-student-id').value = btn.dataset.studentId;
        });

        // شناسایی placeholderها وقتی قالب انتخاب می‌شود
        document.getElementById("template-selector").addEventListener("change", function() {

            let body = this.selectedOptions[0].dataset.body || "";

            // پیدا کردن placeholderها مثل {first_name}
            let matches = body.match(/\{([^}]+)\}/g) || [];

            let area = document.getElementById("placeholders-area");
            area.innerHTML = "";

            matches.forEach(ph => {

                let key = ph.replace('{', '').replace('}', '');

                if (knownPlaceholders.includes(key)) {
                    return; // اگر شناخته‌شده باشد، ورودی نمایش نده
                }

                // اگر ناشناخته بود input بساز
                area.innerHTML += `
                    <div class="mb-3">
                        <label>${key}</label>
                        <input type="text" class="form-control" name="placeholders[${key}]">
                    </div>
                `;
            });
        });
    });
</script>


@endsection