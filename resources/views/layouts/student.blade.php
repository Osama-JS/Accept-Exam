<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'نظام امتحانات القبول')</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary:      #4f46e5;
            --primary-dark: #4338ca;
            --success:      #059669;
            --danger:       #dc2626;
            --warning:      #d97706;
            --body-bg:      #f0f2f7;
            --card-bg:      #ffffff;
            --text-main:    #1a1d2e;
            --text-muted:   #6b7280;
            --border:       #e5e7eb;
            --radius:       16px;
            --shadow:       0 4px 24px rgba(0,0,0,.06);
            --accent-gradient: linear-gradient(135deg, #4f46e5, #7c3aed);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Cairo', sans-serif; background: var(--body-bg); color: var(--text-main); min-height: 100vh; -webkit-font-smoothing: antialiased; }

        /* Header */
        .site-header {
            background: rgba(255,255,255,.88);
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 1px 0 rgba(0,0,0,.06);
            padding: 0 40px;
            height: 68px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
        }
        .site-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; transition: transform .2s; }
        .site-logo:hover { transform: scale(1.02); }
        .logo-icon {
            width: 44px; height: 44px;
            background: var(--accent-gradient);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: #fff;
            box-shadow: 0 4px 14px rgba(79,70,229,.35);
        }
        .logo-text { font-weight: 700; font-size: 16px; color: var(--text-main); }
        .logo-text span { display: block; font-size: 11px; color: var(--text-muted); font-weight: 400; }
        .header-actions a {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 10px;
            font-size: 13px; font-weight: 600;
            background: var(--card-bg); color: var(--text-muted);
            text-decoration: none; border: 1.5px solid var(--border);
            transition: all .25s cubic-bezier(.4,0,.2,1);
        }
        .header-actions a:hover { background: var(--accent-gradient); color: #fff; border-color: transparent; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(79,70,229,.3); }

        /* Main */
        .site-main { min-height: calc(100vh - 130px); }

        /* Footer */
        .site-footer {
            background: linear-gradient(135deg, #0c1222, #1a2744);
            text-align: center;
            padding: 24px 16px;
            color: rgba(255,255,255,.5);
            font-size: 13px;
        }

        /* Cards */
        .card { background: var(--card-bg); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--border); overflow: hidden; }
        .card-body { padding: 28px; }

        /* Forms */
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 6px; }
        .form-control {
            width: 100%; padding: 10px 14px;
            border: 1.5px solid var(--border); border-radius: 8px;
            font-family: 'Cairo', sans-serif; font-size: 14px;
            color: var(--text-main); background: var(--card-bg);
            outline: none; transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
        .form-control.is-invalid { border-color: var(--danger); }
        .invalid-feedback { color: var(--danger); font-size: 12px; margin-top: 4px; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; border: none; transition: all .25s cubic-bezier(.4,0,.2,1); text-decoration: none; white-space: nowrap; }
        .btn-primary { background: var(--accent-gradient); color: #fff; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(79,70,229,.4); }
        .btn-block { width: 100%; justify-content: center; padding: 12px; }

        /* Alerts */
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .alert-success { background: rgba(16,185,129,.1); color: #065f46; border: 1px solid rgba(16,185,129,.3); }
        .alert-danger  { background: rgba(239,68,68,.1);  color: #7f1d1d; border: 1px solid rgba(239,68,68,.3); }
    </style>
    @stack('styles')
</head>
<body>
<header class="site-header">
    <a href="{{ route('home') }}" class="site-logo">
        @if($logo = \App\Models\Setting::get('school_logo'))
            <img src="{{ asset('storage/' . $logo) }}" style="height: 42px; width: auto; object-fit: contain;" alt="شعار المدرسة">
        @else
            <div class="logo-icon">🎓</div>
        @endif
        <div class="logo-text">
            {{ \App\Models\Setting::get('school_name', 'نظام امتحانات القبول') }}
            <span>الاختبار الإلكتروني</span>
        </div>
    </a>
    <div class="header-actions">
        <a href="{{ route('admin.login') }}"><i class="bi bi-shield-lock"></i> دخول الإدارة</a>
    </div>
</header>

<main class="site-main">
    @if(session('error'))
        <div style="max-width:900px;margin:16px auto;padding:0 20px;">
            <div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> {{ session('error') }}</div>
        </div>
    @endif
    @yield('content')
</main>

<footer class="site-footer">
    <p>© {{ date('Y') }} {{ \App\Models\Setting::get('school_name', 'نظام امتحانات القبول للمدارس') }} &mdash; جميع الحقوق محفوظة</p>
</footer>

@stack('scripts')
</body>
</html>
