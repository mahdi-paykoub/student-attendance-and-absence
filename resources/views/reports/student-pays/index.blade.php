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
            واریزی دانش آموزان
        </h4>
    </div>




    <div class="table-wrap">
        <div class="d-flex align-items-center justify-content-between">
            <form method="GET" class="mb-4 d-flex gap-3 align-items-end">

                <div class="d-flex align-items-center ">
                   

                    <div class="d-flex gap-1 me-3">

                     

                        {{-- دکمه ۲: برای کارهای آینده --}}
                        <button type="submit"
                            formaction="{{ route('report.get.pays.pdf') }}"
                            class="btn btn-success bg-admin-green btn-sm">
                            چاپ pdf
                        </button>

                    </div>
                </div>
            </form>

        </div>


        <table class="table table-striped">
            <thead class="table-light">
                <tr class="text-center">
                    <th>#</th>
                    <th>دانش‌آموز</th>
                    <th>کدملی</th>
                    <th>مبلغ</th>
                    <th>نحوه واریز</th>

                    <th>تاریخ واریز</th>
                </tr>
            </thead>

            <tbody>
                @foreach($payments as  $index => $payment)
                <tr class="text-center">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $payment->student->first_name }} {{ $payment->student->last_name }}</td>
                    <td>{{ $payment->student->national_code }}</td>
                    <td>{{ number_format($payment->amount) }}</td>
                    <td>
                        @if($payment->payment_type == 'cash')
                        نقدی
                        @elseif($payment->payment_type == 'installment')
                        پیش‌پرداخت قسط
                        @endif
                    </td>
                   <td>{{ \Morilog\Jalali\Jalalian::forge($payment->date)->format('Y/m/d H:i') }}</td>


                  

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection