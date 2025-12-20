@extends('layouts.app')
@section('styles')
<link rel="stylesheet" href="{{asset('assets/css/data-picker.css')}}">
@endsection

@section('content')
<div class=" mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fs18 fw-bold"> ثبت واریزی</h4>
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

    <div class="table-wrap pb-5">
        <form action="{{route('accounting.deposits.create')}}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6 mt-3">
                    <label class="form-label">عنوان واریزی</label>
                    <input type="text" name="title" class="form-control">
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">میزان واریزی</label>
                    <input type="text" name="amount" class="form-control price-input">
                </div>


                <div class="col-md-6 mt-3">
                    <label class="form-label">طرف حساب واریزی</label>
                    <select name="account_id" class="form-control">
                        @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">تصویر فیش</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">تاریخ و ساعت واریزی</label>
                    <input type="text" name="paid_at" class="form-control" data-jdp>
                </div>

                <div class="col-md-6 mt-3 text-start mt-auto">
                    <button class="btn btn-success bg-admin-green">ثبت واریزی</button>
                </div>
            </div>






        </form>
    </div>


    <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
        <h4 class="fs18 fw-bold">همه واریزی ها</h4>
    </div>


    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>عنوان</th>
                    <th>مبلغ</th>
                    <th>طرف حساب</th>
                    <th>تاریخ واریزی</th>
                    <th>تصویر</th>
                    <th>عملیات</th>
                </tr>
            </thead>

            <tbody>
                @foreach($deposits as $deposit)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $deposit->title }}</td>

                    <td>
                        {{ number_format($deposit->amount) }} تومان
                    </td>

                    <td>
                        {{ $deposit->account->name ?? '-' }}
                    </td>

                    <td>
                        {{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($deposit->paid_at))->format('Y/m/d H:i:s') }}
                    </td>

                    <td>
                        @if($deposit->image)
                        <a class="btn btn-success btn-sm bg-admin-green" href="{{ route('get.image.deposits', basename($deposit->image)) }}" target="_blank">
                            مشاهده تصویر
                        </a>
                        @else
                        -
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('accounting.deposit.delete', $deposit->id) }}"
                            method="POST"
                            onsubmit="return confirm('آیا از حذف این واریزی مطمئن هستید؟ مبلغ به کیف پول برمی‌گردد.')">

                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger btn-sm">
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

@section('scripts')
<script src="{{asset('assets/js/data-picker.js')}}"></script>

<script>
    jalaliDatepicker.startWatch({
        time: true
    });
</script>
@endsection