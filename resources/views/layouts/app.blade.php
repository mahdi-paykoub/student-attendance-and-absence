<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'مدیریت')</title>
    <!-- Bootstrap RTL -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container-fluid">
        <div class="row">

            {{-- منوی راست --}}
            <div class="col-md-3 col-lg-2 vh-100 p-3 admin-right-panel">
                <h3 class="mb-4 fw-blod">مدیریت</h3>
                <ul class="nav flex-column p-0">

                    {{-- منوی دانش‌آموزان --}}
                    <li class="nav-item ">
                        <a class="nav-link d-flex justify-content-between align-items-center"
                            data-bs-toggle="collapse"
                            href="#studentsMenu"
                            role="button"
                            aria-expanded="{{ request()->routeIs('students.*') ? 'true' : 'false' }}"
                            aria-controls="studentsMenu">
                            دانش‌آموزان
                            <span class="bi bi-chevron-down"></span>
                        </a>

                        <div class="collapse {{ request()->routeIs('students.*') ? 'show' : '' }}" id="studentsMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item {{ request()->routeIs('students.index') ? 'active' : '' }}">
                                    <a href="{{ route('students.index') }}" class="nav-link">
                                        لیست دانش‌آموزان
                                    </a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('students.create') ? 'active' : '' }}">
                                    <a href="{{ route('students.create') }}" class="nav-link">
                                        افزودن دانش‌آموز
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    {{-- منوی محصولات --}}
                    <li class="nav-item ">
                        <a class="nav-link d-flex justify-content-between align-items-center"
                            data-bs-toggle="collapse"
                            href="#productsMenu"
                            role="button"
                            aria-expanded="{{ request()->routeIs('products.*') ? 'true' : 'false' }}"
                            aria-controls="productsMenu">
                            محصولات
                            <span class="bi bi-chevron-down"></span>
                        </a>

                        <div class="collapse {{ request()->routeIs('products.*') ? 'show' : '' }}" id="productsMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item {{ request()->routeIs('products.index') ? 'active' : '' }}">
                                    <a href="{{ route('products.index') }}" class="nav-link">
                                        لیست محصولات
                                    </a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('products.create') ? 'active' : '' }}">
                                    <a href="{{ route('products.create') }}" class="nav-link">
                                        افزودن محصول
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    {{-- تخصیص محصول به دانش آموز --}}
                    <li class="nav-item {{ request()->routeIs('student-products.*') ? 'active' : '' }}">
                        <a href="{{ route('student-products.create') }}" class="nav-link">تخصیص محصول</a>
                    </li>


                    {{-- منوی آزمون‌ها --}}
                    <li class="nav-item">
                        <a class="nav-link d-flex justify-content-between align-items-center"
                            data-bs-toggle="collapse"
                            href="#examsMenu"
                            role="button"
                            aria-expanded="{{ request()->routeIs('exams.*') ? 'true' : 'false' }}"
                            aria-controls="examsMenu">
                            آزمون‌ها
                            <span class="bi bi-chevron-down"></span>
                        </a>

                        <div class="collapse {{ request()->routeIs('exams.*') ? 'show' : '' }}" id="examsMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item {{ request()->routeIs('exams.index') ? 'active' : '' }}">
                                    <a href="{{ route('exams.index') }}" class="nav-link">
                                        لیست آزمون‌ها
                                    </a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('exams.create') ? 'active' : '' }}">
                                    <a href="{{ route('exams.create') }}" class="nav-link">
                                        افزودن آزمون
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>


                    {{-- زیرمنوی تنظیمات پایه‌ای --}}
                    <li class="nav-item">
                        <a class="nav-link d-flex justify-content-between align-items-center"
                            data-bs-toggle="collapse"
                            href="#basicSettingsMenu"
                            role="button"
                            aria-expanded="{{ request()->routeIs('grades.*') || request()->routeIs('majors.*') || request()->routeIs('schools.*') || request()->routeIs('provinces.*') || request()->routeIs('cities.*') ? 'true' : 'false' }}"
                            aria-controls="basicSettingsMenu">
                            تنظیمات پایه‌ای
                            <span class="bi bi-chevron-down"></span>
                        </a>

                        <div class="collapse {{ request()->routeIs('grades.*') || request()->routeIs('majors.*') || request()->routeIs('schools.*') || request()->routeIs('provinces.*') || request()->routeIs('cities.*') ? 'show' : '' }}" id="basicSettingsMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item {{ request()->routeIs('grades.*') ? 'active' : '' }}">
                                    <a href="{{ route('grades.index') }}" class="nav-link">پایه تحصیلی</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('majors.*') ? 'active' : '' }}">
                                    <a href="{{ route('majors.index') }}" class="nav-link">رشته تحصیلی</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('schools.*') ? 'active' : '' }}">
                                    <a href="{{ route('schools.index') }}" class="nav-link">مدارس</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('provinces.*') ? 'active' : '' }}">
                                    <a href="{{ route('provinces.index') }}" class="nav-link">استان‌ها</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('cities.*') ? 'active' : '' }}">
                                    <a href="{{ route('cities.index') }}" class="nav-link">شهرستان‌ها</a>
                                </li>
                            </ul>
                        </div>
                    </li>


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
    @yield('scripts')
</body>

</html>