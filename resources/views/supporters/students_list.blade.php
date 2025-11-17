@extends('layouts.app')

@section('title', 'دانش‌آموزان پشتیبان ' . $supporter->name)

@section('content')

<div class="mt-4">

    <h3 class="fw-bold fs18">لیست دانش‌آموزان پشتیبان: {{ $supporter->name }}</h3>
    <hr>

    @if($students->count() == 0)
    <div class="alert alert-warning">
        هیچ دانش‌آموزی برای این پشتیبان ثبت نشده است.
    </div>
    @else

    <div class="table-wrap">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>کدملی</th>
                    <th>پایه</th>
                    <th>رشته</th>
                    <th>تاریخ ارجاع</th>
                    <th>حذف</th>
                </tr>
            </thead>
            <tbody>

                @foreach($students as $student)
                <tr>
                    <td>{{ $student->first_name }}</td>
                    <td>{{ $student->last_name }}</td>
                    <td>{{ $student->national_code }}</td>
                    <td>{{ $student->grade->name }}</td>
                    <td>{{ $student->major->name }}</td>
                    <td>
                        {{ \Morilog\Jalali\Jalalian::fromDateTime($student->pivot->created_at)->format('Y/m/d') }}
                    </td>

                    <td>
                        <form action="{{ route('supporters.remove_student', [$supporter->id, $student->id]) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">حذف</button>
                        </form>
                    </td>

                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    @endif

</div>

@endsection