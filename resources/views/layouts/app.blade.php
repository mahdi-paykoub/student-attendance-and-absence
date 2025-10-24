<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'مدیریت')</title>
    <!-- Bootstrap RTL -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    @yield('styles')
    <link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container-fluid">
        <div class="row">

            {{-- دکمه همبرگری فقط برای موبایل --}}
            <div class="d-xl-none p-3 border-bottom bg-admin-green text-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="fw-bold fs18">
                        منوی اصلی
                    </div>
                    <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="20" width="20" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" stroke-linecap="round" stroke-miterlimit="10" stroke-width="48" d="M88 152h336M88 256h336M88 360h336"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- منوی راست دسکتاپ --}}
            <div class="col-xl-2 d-none d-xl-block p-3 admin-right-panel">
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
                            <ul class="nav flex-column  pe-4">
                                <li class="nav-item {{ request()->routeIs('students.index') ? 'active' : '' }}">
                                    <a href="{{ route('students.index') }}" class="nav-link">لیست دانش‌آموزان</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('students.create') ? 'active' : '' }}">
                                    <a href="{{ route('students.create') }}" class="nav-link">افزودن دانش‌آموز</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('show.students.import') ? 'active' : '' }}">
                                    <a href="{{ route('show.students.import') }}" class="nav-link">افزودن با exel</a>
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
                            <ul class="nav flex-column  pe-4">
                                <li class="nav-item {{ request()->routeIs('products.index') ? 'active' : '' }}">
                                    <a href="{{ route('products.index') }}" class="nav-link">لیست محصولات</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('products.create') ? 'active' : '' }}">
                                    <a href="{{ route('products.create') }}" class="nav-link">افزودن محصول</a>
                                </li>
                            </ul>
                        </div>
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
                            <ul class="nav flex-column  pe-4">
                                <li class="nav-item {{ request()->routeIs('exams.index') ? 'active' : '' }}">
                                    <a href="{{ route('exams.index') }}" class="nav-link">لیست آزمون‌ها</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('exams.create') ? 'active' : '' }}">
                                    <a href="{{ route('exams.create') }}" class="nav-link">افزودن آزمون</a>
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
                            <ul class="nav flex-column  pe-4">
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
                                <li class="nav-item {{ request()->routeIs('advisors.*') ? 'active' : '' }}">
                                    <a href="{{ route('advisors.index') }}" class="nav-link">مشاوران</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('payment-cards.*') ? 'active' : '' }}">
                                    <a href="{{ route('payment-cards.index') }}" class="nav-link">پوز ها</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('settings.editExamProduct.*') ? 'active' : '' }}">
                                    <a href="{{ route('settings.editExamProduct') }}" class="nav-link">
                                        آزمون الزامی
                                    </a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('seats.index.*') ? 'active' : '' }}">
                                    <a href="{{ route('seats.index') }}" class="nav-link">
                                        تخصیص شماره صندلی
                                    </a>
                                </li>



                            </ul>
                        </div>
                    </li>

                    <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" class="nav-link">کاربران</a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('logout') }}" class="nav-link"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            خروج
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>

                </ul>
            </div>

            {{-- محتوای اصلی --}}
            <div class="col-12 col-xl-10 p-4">
                @yield('content')
            </div>

        </div>
    </div>

    {{-- Offcanvas برای موبایل --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header bg-admin-green text-white">
            <h5 class="offcanvas-title fw-bold fs18 ">مدیریت</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
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
                        <ul class="nav flex-column  pe-4">
                            <li class="nav-item {{ request()->routeIs('students.index') ? 'active' : '' }}">
                                <a href="{{ route('students.index') }}" class="nav-link">لیست دانش‌آموزان</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('students.create') ? 'active' : '' }}">
                                <a href="{{ route('students.create') }}" class="nav-link">افزودن دانش‌آموز</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('show.students.import') ? 'active' : '' }}">
                                <a href="{{ route('show.students.import') }}" class="nav-link">افزودن با exel</a>
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
                        <ul class="nav flex-column  pe-4">
                            <li class="nav-item {{ request()->routeIs('products.index') ? 'active' : '' }}">
                                <a href="{{ route('products.index') }}" class="nav-link">لیست محصولات</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('products.create') ? 'active' : '' }}">
                                <a href="{{ route('products.create') }}" class="nav-link">افزودن محصول</a>
                            </li>
                        </ul>
                    </div>
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
                        <ul class="nav flex-column  pe-4">
                            <li class="nav-item {{ request()->routeIs('exams.index') ? 'active' : '' }}">
                                <a href="{{ route('exams.index') }}" class="nav-link">لیست آزمون‌ها</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('exams.create') ? 'active' : '' }}">
                                <a href="{{ route('exams.create') }}" class="nav-link">افزودن آزمون</a>
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
                        <ul class="nav flex-column  pe-4">
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
                            <li class="nav-item {{ request()->routeIs('advisors.*') ? 'active' : '' }}">
                                <a href="{{ route('advisors.index') }}" class="nav-link">مشاوران</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('payment-cards.*') ? 'active' : '' }}">
                                <a href="{{ route('payment-cards.index') }}" class="nav-link">پوز ها</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('settings.editExamProduct.*') ? 'active' : '' }}">
                                <a href="{{ route('settings.editExamProduct') }}" class="nav-link">
                                    آزمون الزامی
                                </a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('seats.index.*') ? 'active' : '' }}">
                                <a href="{{ route('seats.index') }}" class="nav-link">
                                    تخصیص شماره صندلی
                                </a>
                            </li>



                        </ul>
                    </div>
                </li>

                <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}" class="nav-link">کاربران</a>
                </li>


                <li class="nav-item">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        خروج
                    </a>
                </li>
            </ul>
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