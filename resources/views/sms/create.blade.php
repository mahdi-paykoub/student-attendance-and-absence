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
        <form action="{{ route('sms.store.sms.template') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>عنوان قالب</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>متن پیامک</label>
                <textarea name="content" class="form-control" rows="6" required></textarea>
                <small class="text-muted d-block mt-1">
                    می‌توانید از placeholder مثل {name} ، {debt} ، {date} و ... استفاده کنید.
                </small>
            </div>

            <button class="btn btn-success bg-admin-green">ذخیره</button>
        </form>
    </div>


    <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
        <h4 class="fs18 fw-bold"> ثبت پیامک جدید</h4>
    </div>

    <div class="table-wrap">

        <table class="table table-striped">
            <thead class="table-light">
                <tr>
                    <th>عنوان</th>
                    <th>متن پیامک</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $temp)
                <tr>
                    <td>{{ $temp->title }}</td>
                    <td>{{ $temp->content }}</td>
                    <td>
                       

                       
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection