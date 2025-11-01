@extends('layouts.app')

@section('title', ' مدیریت کارت ها')

@section('content')
<div class="">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs18 fw-bold">مدیریت کارت های پرداختی</h4>
        <a href="{{ route('payment-cards.create') }}" class="btn btn-success bg-admin-green">افزودن کارت (پوز) جدید</a>
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
        <table class="table table-striped">
            <thead class="table-light">
                <tr>
                    <th>نام حساب</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cards as $card)
                <tr>
                    <td>{{ $card->name }}</td>
                    <td>
                        <form action="{{ route('payment-cards.destroy', $card) }}" method="POST" onsubmit="return confirm('حذف کارت؟')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-secondary btn-sm">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="text-center text-muted">هیچ کارتی ثبت نشده است</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection