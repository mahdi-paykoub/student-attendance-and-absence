@extends('layouts.suporterApp')

@section('title', 'مشاهده دانش‌آموز')

@section('content')

<div class="mt-4">

    <h3 class="fw-bold fs18">مشاهده دانش‌آموز: <span class="text-success">{{ $student->first_name }} {{ $student->last_name }}</span></h3>


    <div class="table-wrap mt-3 mb-4">
        <h5>مشخصات دانش‌آموز</h5>

        <div class="row">

            <div class="col-4 mt-3">
                پایه: {{ $student->grade->name }}
            </div>
            <div class="col-4 mt-3">
                رشته: {{ $student->major->name }}
            </div>
            <div class="col-4 mt-3">
                محصولات :
                @foreach($student->products()->get() as $product)
                {{ $product->title }} @if(!$loop->last), @endif
                @endforeach
            </div>

        </div>
    </div>
    <ul>



    </ul>


    <div class="bg-body-secondary rounded p-3">

        <h5>پشتیبان‌های فعلی:</h5>


        @if($currentSupporters->count() == 0)
        <p class="text-danger">هیچ پشتیبانی ندارد</p>
        @else
        <ul>
            @foreach($currentSupporters as $s)
            <li>{{ $s->name }}</li>
            @endforeach
        </ul>
        @endif
    </div>


    <h4 class="fw-bold fs18 mt-4">ارجاع به پشتیبان دیگر</h4>

    <div class="table-wrap mt-2">
        @if($otherSupporters->count() == 0)
        <p class="text-muted">پشتیبان دیگری برای ارجاع وجود ندارد.</p>
        @else

        <form action="{{ route('suporter.students.refer', $student->id) }}" method="POST">
            @csrf

            <div class="mb-3 d-flex align-items-center align-items-lg-center">
                <div>
                    <select name="supporter_id" class="form-control w-100" required>
                        <option value="">انتخاب کنید…</option>
                        @foreach($otherSupporters as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button class="btn btn-success bg-admin-green me-2">ثبت ارجاع</button>

            </div>

        </form>

        @endif
    </div>

</div>

@endsection