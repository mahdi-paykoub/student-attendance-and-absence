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
            واریزی ها
        </h4>
    </div>

    <div class="table-wrap">
        <form method="GET">
            <div class="d-flex align-items-center">
                <div class="">
                    <select name="account_id" class="form-control">
                        <option value="">همه حساب‌ها</option>
                        @foreach($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex">
                    {{-- دکمه نمایش در صفحه --}}
                    <button type="submit" class="btn btn-success btn-sm bg-admin-green me-4" formaction="{{ route('report.get.deposits.view') }}">
                        نمایش
                    </button>

                    {{-- دکمه PDF --}}
                    <button type="submit" class="btn btn-success btn-sm bg-admin-green me-1" formaction="{{ route('report.get.deposits.pdf') }}">
                     چاپ pdf
                    </button>
                </div>
            </div>
        </form>




        <table class="table mt-4 table-striped">
            <thead class="table-light">
                <tr>
                    <th>عنوان</th>
                    <th>حساب</th>
                    <th>مبلغ</th>
                    <th>تاریخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deposits as $deposit)
                <tr>
                    <td>{{ $deposit->title }}</td>
                    <td>{{ $deposit->account->name }}</td>
                    <td>{{ number_format($deposit->amount) }}</td>
                    <td>{{ \Morilog\Jalali\Jalalian::fromCarbon($deposit->created_at)->format('Y/m/d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">واریزی‌ وجود ندارد</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection