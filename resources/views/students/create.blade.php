<form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <label>عکس 3×4:</label>
    <input type="file" name="photo"><br>

    <label>نام:</label>
    <input type="text" name="first_name" required><br>

    <label>نام خانوادگی:</label>
    <input type="text" name="last_name" required><br>

    <label>نام پدر:</label>
    <input type="text" name="father_name" required><br>

    <label>کد ملی:</label>
    <input type="text" name="national_code" required><br>

    <label>شماره موبایل دانش‌آموز:</label>
    <input type="text" name="mobile_student" required><br>

    <label>پایه تحصیلی:</label>
    <select name="grade_id">
        <option value="">انتخاب کنید</option>
        @foreach($grades as $grade)
        <option value="{{ $grade->id }}">{{ $grade->name }}</option>
        @endforeach
    </select><br>

    <!-- سایر selectboxها مشابه -->

    <button type="submit">ثبت دانش‌آموز</button>
</form>