@extends('layouts.app')

@section('title', "حضور و غیاب - {$exam->name}")

@section('content')
<div class=" mt-4">
    <h3 class="fw-bold fs18">دانش‌آموزان حاضر در آزمون: <span class="text-success">{{ $exam->name }}</span></h3>

    <div class="table-wrap mt-4 table-responsive-lg">
        <table class="table table-striped">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>کد ملی</th>
                    <th>پایه</th>
                    <th>رشته</th>
                    <th>وضعیت حضور</th>
                    <th>امضا</th>


                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $attendance->student->first_name }}</td>
                    <td>{{ $attendance->student->last_name }}</td>
                    <td>{{ $attendance->student->national_code }}</td>
                    <td>{{ $attendance->student->grade->name ?? '-' }}</td>
                    <td>{{ $attendance->student->major->name ?? '-' }}</td>
                    <td>
                        @if($attendance->is_present)
                        <span class="badge bg-admin-green">حاضر</span>
                        @else
                        <span class="badge bg-danger">غایب</span>
                        @endif
                    </td>
                    <td>

                        <a href="{{ route('signatures.show', $attendance->id) }}" target="_blank" class="btn btn-sm btn-success bg-admin-green">
                            مشاهده امضا
                        </a>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">هیچ دانش‌آموزی ثبت نشده است.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection