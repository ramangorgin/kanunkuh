{{-- Base layout for admin pages. --}}
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'پنل مدیریت')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    {{-- Jalali Datepicker --}}
    <link rel="stylesheet" href="{{ asset('vendor/jalali-datepicker/dist/jalalidatepicker.min.css') }}"></script>

    {{-- App CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">

    {{-- Bootstrap & Icons --}}
    <link rel="stylesheet" href="{{ asset('vendor/cdn/bootstrap/5.3.2/css/bootstrap.rtl.min.css') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/cdn/bootstrap-icons/1.10.5/font/bootstrap-icons.css') }}"></script>
            
    {{-- SweetAlert & Animate --}}
    <link rel="stylesheet" href="{{ asset('vendor/cdn/sweetalert2/11/sweetalert2.min.css') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/cdn/animate.css/4.1.1/animate.min.css') }}"></script>

    {{-- DataTables --}}
    <link rel="stylesheet" href="{{ asset('vendor/cdn/datatables/1.13.6/dataTables.bootstrap5.min.css') }}"></script>

    {{-- Select2 --}}
    <link rel="stylesheet" href="{{ asset('vendor/cdn/select2/4.1.0-rc.0/select2.min.css') }}"></script>

    <link rel="stylesheet" href="{{ asset('vendor/cdn/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css') }}"></script>

    {{-- FilePond --}}
    <link rel="stylesheet" href="{{ asset('vendor/cdn/filepond/4/filepond.min.css') }}"></script>

    {{-- Toastr --}}           
    <link rel="stylesheet" href="{{ asset('vendor/cdn/toastr/latest/toastr.min.css') }}"></script>

    {{-- Neshan --}}           
    <link rel="stylesheet" href="https://static.neshan.org/sdk/leaflet/v1.9.4/neshan-sdk/v1.0.8/index.css"/>


    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Peyda', sans-serif !important;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: #fff;
            color: #333;
            height: 100vh;
            position: fixed;
            top: 0;
            right: 0;
            overflow-y: auto;
            overflow-x: hidden;
            transition: all 0.3s;
            border-left: 1px solid #e0e0e0;
            z-index: 1040;
            box-shadow: -2px 0 10px rgba(0,0,0,0.05);
        }

        .sidebar .brand-section {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .sidebar .menu {
            padding: 15px;
            padding-bottom: 30px; 
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .sidebar a {
            color: #555;
            text-decoration: none;
            display: block;
            padding: 12px 15px;
            transition: all 0.2s;
            border-radius: 8px;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: #e7f1ff;
            color: #0d6efd;
        }

        .sidebar .menu-header {
            font-weight: 700;
            font-size: 0.85rem;
            color: #999;
            padding: 15px 15px 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Topbar */
        .topbar {
            position: fixed;
            top: 0;
            right: 260px;
            left: 0;
            background-color: #fff;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            z-index: 1030;
            transition: right 0.3s;
        }

        /* Header Icon Styling (Synced with User Panel) */
        .header-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #f8f9fa;
            color: #444;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            font-size: 1.4rem;
        }
        .header-icon-btn:hover {
            background-color: #e7f1ff;
            color: #0d6efd;
            transform: scale(1.1);
        }
        .header-icon-btn i {
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .notification-dot {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 10px;
            height: 10px;
            background-color: #dc3545;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .main-content {
            margin-right: 260px;
            margin-top: 70px;
            padding: 30px;
            transition: all 0.3s;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                right: -260px;
            }
            .sidebar.show {
                right: 0;
            }
            .topbar {
                right: 0;
                padding: 0 15px;
            }
            .main-content {
                margin-right: 0;
                padding: 20px 15px;
            }
        }

        .sidebar-toggle {
            display: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            border: 1px solid #dee2e6;
            background: white;
            color: #333;
        }
        
        @media (max-width: 991.98px) {
            .sidebar-toggle {
                display: inline-flex;
            }
        }

        /* Jalali Datepicker Z-Index Fix */
        .jalali-datepicker { z-index: 10000 !important; }
        
        /* Responsive Tables for Admin Panel */
        @media (max-width: 768px) {
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                position: relative;
            }
            
            .table-responsive table {
                min-width: 600px;
                font-size: 0.8rem;
            }
            
            .table-responsive th,
            .table-responsive td {
                padding: 8px 10px !important;
                white-space: nowrap;
            }
            
            .dataTables_wrapper .table-responsive {
                margin: 0 -15px;
                padding: 0 15px;
            }
        }
        
        @media (max-width: 576px) {
            .table-responsive table {
                min-width: 500px;
                font-size: 0.75rem;
            }
            
            .table-responsive th,
            .table-responsive td {
                padding: 6px 8px !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    @include('partials.preloader')

    {{-- Sidebar --}}
    <nav class="sidebar shadow-sm">
        <div class="brand-section">
            <a href="{{ route('home') }}" class="text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 50px;">
                <h6 class="mt-2 fw-bold text-dark">پنل مدیریت کانون</h6>
            </a>
        </div>

        <div class="menu">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> داشبورد
            </a>

            <div class="menu-header">کاربران و اعضا</div>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i> لیست کاربران
            </a>
            <a href="{{ route('admin.users.create') }}" class="{{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                <i class="bi bi-person-plus me-2"></i> ثبت نام کاربر جدید
            </a>
            <a href="{{ route('admin.memberships.pending') }}" class="{{ request()->routeIs('admin.memberships.pending') ? 'active' : '' }}">
                <i class="bi bi-hourglass-split me-2"></i> بررسی عضویت‌ها
            </a>

            <div class="menu-header">برنامه‌ها</div>
            <a href="{{ route('admin.programs.index') }}" class="{{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-event me-2"></i> لیست برنامه‌ها
            </a>
            <a href="{{ route('admin.programs.create') }}" class="{{ request()->routeIs('admin.programs.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle me-2"></i> ایجاد برنامه جدید
            </a>

            <div class="menu-header">گزارش‌های برنامه</div>
            <a href="{{ route('admin.program_reports.index') }}" class="{{ request()->routeIs('admin.program_reports.index') ? 'active' : '' }}">
                <i class="bi bi-file-text me-2"></i> لیست گزارش‌ها
            </a>
            <a href="{{ route('admin.program_reports.create') }}" class="{{ request()->routeIs('admin.program_reports.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle me-2"></i> ایجاد گزارش جدید
            </a>

            <div class="menu-header">دوره‌ها</div>
            <a href="{{ route('admin.courses.index') }}" class="{{ request()->routeIs('admin.courses.index') ? 'active' : '' }}">
                <i class="bi bi-book me-2"></i> لیست دوره‌ها
            </a>
            <a href="{{ route('admin.courses.create') }}" class="{{ request()->routeIs('admin.courses.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle me-2"></i> ایجاد دوره جدید
            </a>

            <div class="menu-header">وبلاگ</div>
            <a href="{{ route('admin.posts.index') }}" class="{{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text me-2"></i> مدیریت پست‌ها
            </a>
            <a href="{{ route('admin.posts.create') }}" class="{{ request()->routeIs('admin.posts.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle me-2"></i> پست جدید
            </a>

            <div class="menu-header">امور مالی</div>
            <a href="{{ route('admin.payments.index') }}" class="{{ request()->routeIs('admin.payments.index') ? 'active' : '' }}">
                <i class="bi bi-credit-card me-2"></i> مدیریت پرداخت‌ها
            </a>

            <div class="menu-header">پشتیبانی</div>
            <a href="{{ route('admin.tickets.index') }}" class="{{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                <i class="bi bi-life-preserver me-2"></i> تیکت‌ها
            </a>

            <div class="menu-header">تنظیمات</div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn w-100 text-start p-0">
                    <a href="#" onclick="this.parentNode.click(); return false;" class="text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> خروج
                    </a>
                </button>
            </form>
        </div>
    </nav>

    {{-- Topbar --}}
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <h5 class="m-0 fw-bold text-secondary d-none d-md-block">
                @yield('title')
            </h5>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Notification -->
            @include('partials.notification_dropdown', ['panel' => 'admin'])

            <!-- Profile Link (Admin) -->
            <a href="#" class="header-icon-btn" title="پروفایل مدیر">
                <i class="bi bi-person-gear"></i>
            </a>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="main-content">
        @yield('content')
    </main>

    {{-- JS and Plugins --}}
    
        <script src="{{ asset('vendor/cdn/jquery/3.6.0/jquery.min.js') }}"></script> 

        <script src="{{ asset('vendor/cdn/bootstrap/5.3.2/js/bootstrap.bundle.min.js') }}"></script>

        <script src="{{ asset('vendor/cdn/sweetalert2/11/sweetalert2.min.js') }}"></script>
            
        <script src="{{ asset('vendor/cdn/datatables/1.13.6/jquery.dataTables.min.js') }}"></script>

        <script src="{{ asset('vendor/cdn/datatables/1.13.6/dataTables.bootstrap5.min.js') }}"></script>

        <script src="{{ asset('vendor/jalali-datepicker/dist/jalalidatepicker.min.js') }}"></script>

        <script src="{{ asset('vendor/cdn/select2/4.1.0-rc.0/select2.min.js') }}"></script>

        <script src="{{ asset('vendor/cdn/filepond/4/filepond.min.js') }}"></script>

        <script src="{{ asset('vendor/cdn/toastr/latest/toastr.min.js') }}"></script>

        <script src="https://static.neshan.org/sdk/leaflet/v1.9.4/neshan-sdk/v1.0.8/index.js"></script>

 
    @stack('modals')

    <script>
        // Sidebar Toggle
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.getElementById('sidebarToggle');
            const content = document.querySelector('.main-content');
            const topbar = document.querySelector('.topbar');

            toggle?.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        });

        // Initialize Jalali Datepicker
        jalaliDatepicker.startWatch({ 
            persianDigits: true,
            zIndex: 3000
        });

        // Toastr Options
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-left",
            "timeOut": "5000",
            "rtl": true
        };

        // Display Session Messages
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif

        // Digit Converter Helpers
        (function(){
            // Persian/Arabic to English for Inputs
            const map = {'۰':'0','۱':'1','۲':'2','۳':'3','۴':'4','۵':'5','۶':'6','۷':'7','۸':'8','۹':'9',
                         '٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9'};
            const pattern = /[۰-۹٠-٩]/g;
            function normalize(str){ return str.replace(pattern, d => map[d] || d); }
            function bind(el){
                el.addEventListener('input', e => {
                    const v = e.target.value;
                    if (pattern.test(v)) {
                        const caret = e.target.selectionStart;
                        e.target.value = normalize(v);
                        if(caret !== null) e.target.setSelectionRange(caret, caret);
                    }
                });
            }
            
            // English to Persian for Display Text
            const faMap = {'0':'۰','1':'۱','2':'۲','3':'۳','4':'۴','5':'۵','6':'۶','7':'۷','8':'۸','9':'۹'};
            function toFa(str){ return String(str).replace(/\d/g, d => faMap[d] || d); }
            function walk(node){
                if(node.nodeType === 3 && node.nodeValue) { // Text node
                    if(!node.parentElement.matches('input, textarea, script, style, code, pre')) {
                        node.nodeValue = toFa(node.nodeValue);
                    }
                } else if(node.nodeType === 1) { // Element
                    node.childNodes.forEach(walk);
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                // Bind inputs
                document.querySelectorAll('input, textarea').forEach(bind);
                new MutationObserver(muts => {
                    muts.forEach(m => m.addedNodes.forEach(n => {
                        if(n.nodeType===1){
                            if(n.matches('input, textarea')) bind(n);
                            n.querySelectorAll?.('input, textarea').forEach(bind);
                        }
                    }));
                }).observe(document.body, {childList:true, subtree:true});

                // Convert display digits
                walk(document.body);
            });
        })();

        // Initialize FilePond globally for elements with class 'filepond'
        document.addEventListener('DOMContentLoaded', function() {
            FilePond.setOptions({
                labelIdle: 'فایل را بکشید و رها کنید یا <span class="filepond--label-action">انتخاب کنید</span>',
                credits: false,
                storeAsFile: true,
            });
            const ponds = document.querySelectorAll('.filepond');
            ponds.forEach(pond => FilePond.create(pond));
        });
    </script>

    @stack('scripts')

</body>
</html>
