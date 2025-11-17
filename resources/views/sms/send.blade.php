@extends('layouts.app')


@section('content')
<div class=" mt-4">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 class="fs18 fw-bold"> ثبت پیامک جدید</h4>

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
        <table class="table mt-3">
            <thead class="table-light">
                <tr>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>کد ملی</th>
                    <th> پایه</th>
                    <th> رشته</th>
                    <th> عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td>
                        {{$student->first_name}}
                    </td>
                    <td>
                        {{$student->last_name}}
                    </td>
                    <td>{{$student->national_code}}</td>
                    <td>
                        {{$student->grade?->name}}
                    </td>
                    <td>
                        {{$student->major?->name}}
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">هیچ دانش اموزی ثبت نشده است.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>



</div>
@endsection