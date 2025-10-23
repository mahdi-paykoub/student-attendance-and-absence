@extends('layouts.app')

@section('content')
<div class="">
    <h3 class="fw-bold fs18">ثبت حضور برای آزمون: {{ $exam->name }}</h3>

    @if(session('success'))
    <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    <div class="table-wrap mt-4">

        <form id="attendanceForm" action="{{ route('attendances.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="exam_id" value="{{ $exam->id }}">
            <input type="hidden" name="student_id" id="student_id" value="">

            <div class="row">
                <div class="col-md-6 mt-3">
                    <label>نام آزمون</label>
                    <input type="text" id="exam_name" class="form-control mt-1" name="exam_name" value="{{ $exam->name }}" readonly>
                </div>

                <div class="col-md-6 mt-3">
                    <label>کد ملی</label>
                    <input type="text" id="national_code" class="form-control mt-1" name="national_code" autocomplete="off">
                </div>

                <div class="col-md-6 mt-3">
                    <label>نام</label>
                    <input type="text" id="first_name" class="form-control mt-1" name="first_name" readonly>
                </div>

                <div class="col-md-6 mt-3">
                    <label>نام خانوادگی</label>
                    <input type="text" id="last_name" class="form-control mt-1" name="last_name" readonly>
                </div>

                <div class="col-md-6 mt-3">
                    <label>پایه تحصیلی</label>
                    <input type="text" id="grade" class="form-control mt-1" name="grade" readonly>
                </div>

                <div class="col-md-6 mt-3">
                    <label>رشته</label>
                    <input type="text" id="major" class="form-control mt-1" name="major" readonly>
                </div>

                <div class="col-md-6 text-center mt-3">
                    <img id="student_photo" src="" alt="عکس دانش‌آموز" style="max-width: 150px; display:none; border-radius: 8px;">
                </div>
            </div>

            {{-- امضا --}}
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="mt-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                امضای دانش‌آموز
                            </div>
                            <div>
                                <button type="button" id="clear-signature" class="btn btn-sm btn-outline-danger">پاک کردن امضا</button>
                            </div>
                        </div>
                        <div>
                            <canvas id="signature-pad" width="330" height="250" class="border rounded bg-white"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success bg-admin-green  mt-3">ثبت حضور</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // عناصر
        const nationalCodeInput = document.getElementById('national_code');
        const firstNameEl = document.getElementById('first_name');
        const lastNameEl = document.getElementById('last_name');
        const gradeEl = document.getElementById('grade');
        const majorEl = document.getElementById('major');
        const studentPhoto = document.getElementById('student_photo');
        const studentIdInput = document.getElementById('student_id');

        // -----------------------------
        // تابع پاک کردن فیلدها
        function clearStudentFields() {
            firstNameEl.value = '';
            lastNameEl.value = '';
            gradeEl.value = '';
            majorEl.value = '';
            studentPhoto.src = '';
            studentPhoto.style.display = 'none';
            studentIdInput.value = '';
        }

        // -----------------------------
        // debounce ساده برای جلوگیری از فراخوانی زیاد
        function debounce(fn, delay = 300) {
            let t;
            return function(...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), delay);
            }
        }

        // -----------------------------
        // درخواست به سرور برای گرفتن دانش‌آموز
        async function fetchStudentByNationalCode(code) {
            if (!code) {
                clearStudentFields();
                return;
            }

            try {
                const url = `{{ route('students.byNationalCode') }}?national_code=${encodeURIComponent(code)}`;
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!res.ok) {
                    clearStudentFields();
                    return;
                }

                const data = await res.json();

                if (data && data.success) {
                    // data.success true یعنی دانش‌آموز پیدا شد و محصول الزامی هم داره
                    const s = data.student;

                    firstNameEl.value = s.first_name ?? '';
                    lastNameEl.value = s.last_name ?? '';
                    gradeEl.value = s.grade ?? '';
                    majorEl.value = s.major ?? '';
                    studentIdInput.value = s.id ?? '';

                    if (s.photo) {
                        studentPhoto.onload = () => {
                            studentPhoto.style.display = 'block';
                        };
                        studentPhoto.onerror = () => {
                            studentPhoto.style.display = 'none';
                            studentPhoto.src = '';
                        };
                        studentPhoto.src = s.photo;
                    } else {
                        studentPhoto.style.display = 'none';
                        studentPhoto.src = '';
                    }

                } else {
                    // دانش‌آموز پیدا نشده یا محصول الزامی ندارد
                    clearStudentFields();
                    const errorMsg = data && data.message ? data.message : 'دانش‌آموز یافت نشد';
                    alert('❌ ' + errorMsg);
                }

            } catch (err) {
                clearStudentFields();
                console.error(err);
            }
        }


        // -----------------------------
        // debounce برای blur
        const debouncedFetch = debounce(() => fetchStudentByNationalCode(nationalCodeInput.value.trim()), 250);
        nationalCodeInput.addEventListener('blur', debouncedFetch);

        // Enter key
        nationalCodeInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                fetchStudentByNationalCode(nationalCodeInput.value.trim());
            }
        });

        // -------------------
        // تنظیمات canvas امضا (شامل touch و scaling)
        // -------------------
        const canvas = document.getElementById('signature-pad');
        const ctx = canvas.getContext('2d');

        // scale برای DPR بالا
        function resizeCanvasToDisplaySize(canvas) {
            const ratio = window.devicePixelRatio || 1;
            const w = canvas.width;
            const h = canvas.height;
            if (canvas.width !== Math.floor(w * ratio) || canvas.height !== Math.floor(h * ratio)) {
                canvas.width = Math.floor(w * ratio);
                canvas.height = Math.floor(h * ratio);
                canvas.style.width = w + 'px';
                canvas.style.height = h + 'px';
                ctx.scale(ratio, ratio);
            }
        }
        resizeCanvasToDisplaySize(canvas);

        let drawing = false;
        let last = {
            x: 0,
            y: 0
        };

        function getPointerPos(e, canvas) {
            const rect = canvas.getBoundingClientRect();
            if (e.touches && e.touches.length > 0) {
                return {
                    x: e.touches[0].clientX - rect.left,
                    y: e.touches[0].clientY - rect.top
                };
            } else {
                return {
                    x: e.clientX - rect.left,
                    y: e.clientY - rect.top
                };
            }
        }

        function startDraw(e) {
            drawing = true;
            const p = getPointerPos(e, canvas);
            last.x = p.x;
            last.y = p.y;
            e.preventDefault();
        }

        function stopDraw(e) {
            drawing = false;
            ctx.beginPath();
            e.preventDefault();
        }

        function draw(e) {
            if (!drawing) return;
            const p = getPointerPos(e, canvas);
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000';
            ctx.beginPath();
            ctx.moveTo(last.x, last.y);
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            last.x = p.x;
            last.y = p.y;
            e.preventDefault();
        }

        // mouse
        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseleave', stopDraw);

        // touch
        canvas.addEventListener('touchstart', startDraw);
        canvas.addEventListener('touchmove', draw);
        canvas.addEventListener('touchend', stopDraw);

        document.getElementById('clear-signature').addEventListener('click', function() {
            // پاک کردن و بازنشانی scale (در صورت نیاز)
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        // ارسال فرم با امضا
        document.getElementById('attendanceForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!studentIdInput.value) {
                alert('لطفا ابتدا کد ملی معتبر وارد کنید تا دانش‌آموز شناسایی شود.');
                return;
            }

            const form = e.target;
            const formData = new FormData(form);

            // تبدیل canvas به blob
            const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
            if (blob) {
                formData.append('signature', blob, 'signature.png');
            }

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (res.ok) {
                    const data = await res.json();
                    alert(data.message || '✅ حضور با موفقیت ثبت شد');
                    location.reload();
                } else if (res.status === 422) {
                    // خطای ولیدیشن یا ثبت تکراری
                    const data = await res.json();
                    if (data.errors) {
                        // نمایش اولین خطا
                        const firstError = Object.values(data.errors)[0][0];
                        alert('❌ ' + firstError);
                    } else if (data.message) {
                        alert('❌ ' + data.message);
                    }
                } else {
                    alert('❌ خطا در ثبت حضور');
                }
            } catch (err) {
                alert('❌ خطا در ثبت حضور (خطای شبکه)');
            }
        });
    }); // DOMContentLoaded
</script>
@endsection