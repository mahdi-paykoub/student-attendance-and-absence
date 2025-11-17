@extends('layouts.app')

@section('content')
<div class="">
    <h3 class="mb-4 fw-bold fs18">لیست کاربران</h3>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif


    <div class="table-wrap table-responsive-lg">
        <table class="table table-striped">
            <thead class="table-light">
                <tr>
                    <th>آیدی</th>
                    <th>نام</th>
                    <th>ایمیل</th>
                    <th>نقش</th>
                    <th>تاریخ ثبت‌نام</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->role == 'admin')
                        ادمین
                        @elseif($user->role == 'suporter')
                        پشتیبان
                        @elseif($user->role == 'none')
                        فاقد نقش
                        @endif
                        
                    </td>
                    <td>{{ \Morilog\Jalali\Jalalian::fromDateTime($user->created_at)->format('Y/m/d') }}</td>
                    <td>

                        <!-- دکمه تبدیل به ادمین فقط برای کاربران عادی -->
                        @if($user->role == 'none')
                        <form action="{{ route('users.makeAdmin', $user) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success bg-admin-green">تبدیل به ادمین</button>
                        </form>
                        <form action="{{ route('users.makeSuporter', $user) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success bg-admin-green">تبدیل به پشتیبان</button>
                        </form>
                        @endif


                        @if($user->role != 'admin')
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('آیا مطمئن هستید؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-secondary">حذف</button>
                        </form>
                        @endif


                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">هیچ کاربری یافت نشد.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</div>
@endsection