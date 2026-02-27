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

                    @if(auth()->user()->role !== 'stucent_register')



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

                    {{-- منوی گزارشات --}}
                    <li class="nav-item">
                        <a class="nav-link d-flex justify-content-between align-items-center"
                            data-bs-toggle="collapse"
                            href="#reportsMenu"
                            role="button"
                            aria-expanded="{{ request()->routeIs('seats.report.*') ? 'true' : 'false' }}"
                            aria-controls="reportsMenu">
                            گزارشات
                            <span class="bi bi-chevron-down"></span>
                        </a>

                        <div class="collapse {{ request()->routeIs('report.*') ? 'show' : '' }}" id="reportsMenu">
                            <ul class="nav flex-column pe-4">
                                <li class="nav-item {{ request()->routeIs('report.seatsNumber.view') ? 'active' : '' }}">
                                    <a href="{{route('report.seatsNumber.view')}}" class="nav-link">
                                        شماره صندلی
                                    </a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('report.student.custom.data.view') ? 'active' : '' }}">
                                    <a href="{{route('report.student.custom.data.view')}}" class="nav-link">
                                        فیلد گزارش
                                    </a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('report.get.debtor.students.view') ? 'active' : '' }}">
                                    <a href="{{route('report.get.debtor.students.view')}}" class="nav-link">
                                        دانش اموزان بدهکار
                                    </a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('report.get.deposits.view') ? 'active' : '' }}">
                                    <a href="{{route('report.get.deposits.view')}}" class="nav-link">
                                        واریزی ها
                                    </a>
                                </li>



                                <li class="nav-item {{ request()->routeIs('report.get.pays.view') ? 'active' : '' }}">
                                    <a href="{{route('report.get.pays.view')}}" class="nav-link">
                                        واریزی دانش‌آموان
                                    </a>
                                </li>

                                <li class="nav-item {{ request()->routeIs('report.sms') ? 'active' : '' }}">
                                    <a href="{{route('report.sms')}}" class="nav-link">
                                        پیامک ها
                                    </a>
                                </li>


                            </ul>
                        </div>
                    </li>







                    {{-- منوی حسابداری --}}
                    <li class="nav-item">
                        <a class="nav-link d-flex justify-content-between align-items-center"
                            data-bs-toggle="collapse"
                            href="#accounting"
                            role="button"
                            aria-expanded="{{ request()->routeIs('accounting.*') ? 'true' : 'false' }}"
                            aria-controls="accounting">
                            حسابداری
                            <span class="bi bi-chevron-down"></span>
                        </a>

                        <div class="collapse {{ request()->routeIs('accounting.*') ? 'show' : '' }}" id="accounting">
                            <ul class="nav flex-column  pe-4">
                                <li class="nav-item {{ request()->routeIs('accounting.register.percentage.view') ? 'active' : '' }}">
                                    <a href="{{ route('accounting.register.percentage.view') }}" class="nav-link">ثبت درصد</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('accounting.partners.view') ? 'active' : '' }}">
                                    <a href="{{ route('accounting.partners.view') }}" class="nav-link">شرکا</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('accounting.costs.view') ? 'active' : '' }}">
                                    <a href="{{ route('accounting.costs.view') }}" class="nav-link">هزینه ها</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('accounting.deposits.view') ? 'active' : '' }}">
                                    <a href="{{ route('accounting.deposits.view') }}" class="nav-link">واریزی ها</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('accounting.wallets.view') ? 'active' : '' }}">
                                    <a href="{{ route('accounting.wallets.view') }}" class="nav-link">کیف پول</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('accounting.profis.view') ? 'active' : '' }}">
                                    <a href="{{ route('accounting.profis.view') }}" class="nav-link">سود خالص </a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('report.get.checks.view') ? 'active' : '' }}">
                                    <a href="{{route('report.get.checks.view')}}" class="nav-link">
                                        چک ها
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>


                    {{-- منوی پیامکی --}}
                    <li class="nav-item">
                        <a class="nav-link d-flex justify-content-between align-items-center"
                            data-bs-toggle="collapse"
                            href="#smsMenu"
                            role="button"
                            aria-expanded="{{ request()->routeIs('sms.*') ? 'true' : 'false' }}"
                            aria-controls="smsMenu">
                            پیامک
                            <span class="bi bi-chevron-down"></span>
                        </a>

                        <div class="collapse {{ request()->routeIs('sms.*') ? 'show' : '' }}" id="smsMenu">
                            <ul class="nav flex-column pe-4">
                                <li class="nav-item {{ request()->routeIs('sms.createor.view') ? 'active' : '' }}">
                                    <a href="{{route('sms.createor.view')}}" class="nav-link">
                                        ساخت پیامک
                                    </a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('sms.send.view') ? 'active' : '' }}">
                                    <a href="{{route('sms.send.view')}}" class="nav-link">
                                        ارسال پیامک
                                    </a>
                                </li>



                            </ul>
                        </div>
                    </li>


                    {{-- منوی پشتیبان --}}
                    <li class="nav-item">
                        <a class="nav-link d-flex justify-content-between align-items-center"
                            data-bs-toggle="collapse"
                            href="#suportersMenu"
                            role="button"
                            aria-expanded="{{ request()->routeIs('supporters.*') ? 'true' : 'false' }}"
                            aria-controls="suportersMenu">
                            پشتیبان
                            <span class="bi bi-chevron-down"></span>
                        </a>
                        <div class="collapse {{ request()->routeIs('supporters.*') ? 'show' : '' }}" id="suportersMenu">
                            <ul class="nav flex-column pe-4">
                                <li class="nav-item {{ request()->routeIs('supporters.index') ? 'active' : '' }}">
                                    <a href="{{route('supporters.index')}}" class="nav-link">
                                        پشتبان ها
                                    </a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('suporter.store.suporter.view') ? 'active' : '' }}">
                                    <a href="{{route('suporter.store.suporter.view')}}" class="nav-link">
                                        افزودن پشتیبان
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>


                    {{-- منوی کاربران --}}
                    <li class="nav-item">
                        <a class="nav-link d-flex justify-content-between align-items-center"
                            data-bs-toggle="collapse"
                            href="#userMenu"
                            role="button"
                            aria-expanded="{{ request()->routeIs('supporters.*') ? 'true' : 'false' }}"
                            aria-controls="userMenu">
                            کاربران
                            <span class="bi bi-chevron-down"></span>
                        </a>
                        <div class="collapse {{ request()->routeIs('supporters.*') ? 'show' : '' }}" id="userMenu">
                            <ul class="nav flex-column pe-4">
                                <li class="nav-item {{ request()->routeIs('users.index') ? 'active' : '' }}">
                                    <a href="{{ route('users.index') }}" class="nav-link">کاربران</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('users.create') ? 'active' : '' }}">
                                    <a href="{{ route('users.create') }}" class="nav-link">ایجاد کاربر</a>
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

                                <li class="nav-item {{ request()->routeIs('advisors.*') ? 'active' : '' }}">
                                    <a href="{{ route('advisors.index') }}" class="nav-link">مشاوران</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('payment-cards.*') ? 'active' : '' }}">
                                    <a href="{{ route('payment-cards.index') }}" class="nav-link">حساب ها</a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('settings.editExamProduct.*') ? 'active' : '' }}">
                                    <a href="{{ route('settings.editExamProduct') }}" class="nav-link">
                                        محصول الزامی
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
                    @endif
                    {{-- منوی خروج --}}
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

                            <li class="nav-item {{ request()->routeIs('advisors.*') ? 'active' : '' }}">
                                <a href="{{ route('advisors.index') }}" class="nav-link">مشاوران</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('payment-cards.*') ? 'active' : '' }}">
                                <a href="{{ route('payment-cards.index') }}" class="nav-link">حساب ها</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('settings.editExamProduct.*') ? 'active' : '' }}">
                                <a href="{{ route('settings.editExamProduct') }}" class="nav-link">
                                    محصول الزامی
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

                {{-- منوی گزارشات --}}
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse"
                        href="#reportsMenu"
                        role="button"
                        aria-expanded="{{ request()->routeIs('seats.report.*') ? 'true' : 'false' }}"
                        aria-controls="reportsMenu">
                        گزارشات
                        <span class="bi bi-chevron-down"></span>
                    </a>

                    <div class="collapse {{ request()->routeIs('seats.report.*') ? 'show' : '' }}" id="reportsMenu">
                        <ul class="nav flex-column pe-4">
                            <li class="nav-item {{ request()->routeIs('seats.report.*') ? 'active' : '' }}">
                                <a href="" class="nav-link">
                                    شماره صندلی
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
                    <a href="{{ route('logout') }}" class="nav-link"
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.price-input').forEach(input => {
                // وقتی فرم ارسال میشه، کاماها حذف بشن تا عدد خام به بک‌اند بره
                input.form?.addEventListener('submit', function() {
                    input.value = input.value.replace(/,/g, '');
                });

                input.addEventListener('input', function(e) {
                    let cursorPos = e.target.selectionStart;
                    let value = e.target.value.replace(/,/g, '').replace(/\D/g, '');

                    if (value === '') {
                        e.target.value = '';
                        return;
                    }

                    // طول قبلی و جدید برای مدیریت کرسر
                    const prevLength = e.target.value.length;
                    e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    const newLength = e.target.value.length;

                    // حفظ موقعیت کرسر
                    e.target.selectionEnd = cursorPos + (newLength - prevLength);
                });

                // موقع لود اولیه اگر عددی هست، فرمتش کن
                if (input.value) {
                    input.value = input.value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                }
            });
        });


        function initPriceInputs(inputs) {
            inputs.forEach(input => {
                // حذف کاما قبل از ارسال فرم
                input.form?.addEventListener('submit', function() {
                    input.value = input.value.replace(/,/g, '');
                });

                input.addEventListener('input', function(e) {
                    let cursorPos = e.target.selectionStart;
                    let value = e.target.value.replace(/,/g, '').replace(/\D/g, '');
                    if (value === '') {
                        e.target.value = '';
                        return;
                    }
                    const prevLength = e.target.value.length;
                    e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    const newLength = e.target.value.length;
                    e.target.selectionEnd = cursorPos + (newLength - prevLength);
                });

                if (input.value) {
                    input.value = input.value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                }
            });
        }

        // 🔹 فراخوانی اولیه برای فیلدهایی که از قبل در صفحه بودن
        document.addEventListener('DOMContentLoaded', function() {
            initPriceInputs(document.querySelectorAll('.price-input'));
        });
    </script>


    @yield('scripts')
</body>

</html>