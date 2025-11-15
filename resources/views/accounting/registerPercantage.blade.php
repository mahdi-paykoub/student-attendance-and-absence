@extends('layouts.app')

@section('title', ' ')

@section('content')
<div class=" mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fs18 fw-bold">افزودن درصد طرف حساب ها</h4>
     
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

   

    <div class="table-wrap mt-3">
          <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                   
                    <th>کد ملی</th>
                    <th>پایه</th>
                    <th>رشته</th>

                    <th>لیست محصولات</th>
                    <th>درصد  مرکزی</th>
                    <th>درصد  نمایندگی</th>
                    <th>سهم مرکزی</th>
                    <th>سهم نمایندگی</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                   
                    <td>{{ $student->first_name }}</td>
                    <td>{{ $student->last_name }}</td>
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