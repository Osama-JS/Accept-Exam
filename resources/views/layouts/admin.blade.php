<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') | مدارس القيم الأهلية</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            /* ألوان الهوية البصرية من الشعار */
            --primary: #76b51b;
            --primary-dark: #629716;
            --primary-light: rgba(118, 181, 27, 0.08);
            --danger: #c30e14;
            --danger-hover: #a10b10;
            
            /* ألوان لوحة التحكم (النمط الداكن المتميز) */
            --sidebar-bg: #0f172a;
            --sidebar-text: #94a3b8;
            --sidebar-hover: rgba(255, 255, 255, 0.04);
            --sidebar-border: rgba(255, 255, 255, 0.06);
            
            /* ألوان المحتوى المتميز */
            --body-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            
            /* الأبعاد والظلال الاحترافية */
            --sidebar-width: 280px;
            --topbar-h: 75px;
            --radius: 16px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.03), 0 4px 6px -4px rgba(0, 0, 0, 0.03);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            --shadow-primary: 0 10px 20px -6px rgba(118, 181, 27, 0.4);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body, input, select, textarea, button, .btn, .form-control, .btn-logout {
            font-family: 'Tajawal', 'Inter', 'Cairo', system-ui, -apple-system, sans-serif !important;
            letter-spacing: -0.01em;
        }
        body { background: var(--body-bg); color: var(--text-main); min-height: 100vh; overflow-x: hidden; }

        /* تحسينات الخط الاحترافية للأرقام والعناوين */
        h1, h2, h3, h4, h5, h6, .card-title, .topbar-title, .ws-card-title, .logo-text {
            font-weight: 900 !important;
            letter-spacing: -0.01em;
            color: #0f172a;
        }
        
        /* تفضيل خط Inter للأرقام والنسب والعدادات والتواريخ والوسوم لإعطاء مظهر SaaS فاخر */
        .stat-value, .badge, .pagination, table td, table th, th, td, .progress-bar, .score, .percentage, .date-text, .metric-main-value, .side-card-val, .side-card-circle {
            font-family: 'Inter', 'Tajawal', 'Cairo', sans-serif !important;
        }

        /* ── Scrollbars ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ── Sidebar (Premium Dark Theme) ── */
        .sidebar {
            position: fixed; top: 0; right: 0;
            width: var(--sidebar-width); height: 100vh;
            background: linear-gradient(180deg, #111827 0%, #0f172a 100%);
            border-left: none;
            display: flex; flex-direction: column;
            z-index: 1000;
            transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: -4px 0 30px rgba(0,0,0,0.15);
        }
        
        .sidebar-overlay { 
            position: fixed; inset: 0; 
            background: rgba(15, 23, 42, 0.4); 
            backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); 
            z-index: 999; display: none; opacity: 0; transition: opacity .3s; 
        }
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(100%); }
            .sidebar.show { transform: translateX(0); }
            .sidebar-overlay.show { display: block; opacity: 1; }
            .main-wrapper { margin-right: 0 !important; }
        }

        .menu-toggle { 
            display: none; width: 44px; height: 44px; 
            align-items: center; justify-content: center; 
            font-size: 24px; cursor: pointer; color: var(--text-main); 
            background: #ffffff; border: 1.5px solid var(--border); 
            border-radius: 12px; transition: all .2s;
            box-shadow: var(--shadow-sm);
        }
        .menu-toggle:hover {
            color: var(--primary);
            border-color: rgba(118, 181, 27, 0.3);
            background: var(--body-bg);
        }
        @media (max-width: 1024px) { .menu-toggle { display: flex; } }

        /* Sidebar Logo Area */
        .sidebar-logo {
            padding: 24px; display: flex; align-items: center; gap: 14px;
            border-bottom: 1px solid var(--sidebar-border);
        }

        .sidebar-logo .logo-text { font-weight: 800; font-size: 15px; color: #fff; line-height: 1.4; }
        .sidebar-logo .logo-text span { color: var(--sidebar-text); font-size: 11px; font-weight: 600; display: block; margin-top: 2px; }

        /* Sidebar Navigation */
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 20px 0; }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
        .nav-section-title { color: #475569; font-size: 11px; font-weight: 800; display: block; padding: 18px 24px 8px; letter-spacing: 0.5px; text-transform: uppercase; }
        
        .nav-item { position: relative; }
        .nav-item a {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 20px; margin: 4px 16px;
            color: var(--sidebar-text); text-decoration: none;
            font-size: 14px; font-weight: 600;
            border-radius: 12px; transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-item a i { font-size: 18px; width: 24px; text-align: center; color: #64748b; transition: color .3s; }
        .nav-item a:hover { background: var(--sidebar-hover); color: #fff; transform: translateX(-6px); }
        .nav-item a:hover i { color: #fff; }
        .nav-item a.active {
            background: linear-gradient(135deg, #76b51b 0%, #5f9416 100%); 
            color: #fff;
            font-weight: 700;
            box-shadow: 0 8px 20px -6px rgba(118, 181, 27, 0.6);
        }
        .nav-item a.active i { color: #fff; }
        /* Active line indicator on the right side */
        .nav-item a.active::after {
            content: '';
            position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
            width: 4px; height: 18px; background: #ffffff;
            border-radius: 10px;
        }

        /* Sidebar Footer */
        .sidebar-footer { padding: 20px; border-top: 1px solid var(--sidebar-border); }
        .admin-info { display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px; background: rgba(0,0,0,0.15); border: 1px solid rgba(255,255,255,0.03); margin-bottom: 12px; }
        .admin-avatar { width: 38px; height: 38px; background: rgba(118, 181, 27, 0.2); color: var(--primary); border: 1.5px solid rgba(118, 181, 27, 0.3); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; }
        .admin-name { font-size: 13px; font-weight: 700; color: #fff; }
        .admin-role { font-size: 11px; color: var(--sidebar-text); }
        .btn-logout { 
            width: 100%; 
            background: linear-gradient(135deg, #c30e14 0%, #991b1b 100%); 
            color: #fff; 
            padding: 12px; border-radius: 50px; font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; border: none; transition: all .3s ease; 
            box-shadow: 0 4px 12px rgba(195, 14, 20, 0.2);
        }
        .btn-logout:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 20px rgba(195, 14, 20, 0.35); 
        }

        /* ── Main Wrapper ── */
        .main-wrapper { margin-right: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }

        /* ── Topbar (Glassmorphic) ── */
        .topbar {
            height: var(--topbar-h); 
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border-bottom: 1.5px solid rgba(226, 232, 240, 0.8);
            display: flex; align-items: center; padding: 0 32px; justify-content: space-between;
            position: sticky; top: 0; z-index: 990;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);
            transition: all 0.3s;
        }
        
        .topbar-btn { 
            width: 44px; height: 44px; border-radius: 12px; 
            background: #ffffff; border: 1.5px solid var(--border); 
            cursor: pointer; display: flex; align-items: center; justify-content: center; 
            color: var(--text-muted); font-size: 19px; transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative; box-shadow: var(--shadow-sm);
        }
        .topbar-btn:hover { background: var(--body-bg); border-color: var(--primary); color: var(--primary); transform: translateY(-1px); }
        
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: var(--text-muted); font-weight: 750; }
        .breadcrumb a { color: var(--text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: color .2s; }
        .breadcrumb a:hover { color: var(--primary); }
        .breadcrumb span { color: #cbd5e0; display: inline-flex; align-items: center; }

        /* ── كبسولة الملف الشخصي (Profile Capsule) ── */
        .profile-capsule {
            display: flex; align-items: center; gap: 10px; background: #ffffff;
            border: 1.5px solid var(--border); padding: 6px 14px 6px 8px; border-radius: 50px;
            cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none; box-shadow: var(--shadow-sm); position: relative;
        }
        .profile-capsule:hover { border-color: var(--primary); background: var(--primary-light); }
        .profile-capsule-name { font-size: 13px; font-weight: 850; color: var(--text-main); }
        .profile-capsule-avatar { width: 32px; height: 32px; border-radius: 50%; border: 2px solid #ffffff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); object-fit: cover; }

        /* ── القوائم المنسدلة للنافبار (Navbar Dropdowns) ── */
        .dropdown-wrapper { position: relative; }
        .navbar-dropdown {
            position: absolute; left: 0; top: calc(100% + 12px); width: 280px;
            background: #ffffff; border-radius: 16px; border: 1px solid rgba(226,232,240,0.8);
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08), 0 5px 15px rgba(15, 23, 42, 0.03);
            opacity: 0; visibility: hidden; transform: translateY(-10px);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); z-index: 1000; overflow: hidden;
        }
        .navbar-dropdown.show { opacity: 1; visibility: visible; transform: translateY(0); }
        
        .dropdown-header-styled {
            padding: 14px 18px; border-bottom: 1px solid #f1f5f9; background: #f8fafc;
            display: flex; align-items: center; justify-content: space-between;
        }
        .dropdown-header-title { font-size: 12.5px; font-weight: 850; color: #475569; }
        
        .dropdown-item-styled {
            display: flex; align-items: center; gap: 12px; padding: 12px 18px;
            color: var(--text-main); text-decoration: none; font-size: 13px; font-weight: 700;
            border-bottom: 1px solid #f8fafc; transition: all 0.2s;
        }
        .dropdown-item-styled:hover { background: #f8fafc; color: var(--primary); }
        .dropdown-item-styled i { font-size: 16px; color: #64748b; }
        .dropdown-item-styled:hover i { color: var(--primary); }
        
        /* ── تخصيص الإشعارات الفاخرة (Premium Notifications List) ── */
        .notif-item { display: flex; gap: 12px; padding: 12px 18px; border-bottom: 1px solid #f1f5f9; transition: background 0.2s; }
        .notif-item:hover { background: var(--primary-light); }
        .notif-icon-circle { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
        .notif-body { flex: 1; }
        .notif-text { font-size: 12.5px; font-weight: 800; color: var(--text-main); line-height: 1.4; margin-bottom: 4px; }
        .notif-time { font-size: 10px; color: #94a3b8; font-weight: 600; display: block; }

        /* Notification Pulse Animation */
        @keyframes bell-pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.08); }
            100% { transform: scale(1); }
        }
        .topbar-btn.bell-active {
            animation: bell-pulse 2s infinite ease-in-out;
        }

        /* ── Page Content ── */
        .page-content { flex: 1; padding: 32px; animation: pageIn .4s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes pageIn { from { opacity:0; transform: translateY(16px); } to { opacity:1; transform: translateY(0); } }

        /* ── Cards & UI Elements ── */
        .card { 
            background: var(--card-bg); 
            border-radius: var(--radius); 
            box-shadow: var(--shadow-md); 
            border: 1px solid rgba(226, 232, 240, 0.7); 
            overflow: hidden; 
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        .card-header { 
            padding: 20px 24px; 
            border-bottom: 1px solid var(--border); 
            display: flex; align-items: center; justify-content: space-between; 
            gap: 12px; background: #ffffff; 
        }
        .card-title { font-size: 16px; font-weight: 800; display: flex; align-items: center; gap: 10px; color: var(--text-main); }
        .card-title i { color: var(--primary); font-size: 20px; }
        .card-body { padding: 24px; }

        /* Buttons Update */
        .btn { 
            display: inline-flex; align-items: center; gap: 8px; 
            padding: 10px 22px; border-radius: 12px; 
            font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 700; 
            cursor: pointer; border: none; transition: all .25s cubic-bezier(0.4, 0, 0.2, 1); 
            text-decoration: none; white-space: nowrap; 
        }
        .btn-primary { background: var(--primary); color: #fff; box-shadow: var(--shadow-primary); }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 12px 24px -6px rgba(118, 181, 27, 0.45); }
        .btn-primary:active { transform: translateY(0); }
        
        .btn-secondary { background: #fff; color: var(--text-main); border: 1.5px solid var(--border); box-shadow: var(--shadow-sm); }
        .btn-secondary:hover { background: var(--body-bg); border-color: #cbd5e1; }
        .btn-secondary:active { transform: translateY(0); }

        /* Forms Update */
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 8px; }
        .form-control { 
            width: 100%; padding: 12px 16px; 
            border: 1.5px solid #cbd5e1; border-radius: 12px; 
            font-family: 'Cairo', sans-serif; font-size: 14px; 
            color: var(--text-main); background: #f8fafc; 
            transition: all .2s ease-in-out; outline: none; 
        }
        .form-control:focus { 
            background: #fff; border-color: var(--primary); 
            box-shadow: 0 0 0 4px rgba(118, 181, 27, 0.12); 
        }

        /* Alerts Update */
        .alert { padding: 14px 20px; border-radius: 12px; font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 10px; margin-bottom: 20px; border-right: 4px solid; box-shadow: var(--shadow-sm); }
        .alert-success { background: #f0fdf4; color: #166534; border-color: #22c55e; }
        .alert-danger { background: #fef2f2; color: #991b1b; border-color: #ef4444; }

        /* Grid System */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        @media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } .page-content { padding: 16px; } }

        /* ── Stats Grid & Premium Glow Cards ── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 20px; margin-bottom: 28px; }
        .stat-card {
            background: #ffffff; 
            border: 1px solid rgba(226, 232, 240, 0.8); 
            border-radius: 20px; padding: 24px;
            display: flex; align-items: center; gap: 18px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            position: relative; overflow: hidden;
            box-shadow: var(--shadow-md);
        }
        .stat-card::before {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(circle at top left, var(--card-glow, rgba(118, 181, 27, 0.05)) 0%, transparent 60%);
            opacity: 0; transition: opacity 0.3s; pointer-events: none;
        }
        .stat-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 20px 25px -5px var(--glow-shadow, rgba(0,0,0,0.05)), 0 10px 10px -5px rgba(0,0,0,0.02);
            border-color: var(--card-border-hover, var(--primary)); 
        }
        .stat-card:hover::before { opacity: 1; }
        
        .stat-icon {
            width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center;
            font-size: 26px; flex-shrink: 0; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative; z-index: 1;
        }
        .stat-card:hover .stat-icon { transform: scale(1.15) rotate(-5deg); }
        .stat-value { font-size: 26px; font-weight: 900; color: var(--text-main); line-height: 1.2; position: relative; z-index: 1; }
        .stat-label { font-size: 13px; color: var(--text-muted); font-weight: 700; margin-top: 2px; position: relative; z-index: 1; }

        /* Stat Card Colors */
        .stat-card.blue { 
            --card-glow: rgba(37, 99, 235, 0.08); 
            --glow-shadow: rgba(37, 99, 235, 0.15); 
            --card-border-hover: rgba(37, 99, 235, 0.4); 
        }
        .stat-card.blue .stat-icon { background: rgba(37, 99, 235, 0.1); color: #2563eb; }

        .stat-card.green { 
            --card-glow: rgba(118, 181, 27, 0.08); 
            --glow-shadow: rgba(118, 181, 27, 0.15); 
            --card-border-hover: rgba(118, 181, 27, 0.4); 
        }
        .stat-card.green .stat-icon { background: rgba(118, 181, 27, 0.1); color: var(--primary); }

        .stat-card.red { 
            --card-glow: rgba(195, 14, 20, 0.08); 
            --glow-shadow: rgba(195, 14, 20, 0.15); 
            --card-border-hover: rgba(195, 14, 20, 0.4); 
        }
        .stat-card.red .stat-icon { background: rgba(195, 14, 20, 0.1); color: var(--danger); }

        .stat-card.purple { 
            --card-glow: rgba(139, 92, 246, 0.08); 
            --glow-shadow: rgba(139, 92, 246, 0.15); 
            --card-border-hover: rgba(139, 92, 246, 0.4); 
        }
        .stat-card.purple .stat-icon { background: rgba(147, 51, 234, 0.1); color: #9333ea; }

        .stat-card.amber { 
            --card-glow: rgba(245, 158, 11, 0.08); 
            --glow-shadow: rgba(245, 158, 11, 0.15); 
            --card-border-hover: rgba(245, 158, 11, 0.4); 
        }
        .stat-card.amber .stat-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }

        .stat-card.cyan { 
            --card-glow: rgba(6, 182, 212, 0.08); 
            --glow-shadow: rgba(6, 182, 212, 0.15); 
            --card-border-hover: rgba(6, 182, 212, 0.4); 
        }
        .stat-card.cyan .stat-icon { background: rgba(6, 182, 212, 0.1); color: #06b6d4; }

        /* ── Welcome Banner (Premium) ── */
        .welcome-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: var(--radius);
            padding: 36px 40px;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 24px;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 28px;
        }
        .welcome-banner::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(circle at 80% 20%, rgba(118, 181, 27, 0.15) 0%, transparent 60%);
            pointer-events: none;
        }
        .welcome-banner h1 {
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 8px;
            background: linear-gradient(120deg, #ffffff, #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .welcome-banner p {
            color: #94a3b8;
            font-size: 14px;
            font-weight: 600;
        }
        .dashboard-filters {
            position: relative;
            z-index: 2;
        }
        .current-year-info {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.15);
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            color: #10b981;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .filter-group label {
            color: #94a3b8 !important;
            font-weight: 700;
        }
        .filter-group select {
            background: rgba(255, 255, 255, 0.06) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
            font-weight: 700;
            padding: 8px 16px !important;
            border-radius: 12px !important;
            cursor: pointer;
            transition: all 0.2s;
            outline: none;
        }
        .filter-group select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(118, 181, 27, 0.25) !important;
            background: #0f172a !important;
        }
        .filter-group select option {
            background: #0f172a;
            color: #ffffff;
        }

        .filter-summary {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .filter-summary .badge {
            padding: 8px 18px;
            font-size: 13px;
            border-radius: 30px;
        }

        /* ── Tables & Badges ── */
        .table-wrapper { width: 100%; overflow-x: auto; background: #fff; border-radius: 12px; }
        table { width: 100%; border-collapse: collapse; text-align: right; }
        th { background: #f8fafc; color: var(--text-muted); font-weight: 800; font-size: 13px; padding: 16px 20px; border-bottom: 2px solid var(--border); }
        td { padding: 16px 20px; font-size: 14px; border-bottom: 1px solid var(--border); color: var(--text-main); vertical-align: middle; }
        tr { transition: background 0.2s; }
        tr:hover td { background: #f8fafc; }
        
        .badge {
            display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 10px;
            font-size: 12px; font-weight: 800; white-space: nowrap; line-height: 1;
            border: 1px solid transparent;
        }
        .badge-primary { background: rgba(118, 181, 27, 0.08); color: var(--primary); border-color: rgba(118, 181, 27, 0.15); }
        .badge-success { background: rgba(16, 185, 129, 0.08); color: #10b981; border-color: rgba(16, 185, 129, 0.15); }
        .badge-danger { background: rgba(195, 14, 20, 0.08); color: var(--danger); border-color: rgba(195, 14, 20, 0.15); }
        .badge-warning { background: rgba(245, 158, 11, 0.08); color: #f59e0b; border-color: rgba(245, 158, 11, 0.15); }
        .badge-info { background: rgba(6, 182, 212, 0.08); color: #06b6d4; border-color: rgba(6, 182, 212, 0.15); }
        .badge-gray { background: #f1f5f9; color: var(--text-muted); border-color: #e2e8f0; }

        .btn-icon { width: 36px; height: 36px; padding: 0 !important; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; border-radius: 10px; }
        
        .empty-state { text-align: center; padding: 48px 24px; color: var(--text-muted); }
        .empty-state i { font-size: 48px; color: #cbd5e0; margin-bottom: 16px; display: block; }
        .empty-state h3 { font-size: 18px; font-weight: 800; color: var(--text-main); margin-bottom: 6px; }
        .empty-state p { font-size: 14px; }

        /* ── Mobile Responsiveness Enhancements for Sidebar & Topbar ── */
        .sidebar-close-btn {
            display: none;
            position: absolute;
            left: 16px;
            top: 22px;
            background: transparent;
            border: none;
            color: var(--sidebar-text);
            font-size: 20px;
            cursor: pointer;
            transition: color 0.2s;
            z-index: 1010;
        }
        .sidebar-close-btn:hover { color: #fff; }

        @media (max-width: 1024px) {
            .sidebar-close-btn { display: block; }
            .topbar { padding: 0 16px !important; }
            .breadcrumb-container { display: none !important; }
        }

        @media (max-width: 576px) {
            .profile-capsule-name { display: none !important; }
            .profile-capsule { padding: 4px !important; border-radius: 50% !important; }
            .topbar-actions { gap: 10px !important; }
            .topbar-btn { width: 38px !important; height: 38px !important; font-size: 16px !important; }
            .topbar-btn.bell-active span { top: 8px !important; right: 8px !important; }
            .menu-toggle { width: 38px !important; height: 38px !important; font-size: 20px !important; border-radius: 10px !important; }
            .navbar-dropdown { width: 260px !important; }
        }
    </style>
    @stack('styles')
</head>
<body>

<aside class="sidebar">
    <button class="sidebar-close-btn" id="sidebarClose" title="إغلاق القائمة"><i class="bi bi-x-lg"></i></button>
    <div class="sidebar-logo" style="padding: 20px 24px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid var(--sidebar-border);">
        <img src="{{ asset('images/school_logo.png') }}" alt="Logo" style="width: 44px; height: 44px; object-fit: contain; filter: drop-shadow(0 0 8px rgba(255,255,255,0.15));">
        <div class="logo-text" style="font-weight: 800; font-size: 15px; color: #fff; line-height: 1.4;">
            مدرسة القيم الأهلية
            <span style="color: var(--sidebar-text); font-size: 11px; font-weight: 600; display: block; margin-top: 2px;">لوحة الإدارة</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <span class="nav-section-title">الرئيسية</span>
        <div class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> لوحة التحكم
            </a>
        </div>

        <span class="nav-section-title">إدارة المحتوى</span>
        <div class="nav-item">
            <a href="{{ route('admin.grades.index') }}" class="{{ request()->routeIs('admin.grades*') ? 'active' : '' }}">
                <i class="bi bi-layers-fill"></i> الصفوف الدراسية
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.subjects.index') }}" class="{{ request()->routeIs('admin.subjects*') ? 'active' : '' }}">
                <i class="bi bi-book-half"></i> المواد الدراسية
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.questions.index') }}" class="{{ request()->routeIs('admin.questions*') ? 'active' : '' }}">
                <i class="bi bi-patch-question-fill"></i> بنك الأسئلة
            </a>
        </div>

        <span class="nav-section-title">الاختبارات</span>
        <div class="nav-item">
            <a href="{{ route('admin.academic-years.index') }}" class="{{ request()->routeIs('admin.academic-years*') ? 'active' : '' }}">
                <i class="bi bi-calendar3-range-fill"></i> السنوات الدراسية
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.exams.index') }}" class="{{ request()->routeIs('admin.exams*') ? 'active' : '' }}">
                <i class="bi bi-journal-check"></i> الاختبارات
            </a>
        </div>

        <span class="nav-section-title">التقارير</span>
        <div class="nav-item">
            <a href="{{ route('admin.results.index') }}" class="{{ request()->routeIs('admin.results*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill"></i> نتائج الطلاب
            </a>
        </div>

        <span class="nav-section-title">الإعدادات</span>
        <div class="nav-item">
            <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i> إعدادات النظام
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="admin-info">
            <div class="admin-avatar">{{ mb_substr(auth('admin')->user()->name ?? 'م', 0, 1) }}</div>
            <div>
                <div class="admin-name">{{ auth('admin')->user()->name ?? 'مدير النظام' }}</div>
                <div class="admin-role">مدير النظام</div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
            </button>
        </form>
    </div>
</aside>

<div class="main-wrapper">
    <header class="topbar">
        <button class="menu-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
        
        <!-- Breadcrumb navigation (on the right for RTL) -->
        <div class="breadcrumb-container" style="display: flex; align-items: center;">
            @hasSection('breadcrumb')
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}"><i class="bi bi-house-door-fill"></i> الرئيسية</a>
                <span><i class="bi bi-chevron-left" style="font-size: 10px;"></i></span>
                <span style="color: var(--text-main); font-weight: 900;">@yield('breadcrumb')</span>
            </div>
            @else
            <div class="breadcrumb" style="color: var(--text-main); font-weight: 900;">
                <span><i class="bi bi-house-door-fill" style="color: var(--primary);"></i> الرئيسية</span>
            </div>
            @endif
        </div>

        <!-- Left action buttons (Notifications & Admin Avatar with Dropdowns) -->
        <div class="topbar-actions" style="display: flex; align-items: center; gap: 16px; margin-right: auto; margin-left: 0;">
            
            <!-- 1. Notification Dropdown -->
            <div class="dropdown-wrapper">
                <button class="topbar-btn bell-active" id="notifBellBtn">
                    <i class="bi bi-bell-fill" style="color: var(--primary);"></i>
                    <span style="position: absolute; top: 11px; right: 11px; width: 9px; height: 9px; background: var(--danger); border: 2px solid #ffffff; border-radius: 50%;"></span>
                </button>
                
                <div class="navbar-dropdown" id="notifDropdown">
                    <div class="dropdown-header-styled">
                        <span class="dropdown-header-title">أحدث الإشعارات</span>
                        <span class="badge badge-success" style="font-size: 10.5px; padding: 4px 8px;">جديد</span>
                    </div>
                    <div style="max-height: 280px; overflow-y: auto;">
                        <div class="notif-item">
                            <span class="notif-icon-circle" style="background: rgba(118, 181, 27, 0.08); color: var(--primary);"><i class="bi bi-person-plus-fill"></i></span>
                            <div class="notif-body">
                                <span class="notif-text">تم تقديم طالب جديد للقبول بالمدرسة</span>
                                <span class="notif-time">منذ ٥ دقائق</span>
                            </div>
                        </div>
                        <div class="notif-item">
                            <span class="notif-icon-circle" style="background: rgba(37, 99, 235, 0.08); color: #2563eb;"><i class="bi bi-file-earmark-check-fill"></i></span>
                            <div class="notif-body">
                                <span class="notif-text">تم الانتهاء من تصحيح اختبار الرياضيات</span>
                                <span class="notif-time">منذ ساعتين</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Admin Profile Dropdown -->
            <div class="dropdown-wrapper">
                <div class="profile-capsule" id="profileCapsuleBtn">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth('admin')->user()->name ?? 'مدير') }}&background=76b51b&color=fff&bold=true" alt="Admin" class="profile-capsule-avatar">
                    <span class="profile-capsule-name">{{ auth('admin')->user()->name ?? 'مدير النظام' }}</span>
                    <i class="bi bi-chevron-down" style="font-size: 11px; color: #94a3b8;"></i>
                </div>
                
                <div class="navbar-dropdown" id="profileDropdown" style="width: 220px;">
                    <div class="dropdown-header-styled">
                        <span class="dropdown-header-title" style="font-weight: 900; color: var(--text-main);">لوحة الخيارات</span>
                    </div>
                    <a href="{{ route('admin.settings.index') }}" class="dropdown-item-styled">
                        <i class="bi bi-gear-fill"></i>
                        <span>إعدادات النظام</span>
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item-styled">
                        <i class="bi bi-grid-fill"></i>
                        <span>لوحة التحكم</span>
                    </a>
                    <div style="border-top: 1px solid #f1f5f9; padding: 10px 14px;">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" style="width: 100%; border: none; background: rgba(195,14,20,0.06); color: var(--danger); border: 1.5px solid rgba(195,14,20,0.12); padding: 10px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s;" onmouseover="this.style.background='var(--danger)'; this.style.color='#fff';" onmouseout="this.style.background='rgba(195,14,20,0.06)'; this.style.color='var(--danger)';">
                                <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </header>

    <main class="page-content">
        @if(session('success'))
            <div class="alert alert-success" id="flash-alert"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger" id="flash-alert"><i class="bi bi-x-circle-fill"></i> {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                    @foreach($errors->all() as $err)
                        <div>{{ $err }}</div>
                    @endforeach
                </div>
            </div>
        @endif
        
        @yield('content')
    </main>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    
    if(toggleBtn) {
        toggleBtn.addEventListener('click', () => { 
            sidebar.classList.toggle('show'); 
            overlay.classList.toggle('show'); 
        });
    }
    const closeBtn = document.getElementById('sidebarClose');
    if(closeBtn) {
        closeBtn.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }
    if(overlay) {
        overlay.addEventListener('click', () => { 
            sidebar.classList.remove('show'); 
            overlay.classList.remove('show'); 
        });
    }
    
    // ── التحكم في منسدلات الهيدر الفاخرة (Header Dropdowns) ──
    const notifBtn = document.getElementById('notifBellBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const profileBtn = document.getElementById('profileCapsuleBtn');
    const profileDropdown = document.getElementById('profileDropdown');

    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('show');
            if (profileDropdown) profileDropdown.classList.remove('show');
        });
    }

    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
            if (notifDropdown) notifDropdown.classList.remove('show');
        });
    }

    document.addEventListener('click', function() {
        if (notifDropdown) notifDropdown.classList.remove('show');
        if (profileDropdown) profileDropdown.classList.remove('show');
    });
    
    setTimeout(() => { 
        const a = document.getElementById('flash-alert'); 
        if(a){
            a.style.transition='opacity .5s';
            a.style.opacity='0';
            setTimeout(()=>a.remove(),500);
        } 
    }, 4000);
    
    // ── نافذة تأكيد الحذف الفاخرة باستخدام SweetAlert2 ──
    function confirmDelete(formId, message = 'هل أنت متأكد من رغبتك في الحذف؟') { 
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c30e14', // --danger
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'نعم، احذف الآن!',
            cancelButtonText: 'تراجع',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // إظهار سبنر التحميل الفاخر أثناء تنفيذ الحذف
                Swal.fire({
                    title: 'جاري الحذف...',
                    html: 'يرجى الانتظار لحين اكتمال العملية بنجاح.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                document.getElementById(formId).submit();
            }
        });
    }

    // ── إشعارات الجلسة التلقائية الفاخرة (Session Flash Toast/Modal Alerts) ──
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
            title: 'خطأ في العملية!',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonColor: '#c30e14',
            confirmButtonText: 'حسناً'
        });
    @endif
</script>
@stack('scripts')
</body>
</html>
