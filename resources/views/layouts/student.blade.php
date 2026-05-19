<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'نظام القبول الإلكتروني | مدارس القيم الأهلية')</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            overflow: hidden;
        }
        .header-inner {
            width: 100%;
            height: 100%;
            max-width: 100%;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 40px 0 0; /* 0 on the right to let the logo tab touch the viewport edge */
            margin-inline-end:auto;
        }

        /* Logo Tab Container (Right) */
        .site-logo-container {
            position: relative;
            height: 80px;
            display: flex;
            align-items: center;
            padding-right: 48px;
            padding-left: 80px; /* space for the S-curve sloped transition */
            z-index: 10;
        }
        .site-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }
        .logo-icon-svg {
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .logo-name { 
            font-weight: 900; 
            font-size: 20px; 
            line-height: 1.2; 
            color: #18191b; 
        }
        .logo-name small { 
            display: block; 
            font-size: 13px; 
            font-weight: 800; 
            color: #99c110;; 
            margin-top: 2px; 
        }

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
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 16px;
            font-weight: 700;
            transition: all .3s ease;
            padding: 8px 4px;
        }
        
        .nav-links a:hover {
            color: #ffffff;
        }
        
        .nav-links a.active {
            color: #76b51b;
            font-weight: 800;
        }

        /* Action Area (Left) */
        .header-actions {
            display: flex;
            align-items: center;
            z-index: 5;
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

        /* ── Responsive Design ── */
        @media (max-width: 992px) {
            .nav-links { display: none; /* إخفاء الروابط في الجوال (يمكنك إضافة قائمة جانبية لاحقاً) */ }
        }
        @media (max-width: 768px) {
            .site-header { padding: 0 16px; height: 70px; padding-inline-start:inherit}
            .logo-name { font-size: 14px; }
            .logo-name small { font-size: 11px; }
            .btn { padding: 8px 16px; font-size: 13px; }
        }
    </style>
    @stack('styles')
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        
        <!-- Logo Area with White S-curve Background Shape -->
        <div class="site-logo-container">
            <!-- Smooth S-curve SVG Background -->
            <div style="position: absolute; top: 0; right: -40px; bottom: 0; left: 0; z-index: -1; pointer-events: none;">
                <svg width="100%" height="100%" viewBox="0 0 350 80" preserveAspectRatio="none" style="display: block;">
                    <path d="M 0 0 C 50 0, 30 80, 80 80 L 350 80 L 350 0 Z" fill="#ffffff" />
                </svg>
            </div>
            
            <a href="{{ route('home') ?? '/' }}" class="site-logo" style="display: flex; align-items: center; justify-content: center; height: 100%; padding-top: 4px; padding-bottom: 4px; margin-left: 20px; margin-right: -60px; text-align: justify;">
                <img src="{{ asset('images/logo2.png') }}" alt="مدارس القيم الأهلية" style="height: 68px; width: auto; object-fit: contain; max-width: 240px;">
                <div class="logo-name">
                   <small style="font-size: medium;"> مدرسة القيم الأهلية</small>
                    <small style="font-size: medium;">ALQIYAM CIVIL SCHOOL</small>
                    <small style="color: #c30e14; text-align: end; font-size: small;">.. تعليمنا قيم</small>
                </div>
            </a>
        </div>

        <!-- Navigation Links in Center Dark Section -->
        <div class="nav-links">
            <a href="{{ route('home') ?? '/' }}" class="{{ request()->is('/') ? 'active' : '' }}">الرئيسية</a>
            <a href="#about">من نحن</a>
            <a href="#programs">البرامج</a>
            <a href="#contact">اتصل بنا</a>
        </div>

        <!-- Admin Capsule Button with Location Icon -->
        <div class="header-actions">
            <a href="{{ route('admin.dashboard') }}" class="btn-admin" style="display: inline-flex; align-items: center; gap: 12px; background: linear-gradient(280deg, #76b51b 35%, #ffffff 90%); color: #ffffff; padding: 10px 24px; border-radius: 50px; font-family: 'Cairo', sans-serif; font-size: 15px; font-weight: 800; text-decoration: none; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25); transition: all 0.3s ease;">
                <span>دخول الإدارة</span>
                <span style="background: #ffffff; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-right: 4px;">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="#76b51b" xmlns="http://www.w3.org/2000/svg" style="display: block;">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                    </svg>
                </span>
            </a>
        </div>

    </div>
</header>

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
</body>
</html>