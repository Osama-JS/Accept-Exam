@extends('layouts.admin')

@section('title', 'لوحة التحكم')
@section('breadcrumb', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم الإحصائية')

@section('content')

@push('styles')
<style>
    /* ── بنر الترحيب السحابي الفاخر (SaaS Dashboard Hero Banner) ── */
    .welcome-banner {
        background: linear-gradient(135deg, var(--sidebar-bg) 0%, #1e293b 100%);
        border-radius: 24px; padding: 36px; color: #fff;
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 24px;
        position: relative; overflow: hidden; box-shadow: var(--shadow-lg);
        margin-bottom: 32px; border: 1px solid rgba(255,255,255,0.06);
    }
    .welcome-banner::after {
        content: '\F2DC'; font-family: 'bootstrap-icons'; position: absolute;
        left: -10px; bottom: -30px; font-size: 140px; opacity: 0.05; pointer-events: none;
    }
    .welcome-content h1 { font-size: 26px; font-weight: 950; color: #fff; margin-bottom: 6px; }
    .welcome-content p { font-size: 14px; color: #94a3b8; font-weight: 550; }

    .dashboard-filters {
        background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 16px 24px; border-radius: 16px; display: flex; align-items: center; gap: 20px;
        backdrop-filter: blur(12px); z-index: 10;
    }
    .current-year-info { display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 700; color: #cbd5e1; }
    
    .filter-select-wrapper { display: flex; flex-direction: column; gap: 6px; }
    .filter-select-wrapper label { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; }
    .filter-select {
        background: rgba(15, 23, 42, 0.6) !important; border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
        color: #fff !important; font-size: 13px; font-weight: 850; padding: 10px 16px !important;
        border-radius: 10px !important; outline: none; cursor: pointer; transition: all 0.2s;
    }
    .filter-select:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(118, 181, 27, 0.25); }

    /* ── كروت الإحصائيات المتميزة (Curated Metric Cards) ── */
    .dashboard-metrics-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 24px; margin-bottom: 32px;
    }
    .metric-card-premium {
        background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 24px;
        display: flex; align-items: center; justify-content: space-between;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative; overflow: hidden;
    }
    .metric-card-premium:hover { transform: translateY(-4px); box-shadow: 0 16px 32px rgba(0,0,0,0.04); border-color: var(--theme-color); }
    
    .metric-premium-details { display: flex; flex-direction: column; gap: 4px; }
    .metric-premium-lbl { font-size: 12.5px; font-weight: 750; color: #64748b; }
    .metric-premium-val { font-size: 28px; font-weight: 950; color: #1e293b; font-family: 'Inter', sans-serif !important; }
    
    .metric-premium-icon {
        width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center;
        font-size: 24px; background: var(--theme-grad); color: var(--theme-color);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.4);
    }

    /* ── التوزيع البصري للمخططات (BI Charts Layout) ── */
    .charts-container { display: grid; grid-template-columns: 1fr 2.2fr; gap: 24px; margin-bottom: 32px; }
    @media (max-width: 1024px) { .charts-container { grid-template-columns: 1fr; } }

    .chart-card-premium {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 24px;
        box-shadow: var(--shadow-sm); display: flex; flex-direction: column; overflow: hidden;
    }
    .chart-card-header {
        padding: 20px 24px; border-bottom: 1.5px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;
    }
    .chart-card-title { font-size: 16px; font-weight: 900; color: #1e293b; display: flex; align-items: center; gap: 10px; }
    
    /* ── خلايا تفاصيل الطلاب الأفقية (Student Avatar Chips) ── */
    .student-avatar-chip {
        width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;
        font-weight: 900; font-size: 14.5px; text-transform: uppercase;
    }
</style>
@endpush

<!-- بنر الترحيب السحابي الفاخر -->
<div class="welcome-banner">
    <div class="welcome-content">
        <h1>مرحباً بك مجدداً، {{ auth()->guard('admin')->user()->name }} 👋</h1>
        <p>إليك لوحة قيادة تحليلات القبول ومراجعة كفاءة المناهج والطلاب.</p>
    </div>
    
    <div class="dashboard-filters">
        <form method="GET" action="{{ route('admin.dashboard') }}" id="filter-form" class="d-flex align-items-center gap-3">
            @if($currentYear)
            <div class="current-year-info d-none d-md-flex">
                <i class="bi bi-calendar-check-fill" style="color: var(--primary); font-size: 16px;"></i>
                <span>العام النشط: <strong style="color: #fff;">{{ $currentYear->name }}</strong></span>
            </div>
            @endif
            
            <div class="filter-select-wrapper">
                <label for="academic_year_id">العام الأكاديمي الحالي:</label>
                <select name="academic_year_id" id="academic_year_id" class="filter-select" onchange="this.form.submit()">
                    @foreach($years as $year)
                        <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                            {{ $year->name }} {{ $year->is_current ? '(الحالية)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<!-- بطاقات المقاييس الـ 8 الملونة والممتازة -->
@php
    $metricThemes = [
        'grades'    => ['color' => '#2563eb', 'grad' => 'linear-gradient(135deg, rgba(37,99,235,0.12) 0%, rgba(37,99,235,0.02) 100%)'],
        'subjects'  => ['color' => '#0d9488', 'grad' => 'linear-gradient(135deg, rgba(13,148,136,0.12) 0%, rgba(13,148,136,0.02) 100%)'],
        'questions' => ['color' => '#8b5cf6', 'grad' => 'linear-gradient(135deg, rgba(139,92,246,0.12) 0%, rgba(139,92,246,0.02) 100%)'],
        'exams'     => ['color' => '#f59e0b', 'grad' => 'linear-gradient(135deg, rgba(245,158,11,0.12) 0%, rgba(245,158,11,0.02) 100%)'],
        'students'  => ['color' => '#0284c7', 'grad' => 'linear-gradient(135deg, rgba(2,132,199,0.12) 0%, rgba(2,132,199,0.02) 100%)'],
        'passed'    => ['color' => '#76b51b', 'grad' => 'linear-gradient(135deg, rgba(118,181,27,0.12) 0%, rgba(118,181,27,0.02) 100%)'],
        'failed'    => ['color' => '#c30e14', 'grad' => 'linear-gradient(135deg, rgba(195,14,20,0.12) 0%, rgba(195,14,20,0.02) 100%)'],
        'success'   => ['color' => '#059669', 'grad' => 'linear-gradient(135deg, rgba(5,150,105,0.12) 0%, rgba(5,150,105,0.02) 100%)']
    ];
@endphp

<div class="dashboard-metrics-grid">
    <!-- 1. الصفوف -->
    <div class="metric-card-premium" style="--theme-color: {{ $metricThemes['grades']['color'] }};">
        <div class="metric-premium-details">
            <span class="metric-premium-lbl">الصفوف الدراسية</span>
            <span class="metric-premium-val">{{ $stats['grades'] }}</span>
        </div>
        <div class="metric-premium-icon" style="--theme-color: {{ $metricThemes['grades']['color'] }}; --theme-grad: {{ $metricThemes['grades']['grad'] }};">
            <i class="bi bi-layers-fill"></i>
        </div>
    </div>
    
    <!-- 2. المواد -->
    <div class="metric-card-premium" style="--theme-color: {{ $metricThemes['subjects']['color'] }};">
        <div class="metric-premium-details">
            <span class="metric-premium-lbl">المواد الدراسية</span>
            <span class="metric-premium-val">{{ $stats['subjects'] }}</span>
        </div>
        <div class="metric-premium-icon" style="--theme-color: {{ $metricThemes['subjects']['color'] }}; --theme-grad: {{ $metricThemes['subjects']['grad'] }};">
            <i class="bi bi-book-half"></i>
        </div>
    </div>

    <!-- 3. الأسئلة -->
    <div class="metric-card-premium" style="--theme-color: {{ $metricThemes['questions']['color'] }};">
        <div class="metric-premium-details">
            <span class="metric-premium-lbl">الأسئلة في البنك</span>
            <span class="metric-premium-val">{{ $stats['questions'] }}</span>
        </div>
        <div class="metric-premium-icon" style="--theme-color: {{ $metricThemes['questions']['color'] }}; --theme-grad: {{ $metricThemes['questions']['grad'] }};">
            <i class="bi bi-patch-question-fill"></i>
        </div>
    </div>

    <!-- 4. الاختبارات -->
    <div class="metric-card-premium" style="--theme-color: {{ $metricThemes['exams']['color'] }};">
        <div class="metric-premium-details">
            <span class="metric-premium-lbl">الاختبارات</span>
            <span class="metric-premium-val">{{ $stats['exams'] }}</span>
        </div>
        <div class="metric-premium-icon" style="--theme-color: {{ $metricThemes['exams']['color'] }}; --theme-grad: {{ $metricThemes['exams']['grad'] }};">
            <i class="bi bi-journal-check"></i>
        </div>
    </div>

    <!-- 5. الطلاب -->
    <div class="metric-card-premium" style="--theme-color: {{ $metricThemes['students']['color'] }};">
        <div class="metric-premium-details">
            <span class="metric-premium-lbl">الطلاب المتقدمين</span>
            <span class="metric-premium-val">{{ $stats['students'] }}</span>
        </div>
        <div class="metric-premium-icon" style="--theme-color: {{ $metricThemes['students']['color'] }}; --theme-grad: {{ $metricThemes['students']['grad'] }};">
            <i class="bi bi-people-fill"></i>
        </div>
    </div>

    <!-- 6. الناجحين -->
    <div class="metric-card-premium" style="--theme-color: {{ $metricThemes['passed']['color'] }};">
        <div class="metric-premium-details">
            <span class="metric-premium-lbl">الطلاب الناجحون</span>
            <span class="metric-premium-val" style="color: {{ $metricThemes['passed']['color'] }};">{{ $stats['passed'] }}</span>
        </div>
        <div class="metric-premium-icon" style="--theme-color: {{ $metricThemes['passed']['color'] }}; --theme-grad: {{ $metricThemes['passed']['grad'] }};">
            <i class="bi bi-check-circle-fill"></i>
        </div>
    </div>

    <!-- 7. الراسبين -->
    <div class="metric-card-premium" style="--theme-color: {{ $metricThemes['failed']['color'] }};">
        <div class="metric-premium-details">
            <span class="metric-premium-lbl">الطلاب المتعثرون</span>
            <span class="metric-premium-val" style="color: {{ $metricThemes['failed']['color'] }};">{{ $stats['failed'] }}</span>
        </div>
        <div class="metric-premium-icon" style="--theme-color: {{ $metricThemes['failed']['color'] }}; --theme-grad: {{ $metricThemes['failed']['grad'] }};">
            <i class="bi bi-x-circle-fill"></i>
        </div>
    </div>

    <!-- 8. نسبة النجاح -->
    @if($stats['total_results'] > 0)
        @php
            $successPct = round(($stats['passed']/$stats['total_results'])*100);
        @endphp
        <div class="metric-card-premium" style="--theme-color: {{ $metricThemes['success']['color'] }};">
            <div class="metric-premium-details">
                <span class="metric-premium-lbl">نسبة النجاح العامة</span>
                <span class="metric-premium-val" style="color: {{ $metricThemes['success']['color'] }};">{{ $successPct }}%</span>
            </div>
            <div class="metric-premium-icon" style="--theme-color: {{ $metricThemes['success']['color'] }}; --theme-grad: {{ $metricThemes['success']['grad'] }};">
                <i class="bi bi-percent"></i>
            </div>
        </div>
    @endif
</div>

<!-- قسم الإجراءات السريعة والروابط المباشرة (Premium Admin Shortcuts Panel) -->
<div class="card mb-4" style="border-radius: 24px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: var(--shadow-sm);">
    <div class="card-header" style="padding: 20px 24px; border-bottom: 1.5px solid #f1f5f9; background: #fff;">
        <span class="chart-card-title" style="font-size: 16px;">
            <i class="bi bi-lightning-charge-fill" style="color: #f59e0b;"></i> مركز الإجراءات السريعة
        </span>
    </div>
    <div class="card-body" style="padding: 24px; display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; background: #ffffff;">
        <a href="{{ route('admin.questions.create') }}" style="display: flex; align-items: center; gap: 14px; padding: 18px; border-radius: 16px; border: 1.5px solid #e2e8f0; text-decoration: none; color: #1e293b; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background: #fdfdfd;" onmouseover="this.style.borderColor='var(--primary)'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.03)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='none'; this.style.boxShadow='none';">
            <i class="bi bi-patch-question-fill" style="font-size: 22px; color: #8b5cf6; background: #f5f3ff; width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"></i>
            <div>
                <span style="font-weight: 850; font-size: 14px; display: block;">أضف سؤال جديد</span>
                <span style="font-size: 11.5px; color: #64748b; font-weight: 700; display: block; margin-top: 3px;">توسيع بنك الأسئلة</span>
            </div>
        </a>
        <a href="{{ route('admin.exams.create') }}" style="display: flex; align-items: center; gap: 14px; padding: 18px; border-radius: 16px; border: 1.5px solid #e2e8f0; text-decoration: none; color: #1e293b; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background: #fdfdfd;" onmouseover="this.style.borderColor='var(--primary)'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.03)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='none'; this.style.boxShadow='none';">
            <i class="bi bi-journal-plus" style="font-size: 22px; color: #f59e0b; background: #fffbeb; width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"></i>
            <div>
                <span style="font-weight: 850; font-size: 14px; display: block;">إنشاء اختبار جديد</span>
                <span style="font-size: 11.5px; color: #64748b; font-weight: 700; display: block; margin-top: 3px;">تجهيز بوابة القبول</span>
            </div>
        </a>
        <a href="{{ route('admin.settings.index') }}" style="display: flex; align-items: center; gap: 14px; padding: 18px; border-radius: 16px; border: 1.5px solid #e2e8f0; text-decoration: none; color: #1e293b; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background: #fdfdfd;" onmouseover="this.style.borderColor='var(--primary)'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.03)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='none'; this.style.boxShadow='none';">
            <i class="bi bi-sliders" style="font-size: 22px; color: #0d9488; background: #f0fdfa; width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"></i>
            <div>
                <span style="font-weight: 850; font-size: 14px; display: block;">إعدادات النظام</span>
                <span style="font-size: 11.5px; color: #64748b; font-weight: 700; display: block; margin-top: 3px;">تهيئة ضوابط القبول</span>
            </div>
        </a>
        <a href="{{ route('admin.academic-years.index') }}" style="display: flex; align-items: center; gap: 14px; padding: 18px; border-radius: 16px; border: 1.5px solid #e2e8f0; text-decoration: none; color: #1e293b; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background: #fdfdfd;" onmouseover="this.style.borderColor='var(--primary)'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.03)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='none'; this.style.boxShadow='none';">
            <i class="bi bi-calendar-range-fill" style="font-size: 22px; color: #2563eb; background: #eff6ff; width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"></i>
            <div>
                <span style="font-weight: 850; font-size: 14px; display: block;">السنوات الدراسية</span>
                <span style="font-size: 11.5px; color: #64748b; font-weight: 700; display: block; margin-top: 3px;">تفعيل الفترات الزمنية</span>
            </div>
        </a>
    </div>
</div>

<!-- حاوية المخططات البيانية (BI Charts Panel) -->
<div class="charts-container">
    <!-- 1. توزيع النتائج -->
    <div class="chart-card-premium">
        <div class="chart-card-header">
            <span class="chart-card-title">
                <i class="bi bi-pie-chart-fill" style="color: var(--primary);"></i> إجمالي حالة الطلاب
            </span>
        </div>
        <div class="card-body" style="height: 320px; display: flex; align-items: center; justify-content: center; padding: 24px;">
            @if($stats['total_results'] > 0)
                <canvas id="resultsChart"></canvas>
            @else
                <div class="empty-state" style="padding: 0;">
                    <i class="bi bi-bar-chart" style="font-size: 40px; color: #cbd5e0;"></i>
                    <h3 style="font-size: 14.5px; font-weight: 800; color: var(--text-muted); margin-top: 10px;">لا توجد نتائج مضافة حالياً</h3>
                </div>
            @endif
        </div>
    </div>

    <!-- 2. أداء الطلاب حسب الصف -->
    <div class="chart-card-premium">
        <div class="chart-card-header">
            <span class="chart-card-title">
                <i class="bi bi-bar-chart-line-fill" style="color: var(--primary);"></i> أداء الطلاب ومعدلات النجاح / الصف الدراسي
            </span>
        </div>
        <div class="card-body" style="height: 320px; display: flex; align-items: center; justify-content: center; padding: 24px;">
            @if($resultsByGrade->sum('passed') + $resultsByGrade->sum('failed') > 0)
                <canvas id="gradesChart"></canvas>
            @else
                <div class="empty-state" style="padding: 0;">
                    <i class="bi bi-calendar-x" style="font-size: 40px; color: #cbd5e0;"></i>
                    <h3 style="font-size: 14.5px; font-weight: 800; color: var(--text-muted); margin-top: 10px;">لا توجد إحصائيات كافية للصفوف</h3>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- جدول آخر النتائج المضافة -->
<div class="card" style="border-radius: 24px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: var(--shadow-sm);">
    <div class="card-header" style="padding: 20px 24px; border-bottom: 1.5px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; background: #fff;">
        <span class="chart-card-title" style="font-size: 16px;">
            <i class="bi bi-clock-history" style="color: var(--primary);"></i> النتائج المسجلة حديثاً (آخر الاختبارات)
        </span>
        <a href="{{ route('admin.results.index') }}" class="btn btn-secondary" style="border: 1.5px solid var(--border); background: #ffffff; color: var(--text-main); font-weight: 850; border-radius: 10px; padding: 8px 18px; font-size: 12.5px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;">
            <span>عرض سجل النتائج الكامل</span>
            <i class="bi bi-arrow-left"></i>
        </a>
    </div>
    
    <div class="table-wrapper" style="width: 100%; overflow-x: auto; background: #fff;">
        @if($recentResults->isEmpty())
            <div class="empty-state" style="padding: 60px 24px;">
                <i class="bi bi-inbox" style="font-size: 48px; color: #cbd5e0; margin-bottom: 12px; display: block;"></i>
                <h3 style="font-size: 17px; font-weight: 850; color: var(--text-main); margin-bottom: 4px;">لا توجد نتائج مسجلة</h3>
                <p style="font-size: 13.5px; color: var(--text-muted);">ستظهر نتائج الطلاب هنا بمجرد انتهائهم من الإجابة.</p>
            </div>
        @else
        <table style="width: 100%; border-collapse: collapse; text-align: right;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 1.5px solid #e2e8f0;">
                    <th style="padding: 18px 24px; font-weight: 850; color: #475569; font-size: 12.5px;">الاسم الكامل للترشيح</th>
                    <th style="padding: 18px 24px; font-weight: 850; color: #475569; font-size: 12.5px;">الاختبار المفروض</th>
                    <th style="padding: 18px 24px; font-weight: 850; color: #475569; font-size: 12.5px; text-align: center;">الصف</th>
                    <th style="padding: 18px 24px; font-weight: 850; color: #475569; font-size: 12.5px; text-align: center;">الدرجة المحققة</th>
                    <th style="padding: 18px 24px; font-weight: 850; color: #475569; font-size: 12.5px; text-align: center;">الحالة والنتيجة</th>
                    <th style="padding: 18px 24px; font-weight: 850; color: #475569; font-size: 12.5px;">التاريخ والوقت</th>
                    <th style="padding: 18px 24px; width: 80px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentResults as $r)
                @php
                    // توليد لون خلفية عشوائي متميز للأفاتار
                    $bgColors = ['#eff6ff', '#f0fdf4', '#f5f3ff', '#fff7ed', '#f0fdfa'];
                    $textColors = ['#2563eb', '#16a34a', '#7c3aed', '#ea580c', '#0d9488'];
                    $colorIndex = $r->id % count($bgColors);
                    
                    // استخراج الحروف الأولى من الاسم
                    $initials = mb_substr($r->student->name, 0, 2, 'utf-8');
                @endphp
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                    <td style="padding: 16px 24px; font-weight: 750; color: var(--text-main);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span class="student-avatar-chip" style="background: {{ $bgColors[$colorIndex] }}; color: {{ $textColors[$colorIndex] }};">
                                {{ $initials }}
                            </span>
                            <div>
                                <span style="font-weight: 800; font-size: 14.5px; display: block;">{{ $r->student->name }}</span>
                                <span style="font-size: 11.5px; color: #94a3b8; font-weight: 650; display: block; margin-top: 2px;">{{ $r->student->previous_school ?? 'مدرسة سابقة غير محددة' }}</span>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 16px 24px; color: #334155; font-weight: 700; font-size: 14px;">{{ $r->exam->title }}</td>
                    <td style="padding: 16px 24px; text-align: center;">
                        <span class="badge badge-primary" style="background: rgba(37,99,235,0.06); color: #2563eb; border: 1.5px solid rgba(37,99,235,0.12); font-weight: 800; font-size: 12px; padding: 6px 12px; border-radius: 8px;">
                            {{ $r->exam->grade->name }}
                        </span>
                    </td>
                    <td style="padding: 16px 24px; text-align: center; font-weight: 900; font-size: 14.5px; color: #1e293b; font-family: 'Inter', sans-serif !important;">
                        {{ $r->score }} <span style="color: #94a3b8; font-weight: 700; font-size: 12px;">/ {{ $r->total_marks }}</span>
                    </td>
                    <td style="padding: 16px 24px; text-align: center;">
                        @if($r->isPassed())
                            <span class="badge" style="background: rgba(118,181,27,0.06); color: var(--primary); border: 1.5px solid rgba(118,181,27,0.12); font-weight: 850; font-size: 12.5px; padding: 6px 14px; border-radius: 30px; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="bi bi-patch-check-fill" style="color: var(--primary);"></i> ناجح
                            </span>
                        @else
                            <span class="badge" style="background: rgba(195,14,20,0.06); color: var(--danger); border: 1.5px solid rgba(195,14,20,0.12); font-weight: 850; font-size: 12.5px; padding: 6px 14px; border-radius: 30px; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="bi bi-exclamation-triangle-fill" style="color: var(--danger);"></i> متعثر
                            </span>
                        @endif
                    </td>
                    <td style="padding: 16px 24px; color: #64748b; font-weight: 700; font-size: 13px;">{{ $r->submitted_at?->diffForHumans() }}</td>
                    <td style="padding: 16px 24px; text-align: center;">
                        <a href="{{ route('admin.results.show', $r) }}" class="btn-action-icon btn-action-edit" style="width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; background: rgba(8,145,178,.08); color: #0891b2; border: 1.5px solid rgba(8,145,178,.15); transition: all 0.2s;" title="عرض تفاصيل النتيجة المكتملة">
                            <i class="bi bi-eye-fill" style="font-size: 14px;"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

@push('scripts')
<script>
// تفعيل الإضافات لـ Chart.js
Chart.register(ChartDataLabels);

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. مخطط Doughnut الاحترافي مع تدرجات لونية براقة (Glowing Linear Gradients)
    const ctxResults = document.getElementById('resultsChart');
    if (ctxResults) {
        const resultsCanvas = ctxResults.getContext('2d');
        
        // تدرج اللون الأخضر للناجحين
        const gradPass = resultsCanvas.createLinearGradient(0, 0, 0, 300);
        gradPass.addColorStop(0, '#8ec924');
        gradPass.addColorStop(1, '#5f9416');
        
        // تدرج اللون الأحمر للمتعثرين
        const gradFail = resultsCanvas.createLinearGradient(0, 0, 0, 300);
        gradFail.addColorStop(0, '#ef4444');
        gradFail.addColorStop(1, '#c30e14');

        new Chart(ctxResults, {
            type: 'doughnut',
            data: {
                labels: ['ناجحين', 'متعثرين'],
                datasets: [{
                    data: [{{ $stats['passed'] }}, {{ $stats['failed'] }}],
                    backgroundColor: [gradPass, gradFail],
                    hoverOffset: 8,
                    borderWidth: 0,
                    weight: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '76%',
                plugins: {
                    legend: { 
                        position: 'bottom', 
                        labels: { 
                            font: { family: 'Tajawal', weight: '800', size: 12.5 },
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20,
                            color: '#1e293b'
                        } 
                    },
                    datalabels: {
                        color: '#fff',
                        font: { family: 'Tajawal', weight: '900', size: 13 },
                        formatter: (value, context) => {
                            let sum = 0;
                            let dataArr = context.chart.data.datasets[0].data;
                            dataArr.map(data => { sum += data; });
                            let percentage = sum > 0 ? Math.round((value / sum) * 100) + '%' : '';
                            return value > 0 ? value + ' (' + percentage + ')' : '';
                        },
                        display: 'auto'
                    }
                }
            }
        });
    }

    // 2. مخطط الأعمدة لأداء الطلاب حسب الصف مع تدرجات لونية رأسية مذهلة
    const ctxGrades = document.getElementById('gradesChart');
    if (ctxGrades) {
        const gradesCanvas = ctxGrades.getContext('2d');
        
        // تدرجات الأعمدة الخضراء والحمراء
        const barGradPass = gradesCanvas.createLinearGradient(0, 0, 0, 250);
        barGradPass.addColorStop(0, '#8ec924');
        barGradPass.addColorStop(1, 'rgba(118,181,27,0.3)');

        const barGradFail = gradesCanvas.createLinearGradient(0, 0, 0, 250);
        barGradFail.addColorStop(0, '#ef4444');
        barGradFail.addColorStop(1, 'rgba(195,14,20,0.3)');

        new Chart(ctxGrades, {
            type: 'bar',
            data: {
                labels: {! json_encode($resultsByGrade->pluck('name')) !!},
                datasets: [
                    {
                        label: 'ناجحين',
                        data: {! json_encode($resultsByGrade->pluck('passed')) !!},
                        backgroundColor: barGradPass,
                        borderRadius: 8,
                        borderSkipped: false,
                        barPercentage: 0.6,
                        categoryPercentage: 0.5
                    },
                    {
                        label: 'متعثرين',
                        data: {! json_encode($resultsByGrade->pluck('failed')) !!},
                        backgroundColor: barGradFail,
                        borderRadius: 8,
                        borderSkipped: false,
                        barPercentage: 0.6,
                        categoryPercentage: 0.5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: 'rgba(226, 232, 240, 0.5)', drawBorder: false },
                        ticks: { 
                            precision: 0, 
                            font: { family: 'Tajawal', weight: '700', size: 11 }, 
                            color: '#64748b',
                            padding: 8
                        } 
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { 
                            font: { family: 'Tajawal', weight: '850', size: 12 }, 
                            color: '#1e293b',
                            padding: 8
                        } 
                    }
                },
                plugins: {
                    legend: { 
                        position: 'top', 
                        labels: { 
                            font: { family: 'Tajawal', weight: '800', size: 12.5 },
                            usePointStyle: true,
                            pointStyle: 'circle',
                            color: '#1e293b',
                            padding: 16
                        } 
                    },
                    tooltip: { 
                        titleFont: { family: 'Tajawal', weight: '900' }, 
                        bodyFont: { family: 'Tajawal' },
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        padding: 12,
                        cornerRadius: 10
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#64748b',
                        font: { family: 'Tajawal', size: 11, weight: '850' },
                        formatter: (value) => value > 0 ? value : '',
                        offset: 4
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
