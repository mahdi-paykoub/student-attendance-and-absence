@extends('layouts.auth-app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        داشبورد
                    </div>
                    <div>

                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('students.index') }}" class="btn btn-primary btn-sm">
                            پنل کاربری
                        </a>
                        @elseif(auth()->user()->role === 'suporter')
                        <a href="{{ route('suporter.filter.students') }}" class="btn btn-primary btn-sm">
                            پنل کاربری
                        </a>
                        @endif


                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    شما وارد شدید
                </div>
            </div>
        </div>
    </div>
</div>
@endsection