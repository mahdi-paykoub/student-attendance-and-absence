<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'مدیریت')</title>
    <!-- Bootstrap RTL -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container-fluid">
        <div class="row">

            {{-- منوی راست --}}
            <div class="col-md-3 col-lg-2 bg-white shadow-sm vh-100 p-3">
                <h5 class="mb-4 text-primary">مدیریت</h5>
                <ul class="nav flex-column">
                    <li class="nav-item"><a href="{{ route('students.index') }}" class="nav-link">دانش‌آموزان</a></li>
                    <li class="nav-item"><a href="{{ route('grades.index') }}" class="nav-link">پایه‌ها</a></li>
                    <li class="nav-item"><a href="{{ route('majors.index') }}" class="nav-link">رشته‌ها</a></li>
                    <li class="nav-item"><a href="{{ route('schools.index') }}" class="nav-link">مدارس</a></li>
                    <li class="nav-item"><a href="{{ route('provinces.index') }}" class="nav-link">استان‌ها</a></li>
                    <li class="nav-item"><a href="{{ route('cities.index') }}" class="nav-link">شهرستان‌ها</a></li>
                </ul>
            </div>

            {{-- محتوای اصلی --}}
            <div class="col-md-9 col-lg-10 p-4">
                @yield('content')
            </div>
        </div>
    </div>


    <script src="{{asset('assets/js/bootstrap.min.js')}}"></script>

    <script>
        document.querySelectorAll('.toggle-submenu').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.nextElementSibling.classList.toggle('show');
            });
        });
    </script>
</body>

</html>