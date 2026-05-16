<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | نظام امتحانات القبول</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Cairo', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0c1222 0%, #1a1040 50%, #0e1528 100%);
            display: flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden;
        }
        body::before {
            content: '';
            position: fixed; inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 20% 40%, rgba(79,70,229,.12) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 80% 70%, rgba(168,85,247,.1) 0%, transparent 60%);
        }
        body::after {
            content: '';
            position: fixed; inset: 0;
            background-image: radial-gradient(rgba(99,102,241,.08) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }
        .login-card {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
            border-radius: 24px;
            padding: 52px 44px;
            width: 100%;
            max-width: 430px;
            position: relative; z-index: 1;
            box-shadow: 0 32px 80px rgba(0,0,0,.5), inset 0 1px 0 rgba(255,255,255,.06);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 36px;
        }
        .login-logo .icon {
            width: 68px; height: 68px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed, #a855f7);
            border-radius: 18px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 32px;
            margin-bottom: 18px;
            box-shadow: 0 8px 32px rgba(79,70,229,.45);
            animation: iconFloat 4s ease-in-out infinite;
        }
        @keyframes iconFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
        .login-logo h1 { color: #f8fafc; font-size: 24px; font-weight: 800; }
        .login-logo p  { color: #94a3b8; font-size: 13px; margin-top: 6px; }

        .form-group { margin-bottom: 20px; }
        label { display: block; color: #cbd5e1; font-size: 13px; font-weight: 600; margin-bottom: 8px; }
        .input-wrapper { position: relative; }
        .input-wrapper i { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 18px; pointer-events: none; }
        .form-control {
            width: 100%;
            padding: 12px 44px 12px 14px;
            background: rgba(255,255,255,.06);
            border: 1.5px solid rgba(255,255,255,.1);
            border-radius: 10px;
            color: #f1f5f9;
            font-family: 'Cairo', sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color .2s, background .2s;
        }
        .form-control:focus { border-color: #3b82f6; background: rgba(255,255,255,.08); }
        .form-control::placeholder { color: #475569; }
        .invalid-feedback { color: #f87171; font-size: 12px; margin-top: 6px; display: block; }

        .remember-row { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; }
        .remember-row input[type=checkbox] { accent-color: #3b82f6; width: 16px; height: 16px; cursor: pointer; }
        .remember-row label { color: #94a3b8; font-size: 13px; margin-bottom: 0; cursor: pointer; }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Cairo', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all .3s cubic-bezier(.4,0,.2,1);
            box-shadow: 0 4px 20px rgba(79,70,229,.4);
            position: relative; overflow: hidden;
        }
        .btn-login::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, transparent 30%, rgba(255,255,255,.1) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform .5s;
        }
        .btn-login:hover::before { transform: translateX(100%); }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(79,70,229,.5); }
        .btn-login:active { transform: translateY(0); }

        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #64748b; font-size: 13px; text-decoration: none; transition: color .2s; }
        .back-link a:hover { color: #94a3b8; }

        .alert-danger { background: rgba(239,68,68,.15); border: 1px solid rgba(239,68,68,.3); color: #fca5a5; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }

        @keyframes fadeIn { from { opacity:0; transform: translateY(16px); } to { opacity:1; transform: translateY(0); } }
        .login-card { animation: fadeIn .4s ease; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <div class="icon">🎓</div>
        <h1>نظام امتحانات القبول</h1>
        <p>تسجيل الدخول للوحة الإدارة</p>
    </div>

    @if($errors->any())
        <div class="alert-danger">
            <i class="bi bi-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf
        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <div class="input-wrapper">
                <i class="bi bi-envelope"></i>
                <input type="email" id="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    placeholder="admin@school.com" value="{{ old('email') }}" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label for="password">كلمة المرور</label>
            <div class="input-wrapper">
                <i class="bi bi-lock"></i>
                <input type="password" id="password" name="password" class="form-control"
                    placeholder="••••••••" required>
            </div>
        </div>

        <div class="remember-row">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">تذكرني</label>
        </div>

        <button type="submit" class="btn-login">
            <i class="bi bi-box-arrow-in-right"></i> دخول
        </button>
    </form>

    <div class="back-link">
        <a href="{{ route('home') }}"><i class="bi bi-arrow-right"></i> العودة للموقع</a>
    </div>
</div>
</body>
</html>
