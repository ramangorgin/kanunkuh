<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'داشبورد')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Faveico meta tags -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <!-- Fonts -->
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">

    <!-- JalaliDatePicker - Load before Bootstrap to ensure proper styling -->
    <link rel="stylesheet" href="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">


    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
    <style>
        /* Jalali Datepicker Z-Index Fix - Must be before font rules */
        .jalali-datepicker { 
            z-index: 10000 !important; 
        }
        
        /* Ensure fonts apply to all elements - loaded after Bootstrap to override */
        /* This style block loads after Bootstrap CSS, so it will override Bootstrap's font settings */
        
        /* Headings */
        h1, h2, h3, h4, h5, h6,
        .h1, .h2, .h3, .h4, .h5, .h6,
        .navbar-brand {
            font-family: 'Modam', 'Peyda', sans-serif !important;
        }
        

        

        
        /* Buttons - all variants */
        .btn, 
        button, 
        [type="button"], 
        [type="submit"], 
        [type="reset"],
        .btn-primary,
        .btn-secondary,
        .btn-success,
        .btn-danger,
        .btn-warning,
        .btn-info,
        .btn-light,
        .btn-dark,
        .btn-outline-primary,
        .btn-outline-secondary,
        .btn-outline-success,
        .btn-outline-danger,
        .btn-outline-warning,
        .btn-outline-info,
        .btn-outline-light,
        .btn-outline-dark,
        .btn-sm,
        .btn-lg {
            font-family: 'Peyda', sans-serif !important;
        }
        
        /* Dashboard specific */
        .sidebar, .sidebar a, .sidebar button,
        .main-content,
        .active-link,
        #userpanel,
        .header-icon-btn,
        .sidebar-toggler-btn {
            font-family: 'Peyda', sans-serif !important;
        }
        
        /* Cards */
        .card, .card-header, .card-body, .card-footer, .card-title {
            font-family: 'Peyda', sans-serif !important;
        }
        
        /* Forms */
        input, 
        select, 
        textarea, 
        .form-control, 
        .form-select, 
        .form-label, 
        .form-check-label,
        .input-group-text {
            font-family: 'Peyda', sans-serif !important;
        }
        
        /* Other Bootstrap components */
        .alert, .badge, .breadcrumb,
        .table, .table th, .table td,
        .nav-link, .dropdown-menu, .dropdown-item,
        .list-group, .list-group-item {
            font-family: 'Peyda', sans-serif !important;
        }
        


        body {
            margin: 0;
            direction: rtl;
            background-color: #f9f9f9;
        }

        .dashboard-container {
            display: flex;
            flex-direction: row; 
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #ffffff;
            border-left: 1px solid #ddd;
            padding: 25px 20px;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.05);
        }

        .sidebar a {
            display: block;
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            color: #212529;
            text-decoration: none;
            background-color: #f8f9fa;
            transition: background-color 0.2s ease;
        }

        .sidebar a:hover {
            background-color: #e9ecef;
        }

        .main-content {
            flex: 1;
            padding: 30px;
        }

        .breadcrumb {
            background-color: #e2e6ea;
            padding: 10px 20px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .active-link {
            background-color: #e7f1ff;
            color: #0d6efd;
            font-weight: bold;
            border-radius: 5px;
        }

        #userpanel {
            background-color: #007bff;
            color: #ffffff;
            padding: 20px;
            justify-content: center;
            font-weight: 900;
        }

        /* Header Icon Styling */
        .header-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background-color: #f8f9fa; /* light gray bg */
            color: #222; /* Darker color for better contrast */
            transition: all 0.3s ease;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none; /* Remove underline from links */
        }
        .header-icon-btn:hover {
            background-color: #e7f1ff;
            color: #0d6efd;
            transform: scale(1.1);
        }
        .header-icon-btn i {
            font-size: 1.6rem;
            line-height: 1; /* Fix vertical alignment */
            display: flex;
        }

        /* Specific styling for sidebar toggle to match icons */
        .sidebar-toggler-btn {
            width: 55px;
            height: 55px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 1px solid #dee2e6;
            background-color: #fff;
            color: #222;
            transition: all 0.3s ease;
        }
        .sidebar-toggler-btn:hover {
            background-color: #e7f1ff;
            color: #0d6efd;
            border-color: #0d6efd;
            transform: scale(1.05);
        }
        .sidebar-toggler-btn i {
            font-size: 1.6rem;
            line-height: 1;
            display: flex;
        }

        /* Notification dot */
        .notification-dot {
            position: absolute;
            top: 8px;
            left: 8px;
            width: 8px;
            height: 8px;
            background-color: #dc3545;
            border-radius: 50%;
            border: 1px solid #fff;
        }

        @media (max-width: 991.98px) {
            .dashboard-container {
                flex-direction: column;
            }

            .sidebar {
                position: fixed;
                top: 0;
                right: -260px;
                width: 250px;
                height: 100%;
                z-index: 1050;
                background-color: #fff;
                transition: right 0.3s ease;
                box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
            }

            .sidebar.show {
                right: 0;
            }

            .main-content {
                padding: 20px 15px;
            }

            .sidebar a {
                font-size: 0.95rem;
                padding: 8px 12px;
            }

            #userpanel {
                font-size: 1rem;
                padding: 15px;
            }
        }

        @media (max-width: 575.98px) {
            body {
                font-size: 14px;
            }

            .navbar-brand img {
                height: 45px;
            }

            .nav-link {
                font-size: 13pt !important;
            }

            .sidebar {
                width: 220px;
            }

            .main-content {
                padding: 15px;
            }

            .breadcrumb {
                padding: 8px 12px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
@include('partials.preloader')
<header>
    <nav class="navbar bg-white shadow-sm py-3 px-3 px-lg-4">
        <div class="container-fluid d-flex align-items-center justify-content-between">

            <!-- Right Side: Logo + Desktop Title -->
            <div class="d-flex align-items-center gap-3">
                <a class="navbar-brand d-flex align-items-center m-0" href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="کانون کوه" style="height: 65px;">
                </a>
                <!-- Desktop Title -->
                <span class="d-none d-lg-block fw-bold fs-5 text-dark pe-3">
                    داشبورد کاربر - سامانه کانون کوه
                </span>
            </div>

            <!-- Left Side: Icons + Toggler -->
            <div class="d-flex align-items-center gap-2 gap-md-3">
                
                <!-- Notification Icon -->
                @include('partials.notification_dropdown', ['panel' => 'user'])

                <!-- Profile Icon -->
                <a href="{{ route('dashboard.profile.edit') }}" class="header-icon-btn" title="پروفایل">
                    <i class="bi bi-person-circle"></i>
                </a>

                <!-- Mobile Sidebar Toggler -->
                <button class="sidebar-toggler-btn d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>

            </div>

        </div>
    </nav>
</header>

<div class="dashboard-container">
    <aside class="sidebar">
        <h5 class="text-center" id="userpanel">پنل کاربری</h5>
        <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active-link' : '' }}">
            <i class="bi bi-house-door-fill me-2"></i> خانه داشبورد
        </a>
        <a href="{{ route('dashboard.profile.edit') }}" class="{{ request()->routeIs('dashboard.profile.edit') ? 'active-link' : '' }}">
            <i class="bi bi-person-lines-fill me-2"></i> ویرایش مشخصات
        </a>

        <a href="{{ route('dashboard.medicalRecord.edit') }}" class="{{ request()->routeIs('dashboard.medicalRecord.edit') ? 'active-link' : '' }}">
            <i class="bi bi-clipboard2-pulse-fill me-2"></i>  پرونده پزشکی  
        </a>

        <a href="{{ route('dashboard.educationalHistory.index') }}" class="{{ request()->routeIs('dashboard.educationalHistory.index') ? 'active-link' : '' }}">
            <i class="bi bi-book-fill me-2"></i>  سوابق آموزشی
        </a>

        <a href="{{ route('dashboard.payments.index') }}" class="{{ request()->routeIs('dashboard.payments.index') ? 'active-link' : '' }}">
            <i class="bi bi-credit-card-2-front-fill me-2"></i> پرداخت‌ها
        </a>

        <a href="{{ route('dashboard.programs.index') }}" class="{{ request()->routeIs('dashboard.programs.*') ? 'active-link' : '' }}">
            <i class="bi bi-calendar-event-fill me-2"></i> برنامه‌های من
        </a>

        <a href="{{ route('dashboard.courses.index') }}" class="{{ request()->routeIs('dashboard.courses.*') ? 'active-link' : '' }}">
            <i class="bi bi-book-fill me-2"></i> دوره‌های من
        </a>

        @auth
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit"
                    style="display:block; width:100%; text-align:right; padding:10px 15px; margin-bottom:10px; border-radius:5px; color:#212529; background-color:#f8f9fa; border:0;"
                    class="{{ request()->routeIs('logout') ? 'active-link' : '' }}">
                    <i class="bi bi-box-arrow-right me-2"></i> خروج
                </button>
            </form>
        @endauth
    </aside>

    <div class="main-content">
        @hasSection('breadcrumb')
        <div class="breadcrumb">
            @yield('breadcrumb')
        </div>
        @endif
        @yield('content')
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButton = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        toggleButton.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });
    });
</script>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script type="text/javascript" src="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.js"></script>

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
        if (node.nodeType === 3) { // text
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


<script>AOS.init();</script>

{{-- نقشه و تاریخ --}}
<script>
    // سال شمسی به فارسی
    (function(){
        try {
            const el = document.getElementById('shamsi-year');
            if (el) {
                const now = new Date();
                const year = new Intl.DateTimeFormat('fa-IR-u-nu-latn', { year: 'numeric' }).format(now);
                el.innerText = year.replace(/\d/g, d => '۰۱۲۳۴۵۶۷۸۹'[d]);
            }
        } catch (e) {}
    })();
    // نقشه (در صورت وجود المنت نقشه)
    (function(){
        const mapEl = document.getElementById('map');
        if (mapEl && window.L) {
            var map = L.map('map').setView([35.8232941, 50.9331318], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            L.marker([35.8232941, 50.9331318]).addTo(map);
        }
    })();
</script>    


@stack('scripts')
</body>
</html>
