@extends('layouts.app')
@section('styles')
<link rel="stylesheet" href="{{asset('assets/css/data-picker.css')}}">
@endsection


@section('content')
<div class=" mt-4">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 class="fs18 fw-bold"> ثبت هزینه</h4>

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
        <form action="{{route('accounting.costs.create')}}" method="POST" enctype="multipart/form-data" class="p-3">
            @csrf
            <div class="row ">
                <div class="col-md-6 mt-3">
                    <label class="form-label">عنوان هزینه</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label"> مقدار هزینه</label>
                    <input type="text" name="amount" class="form-control price-input" required>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">نوع هزینه</label>
                    <select name="type" class="form-control" required>
                        <option value="">انتخاب کنید</option>
                        <option value="consumable">مصرفی</option>
                        <option value="capital">سرمایه‌ای</option>
                        <option value="gift">هدایا</option>
                    </select>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">تصویر فیش</label>
                    <input type="file" name="receipt_image" class="form-control" accept="image/*">
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">تاریخ و ساعت هزینه</label>
                    <input type="text" name="expense_date" class="form-control" required data-jdp>
                </div>

                <div class="col-md-6 text-start mt-auto">
                    <button class="btn btn-success bg-admin-green">ثبت هزینه</button>
                </div>
            </div>


        </form>

    </div>

    <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
        <h4 class="fs18 fw-bold">هزینه های ثبت شده</h4>
    </div>

    <div class="table-wrap">
        <table class="table mt-3">
            <thead class="table-light">
                <tr>
                    <th>نوع هزینه</th>
                    <th>عنوان</th>
                    <th>مقدار هزینه</th>
                    <th>تاریخ و ساعت</th>
                    <th>تصویر فیش</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td>
                        @if($expense->type == 'consumable') مصرفی
                        @elseif($expense->type == 'capital') سرمایه‌ای
                        @else هدایا
                        @endif
                    </td>
                    <td>{{ $expense->title }}</td>
                    <td>{{number_format( $expense->amount) }}</td>
                    <td>
                        {{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($expense->expense_datetime))->format('Y/m/d H:i:s') }}
                    </td>
                    <td>
                        @if($expense->receipt_image)
                        <a class="btn btn-sm btn-success bg-admin-green" href="{{ route('get.image.costs', basename($expense->receipt_image)) }}" target="_blank">
                            مشاهده فیش
                        </a>
                        @else
                        ندارد
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">هیچ هزینه‌ای ثبت نشده است.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

@section('scripts')
<script src="{{asset('assets/js/data-picker.js')}}"></script>
<script>
    jalaliDatepicker.startWatch({
        'time': true
    });
</script>

@endsection