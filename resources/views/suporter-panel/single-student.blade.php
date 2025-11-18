@extends('layouts.suporterApp')

@section('title', 'مشاهده دانش‌آموز')

@section('content')

<div class="mt-4">

    <h3 class="fw-bold fs18">مشاهده دانش‌آموز: <span class="text-success">{{ $student->first_name }} {{ $student->last_name }}</span></h3>


    <div class="table-wrap mt-3 mb-4">
        <h6>مشخصات دانش‌آموز</h6>

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

            <div class="mt-4">


                @if($currentSupporters->count() == 0)
                <p class="text-danger">هیچ پشتیبانی ندارد</p>
                @else
                <div class="d-flex align-items-center">
                    <h6 class="pt-2">پشتیبان ها:</h6>
                    @foreach($currentSupporters as $s)
                    <span class="badge bg-dark me-2">
                        {{ $s->name }}
                    </span>
                    @endforeach
                </div>

                @endif
            </div>

        </div>
    </div>






    <div class="row">
        <div class="col-lg-6 mt-3">
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
        <div class="col-lg-6 mt-3">
            <div class="mt-4">
                <h5 class="fw-bold fs18 mt-4">وضعیت رسیدگی من:</h5>

                @php
                $myPivot = $student->supporters->where('id', auth()->id())->first()->pivot ?? null;
                $currentStatus = $myPivot->progress_status ?? 'pending';
                @endphp

                <div class="table-wrap">
                    <form action="{{ route('suporter.students.update_status', $student->id) }}" method="POST">
                        @csrf
                        <select name="progress_status" class="form-select w-25 d-inline-block">
                            <option value="pending" @if($currentStatus=='pending' ) selected @endif>در انتظار</option>
                            <option value="in_progress" @if($currentStatus=='in_progress' ) selected @endif>در حال انجام</option>
                            <option value="done" @if($currentStatus=='done' ) selected @endif>تکمیل شد</option>
                        </select>

                        <button class="btn btn-success bg-admin-green me-2">ثبت وضعیت</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-12 mt-4">
            <div class="mt-4">
                <h5 class="fw-bold fs18">ثبت کارهای انجام شده:</h5>

                <div class="table-wrap mt-4">
                    {{-- فرم ثبت یادداشت جدید --}}
                    <form action="{{route('suporter.students.add.note' , $student->id)}}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="title" class="form-control" placeholder="عنوان یادداشت" required>
                        </div>
                        <div class="mb-2">
                            <textarea name="content" class="form-control" placeholder="متن یادداشت" rows="3" required></textarea>
                        </div>
                        <div class="form-check mb-2 d-flex align-items-center">

                            <input class="form-check-input" type="checkbox" name="is_shared" id="is_shared">
                            <label class="pe-2 me-4" for="is_shared">
                                قابل مشاهده برای همه پشتیبان‌ها
                            </label>
                        </div>
                        <button class="btn btn-success bg-admin-green mt-3">ثبت یادداشت</button>
                    </form>



                </div>





                <h5 class="fw-bold mt-4 fs18"> کارهای انجام شده:</h5>

                <div class="table-wrap mt-2">
                    @if($notes->count() == 0)
                    <p class="text-muted">هیچ یادداشتی ثبت نشده است.</p>
                    @else
                    <div class="list-group mb-3 p-0">
                        @foreach($notes as $note)
                        <div class=" border rounded p-3  mt-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <strong>{{ $note->title }}</strong>
                                </div>
                                <div>

                                    <span class="fs14">
                                        ({{ $note->user->name }} - {{ \Morilog\Jalali\Jalalian::fromDateTime($note->created_at)->format('Y/m/d H:i') }})
                                    </span>

                                    <span class="me-3">
                                        @if($note->is_shared)
                                        <span class="badge bg-info">مشترک</span>
                                        @else
                                        <span class="badge bg-secondary">خصوصی</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="mt-2">
                                <p>{{ $note->content }}</p>
                            </div>
                        </div>

                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>

@endsection