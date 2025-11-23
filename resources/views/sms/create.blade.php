@extends('layouts.app')


@section('content')
<div class=" mt-4">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 class="fs18 fw-bold"> ثبت پیامک جدید</h4>

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
        <form action="{{ route('sms.store.sms.template') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-lg-4 mt-3">
                    <label>عنوان قالب</label>
                    <input type="text" name="title" class="form-control mt-1" required>
                </div>
                <div class="col-lg-4 mt-3">
                    <label>دریافت‌کننده پیامک</label>
                    <select name="receiver_type" class="form-select mt-1" required>
                        <option value="student">دانش‌آموز</option>
                        <option value="father">پدر</option>
                        <option value="mother">مادر</option>
                    </select>
                </div>
                <div class="col-lg-4 mt-3">
                    <label>خط ارسال</label>
                    <select name="gateway" class="form-select mt-1" required>
                        <option value="5000300030">5000300030</option>
                        <option value="50003190">50003190</option>
                    </select>
                </div>

                <div class="col-12 mt-3">
                    <label>متن پیامک</label>
                    <textarea name="content" class="form-control mt-1" rows="6" required></textarea>
                    <small class="text-muted d-block mt-1">
                        می‌توانید از placeholder مثل {name} ، {debt} ، {date} و ... استفاده کنید.
                    </small>
                </div>
            </div>

            <button class="btn btn-success bg-admin-green mt-3">ذخیره</button>
        </form>

        <table class="table table-bordered mt-3">
            <thead class="table-light">
                <tr>
                    <th>کلید (Placeholder)</th>
                    <th>توضیح</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>first_name</td>
                    <td>نام کوچک دانش‌آموز</td>
                </tr>
                <tr>
                    <td>last_name</td>
                    <td>نام خانوادگی دانش‌آموز</td>
                </tr>
                <tr>
                    <td>full_name</td>
                    <td>نام و نام خانوادگی کامل دانش‌آموز</td>
                </tr>
                <tr>
                    <td>mobile</td>
                    <td>شماره موبایل دانش‌آموز</td>
                </tr>
                <tr>
                    <td>father_name</td>
                    <td>نام پدر دانش‌آموز</td>
                </tr>
                <tr>
                    <td>national_code</td>
                    <td>کد ملی دانش‌آموز</td>
                </tr>
                <tr>
                    <td>grade</td>
                    <td>پایه تحصیلی (مثلاً دهم، یازدهم، دوازدهم)</td>
                </tr>
                <tr>
                    <td>major</td>
                    <td>رشته تحصیلی (ریاضی، تجربی، انسانی و ...)</td>
                </tr>
                <tr>
                    <td>mobile_student</td>
                    <td>شماره موبایل دانش‌آموز</td>
                </tr>
                <tr>
                    <td>phone</td>
                    <td>شماره تلفن ثابت منزل</td>
                </tr>
                <tr>
                    <td>seat_number</td>
                    <td>شماره صندلی یا شماره اختصاصی دانش‌آموز</td>
                </tr>
                <tr>
                    <td>province</td>
                    <td>استان محل سکونت</td>
                </tr>
                <tr>
                    <td>city</td>
                    <td>شهر محل سکونت</td>
                </tr>
                <tr>
                    <td>address</td>
                    <td>آدرس کامل دانش‌آموز</td>
                </tr>
                <tr>
                    <td>total_products_price</td>
                    <td>مجموع قیمت تمام محصولات/خدمات خریداری‌شده</td>
                </tr>
                <tr>
                    <td>debt</td>
                    <td>مقدار بدهی فعلی دانش‌آموز</td>
                </tr>
                <tr>
                    <td>totalPayments</td>
                    <td>مجموع مبالغ پرداختی دانش‌آموز</td>
                </tr>
            </tbody>
        </table>
    </div>


    <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
        <h4 class="fs18 fw-bold"> قالب های ساخته شده</h4>
    </div>

    <div class="table-wrap">

        <table class="table table-striped">
            <thead class="table-light">
                <tr>
                    <th>عنوان</th>
                    <th>متن پیامک</th>
                    <th>دریافت کننده</th>
                    <th>خط ارسال</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $temp)
                <tr>
                    <td>{{ $temp->title }}</td>
                    <td>{{ $temp->content }}</td>
                    <td>{{ $temp->gateway }}</td>
                    {{-- دریافت کننده پیامک بر اساس enum --}}
                    <td>
                        @switch($temp->parent_type)
                        @case('father')
                        پدر
                        @break
                        @case('mother')
                        مادر
                        @break
                        @default
                        دانش‌آموز
                        @endswitch
                    </td>

                    <td>
                        <form action="{{ route('sms.delete', $temp->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="submit">
                                حذف
                            </button>
                        </form>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection