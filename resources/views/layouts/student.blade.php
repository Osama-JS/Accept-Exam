<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'نظام القبول الإلكتروني | ' . \App\Models\Setting::get('school_name', 'مدارس القيم الأهلية'))</title>
    
    <!-- Favicon and App Icons -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            /* ألوان الهوية البصرية */
            --primary:      #76b51b;
            --primary-dark: #629716;
            --primary-light:rgba(118, 181, 27, 0.1);
            --danger:       #c30e14;
            
            /* ألوان الواجهة العامة */
            --body-bg:      #ffffff;
            --card-bg:      #ffffff;
            --text-main:    #1e293b; /* لون داكن ناعم للنصوص */
            --text-muted:   #64748b;
            --border:       #e2e8f0;
            --radius:       12px;
            --shadow-sm:    0 2px 8px rgba(0,0,0,.04);
            --shadow-md:    0 8px 24px rgba(0,0,0,.06);
            --shadow-primary: 0 4px 14px rgba(118, 181, 27, 0.25);
        }
        
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body, input, select, textarea, button, .btn, .btn-admin { 
            font-family: 'Tajawal', 'Inter', 'Cairo', system-ui, -apple-system, sans-serif !important; 
            letter-spacing: -0.01em;
        }
        html, body {
            overflow-x: auto; /* السماح بالتمرير الأفقي إذا لزم الأمر */
            width: 100%;
            min-width: 421px; /* تثبيت عرض الموقع بحيث لا يقل عن 421 بيكسل */
        }
        body { 
            background: var(--body-bg); 
            color: var(--text-main); 
            min-height: 100vh; 
            -webkit-font-smoothing: antialiased; 
            display: flex;
            flex-direction: column;
        }

        /* ── Header (Premium S-Curve Design) ── */
        .site-header {
            background: #0f1011;
            height: 80px;
            display: flex; align-items: center; justify-content: center;
            position: sticky; top: 0; z-index: 100;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
            padding: 0;
            overflow: hidden; /* prevents SVG spill */
        }
        .header-inner {
            width: 100%;
            height: 100%;
            display: flex; align-items: center; justify-content: space-between;
        }

        /* Logo Tab Container (Right) */
        .site-logo-container {
            position: relative;
            height: 100%;
            display: flex;
            align-items: center;
            padding-right: 20px;
            padding-left: 60px; /* space for the S-curve sloped transition */
            z-index: 10;
        }
        .header-svg-bg {
            position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: -1; pointer-events: none;
            /* Stretch SVG to fill container completely */
        }
        .header-svg-bg svg {
            width: 100%; height: 100%; display: block;
        }
        
        .site-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .site-logo img {
            height: 60px; width: auto; object-fit: contain;
        }
        .logo-name { 
            display: flex; flex-direction: column; justify-content: center;
        }
        .logo-text-1 { font-size: 16px; font-weight: 900; color: #18191b; line-height: 1.2; }
        .logo-text-2 { font-size: 11px; font-weight: 800; color: #555; line-height: 1.2; }
        .logo-text-3 { font-size: 11px; font-weight: 800; color: #c30e14; line-height: 1.2; text-align: left; }

        /* Navigation Links (Center - On Dark Background) */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 32px;
            list-style: none;
            margin: 0;
            padding: 0;
            z-index: 5;
        }
        .nav-links a {
            color: #F9FAFB;
            text-decoration: none;
            font-size: 16px;
            font-weight: 700;
            transition: all .3s ease;
            padding: 8px 4px;
        }
        .nav-links a:hover { color: #65A30D; }
        .nav-links a.active { color: #76b51b; font-weight: 800; }

        /* Action Area (Left) */
        .header-actions {
            display: flex;
            align-items: center;
            padding-left: 20px;
            gap: 16px;
            z-index: 5;
        }
        
        .btn-admin {
            display: inline-flex; align-items: center; gap: 12px; 
            background: linear-gradient(280deg, #76b51b 35%, #ffffff 90%); 
            color: #ffffff; padding: 10px 24px; border-radius: 50px; 
            font-family: 'Cairo', sans-serif; font-size: 15px; font-weight: 800; 
            text-decoration: none; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25); 
            transition: all 0.3s ease;
        }
        .btn-admin .icon-circle {
            background: #ffffff; width: 26px; height: 26px; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }

        /* ── Main Content Area ── */
        .site-main { flex: 1; display: flex; flex-direction: column; }

        /* ── Footer ── */
        .site-footer {
            background: #f8fafc; border-top: 1px solid var(--border);
            text-align: center; padding: 24px 16px; 
            color: var(--text-muted); font-size: 14px; font-weight: 500;
        }

        /* ── Shared UI Components ── */
        .btn { 
            display: inline-flex; align-items: center; gap: 8px; 
            padding: 10px 24px; border-radius: 10px; 
            font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 700; 
            cursor: pointer; border: none; transition: all .2s; text-decoration: none; white-space: nowrap; 
        }
        .btn-primary { background: var(--primary); color: #fff; box-shadow: var(--shadow-primary); }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(118, 181, 27, 0.3); }
        
        .alert { padding: 14px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px; margin-bottom: 20px; border-right: 4px solid; }
        .alert-success { background: #f0fdf4; color: #166534; border-color: #22c55e; }
        .alert-danger { background: #fef2f2; color: #991b1b; border-color: #ef4444; }

        /* ── Responsive Design & Premium Mobile Nav Drawer ── */
        .mobile-menu-toggle {
            display: none;
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 28px;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s;
        }
        .mobile-menu-toggle:hover {
            color: var(--primary);
        }

        .mobile-nav-drawer {
            position: fixed;
            top: 0;
            right: -320px; /* Completely hidden off-screen */
            width: 280px;
            height: 100vh;
            background: rgba(15, 16, 17, 0.96);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            padding: 24px;
            transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: -10px 0 30px rgba(0,0,0,0.5);
            text-align: right;
        }
        .mobile-nav-drawer.show {
            right: 0;
        }
        
        .drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .drawer-title {
            color: #ffffff;
            font-size: 16px;
            font-weight: 800;
        }
        .drawer-close-btn {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            font-size: 20px;
            cursor: pointer;
            transition: color 0.2s;
        }
        .drawer-close-btn:hover {
            color: #ffffff;
        }
        
        .drawer-links {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .drawer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            padding: 12px 16px;
            border-radius: 10px;
            transition: all 0.2s;
            display: block;
        }
        .drawer-links a:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.04);
            transform: translateX(-4px);
        }
        .drawer-links a.active {
            color: #ffffff;
            background: var(--primary);
            box-shadow: var(--shadow-primary);
        }
        
        .drawer-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .drawer-backdrop.show {
            display: block;
            opacity: 1;
        }

        @media (max-width: 992px) {
            .nav-links { display: none !important; }
            .mobile-menu-toggle { display: block; }
            .btn-admin { display: none !important; }
        }

        @media (max-width: 768px) {
            .site-logo-container {
                padding-right: 15px;
                padding-left: 45px; 
            }
            .site-logo img {
                height: 50px;
            }
            .logo-text-1 { font-size: 14px; }
            .logo-text-2 { font-size: 9px; }
            .logo-text-3 { font-size: 9px; }
        }

        @media (max-width: 576px) {
            .site-header { height: 70px; }
            .site-logo-container {
                padding-right: 10px;
                padding-left: 30px; 
            }
            .site-logo { gap: 8px; }
            .site-logo img {
                height: 44px;
            }
            .logo-text-1 { font-size: 12px; }
            .logo-text-2 { font-size: 8px; }
            .logo-text-3 { font-size: 8px; }
            .header-actions { padding-left: 10px; left: 0; }
            .mobile-menu-toggle { font-size: 24px; }
        }

        

        /* إزالة قواعد ما دون 400 بيكسل لأننا قمنا بتثبيت عرض الموقع على 421 بيكسل كحد أدنى */
    </style>
    @stack('styles')
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        
        <!-- Logo Area with White S-curve Background Shape -->
        <div class="site-logo-container">
            <div class="header-svg-bg">
                <svg viewBox="0 0 350 80" preserveAspectRatio="none">
                    <path d="M 0 0 C 50 0, 30 80, 80 80 L 350 80 L 350 0 Z" fill="#ffffff" />
                </svg>
            </div>
            
            <a href="{{ route('home') ?? '/' }}" class="site-logo">
                <img src="{{ asset('images/logo2.png') }}" alt="مدارس القيم الأهلية">
                <div class="logo-name">
                   <small class="logo-text-1">مدرسة القيم الأهلية</small>
                   <small class="logo-text-2">ALQIYAM CIVIL SCHOOL</small>
                   <small class="logo-text-3">.. تعليمنا قيم</small>
                </div>
            </a>
        </div>

        <!-- Navigation Links in Center Dark Section -->
        <div class="nav-links">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">الرئيسية</a>
            <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">من نحن</a>
            <a href="#contact">اتصل بنا</a>
        </div>

        <!-- Action Area -->
        <div class="header-actions">
            <a href="{{ route('admin.dashboard') }}" class="btn-admin">
                <span>دخول الإدارة</span>
                <span class="icon-circle">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="#76b51b" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                    </svg>
                </span>
            </a>
            <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="القائمة"><i class="bi bi-list"></i></button>
        </div>

    </div>
</header>

<div class="mobile-nav-drawer" id="mobileNavDrawer">
    <div class="drawer-header">
        <span class="drawer-title">القائمة الرئيسية</span>
        <button class="drawer-close-btn" id="mobileDrawerClose" title="إغلاق القائمة"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="drawer-links" style="flex: 1;">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">الرئيسية</a>
        <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">من نحن</a>
        <a href="#contact">اتصل بنا</a>
    </div>
    <div class="drawer-footer" style="margin-top: auto; padding-top: 16px; border-top: 1px solid rgba(255, 255, 255, 0.08);">
        <a href="{{ route('admin.dashboard') }}" class="btn-admin-mobile" style="display: flex; align-items: center; justify-content: center; gap: 10px; background: var(--primary); color: #ffffff; padding: 12px; border-radius: 10px; font-weight: 800; font-size: 15px; text-decoration: none; box-shadow: var(--shadow-primary); transition: all 0.2s;">
            <i class="bi bi-shield-lock-fill"></i>
            <span>دخول الإدارة</span>
        </a>
    </div>
</div>
<div class="drawer-backdrop" id="drawerBackdrop"></div>

<main class="site-main">
    @if(session('error'))
        <div style="width: 100%; max-width: 1200px; margin: 24px auto; padding: 0 24px;">
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
        </div>
    @endif
    
    @yield('content')
</main>

<footer class="site-footer">
    <p>© {{ date('Y') }} {{ \App\Models\Setting::get('school_name', 'مدارس القيم الأهلية') }} &mdash; جميع الحقوق محفوظة</p>
</footer>

@stack('scripts')
<script>
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileNavDrawer = document.getElementById('mobileNavDrawer');
    const mobileDrawerClose = document.getElementById('mobileDrawerClose');
    const drawerBackdrop = document.getElementById('drawerBackdrop');

    function openDrawer() {
        if(mobileNavDrawer) mobileNavDrawer.classList.add('show');
        if(drawerBackdrop) {
            drawerBackdrop.classList.add('show');
            drawerBackdrop.style.display = 'block';
        }
    }

    function closeDrawer() {
        if(mobileNavDrawer) mobileNavDrawer.classList.remove('show');
        if(drawerBackdrop) {
            drawerBackdrop.classList.remove('show');
            setTimeout(() => {
                if(!mobileNavDrawer.classList.contains('show')) {
                    drawerBackdrop.style.display = 'none';
                }
            }, 300);
        }
    }

    if(mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', openDrawer);
    }
    if(mobileDrawerClose) {
        mobileDrawerClose.addEventListener('click', closeDrawer);
    }
    if(drawerBackdrop) {
        drawerBackdrop.addEventListener('click', closeDrawer);
    }

    // ── إشعارات الطالب التلقائية الفاخرة بالـ SweetAlert2 ──
    @if(session('success'))
        Swal.fire({
            title: 'عملية ناجحة!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#76b51b',
            confirmButtonText: 'ممتاز',
            timer: 4000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'تنبيه!',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonColor: '#c30e14',
            confirmButtonText: 'موافق'
        });
    @endif
</script>
</body>
</html>