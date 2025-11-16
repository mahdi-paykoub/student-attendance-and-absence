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


    <div class="mb-3 text-start">
        <form action="{{route('report.get.debtor.students.pdf')}}">
            @csrf
            <button class="btn btn-success bg-admin-green">
                دریافت pdf
            </button>
        </form>
    </div>
    <div class="table-wrap table-responsive-xl students-list-table">

        <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>کد ملی</th>
                    <th>جنسیت</th>
                    <th>پایه</th>
                    <th>رشته</th>
                </tr>
            </thead>
            <tbody>
                @forelse($debtors as $student)
                <tr>


                    <td>{{ $student->first_name }}</td>
                    <td>{{ $student->last_name }}</td>
                    <td>{{ $student->national_code }}</td>
                    <td>
                        @if($student->gender == 'male')
                        <span>پسر</span>
                        @else
                        <span>دختر</span>
                        @endif
                    </td>
                    <td>{{ optional($student->grade)->name }}</td>
                    <td>{{ optional($student->major)->name }}</td>


                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted">هیچ دانش‌آموزی ثبت نشده است.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection