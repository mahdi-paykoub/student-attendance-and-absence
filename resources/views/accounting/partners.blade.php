@extends('layouts.app')

@section('title', ' ')

@section('content')
<div class=" mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fs18 fw-bold"> بخش شرکا</h4>

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



    <div class="d-flex bg-admin-green p-3 rounded justify-content-between align-items-center text-white">
        <div>
            جمع کل سهم نمایندگی
        </div>
        <div>
            {{number_format($wallet?->balance)}}
            <span class="me-1">
                تومان
            </span>
        </div>
    </div>
    <div class="d-flex bg-admin-green p-3 rounded justify-content-between align-items-center text-white mt-2">
        @foreach($partners as $partner)
        <div class="d-flex align-items-center">
            <div>
                {{$partner->name}}:
            </div>
            <div class="me-4">
                {{number_format($partner->wallet()->first()?->balance)}}
                <span class="fs14 me-1">
                    تومان
                </span>
            </div>
        </div>
        @endforeach
    </div>


    <div class="table-wrap mt-3">



        <form action="{{ route('accounting.partners.create') }}" method="post">
            @csrf
            {{-- شریک 1 --}}
            <div class="mb-1">شریک 1</div>
            <div class="row bg-body-secondary p-2">
                <div class="col-4">
                    <label>نام و نام خانوادگی</label>
                    <input value="{{ $partners[0]->name ?? '' }}" type="text" name="partners[0][name]" class="form-control mt-1">
                </div>
                <div class="col-4">
                    <label>درصد سهم شریک</label>
                    <input value="{{ $partners[0]->percentage ?? '' }}" type="text" name="partners[0][percent]" class="form-control mt-1">
                </div>

            </div>

            {{-- شریک 2 --}}
            <div class="mt-2 mb-1">شریک 2</div>
            <div class="row bg-body-secondary p-2">
                <div class="col-4">
                    <label>نام و نام خانوادگی</label>
                    <input value="{{ $partners[1]->name ?? '' }}" type="text" name="partners[1][name]" class="form-control mt-1">
                </div>
                <div class="col-4">
                    <label>درصد سهم شریک</label>
                    <input value="{{ $partners[1]->percentage ?? '' }}" type="text" name="partners[1][percent]" class="form-control mt-1">
                </div>
            </div>

            {{-- شریک 3 --}}
            <div class="mt-2 mb-1">شریک 3</div>
            <div class="row bg-body-secondary p-2">
                <div class="col-4">
                    <label>نام و نام خانوادگی</label>
                    <input value="{{ $partners[2]->name ?? '' }}" type="text" name="partners[2][name]" class="form-control mt-1">
                </div>
                <div class="col-4">
                    <label>درصد سهم شریک</label>
                    <input value="{{ $partners[2]->percentage ?? '' }}" type="text" name="partners[2][percent]" class="form-control mt-1">
                </div>
            </div>

            <button class="btn btn-success bg-admin-green mt-4">
                ارسال
            </button>
        </form>
    </div>



</div>
@endsection