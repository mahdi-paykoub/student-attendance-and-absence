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
                    <input type="number" name="amount" class="form-control">
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