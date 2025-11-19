@extends('layouts.app')

@section('content')
<div class=" mt-4">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 class="fs18 fw-bold">
            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="25" width="25" xmlns="http://www.w3.org/2000/svg">
                <path fill="none" d="M0 0h24v24H0z"></path>
                <path d="M22 11V3h-7v3H9V3H2v8h7V8h2v10h4v3h7v-8h-7v3h-2V8h2v3h7zM7 9H4V5h3v4zm10 6h3v4h-3v-4zm0-10h3v4h-3V5z"></path>
            </svg>
            سود خالص طرف حساب ها
        </h4>

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


    <div class="row mt-4 gap-2">
        <div class="col-lg-2 mt-2 d-flex align-items-center justify-content-center p-4 bg-body-secondary rounded">
            <div class="text-center">
                <div class="fw-bold">
                    سود کل بخش مرکزی
                </div>
                <div class="fw-bold mt-3">
                    {{number_format($centralTotal)}}
                </div>

            </div>
        </div>
        <div class="col-lg-2 mt-2 d-flex align-items-center justify-content-center p-4 bg-body-secondary rounded">
            <div class="text-center">
                <div class="fw-bold">
                    سود کل بخش نمایندگی
                </div>
                <div class="fw-bold mt-3">
                    {{number_format($agencyTotal)}}
                </div>

            </div>
        </div>

        <!-- سهم هر شریک نمایندگی -->
        @foreach($partnersProfits as $partnerName => $profit)
        <div class="col-lg-2 mt-2 d-flex align-items-center justify-content-center p-4 bg-body-secondary rounded">
            <div class="text-center">
                <div class="fw-bold">
                    سود {{ $partnerName }}
                </div>
                <div class="fw-bold mt-3">
                    {{ number_format($profit) }} تومان
                </div>
            </div>
        </div>
        @endforeach
    </div>



    <div class="mt-5 table-wrap">
        <div class="fs18 fw-bold">
            سود از هر دانش‌آموز
        </div>

        <div class="mt-3">

            <table class="table table-striped text-center table-responsive">
                <thead class="table-light">
                    <tr>
                        <th>نام دانش‌آموز</th>
                        <th>سود بخش مرکزی</th>
                        <th>سود بخش نمایندگی</th>
                        <th>جمع کل سود</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>

                        <td class="text-success fw-bold">
                            {{ number_format($student->central_profit) }} تومان
                        </td>

                        <td class="text-success fw-bold">
                            {{ number_format($student->agency_profit) }} تومان
                        </td>

                        <td class="text-dark fw-bold">
                            {{ number_format($student->central_profit + $student->agency_profit) }} تومان
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>


</div>
@endsection