@extends('layouts.app')

@section('content')
<div class="container">
    <h3>ثبت حضور برای آزمون: {{ $exam->name }}</h3>

    @if(session('success'))
    <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    <form id="attendanceForm" action="{{ route('attendances.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $exam->id }}">
        <input type="hidden" name="student_id" id="student_id">

        <div class="row">
            <div class="col-md-6">
                <label>کد ملی</label>
                <input type="text" id="national_code" class="form-control" name="national_code">
            </div>

            <div class="col-md-6">
                <label>نام</label>
                <input type="text" id="first_name" class="form-control" name="first_name" readonly>
            </div>

            <div class="col-md-6">
                <label>نام خانوادگی</label>
                <input type="text" id="last_name" class="form-control" name="last_name" readonly>
            </div>

            <div class="col-md-6">
                <label>پایه تحصیلی</label>
                <input type="text" id="grade" class="form-control" name="grade" readonly>
            </div>

            <div class="col-md-6">
                <label>رشته</label>
                <input type="text" id="major" class="form-control" name="major" readonly>
            </div>

            <div class="col-md-6 text-center mt-3">
                <img id="student_photo" src="" alt="عکس دانش‌آموز" style="max-width: 150px; display:none; border-radius: 8px;">
            </div>
        </div>


        {{-- امضا --}}
        <div class="mt-4">
            <label>امضای دانش‌آموز</label>
            <canvas id="signature-pad" width="400" height="200" class="border rounded bg-white"></canvas>
            <div class="mt-2">
                <button type="button" id="clear-signature" class="btn btn-sm btn-outline-danger">پاک کردن امضا</button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">ثبت حضور</button>
    </form>
</div>

@endsection


@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const nationalCodeInput = document.getElementById("national_code");

        nationalCodeInput.addEventListener("blur", function() {
            const code = nationalCodeInput.value.trim();
            if (!code) return;

            fetch(`{{ route('students.byNationalCode') }}?national_code=${code}`)
                .then(res => res.json())
                .then(data => {
                    console.log(data.student.first_name)
                    if (data.success) {
                        document.getElementById("first_name").value = data.student.first_name;
                        document.getElementById("last_name").value = data.student.last_name;
                        document.getElementById("grade").value = data.student.grade;
                        document.getElementById("major").value = data.student.major;

                        const photo = document.getElementById("student_photo");
                        photo.src = data.student.photo;
                        photo.style.display = "block";
                    } else {
                        document.getElementById("first_name").value = "";
                        document.getElementById("last_name").value = "";
                        document.getElementById("grade").value = "";
                        document.getElementById("major").value = "";
                        document.getElementById("student_photo").style.display = "none";
                    }
                });
        });

        document.addEventListener("click", function(e) {
            if (!nationalCodeInput.contains(e.target)) {
                nationalCodeInput.dispatchEvent(new Event("blur"));
            }
        });
    });

    // امضا
    const canvas = document.getElementById('signature-pad');
    const ctx = canvas.getContext('2d');
    let drawing = false;

    canvas.addEventListener('mousedown', () => drawing = true);
    canvas.addEventListener('mouseup', () => {
        drawing = false;
        ctx.beginPath();
    });
    canvas.addEventListener('mousemove', e => {
        if (!drawing) return;
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';
        ctx.lineTo(e.offsetX, e.offsetY);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(e.offsetX, e.offsetY);
    });

    document.getElementById('clear-signature').addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    });

    // ارسال فرم
    document.getElementById('attendanceForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
        formData.append('signature', blob, 'signature.png');

        const res = await fetch(e.target.action, {
            method: 'POST',
            body: formData
        });
        if (res.ok) {
            alert('✅ حضور با موفقیت ثبت شد');
            location.reload();
        } else {
            alert('❌ خطا در ثبت حضور');
        }
    });
</script>

@endsection