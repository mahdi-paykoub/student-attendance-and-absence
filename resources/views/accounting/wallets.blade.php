@extends('layouts.app')

@section('content')
<div class=" mt-4">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 class="fs18 fw-bold">
            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="25" width="25" xmlns="http://www.w3.org/2000/svg">
                <path d="M95.5 104h320a87.73 87.73 0 0 1 11.18.71 66 66 0 0 0-77.51-55.56L86 94.08h-.3a66 66 0 0 0-41.07 26.13A87.57 87.57 0 0 1 95.5 104zm320 24h-320a64.07 64.07 0 0 0-64 64v192a64.07 64.07 0 0 0 64 64h320a64.07 64.07 0 0 0 64-64V192a64.07 64.07 0 0 0-64-64zM368 320a32 32 0 1 1 32-32 32 32 0 0 1-32 32z"></path>
                <path d="M32 259.5V160c0-21.67 12-58 53.65-65.87C121 87.5 156 87.5 156 87.5s23 16 4 16-18.5 24.5 0 24.5 0 23.5 0 23.5L85.5 236z"></path>
            </svg>
            کیف پول
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

    <div class="row mt-4">


        @foreach($wallets as $wallet)
        <div class="col-2">
            <div class="bg-body-secondary rounded text-center p-4">
                <div class="fw-bold">
                    {{$wallet->account->name}}
                </div>
                <div class="mt-2 fw-bold" dir="ltr">
                    @if($wallet->balance >= 0)
                    {{number_format($wallet->balance)}}
                    @else
                    <span class="text-danger">
                        {{number_format($wallet->balance)}}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

    </div>

    <h4 class="fw-bold fs18 mt-5 mb-3">
        تراکنش ها
    </h4>
    <div class="table-wrap">
        <table class="table mt-3">
            <thead class="table-light">
                <tr>
                    <th>کیف پول</th>
                    <th>مبلغ</th>
                    <th>نوع</th>
                    <th>وضعیت</th>
                    <th>تاریخ</th>


                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                <tr>
                    <td>
                        {{$transaction->wallet->account->name}}
                    </td>
                    <td>
                        @if($transaction->amount >= 0)
                        <span>{{number_format($transaction->amount)}}</span>
                        @else
                        <span class="text-danger">
                            {{number_format($transaction->amount)}}
                        </span>
                        @endif

                    </td>
                    <td>
                        @if($transaction->type == 'deposit')
                        <span>
                            واریزی
                        </span>
                        @elseif('withdraw')
                        <span>
                            برداشت
                        </span>
                        @endif
                    </td>
                    <td>
                        @if($transaction->status == 'success')
                        <span class="badge bg-admin-green">
                            موفق
                        </span>
                        @else
                        <span class="badge bg-danger">
                            نا
                            موفق
                        </span>
                        @endif
                    </td>

                    <td class="text-end" dir="ltr">
                        {{ \Morilog\Jalali\Jalalian::fromDateTime($transaction->created_at)->format('Y/m/d H:i:s') }}
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">هیچ تراکنشی ثبت نشده است.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection