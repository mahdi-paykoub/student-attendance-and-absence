@extends('layouts.app')



@section('content')
<div class="mt-4">

    {{-- پیام موفقیت --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif


    <div>
        <h4 class="fw-bold fs18 mb-3">
            چک ها
        </h4>
    </div>




    <div class="table-wrap">
        <div class="d-flex align-items-center justify-content-between">
            <form method="GET" class="mb-4 d-flex gap-3 align-items-end">

                <div class="d-flex align-items-center ">
                    <div>
                        <select name="status" class="form-select">
                            <option value="">همه</option>
                            <option value="cleared">وصول شده</option>
                            <option value="not_cleared">وصول نشده</option>
                        </select>
                    </div>

                    <div class="d-flex gap-1 me-3">

                        {{-- دکمه ۱: ارسال برای فیلتر شدن --}}
                        <button type="submit"
                            formaction="{{ route('report.get.checks.view') }}"
                            class="btn btn-success bg-admin-green btn-sm">
                            اعمال فیلتر
                        </button>

                        {{-- دکمه ۲: برای کارهای آینده --}}
                        <button type="submit"
                            formaction="{{ route('report.get.checks.pdf') }}"
                            class="btn btn-success bg-admin-green btn-sm">
                            چاپ pdf
                        </button>

                    </div>
                </div>
            </form>
            <div class="d-flex align-items-center">
                <div class="fs14">
                    مجموع های وصول شده:
                    <span class="badge bg-admin-green">
                        {{number_format($totalCleared)}}
                    </span>
                </div>
                <div class="me-4">
                    مجموع های وصول نشده:
                    <span class="badge bg-danger">
                        {{number_format($totalUnCleared)}}
                    </span>
                </div>
            </div>
        </div>


        <table class="table table-striped">
            <thead class="table-light">
                <tr class="text-center">
                    <th>#</th>
                    <th>دانش‌آموز</th>
                    <th>تاریخ</th>
                    <th>مبلغ</th>
                    <th>سریال</th>
                    <th>شناسه صیاد</th>
                    <th>موبایل</th>
                    <th>نام صاحب چک</th>
                    <th>تغییر وضعیت</th>
                    <th>وضعیت وصول</th>
                </tr>
            </thead>

            <tbody>
                @foreach($checks as $index => $check)
                <tr class="text-center">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $check->student->first_name }} {{ $check->student->last_name }}</td>
                    <td>{{ \Morilog\Jalali\Jalalian::forge($check->date)->format('Y/m/d') }}</td>
                    <td>{{ number_format($check->amount) }} تومان</td>
                    <td>{{ $check->serial }}</td>
                    <td>{{ $check->sayad_code }}</td>
                    <td>{{ $check->owner_phone }}</td>
                    <td>{{ $check->owner_name }}</td>
                    <td class="d-flex align-items-center">

                        @if(!$check->is_cleared)
                        <form action="{{ route('checks.clear', ['check' => $check->id, 'student' => $check->student_id ]) }}" method="POST" onsubmit="return confirm('آیا از وصول این چک مطمئن هستید؟')">
                            @csrf
                            <button class="btn btn-success bg-admin-green me-1 btn-sm">
                                وصول شود
                            </button>
                        </form>
                        @else
                        <button class="btn btn-dark  me-1 btn-sm">
                          وصول شده
                        </button>
                        @endif


                    </td>
                    <td>
                        @if($check->is_cleared)
                        <span class="badge bg-admin-green">وصول شده</span>
                        @else
                        <span class="badge bg-danger">وصول نشده</span>
                        @endif
                    </td>


                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection