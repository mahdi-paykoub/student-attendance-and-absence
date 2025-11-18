@extends('layouts.suporterApp')

@section('content')
<div class="container mt-4">
    <h3 class="fw-bold fs18">دانش آموزان من</h3>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif


    <div class="table-wrap mt-3">

        <div class="mb-3">

            <form method="GET" id="filterForm" class="row g-3 mb-3">

                {{-- فیلتر نوع ارتباط --}}
                <div class="col-md-3">
                    <label class="form-label">نوع ارتباط</label>
                    <select name="relation_type" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">همه</option>
                        <option value="assigned" {{ request('relation_type')=='assigned' ? 'selected' : '' }}>اصلی</option>
                        <option value="referred" {{ request('relation_type')=='referred' ? 'selected' : '' }}>ارجاعی</option>
                    </select>
                </div>

                {{-- فیلتر وضعیت رسیدگی --}}
                <div class="col-md-3">
                    <label class="form-label">وضعیت رسیدگی</label>
                    <select name="progress_status" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">همه</option>
                        <option value="pending" {{ request('progress_status')=='pending' ? 'selected' : '' }}>در انتظار</option>
                        <option value="in_progress" {{ request('progress_status')=='in_progress' ? 'selected' : '' }}>در حال انجام</option>
                        <option value="done" {{ request('progress_status')=='done' ? 'selected' : '' }}>تکمیل شده</option>
                    </select>
                </div>

            </form>

        </div>
        @if($students->count())
        <table class="table table-striped">
            <thead class="table-light">
                <tr>
                    <th>نام</th>
                    <th>پایه</th>
                    <th>رشته</th>
                    <th>محصولات</th>
                    <th>نوع ارتباط</th>
                    <th>وضعیت رسیدگی</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr>
                    <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                    <td>{{ $student->grade->name }}</td>
                    <td>{{ $student->major->name }}</td>

                    <td class="fs14">
                        @foreach($student->products as $product)
                        {{ $product->title }} @if(!$loop->last), @endif
                        @endforeach
                    </td>
                    {{-- نوع ارتباط --}}
                    <td>
                        @if($student->pivot->relation_type == 'assigned')
                        <span class="badge bg-primary">اصلی</span>
                        @elseif($student->pivot->relation_type == 'referred')
                        <span class="badge bg-warning">ارجاعی</span>
                        @endif
                    </td>

                    {{-- وضعیت رسیدگی --}}
                    <td>
                        @if($student->pivot->progress_status == 'pending')
                        <span class="badge bg-secondary">در انتظار</span>
                        @elseif($student->pivot->progress_status == 'in_progress')
                        <span class="badge bg-info text-dark">در حال انجام</span>
                        @elseif($student->pivot->progress_status == 'done')
                        <span class="badge bg-success">تکمیل شد</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('suporter.students.show', $student->id) }}" class="btn btn-success bg-admin-green btn-sm">
                            مشاهده دانش‌آموز
                        </a>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p>هیچ دانش آموزی ثبت نشده است.</p>
        @endif
    </div>

</div>
@endsection