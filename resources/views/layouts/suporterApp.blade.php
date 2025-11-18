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
                <h3 class="mb-4 fw-blod">پشتیبانی </h3>

                <ul class="nav flex-column p-0">

                    {{-- منوی دانش‌آموزان --}}
                    <li class="nav-item {{ request()->routeIs('suporter.filter.students') ? 'active' : '' }}">
                        <a href="{{ route('suporter.filter.students') }}" class="nav-link">فیلتر دانش‌آموزان</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('suporter.students') ? 'active' : '' }}">
                        <a href="{{ route('suporter.students') }}" class="nav-link">دانش‌آموزان من</a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('suporter.referential.students') ? 'active' : '' }}">
                        <a href="{{ route('suporter.referential.students') }}" class="nav-link">دانش‌آموزان ارجاعی</a>
                    </li>








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
                <div class="d-flex justify-content-end">
                    <span class="fs14 badge bg-dark"> {{auth()->user()->name}}</span>
                </div>
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