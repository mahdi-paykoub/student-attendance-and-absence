@extends('layouts.app')
@section('styles')
<link rel="stylesheet" href="{{asset('assets/css/data-picker.css')}}">
@endsection

@section('title', 'افزودن آزمون')

@section('content')
<div class=" mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-admin-green text-white">
            <h5 class="mb-0">افزودن آزمون جدید</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('exams.store') }}" method="POST">
                @csrf
                <div class="">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">نام آزمون</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="col-md-6">
                            <label for="domain" class="form-label">نام حوزه</label>
                            <input type="text" class="form-control" id="domain" name="domain" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="domain_manager" class="form-label">نام مسئول حوزه</label>
                            <input type="text" class="form-control" id="domain_manager" name="domain_manager" required>
                        </div>

                        <div class="col-md-6">
                            <label for="exam_datetime" class="form-label">تاریخ و ساعت آزمون</label>
                            <input type="text" data-jdp class="form-control" id="mydate" name="exam_datetime" required>
                        </div>
                    </div>


                    <div class="mb-3">
                        <label class="form-label">انتخاب مراقبین</label>
                        <div class="row">
                            @foreach($advisors as $advisor)
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="supervisors[]" value="{{ $advisor->id }}" id="advisor{{ $advisor->id }}">
                                    <label class="form-check-label" for="advisor{{ $advisor->id }}">
                                        {{ $advisor->name }} ({{ $advisor->phone }})
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>


                    <button type="submit" class="btn btn-success bg-admin-green">ذخیره آزمون</button>
                </div>
            </form>


        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/js/data-picker.js')}}"></script>
<script>
    jalaliDatepicker.startWatch();
</script>

@endsection