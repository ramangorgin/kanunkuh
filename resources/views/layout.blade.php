<!DOCTYPE html>
<html lang="fa" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'کانون کوه')</title>
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet"
              onerror="this.onerror=null;this.href='{{ asset('vendor/cdn/bootstrap/5.3.2/css/bootstrap.rtl.min.css') }}'">
          <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"
              onerror="this.onerror=null;this.href='{{ asset('vendor/cdn/bootstrap-icons/1.10.5/font/bootstrap-icons.css') }}'">
          <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
              onerror="this.onerror=null;this.href='{{ asset('vendor/cdn/leaflet/1.9.4/leaflet.css') }}'" />
        <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
          <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
              onerror="this.onerror=null;this.href='{{ asset('vendor/cdn/select2/4.1.0-rc.0/select2.min.css') }}'" />
        <link rel="stylesheet" href="{{ asset('vendor/jalali-datepicker/dist/jalalidatepicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @arcaptchaScript
        @stack('styles')

        <style>
            .ltr-footer{ direction:ltr !important; text-align:left !important; }
            .ltr-footer li{
                display:flex; align-items:center; justify-content:flex-start; gap:.5rem;
            }
            .ltr-footer li span, .ltr-footer li a{ direction:ltr !important; }
            .ltr-footer i{ font-size:1rem; }
        </style>
    </head>
    <body>
        @include('partials.preloader')
        <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
                <div class="container">
                    {{-- ستون راست (لوگو) --}}
                    <a class="navbar-brand order-1 order-lg-1" href="{{ route('home') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="کانون کوه" style="height: 60px;">
                    </a>

                    {{-- ستون وسط (منو اصلی - فقط در لپ‌تاپ) --}}
                    <div class="d-none d-lg-block order-2 w-100 text-center">
                        <ul class="navbar-nav justify-content-center flex-row gap-4">
                            <li class="nav-item"><a class="nav-link" href="{{ route('home') }}" style="font-size: 15pt;">خانه</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('programs.archive') }}" style="font-size: 15pt;">برنامه‌ها</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('courses.archive') }}" style="font-size: 15pt;">دوره‌ها</a></li>
                            @if(Route::has('blog.index'))
                                <li class="nav-item"><a class="nav-link" href="{{ route('blog.index') }}" style="font-size: 15pt;">بلاگ</a></li>
                            @endif
                        </ul>
                    </div>

                    {{-- ستون چپ (ورود/ثبت‌نام - فقط در لپ‌تاپ) --}}
                    <div class="d-none d-lg-block order-3">
                        @auth
                        <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary py-3 px-5">داشبورد</a>
                        @else
                        <div class="row">
                            <div class="col-md-6"><a href="{{ route('auth.phone') }}" class="btn btn-primary ms-2" style="width: 150px;"> ورود | ثبت‌نام </a></div>
                        </div>
                        @endauth
                    </div>

                    {{-- همبرگر موبایل --}}
                    <button class="navbar-toggler d-lg-none order-2" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </nav>

            {{-- موبایل منو --}}
            <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title">منو</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">خانه</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('programs.archive') }}">برنامه‌ها</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('courses.archive') }}">دوره‌ها</a></li>
                        @if(Route::has('blog.index'))
                            <li class="nav-item"><a class="nav-link" href="{{ route('blog.index') }}">بلاگ</a></li>
                        @endif
                    </ul>

                    <hr>

                    @auth
                        <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary w-100 mb-2">ورود به داشبورد</a>
                    @else
                        <a href="{{ route('auth.phone') }}" class="btn btn-primary w-100 mb-2">ورود | ثبت‌نام</a>
                    @endauth
                </div>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="bg-dark text-light pt-5 mt-0 border-top">
        <div class="container">
            <div class="row gy-4">

                {{-- ستون ۱: آدرس و نقشه --}}
                <div class="col-md-3">
                    <h5 class="fw-bold text-center mb-3">آدرس</h5>
                    <div id="map" style="height: 220px; border-radius: 8px; margin-bottom: 1rem;"></div>
                    <p class="mb-1"><i class="bi bi-geo-alt-fill me-2"></i>کرج، گلشهر، بلوار گلزار غربی، خیابان یاس، ساختمان سینا، طبقه سوم، واحد شش</p>
                    <p><i class="bi bi-mailbox me-2"></i>کد پستی: ۳۱۹۸۷۱۷۸۱۵</p>
                </div>

                {{-- ستون ۲: تماس با ما --}}
                <div class="col-md-3">
                    <h5 class="fw-bold mb-3 text-center">تماس با ما</h5>
                    <ul class="list-unstyled ps-1 fs-6 ltr-footer">
                        <li class="mb-5 mt-5">
                            <i class="bi bi-telephone-fill"></i>
                            <span>۰۲۶۳۳۵۰۸۰۱۸</span>
                        </li>
                        <li class="mb-5">
                            <i class="bi bi-phone-fill"></i>
                            <span>۰۹۱۰۶۸۷۱۱۸۵</span>
                        </li>
                        <li class="mb-5">
                            <i class="bi bi-envelope-fill"></i>
                            <a href="#" class="text-info text-decoration-none">رایانامه</a>
                        </li>
                        <li class="mb-5">
                            <i class="bi bi-instagram"></i>
                            <a href="https://instagram.com/kanoonkooh" class="text-info text-decoration-none" target="_blank">صفحه اینستاگرام</a>
                        </li>
                        <li>
                            <i class="bi bi-telegram"></i>
                            <a href="https://t.me/kanoonkooh" class="text-info text-decoration-none" target="_blank">کانال تلگرام</a>
                        </li>
                    </ul>
                </div>


                {{-- ستون ۳: لینک‌ها --}}
                <div class="col-md-3 text-center">
                    <h5 class="fw-bold mb-3">لینک‌های مهم</h5>
                    <ul class="list-unstyled fs-6 m-0 p-0 d-flex flex-column align-items-center">
                        <li class="mb-4 mt-4 w-100"><a href="{{ route('courses.archive') }}" class="text-light text-decoration-none d-inline-block w-100">آخرین دوره‌ها</a></li>
                        <li class="mb-4 w-100"><a href="{{ route('programs.archive') }}" class="text-light text-decoration-none d-inline-block w-100">آخرین برنامه‌ها</a></li>
                        <li class="w-100"><a href="{{ route('conditions') }}" class="text-light text-decoration-none d-inline-block w-100">شرایط عضویت</a></li>
                    </ul>
                </div>

                {{-- ستون ۴: درباره باشگاه --}}
                <div class="col-md-3">
                    <h5 class="fw-bold text-center mb-3">درباره باشگاه</h5>
                    <img src="{{ asset('images/logo-blue.png') }}" alt="کانون کوه" class="mb-3 d-block mx-auto" style="width: 100%;">
                    <p class="text-justify small" style="text-align: justify;">
                        در اواخر دهه ۸۰، جمعی از بازنشستگان علاقه‌مند به کوهنوردی گروهی منسجم تشکیل دادند که بعدها به باشگاه کوهنوردی کانون کوه تبدیل شد.
                        این باشگاه با برگزاری دوره‌ها و برنامه‌های منظم، به یکی از فعال‌ترین باشگاه‌های کوهنوردی، طبیعت‌گردی و حامی محیط‌زیست در البرز تبدیل شده است.
                    </p>
                </div>

            </div>

            <hr class="border-light my-4">

            {{-- سطر پایانی --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pb-3">
            <div>
                <span>© <span id="shamsi-year" class="persian-number"></span> تمامی حقوق برای باشگاه کانون کوه محفوظ است.</span>
            </div>
                <div class="text-center text-md-end">
                    طراحی شده با ❤️ توسط
                    <a href="https://linkedin.com/in/ramangorgin" target="_blank" class="text-info text-decoration-none fw-bold">رامان گرگین پاوه</a>
                </div>
            </div>
        </div>
    </footer>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            onerror="this.onerror=null;this.remove();var s=document.createElement('script');s.src='{{ asset('vendor/cdn/jquery/3.6.0/jquery.min.js') }}';document.head.appendChild(s);"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
            onerror="this.onerror=null;this.remove();var s=document.createElement('script');s.src='{{ asset('vendor/cdn/bootstrap/5.3.0/js/bootstrap.bundle.min.js') }}';document.head.appendChild(s);"></script>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            onerror="this.onerror=null;this.remove();var s=document.createElement('script');s.src='{{ asset('vendor/cdn/leaflet/1.9.4/leaflet.js') }}';document.head.appendChild(s);"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"
            onerror="this.onerror=null;this.remove();var s=document.createElement('script');s.src='{{ asset('vendor/cdn/select2/4.1.0-rc.0/select2.min.js') }}';document.head.appendChild(s);"></script>
        <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"
            onerror="this.onerror=null;this.remove();var s=document.createElement('script');s.src='{{ asset('vendor/cdn/ckeditor5/41.3.1/classic/ckeditor.js') }}';document.head.appendChild(s);"></script>
        {{-- نقشه و تاریخ --}}
        <script>
            // سال شمسی به فارسی
            try {
                const date = new Date();
                const year = new Intl.DateTimeFormat('fa-IR-u-nu-latn', { year: 'numeric' }).format(date);
                document.getElementById('shamsi-year').innerText = year.replace(/\d/g, d => '۰۱۲۳۴۵۶۷۸۹'[d]);
            } catch (e) {}

            // نقشه
            var map = L.map('map').setView([35.8232941, 50.9331318], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            L.marker([35.8232941, 50.9331318]).addTo(map);
        </script>

    <script src="{{ asset('vendor/jalali-datepicker/dist/jalalidatepicker.min.js') }}"></script>
    <script src="{{ asset('js/jalali-datepicker-init.js') }}"></script>

    @stack('modals')

    <script>
    // Normalize Persian/Arabic digits to English in inputs/textareas
    (function(){
        const map = {'۰':'0','۱':'1','۲':'2','۳':'3','۴':'4','۵':'5','۶':'6','۷':'7','۸':'8','۹':'9',
                     '٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9'};
        const pattern = /[۰-۹٠-٩]/g;
        function normalize(str){ return String(str).replace(pattern, d => map[d] || d); }
        function bind(el){
            el.addEventListener('input', e => {
                const v = e.target.value;
                if (pattern.test(v)) {
                    const start = e.target.selectionStart, end = e.target.selectionEnd;
                    e.target.value = normalize(v);
                    if (start != null && end != null) e.target.setSelectionRange(start, end);
                }
            });
        }
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('input, textarea').forEach(bind);
            new MutationObserver(muts => muts.forEach(m => m.addedNodes.forEach(n => {
                if (n.nodeType === 1) {
                    if (n.matches && n.matches('input,textarea')) bind(n);
                    n.querySelectorAll?.('input,textarea').forEach(bind);
                }
            }))).observe(document.body, {childList:true, subtree:true});
        });
    })();

    // Convert English digits to Persian in rendered text (not in form fields)
    (function(){
        const map = {'0':'۰','1':'۱','2':'۲','3':'۳','4':'۴','5':'۵','6':'۶','7':'۷','8':'۸','9':'۹'};
        function toFa(str){ return String(str).replace(/\d/g, d => map[d] || d); }
        function shouldSkip(node){
            return node.closest && node.closest('input,textarea,script,style,pre,code');
        }
        function walk(node){
            if (node.nodeType === 3) {
                if (!shouldSkip(node)) node.nodeValue = toFa(node.nodeValue);
                return;
            }
            if (node.nodeType === 1 && !['INPUT','TEXTAREA','SCRIPT','STYLE'].includes(node.tagName)) {
                node.childNodes.forEach(walk);
            }
        }
        document.addEventListener('DOMContentLoaded', () => walk(document.body));
    })();
    </script>

        @stack('scripts')
    </body>
</html>
