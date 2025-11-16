@extends('layouts.app')

@section('title', ' ')

@section('content')
<div class=" mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
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

    </div>


</div>
@endsection