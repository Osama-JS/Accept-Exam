<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') | نظام امتحانات القبول</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <style>
        :root {
            --primary:       #4f46e5;
            --primary-dark:  #4338ca;
            --primary-light: #6366f1;
            --success:       #059669;
            --danger:        #dc2626;
            --warning:       #d97706;
            --info:          #0891b2;
            --sidebar-bg:    #0c1222;
            --sidebar-hover: #1a2744;
            --sidebar-active:#4f46e5;
            --body-bg:       #f0f2f7;
            --card-bg:       #ffffff;
            --text-main:     #1a1d2e;
            --text-muted:    #6b7280;
            --border:        #e5e7eb;
            --sidebar-width: 270px;
            --topbar-h:      68px;
            --radius:        14px;
            --shadow:        0 1px 3px rgba(0,0,0,.04), 0 6px 24px rgba(0,0,0,.06);
            --shadow-md:     0 8px 32px rgba(0,0,0,.1);
            --accent-gradient: linear-gradient(135deg, #4f46e5, #7c3aed, #a855f7);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--body-bg);
            color: var(--text-main);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0; right: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #0c1222 0%, #111a33 50%, #0e1528 100%);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 1px solid rgba(255,255,255,.06);
        }
        .sidebar::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(ellipse 120% 60% at 80% 0%, rgba(79,70,229,.12) 0%, transparent 60%);
            pointer-events: none;
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity .3s;
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(100%);
            }
            .sidebar.show {
                transform: translateX(0);
                box-shadow: -10px 0 30px rgba(0,0,0,0.2);
            }
            .sidebar-overlay.show {
                display: block;
                opacity: 1;
            }
            .main-wrapper {
                margin-right: 0 !important;
            }
            .stats-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)) !important;
            }
        }

        /* ── Topbar Toggle ── */
        .menu-toggle {
            display: none;
            width: 40px; height: 40px;
            align-items: center; justify-content: center;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-main);
            background: var(--card-bg);
            border-radius: 10px;
            border: 1px solid var(--border);
            margin-left: 12px;
            transition: all .2s;
        }
        .menu-toggle:hover { background: var(--body-bg); color: var(--primary); }
        @media (max-width: 1024px) {
            .menu-toggle { display: flex; }
        }
        .sidebar-logo {
            padding: 22px 24px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
        }
        .sidebar-logo .logo-icon {
            width: 44px; height: 44px;
            background: var(--accent-gradient);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 16px rgba(79,70,229,.4);
            animation: logoPulse 3s ease-in-out infinite;
        }
        @keyframes logoPulse { 0%,100%{box-shadow:0 4px 16px rgba(79,70,229,.4)} 50%{box-shadow:0 6px 24px rgba(79,70,229,.6)} }
        .sidebar-logo .logo-text {
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            line-height: 1.4;
        }
        .sidebar-logo .logo-text span { color: #94a3b8; font-size: 12px; font-weight: 400; }

        .sidebar-nav { flex: 1; overflow-y: auto; padding: 16px 0; }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }

        .nav-section-title {
            color: #475569;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: 12px 24px 6px;
        }
        .nav-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            color: #8b95a8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: 10px;
            margin: 2px 10px;
            transition: all .25s cubic-bezier(.4,0,.2,1);
            position: relative;
        }
        .nav-item a i { font-size: 18px; width: 22px; text-align: center; flex-shrink: 0; transition: transform .2s; }
        .nav-item a:hover { background: var(--sidebar-hover); color: #e2e8f0; }
        .nav-item a:hover i { transform: scale(1.15); }
        .nav-item a.active {
            background: var(--accent-gradient);
            color: #fff;
            box-shadow: 0 6px 20px rgba(79,70,229,.45);
        }
        .nav-item a .badge {
            margin-right: auto;
            background: rgba(255,255,255,.2);
            color: #fff;
            font-size: 11px;
            padding: 1px 7px;
            border-radius: 20px;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,.07);
        }
        .admin-info {
            display: flex; align-items: center; gap: 10px;
            padding: 10px;
            border-radius: 8px;
            background: rgba(255,255,255,.04);
            margin-bottom: 8px;
        }
        .admin-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg,var(--primary),#8b5cf6);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 14px;
        }
        .admin-name { color: #e2e8f0; font-size: 13px; font-weight: 600; }
        .admin-role { color: #64748b; font-size: 11px; }
        .btn-logout {
            width: 100%;
            background: rgba(239,68,68,.1);
            border: 1px solid rgba(239,68,68,.2);
            color: #f87171;
            padding: 8px;
            border-radius: 8px;
            font-family: 'Cairo', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 6px;
            transition: all .2s;
        }
        .btn-logout:hover { background: rgba(239,68,68,.2); color: #ef4444; }

        /* ── Main Content ── */
        .main-wrapper {
            margin-right: var(--sidebar-width);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* ── Topbar ── */
        .topbar {
            height: var(--topbar-h);
            background: rgba(255,255,255,.85);
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0,0,0,.06);
            display: flex; align-items: center;
            padding: 0 32px;
            gap: 16px;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { font-size: 18px; font-weight: 700; color: var(--text-main); flex: 1; }
        .topbar-actions { display: flex; align-items: center; gap: 10px; }
        .topbar-btn {
            width: 38px; height: 38px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: transparent;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-muted);
            font-size: 18px;
            transition: all .2s;
            text-decoration: none;
        }
        .topbar-btn:hover { background: var(--body-bg); color: var(--primary); }
        .breadcrumb {
            display: flex; align-items: center; gap: 6px;
            font-size: 13px; color: var(--text-muted);
        }
        .breadcrumb a { color: var(--primary); text-decoration: none; }
        .breadcrumb .sep { color: var(--border); }

        /* ── Page Content ── */
        .page-content { flex: 1; padding: 28px; }

        /* ── Cards ── */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: box-shadow .3s, transform .3s;
        }
        .card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,.08);
        }
        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px;
        }
        .card-title { font-size: 16px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px; }
        .card-body { padding: 24px; }

        /* ── Stat Cards ── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px,1fr)); gap: 20px; margin-bottom: 28px; }
        .stat-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            display: flex; align-items: flex-start; gap: 16px;
            transition: transform .3s cubic-bezier(.4,0,.2,1), box-shadow .3s;
            position: relative; overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 4px; height: 100%;
            transition: width .3s;
        }
        .stat-card.blue::before   { background: var(--primary); }
        .stat-card.green::before  { background: var(--success); }
        .stat-card.red::before    { background: var(--danger); }
        .stat-card.amber::before  { background: var(--warning); }
        .stat-card.cyan::before   { background: var(--info); }
        .stat-card.purple::before { background: #8b5cf6; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
        .stat-card:hover::before { width: 6px; }
        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; flex-shrink: 0;
        }
        .stat-card.blue   .stat-icon { background: rgba(37,99,235,.1);  color: var(--primary); }
        .stat-card.green  .stat-icon { background: rgba(16,185,129,.1); color: var(--success); }
        .stat-card.red    .stat-icon { background: rgba(239,68,68,.1);  color: var(--danger); }
        .stat-card.amber  .stat-icon { background: rgba(245,158,11,.1); color: var(--warning); }
        .stat-card.cyan   .stat-icon { background: rgba(6,182,212,.1);  color: var(--info); }
        .stat-card.purple .stat-icon { background: rgba(139,92,246,.1); color: #8b5cf6; }
        .stat-value { font-size: 30px; font-weight: 800; color: var(--text-main); line-height: 1; letter-spacing: -0.02em; }
        .stat-label { font-size: 13px; color: var(--text-muted); margin-top: 6px; font-weight: 500; }

        /* ── Tables ── */
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        thead tr { background: var(--body-bg); }
        th { padding: 12px 16px; text-align: right; font-weight: 600; color: var(--text-muted); font-size: 12px; text-transform: uppercase; letter-spacing: .04em; white-space: nowrap; border-bottom: 2px solid var(--border); }
        td { padding: 14px 16px; border-bottom: 1px solid var(--border); color: var(--text-main); vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #f8fafc; }

        /* ── Badges ── */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-success { background: rgba(16,185,129,.1); color: var(--success); }
        .badge-danger  { background: rgba(239,68,68,.1);  color: var(--danger); }
        .badge-warning { background: rgba(245,158,11,.1); color: var(--warning); }
        .badge-primary { background: rgba(37,99,235,.1);  color: var(--primary); }
        .badge-info    { background: rgba(6,182,212,.1);  color: var(--info); }
        .badge-gray    { background: #f1f5f9; color: var(--text-muted); }

        /* ── Buttons ── */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; border: none; transition: all .25s cubic-bezier(.4,0,.2,1); text-decoration: none; white-space: nowrap; }
        .btn-primary { background: var(--accent-gradient); color: #fff; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(79,70,229,.4); }
        .btn-success { background: linear-gradient(135deg, #059669, #10b981); color: #fff; }
        .btn-success:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(5,150,105,.4); }
        .btn-danger  { background: linear-gradient(135deg, #dc2626, #ef4444); color: #fff; }
        .btn-danger:hover  { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(220,38,38,.4); }
        .btn-warning { background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff; }
        .btn-warning:hover { transform: translateY(-1px); }
        .btn-secondary { background: var(--card-bg); color: var(--text-main); border: 1.5px solid var(--border); }
        .btn-secondary:hover { background: var(--body-bg); border-color: #d1d5db; }
        .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 8px; }
        .btn-icon { width: 36px; height: 36px; padding: 0; justify-content: center; border-radius: 10px; }

        /* ── Forms ── */
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 6px; }
        .form-control {
            width: 100%;
            padding: 11px 16px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'Cairo', sans-serif;
            font-size: 14px;
            color: var(--text-main);
            background: var(--card-bg);
            transition: border-color .25s, box-shadow .25s, background .25s;
            outline: none;
        }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(79,70,229,.08); background: #fafaff; }
        .form-control.is-invalid { border-color: var(--danger); box-shadow: 0 0 0 3px rgba(220,38,38,.08); }
        .invalid-feedback { color: var(--danger); font-size: 12px; margin-top: 4px; display: block; }
        select.form-control { cursor: pointer; }
        textarea.form-control { resize: vertical; min-height: 80px; }

        /* ── Alerts ── */
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
        .alert-success { background: rgba(16,185,129,.1); color: #065f46; border: 1px solid rgba(16,185,129,.2); }
        .alert-danger  { background: rgba(239,68,68,.1);  color: #7f1d1d; border: 1px solid rgba(239,68,68,.2); }
        .alert-warning { background: rgba(245,158,11,.1); color: #78350f; border: 1px solid rgba(245,158,11,.2); }
        .alert-info    { background: rgba(6,182,212,.1);  color: #0e4f6b; border: 1px solid rgba(6,182,212,.2); }

        /* ── Pagination ── */
        .pagination { display: flex; gap: 4px; justify-content: center; margin-top: 20px; }
        .pagination a, .pagination span {
            padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600;
            border: 1px solid var(--border); color: var(--text-muted); text-decoration: none;
            transition: all .2s;
        }
        .pagination a:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
        .pagination .active span { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── Modal ── */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.5); backdrop-filter: blur(4px); z-index: 200; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity .2s; }
        .modal-overlay.open { opacity: 1; pointer-events: all; }
        .modal-box { background: var(--card-bg); border-radius: 16px; padding: 28px; width: 90%; max-width: 480px; box-shadow: var(--shadow-md); transform: scale(.95); transition: transform .2s; }
        .modal-overlay.open .modal-box { transform: scale(1); }
        .modal-title { font-size: 18px; font-weight: 700; margin-bottom: 16px; }

        /* ── Toast ── */
        .toast-container { position: fixed; bottom: 24px; left: 24px; z-index: 999; display: flex; flex-direction: column; gap: 10px; }
        .toast { background: #1e293b; color: #f8fafc; padding: 14px 20px; border-radius: 10px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; box-shadow: var(--shadow-md); min-width: 280px; animation: slideIn .3s ease; }
        .toast.success { border-right: 4px solid var(--success); }
        .toast.danger  { border-right: 4px solid var(--danger); }
        .toast.warning { border-right: 4px solid var(--warning); }
        @keyframes slideIn { from { transform: translateX(-20px); opacity:0; } to { transform: translateX(0); opacity:1; } }
        @keyframes slideOut { to { transform: translateX(-20px); opacity:0; } }

        /* ── Page entrance animation ── */
        .page-content { animation: pageIn .4s ease-out; }
        @keyframes pageIn { from { opacity:0; transform: translateY(8px); } to { opacity:1; transform: translateY(0); } }

        /* ── Table row hover ── */
        tbody tr { transition: background .2s; }
        tbody tr:hover { background: #f8f9fc; }

        /* ── Empty State ── */
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 64px; color: #cbd5e1; margin-bottom: 16px; display: block; }
        .empty-state h3 { font-size: 18px; color: var(--text-muted); margin-bottom: 8px; }
        .empty-state p { font-size: 14px; color: #94a3b8; }

        /* ── Grid ── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        @media (max-width: 768px) { .grid-2, .grid-3 { grid-template-columns: 1fr; } .stats-grid { grid-template-columns: 1fr 1fr; } }

        /* ── Divider ── */
        .divider { height: 1px; background: var(--border); margin: 20px 0; }

        /* ── Helpers ── */
        .text-muted { color: var(--text-muted); }
        .text-success { color: var(--success); }
        .text-danger  { color: var(--danger); }
        .text-primary { color: var(--primary); }
        .fw-bold { font-weight: 700; }
        /* ── Welcome Banner ── */
        .welcome-banner {
            background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 50%, #7c3aed 100%);
            border: none;
            border-radius: 20px;
            padding: 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            box-shadow: 0 8px 32px rgba(79,70,229,.25);
            margin-bottom: 32px;
            position: relative; overflow: hidden;
            color: #fff;
        }
        .welcome-banner::before {
            content: '';
            position: absolute; top: -50%; left: -30%; width: 80%; height: 200%;
            background: radial-gradient(ellipse, rgba(255,255,255,.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .welcome-content h1 { font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 8px; }
        .welcome-content p { color: rgba(255,255,255,.8); font-size: 15px; }
        .current-year-badge {
            background: rgba(255,255,255,.12);
            border: 1.5px solid rgba(255,255,255,.2);
            padding: 12px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff;
            font-size: 14px;
            backdrop-filter: blur(8px);
        }
        .current-year-badge i { font-size: 20px; }
        .current-year-badge strong { color: #fff; font-weight: 800; font-size: 16px; }

        .dashboard-filters { display: flex; align-items: center; gap: 16px; }
        .current-year-info { font-size: 13px; color: var(--text-muted); }
        .current-year-info strong { color: var(--text-main); }
        .filter-summary { margin-top: -10px; }

        .mb-4 { margin-bottom: 24px; }
        .mb-0 { margin-bottom: 0; }
        .mt-auto { margin-top: auto; }
        .d-flex { display: flex; }
        .align-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .ms-auto { margin-right: auto; }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">🎓</div>
        <div class="logo-text">
            نظام القبول<br>
            <span>لوحة الإدارة</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-title">الرئيسية</div>
        <div class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> لوحة التحكم
            </a>
        </div>

        <div class="nav-section-title">إدارة المحتوى</div>
        <div class="nav-item">
            <a href="{{ route('admin.grades.index') }}" class="{{ request()->routeIs('admin.grades*') ? 'active' : '' }}">
                <i class="bi bi-layers"></i> الصفوف الدراسية
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.subjects.index') }}" class="{{ request()->routeIs('admin.subjects*') ? 'active' : '' }}">
                <i class="bi bi-book"></i> المواد الدراسية
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.questions.index') }}" class="{{ request()->routeIs('admin.questions*') ? 'active' : '' }}">
                <i class="bi bi-patch-question"></i> بنك الأسئلة
            </a>
        </div>

        <div class="nav-section-title">الاختبارات</div>
        <div class="nav-item">
            <a href="{{ route('admin.academic-years.index') }}" class="{{ request()->routeIs('admin.academic-years*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> السنوات الدراسية
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.exams.index') }}" class="{{ request()->routeIs('admin.exams*') ? 'active' : '' }}">
                <i class="bi bi-journal-check"></i> الاختبارات
            </a>
        </div>

        <div class="nav-section-title">التقارير</div>
        <div class="nav-item">
            <a href="{{ route('admin.results.index') }}" class="{{ request()->routeIs('admin.results*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart"></i> نتائج الطلاب
            </a>
        </div>

        <div class="nav-section-title">الإعدادات</div>
        <div class="nav-item">
            <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> إعدادات النظام
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="admin-info">
            <div class="admin-avatar">{{ mb_substr(auth('admin')->user()->name, 0, 1) }}</div>
            <div>
                <div class="admin-name">{{ auth('admin')->user()->name }}</div>
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

<!-- Main -->
<div class="main-wrapper">

    <!-- Topbar -->
    <header class="topbar">
        <button class="menu-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-title">@yield('page-title', 'لوحة التحكم')</div>
        <div class="topbar-actions">
            <a href="{{ route('home') }}" target="_blank" class="topbar-btn" title="عرض الموقع">
                <i class="bi bi-box-arrow-up-left"></i>
            </a>
        </div>
        @hasSection('breadcrumb')
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">الرئيسية</a>
            <span class="sep">/</span>
            @yield('breadcrumb')
        </div>
        @endif
    </header>

    <!-- Page Content -->
    <main class="page-content">

        @if(session('success'))
            <div class="alert alert-success" id="flash-alert">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger" id="flash-alert">
                <i class="bi bi-x-circle-fill"></i>
                {{ session('error') }}
            </div>
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

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
// Sidebar Toggle
const sidebar = document.querySelector('.sidebar');
const overlay = document.getElementById('sidebarOverlay');
const toggleBtn = document.getElementById('sidebarToggle');

if(toggleBtn) {
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    });
}

if(overlay) {
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    });
}

// Auto-dismiss flash alerts
setTimeout(() => {
    const alert = document.getElementById('flash-alert');
    if (alert) { alert.style.transition = 'opacity .5s'; alert.style.opacity = '0'; setTimeout(() => alert.remove(), 500); }
}, 4000);

// Confirm delete
function confirmDelete(formId, message = 'هل أنت متأكد من الحذف؟') {
    if (confirm(message)) document.getElementById(formId).submit();
}
</script>

@stack('scripts')
</body>
</html>
