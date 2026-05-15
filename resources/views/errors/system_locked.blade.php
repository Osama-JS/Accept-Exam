<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>النظام مغلق مؤقتاً</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Cairo', sans-serif; background: #f1f5f9; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; color: #1e293b; }
        .locked-card { background: white; padding: 40px; border-radius: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); text-align: center; max-width: 450px; width: 90%; }
        .icon-circle { width: 80px; height: 80px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 24px; }
        h1 { margin: 0 0 12px; font-size: 24px; }
        p { color: #64748b; line-height: 1.6; margin-bottom: 30px; }
        .btn-refresh { display: inline-flex; align-items: center; gap: 8px; background: #2563eb; color: white; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .btn-refresh:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="locked-card">
        <div class="icon-circle">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h1>النظام مغلق حالياً</h1>
        <p>نعتذر منك، نظام امتحانات القبول مغلق مؤقتاً في الوقت الحالي. يرجى المحاولة مرة أخرى لاحقاً أو التواصل مع إدارة المدرسة.</p>
        <a href="javascript:location.reload()" class="btn-refresh">
            <i class="bi bi-arrow-clockwise"></i> تحديث الصفحة
        </a>
    </div>
</body>
</html>
