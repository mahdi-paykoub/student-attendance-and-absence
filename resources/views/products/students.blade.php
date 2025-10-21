@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4 fw-bold fs18">دانش‌آموزانی که محصول <span class="text-success">{{$product->title}}</span>  را دارند:</h4>

    @if($students->isEmpty())
    <div class="alert alert-warning">هیچ دانش‌آموزی برای این محصول ثبت نشده است.</div>
    @else
    <div class="table-wrap">
        <div class="">
            <div class="">
                <table class="table table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>نام</th>
                            <th>نام خانوادگی</th>
                            <th>کد ملی</th>
                            <th>شماره تماس</th>
                            <th>نوع پرداخت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <td>{{ $student->first_name }}</td>
                            <td>{{ $student->last_name }}</td>
                            <td>{{ $student->national_code }}</td>
                            <td>{{ $student->phone }}</td>
                            <td>
                                @switch($student->pivot->payment_type)
                                @case('cash') نقدی @break
                                @case('installment') اقساطی @break
                                @case('scholarship') بورسیه @break
                                @default -
                                @endswitch
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection