@extends('layouts.admin')

@section('title', 'إدارة الصفوف الدراسية')
@section('page-title', 'الصفـوف الدراسيـة')

@section('breadcrumb')
    <span style="color: var(--text-muted); font-weight: 500;">إدارة الصفوف</span>
    <span style="color: #cbd5e0; margin: 0 4px;"><i class="bi bi-chevron-left" style="font-size: 10px;"></i></span>
    <span style="color: var(--text-main); font-weight: 700;">قائمة الصفوف</span>
@endsection

@push('styles')
<style>
    /* ── بنر مؤشرات الصفوف الفاخرة (Academic Levels BI Banner) ── */
    .grades-insights-banner {
        display: grid; grid-template-columns: 1.2fr 1.5fr; gap: 24px; margin-bottom: 32px;
    }
    @media (max-width: 992px) {
        .grades-insights-banner { grid-template-columns: 1fr; }
    }
    
    .grades-main-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: 24px; padding: 32px; color: #fff;
        display: flex; flex-direction: column; justify-content: space-between; gap: 20px;
        position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(118, 181, 27, 0.2);
    }
    .grades-main-card::after {
        content: '\F448'; font-family: 'bootstrap-icons'; position: absolute;
        left: -15px; bottom: -20px; font-size: 100px; opacity: 0.12; pointer-events: none;
    }
    .grades-main-content { display: flex; flex-direction: column; gap: 6px; }
    .grades-main-content .hero-lbl { font-size: 13px; font-weight: 800; opacity: 0.9; text-transform: uppercase; }
    .grades-main-content .hero-val { font-size: 30px; font-weight: 950; }
    
    .btn-create-grade {
        display: inline-flex; align-items: center; gap: 8px; background: #ffffff; color: var(--primary-dark);
        padding: 12px 24px; border-radius: 12px; font-size: 13.5px; font-weight: 850; text-decoration: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: all 0.2s; border: none; width: fit-content;
    }
    .btn-create-grade:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); color: var(--primary); }

    .grades-side-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
    }
    @media (max-width: 576px) {
        .grades-side-grid { grid-template-columns: 1fr; }
    }
    
    .grades-side-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 24px;
        display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .grades-side-card:hover { transform: translateY(-2px); box-shadow: 0 12px 20px rgba(0,0,0,0.02); }
    
    .side-card-icon {
        width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center;
        font-size: 24px; flex-shrink: 0;
    }
    .side-card-lbl { font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 4px; display: block; }
    .side-card-val { font-size: 20px; font-weight: 900; color: #1e293b; }

    /* ── شريط التحكم العلوي (Toolbar) ── */
    .page-toolbar {
        display: flex; flex-direction: column; gap: 16px; align-items: stretch;
        background: #fff; padding: 24px; border-radius: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.02); border: 1px solid rgba(226, 232, 240, 0.8);
        margin-bottom: 28px;
    }

    .toolbar-actions-row {
        display: flex; flex-wrap: wrap; gap: 16px; align-items: center; justify-content: space-between;
    }

    .toolbar-filters-form {
        display: flex; gap: 12px; flex: 1; max-width: 650px; justify-content: flex-end; align-items: center;
    }
    @media (max-width: 768px) {
        .toolbar-filters-form { max-width: 100%; width: 100%; }
    }
    
    /* مجموعة الحقل مع أيقونة */
    .input-icon-group {
        position: relative; width: 100%; flex: 1; min-width: 240px;
    }
    .input-icon-group i {
        position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
        color: #94a3b8; font-size: 15px; pointer-events: none; transition: color 0.2s; z-index: 10;
    }
    .input-icon-group input { 
        width: 100%; padding: 11px 16px 11px 40px; border-radius: 12px; 
        border: 1.5px solid #cbd5e1; font-size: 13px; background: #fff; transition: all 0.2s;
        outline: none;
    }
    .input-icon-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-light); }
    .input-icon-group:focus-within i { color: var(--primary); }

    /* ── صناديق الاختيار الاحترافية (Checkboxes) ── */
    .custom-checkbox {
        width: 18px; height: 18px; accent-color: var(--primary);
        cursor: pointer; border-radius: 6px; transition: transform 0.15s ease;
    }
    .custom-checkbox:hover { transform: scale(1.1); }

    /* ── شريط الإجراءات الجماعية (Bulk Actions Toolbar) ── */
    .bulk-actions-toolbar {
        background: rgba(195, 14, 20, 0.05); 
        border: 1.5px solid rgba(195, 14, 20, 0.15);
        padding: 16px 24px; border-radius: 20px;
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 24px; display: none; /* مخفي افتراضياً */
        animation: slideDown 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 4px 12px rgba(195, 14, 20, 0.02);
    }
    .bulk-actions-toolbar .selected-count { 
        color: var(--danger); font-weight: 800; font-size: 14px; 
        display: flex; align-items: center; gap: 8px;
    }
    @keyframes slideDown { 
        from { opacity: 0; transform: translateY(-12px); } 
        to { opacity: 1; transform: translateY(0); } 
    }

    /* ── لوحة الفلاتر النشطة والتاغات ── */
    .active-tag-pill {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(118, 181, 27, 0.06); color: var(--primary-dark);
        border: 1.5px solid rgba(118, 181, 27, 0.12); border-radius: 30px;
        padding: 6px 14px; font-size: 12.5px; font-weight: 800;
        animation: popScale 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .active-tag-pill button {
        background: none; border: none; color: var(--primary-dark);
        font-size: 16px; cursor: pointer; padding: 0; display: flex; align-items: center;
    }
    .active-tag-pill button:hover { color: var(--danger); }
    @keyframes popScale {
        0% { transform: scale(0.8); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    /* ── شبكة البطاقات ── */
    .grades-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; margin-bottom: 28px;
        transition: opacity 0.2s ease-in-out;
    }

    /* ── تصميم بطاقة الصفوف الدراسية الفاخر ── */
    .grade-card {
        background: #fff; border-radius: 24px; border: 1px solid #e2e8f0;
        padding: 26px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex; flex-direction: column; position: relative;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01), 0 2px 4px -1px rgba(0,0,0,0.01);
    }
    .grade-card:hover { transform: translateY(-4px); box-shadow: 0 16px 32px rgba(0,0,0,0.05); border-color: var(--theme-color, var(--primary)); }

    /* رأس البطاقة */
    .card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
    
    .icon-wrapper {
        width: 52px; height: 52px; border-radius: 14px;
        background: var(--theme-grad);
        color: var(--theme-color); display: flex; align-items: center; justify-content: center;
        font-size: 20px; font-weight: 900; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.4);
    }

    .select-card-wrapper {
        display: flex; align-items: center; gap: 10px;
    }
    
    /* معلومات المادة */
    .grade-info { flex: 1; }
    .grade-info h3 { font-size: 19px; font-weight: 900; color: #1e293b; margin-bottom: 8px; line-height: 1.3; }
    .grade-desc { font-size: 13.5px; color: #64748b; margin-bottom: 20px; line-height: 1.5; min-height: 40px; }
    
    /* بطاقات المقاييس الفرعية للبطاقة */
    .grade-metrics-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;
    }
    .grade-metric-box {
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px;
        display: flex; flex-direction: column; gap: 4px;
    }
    .grade-metric-lbl { font-size: 11px; font-weight: 750; color: #94a3b8; }
    .grade-metric-val { font-size: 14px; font-weight: 800; color: #334155; display: flex; align-items: center; gap: 6px; }

    /* أزرار الإجراءات */
    .card-actions-wrapper {
        display: flex; gap: 10px; align-items: center;
    }
    .btn-action-icon {
        width: 44px; height: 44px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;
        border: 1.5px solid transparent; transition: all 0.2s; cursor: pointer; text-decoration: none; background: transparent;
    }
    .btn-action-edit { background: rgba(8,145,178,.06); color: #0891b2; border-color: rgba(8,145,178,.12); }
    .btn-action-edit:hover { background: #0891b2; color: #fff; }
    
    .btn-action-delete { background: rgba(195,14,20,.06); color: var(--danger); border-color: rgba(195,14,20,.12); }
    .btn-action-delete:hover { background: var(--danger); color: #fff; }

    .btn-manage-subjects {
        flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;
        padding: 12px 16px; border-radius: 12px; font-size: 13.5px; font-weight: 850;
        color: var(--theme-color); background: transparent; border: 2px solid var(--theme-color-light);
        text-decoration: none; transition: all 0.3s;
    }
    .grade-card:hover .btn-manage-subjects { background: var(--theme-color); color: #fff; border-color: var(--theme-color); }

    /* ── الترقيم المطور المدمج مع لارافل (Pagination) ── */
    .pagination-wrapper {
        background: #ffffff; border: 1px solid rgba(226, 232, 240, 0.8);
        padding: 20px 28px; border-radius: 16px; margin-top: 24px;
    }
</style>
@endpush

@section('content')

@php
    $totalGrades = $grades->count();
    $totalSubjects = $grades->sum('subjects_count');
    $averageSubjects = $totalGrades > 0 ? round($totalSubjects / $totalGrades) : 0;
@endphp

<!-- بنر تحليلات وإحصائيات الصفوف الدراسية المطور -->
<div class="grades-insights-banner">
    <div class="grades-main-card">
        <div class="grades-main-content">
            <span class="hero-lbl"><i class="bi bi-layers-fill"></i> لوحة مؤشرات الصفوف والمراحل</span>
            <span class="hero-val">{{ $totalCount ?? 0 }} <span style="font-size: 16px; font-weight: 700; opacity: 0.85;">مراحل دراسية نشطة</span></span>
        </div>
        <a href="{{ route('admin.grades.create') }}" class="btn-create-grade">
            <i class="bi bi-plus-lg"></i> إضافة مرحلة / صف جديد
        </a>
    </div>
    
    <div class="grades-side-grid">
        <div class="grades-side-card">
            <i class="bi bi-book-half side-card-icon" style="color: #2563eb; background: #eff6ff;"></i>
            <div>
                <span class="side-card-lbl">إجمالي المواد النشطة</span>
                <span class="side-card-val">{{ $totalSubjects }} مواد</span>
            </div>
        </div>
        <div class="grades-side-card">
            <i class="bi bi-diagram-3 side-card-icon" style="color: #16a34a; background: #f0fdf4;"></i>
            <div>
                <span class="side-card-lbl">متوسط المقررات لكل صف</span>
                <span class="side-card-val">{{ $averageSubjects }} مقررات</span>
            </div>
        </div>
    </div>
</div>

<!-- شريط التحكم والتصفية الفورية بالـ AJAX وبحث فوري مع جميع الفلاتر -->
<div class="page-toolbar">
    
    <div class="toolbar-actions-row">
        <!-- تحديد الكل للمسح الجماعي -->
        <div class="select-card-wrapper">
            <input type="checkbox" id="selectAllCheckbox" class="custom-checkbox">
            <label for="selectAllCheckbox" style="font-size: 14px; font-weight: 800; color: var(--text-main); cursor: pointer; user-select: none; display: flex; align-items: center; gap: 6px;">
                <i class="bi bi-check2-all" style="color: var(--primary); font-size: 16px;"></i> تحديد الكل للإجراءات الجماعية
            </label>
        </div>

        <form method="GET" action="{{ route('admin.grades.index') }}" id="gradesFilterForm" class="toolbar-filters-form" onsubmit="return false;">
            <!-- البحث بالنص -->
            <div class="input-icon-group">
                <i class="bi bi-search"></i>
                <input type="text" name="search" id="searchInput" placeholder="ابحث باسم الصف أو الوصف..." value="{{ request('search') }}" autocomplete="off">
                <!-- سبنر البحث الفوري -->
                <div class="spinner-border text-primary" id="searchSpinner" role="status" style="position: absolute; left: 14px; top: 30%; width: 18px; height: 18px; display: none;"></div>
            </div>
            
            <!-- زر التصفية المتقدمة لفتح الجارور السفلي -->
            <button type="button" id="toggleAdvancedFilters" style="padding: 11px 20px; border-radius: 12px; border: 1.5px solid #cbd5e1; background: #fff; color: var(--text-main); font-weight: 800; font-size: 13.5px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;">
                <i class="bi bi-funnel-fill" style="color: var(--primary);"></i>
                <span>فلاتر متقدمة</span>
                <i class="bi bi-chevron-down" id="filtersChevron" style="font-size: 11px; transition: transform 0.2s;"></i>
            </button>
        </form>
    </div>

    <!-- لوحة التصفية المتقدمة التفاعلية القابلة للمط (Advanced Drawer Panel) -->
    <div id="advancedFiltersPanel" style="display: none; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; padding-top: 16px; border-top: 1.5px dashed #f1f5f9;">
        <!-- 1. سعة المواد -->
        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 11.5px; font-weight: 850; color: #64748b; text-transform: uppercase;">سعة المواد الدراسية</label>
            <select name="subjects_filter" id="subjectsFilterSelect" onchange="fetchFilteredGrades()" style="padding: 11px 16px; border-radius: 12px; border: 1.5px solid #cbd5e1; font-size: 13px; color: var(--text-main); background: #f8fafc; cursor: pointer; outline: none; transition: all 0.2s;">
                <option value="">كل الحالات</option>
                <option value="has_subjects" {{ request('subjects_filter') === 'has_subjects' ? 'selected' : '' }}>يحتوي على مواد</option>
                <option value="no_subjects" {{ request('subjects_filter') === 'no_subjects' ? 'selected' : '' }}>بدون مواد (فارغ)</option>
            </select>
        </div>

        <!-- 2. ارتباط الاختبارات -->
        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 11.5px; font-weight: 850; color: #64748b; text-transform: uppercase;">ارتباط الاختبارات</label>
            <select name="exams_filter" id="examsFilterSelect" onchange="fetchFilteredGrades()" style="padding: 11px 16px; border-radius: 12px; border: 1.5px solid #cbd5e1; font-size: 13px; color: var(--text-main); background: #f8fafc; cursor: pointer; outline: none; transition: all 0.2s;">
                <option value="">كل الحالات</option>
                <option value="has_exams" {{ request('exams_filter') === 'has_exams' ? 'selected' : '' }}>يحتوي على اختبارات نشطة</option>
                <option value="no_exams" {{ request('exams_filter') === 'no_exams' ? 'selected' : '' }}>لا يحتوي على اختبارات</option>
            </select>
        </div>

        <!-- 3. فرز الترتيب -->
        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 11.5px; font-weight: 850; color: #64748b; text-transform: uppercase;">ترتيب وفرز القائمة حسب</label>
            <select name="sort_by" id="sortBySelect" onchange="fetchFilteredGrades()" style="padding: 11px 16px; border-radius: 12px; border: 1.5px solid #cbd5e1; font-size: 13px; color: var(--text-main); background: #f8fafc; cursor: pointer; outline: none; transition: all 0.2s;">
                <option value="">الترتيب الأكاديمي (الافتراضي)</option>
                <option value="subjects_count" {{ request('sort_by') === 'subjects_count' ? 'selected' : '' }}>المقررات الأكثر مواداً</option>
                <option value="exams_count" {{ request('sort_by') === 'exams_count' ? 'selected' : '' }}>المقررات الأكثر اختبارات</option>
            </select>
        </div>
    </div>

    <!-- وسوم وحبوب التصفية النشطة الفورية (Interactive Pill Tags) -->
    <div id="activeFilterTags" style="display: none; flex-wrap: wrap; gap: 10px; align-items: center; padding-top: 14px; border-top: 1.5px solid #f1f5f9; margin-top: 14px;">
        <span style="font-size: 12.5px; font-weight: 850; color: #64748b; display: inline-flex; align-items: center; gap: 6px;">
            <i class="bi bi-funnel-fill" style="color: var(--primary);"></i> الفلاتر النشطة حالياً:
        </span>
        <div id="tagsContainer" style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center;"></div>
        <button type="button" onclick="clearAllGradesFilters()" style="background: none; border: none; color: var(--danger); font-size: 12.5px; font-weight: 850; cursor: pointer; text-decoration: underline; padding-right: 10px;">
            مسح جميع الفلاتر
        </button>
    </div>
</div>

<!-- شريط الإجراءات الجماعية المطور -->
<div class="bulk-actions-toolbar" id="bulkActionsToolbar">
    <div class="selected-count">
        <i class="bi bi-check2-square"></i> تم تحديد <span id="selectedCountText">0</span> صفوف دراسية
    </div>
    <div style="display: flex; gap: 8px;">
        <button type="button" class="btn btn-secondary btn-sm" onclick="exportSelectedExcel()" style="border: 1.5px solid var(--border); background: #ffffff; color: var(--text-main); font-weight: 800; border-radius: 12px; padding: 10px 20px; display: inline-flex; align-items: center; gap: 6px; height: 42px; cursor: pointer;">
            <i class="bi bi-file-earmark-arrow-down-fill" style="color: #16a34a;"></i> تصدير المحدد (Excel)
        </button>
        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteMultiple()" style="background: var(--danger); border: none; color: #ffffff; font-weight: 800; border-radius: 12px; padding: 10px 20px; display: inline-flex; align-items: center; gap: 6px; height: 42px; cursor: pointer; box-shadow: 0 4px 12px rgba(195,14,20,0.15);">
            <i class="bi bi-trash-fill"></i> حذف الصفوف المحددة
        </button>
    </div>
</div>

<!-- شبكة الصفوف التفاعلية والمقرونة بالـ AJAX -->
<div class="grades-grid" id="dataGrid">
    @if($grades->isEmpty())
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #fff; border-radius: 24px; border: 1px dashed #cbd5e0;">
            <div style="font-size: 48px; color: #cbd5e0; margin-bottom: 16px;"><i class="bi bi-journal-x"></i></div>
            <h3 style="font-size: 18px; font-weight: 850; color: var(--text-main);">لم يتم العثور على صفوف دراسية</h3>
            <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 20px;">لم نعثر على أي صفوف تطابق معايير التصفية والبحث المحددة.</p>
            <button type="button" onclick="clearAllGradesFilters()" class="btn btn-primary" style="border-radius: 10px; padding: 8px 20px; font-weight: 800; background: var(--primary); border: none;">مسح الفلاتر والعودة</button>
        </div>
    @else
        <!-- لوحة الألوان الخاصة بالصفوف -->
        @php
            $colors = [
                'blue'   => ['bg' => 'rgba(37,99,235,0.08)', 'text' => '#2563eb', 'grad' => 'linear-gradient(135deg, rgba(37,99,235,0.12) 0%, rgba(37,99,235,0.02) 100%)'],
                'purple' => ['bg' => 'rgba(139,92,246,0.08)', 'text' => '#8b5cf6', 'grad' => 'linear-gradient(135deg, rgba(139,92,246,0.12) 0%, rgba(139,92,246,0.02) 100%)'],
                'green'  => ['bg' => 'rgba(118,181,27,0.08)', 'text' => '#76b51b', 'grad' => 'linear-gradient(135deg, rgba(118,181,27,0.12) 0%, rgba(118,181,27,0.02) 100%)'],
                'orange' => ['bg' => 'rgba(245,158,11,0.08)', 'text' => '#f59e0b', 'grad' => 'linear-gradient(135deg, rgba(245,158,11,0.12) 0%, rgba(245,158,11,0.02) 100%)'],
                'teal'   => ['bg' => 'rgba(13,148,136,0.08)', 'text' => '#0d9488', 'grad' => 'linear-gradient(135deg, rgba(13,148,136,0.12) 0%, rgba(13,148,136,0.02) 100%)']
            ];
            $colorKeys = array_keys($colors);
        @endphp

        @foreach($grades as $grade)
            @php
                $colorKey = $colorKeys[$grade->id % count($colorKeys)];
                $theme = $colors[$colorKey];
            @endphp
            
            <div class="grade-card data-card" style="--theme-color: {{ $theme['text'] }}; --theme-grad: {{ $theme['grad'] }}; --theme-color-light: {{ $theme['bg'] }}; animate-slideUp">
                
                <div class="card-top">
                    <!-- أيقونة الترتيب الرقمية الأنيقة بلون الصف المخصص -->
                    <div class="icon-wrapper">
                        #{{ $grade->order }}
                    </div>
                    
                    <!-- صندوق اختيار للتصدير أو الحذف الجماعي -->
                    <input type="checkbox" class="custom-checkbox row-checkbox" value="{{ $grade->id }}">
                </div>

                <div class="grade-info">
                    <h3 class="grade-name">{{ $grade->name }}</h3>
                    <p class="grade-desc">{{ $grade->description ?? 'لا يوجد وصف مخصص لهذا الصف الدراسي حالياً.' }}</p>
                    
                    <!-- إحصائيات سريعة فريدة داخل الكرت -->
                    <div class="grade-metrics-grid">
                        <div class="grade-metric-box">
                            <span class="grade-metric-lbl">المواد المرتبطة</span>
                            <span class="grade-metric-val">
                                <i class="bi bi-book-half" style="color: {{ $theme['text'] }};"></i>
                                {{ $grade->subjects_count }} مواد
                            </span>
                        </div>
                        <div class="grade-metric-box">
                            <span class="grade-metric-lbl">الاختبارات النشطة</span>
                            <span class="grade-metric-val">
                                <i class="bi bi-file-earmark-check-fill" style="color: #16a34a;"></i>
                                {{ $grade->exams_count ?? 0 }} اختبار
                            </span>
                        </div>
                    </div>
                </div>

                <!-- الإجراءات الخاصة بالبطاقة -->
                <div class="card-actions-wrapper">
                    <a href="{{ route('admin.subjects.index', ['grade_id' => $grade->id]) }}" class="btn-manage-subjects">
                        <span>إدارة المواد</span>
                        <i class="bi bi-arrow-left-circle-fill"></i>
                    </a>
                    
                    <a href="{{ route('admin.grades.edit', $grade) }}" class="btn-action-icon btn-action-edit" title="تعديل الصف">
                        <i class="bi bi-pencil-fill"></i>
                    </a>
                    
                    <form id="del-grade-{{ $grade->id }}" action="{{ route('admin.grades.destroy', $grade) }}" method="POST" style="margin: 0; display: inline-block;">
                        @csrf @method('DELETE')
                        <button type="button" class="btn-action-icon btn-action-delete" title="حذف الصف" onclick="confirmDelete('del-grade-{{ $grade->id }}', 'هل أنت متأكد من رغبتك في حذف هذا الصف الدراسي بالكامل؟')">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </form>
                </div>
                
            </div>
        @endforeach
    @endif
</div>

<!-- حاوية الترقيم للتصفية الفورية بالـ AJAX -->
<div id="pagination-container">
    @if($grades->hasPages())
        <div class="pagination-wrapper">
            {{ $grades->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    // ── دالة لتحديث شريط الإجراءات الجماعية ──
    function initBulkActions() {
        const selectAllBtn = document.getElementById('selectAllCheckbox');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkToolbar = document.getElementById('bulkActionsToolbar');
        const countText = document.getElementById('selectedCountText');

        function updateBulkToolbar() {
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            if (checkedCount > 0) {
                bulkToolbar.style.display = 'flex';
                countText.textContent = checkedCount;
            } else {
                bulkToolbar.style.display = 'none';
            }

            if (selectAllBtn) {
                selectAllBtn.checked = checkedCount === rowCheckboxes.length && rowCheckboxes.length > 0;
            }
        }

        if (selectAllBtn) {
            const newSelectAll = selectAllBtn.cloneNode(true);
            selectAllBtn.parentNode.replaceChild(newSelectAll, selectAllBtn);

            newSelectAll.addEventListener('change', function() {
                const isChecked = this.checked;
                const freshCheckboxes = document.querySelectorAll('.row-checkbox');
                freshCheckboxes.forEach(cb => {
                    cb.checked = isChecked;
                });
                updateBulkToolbar();
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkToolbar);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initBulkActions();

        // ── 1. فتح وإغلاق جارور الفلاتر المتقدمة ──
        const toggleFiltersBtn = document.getElementById('toggleAdvancedFilters');
        const filtersPanel = document.getElementById('advancedFiltersPanel');
        const filtersChevron = document.getElementById('filtersChevron');

        if (toggleFiltersBtn && filtersPanel) {
            toggleFiltersBtn.addEventListener('click', function() {
                if (filtersPanel.style.display === 'none') {
                    filtersPanel.style.display = 'grid';
                    filtersChevron.style.transform = 'rotate(180deg)';
                } else {
                    filtersPanel.style.display = 'none';
                    filtersChevron.style.transform = 'rotate(0deg)';
                }
            });
        }

        // ── 2. محرك الفلاتر وتحديث شارات وسوم البحث ──
        const searchInput = document.getElementById('searchInput');
        const searchSpinner = document.getElementById('searchSpinner');
        const filterForm = document.getElementById('gradesFilterForm');
        const dataGrid = document.getElementById('dataGrid');
        const pagContainer = document.getElementById('pagination-container');
        
        const subjectsSelect = document.getElementById('subjectsFilterSelect');
        const examsSelect = document.getElementById('examsFilterSelect');
        const sortBySelect = document.getElementById('sortBySelect');
        
        const tagsWrapper = document.getElementById('activeFilterTags');
        const tagsContainer = document.getElementById('tagsContainer');

        function renderActiveTags() {
            let tagsHtml = '';
            let hasActive = false;

            if (subjectsSelect && subjectsSelect.value) {
                const text = subjectsSelect.options[subjectsSelect.selectedIndex].text;
                tagsHtml += `<span class="active-tag-pill">المواد: ${text} <button type="button" onclick="clearSpecificFilter('subjects')">&times;</button></span>`;
                hasActive = true;
            }
            if (examsSelect && examsSelect.value) {
                const text = examsSelect.options[examsSelect.selectedIndex].text;
                tagsHtml += `<span class="active-tag-pill">الاختبارات: ${text} <button type="button" onclick="clearSpecificFilter('exams')">&times;</button></span>`;
                hasActive = true;
            }
            if (sortBySelect && sortBySelect.value) {
                const text = sortBySelect.options[sortBySelect.selectedIndex].text;
                tagsHtml += `<span class="active-tag-pill">الفرز: ${text} <button type="button" onclick="clearSpecificFilter('sort')">&times;</button></span>`;
                hasActive = true;
            }

            if (hasActive) {
                tagsContainer.innerHTML = tagsHtml;
                tagsWrapper.style.display = 'flex';
            } else {
                tagsContainer.innerHTML = '';
                tagsWrapper.style.display = 'none';
            }
        }

        window.clearSpecificFilter = function(type) {
            if (type === 'subjects') subjectsSelect.value = '';
            if (type === 'exams') examsSelect.value = '';
            if (type === 'sort') sortBySelect.value = '';
            
            fetchFilteredGrades();
        }

        window.clearAllGradesFilters = function() {
            if (searchInput) searchInput.value = '';
            if (subjectsSelect) subjectsSelect.value = '';
            if (examsSelect) examsSelect.value = '';
            if (sortBySelect) sortBySelect.value = '';
            
            fetchFilteredGrades();
        }

        function debounce(func, delay) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }

        window.fetchFilteredGrades = function() {
            if (searchSpinner) searchSpinner.style.display = 'inline-block';
            dataGrid.style.opacity = '0.5';
            
            renderActiveTags();
            
            // تجميع كامل بارامترات الاستعلام
            const formData = new FormData(filterForm);
            
            if (subjectsSelect && subjectsSelect.value) formData.append('subjects_filter', subjectsSelect.value);
            if (examsSelect && examsSelect.value) formData.append('exams_filter', examsSelect.value);
            if (sortBySelect && sortBySelect.value) formData.append('sort_by', sortBySelect.value);
            
            const queryParams = new URLSearchParams(formData).toString();
            const url = `{{ route('admin.grades.index') }}?${queryParams}`;
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newGrid = doc.getElementById('dataGrid');
                const newPag = doc.getElementById('pagination-container');
                
                if (newGrid) {
                    dataGrid.innerHTML = newGrid.innerHTML;
                }
                if (pagContainer) {
                    pagContainer.innerHTML = newPag ? newPag.innerHTML : '';
                }
                
                initBulkActions();
                dataGrid.style.opacity = '1';
                if (searchSpinner) searchSpinner.style.display = 'none';
            })
            .catch(error => {
                console.error('Error fetching grades:', error);
                dataGrid.style.opacity = '1';
                if (searchSpinner) searchSpinner.style.display = 'none';
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', debounce(fetchFilteredGrades, 350));
        }

        // الاستدعاء الأولي لرسم التاغات إذا تم إعادة تحميل الصفحة ببارامترات
        renderActiveTags();

        // اعتراض أزرار صفحات Laravel وتمرير طلباتها بالـ AJAX لتأكيد السرعة
        document.addEventListener('click', function(e) {
            const pageLink = e.target.closest('.pagination a');
            if (pageLink && pagContainer.contains(e.target)) {
                e.preventDefault();
                const url = pageLink.getAttribute('href');
                
                dataGrid.style.opacity = '0.5';
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newGrid = doc.getElementById('dataGrid');
                        const newPag = doc.getElementById('pagination-container');
                        if (newGrid) {
                            dataGrid.innerHTML = newGrid.innerHTML;
                        }
                        if (pagContainer) {
                            pagContainer.innerHTML = newPag ? newPag.innerHTML : '';
                        }
                        initBulkActions();
                        dataGrid.style.opacity = '1';
                        window.scrollTo({ top: dataGrid.offsetTop - 100, behavior: 'smooth' });
                    })
                    .catch(() => {
                        dataGrid.style.opacity = '1';
                    });
            }
        });

        // ── التصدير الفوري المحدد إلى ملف Excel (Excel Export) ──
        window.exportSelectedExcel = function() {
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkedBoxes.length === 0) return;
            
            let csvContent = "\uFEFF"; // إشارة دعم الترميز العربي في ملفات Excel
            csvContent += "الترتيب الرقمي,اسم الصف الدراسي,الوصف,عدد المواد,عدد الاختبارات\n";
            
            checkedBoxes.forEach(cb => {
                const card = cb.closest('.grade-card');
                if (card) {
                    const order = card.querySelector('.icon-wrapper').textContent.replace('#', '').trim();
                    const name = card.querySelector('.grade-name').textContent.trim();
                    const desc = card.querySelector('.grade-desc').textContent.trim();
                    const boxes = card.querySelectorAll('.grade-metric-val');
                    const subjects = boxes[0].textContent.replace('مواد', '').trim();
                    const exams = boxes[1].textContent.replace('اختبار', '').trim();
                    
                    csvContent += `"${order}","${name}","${desc}","${subjects}","${exams}"\n`;
                }
            });
            
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement("a");
            link.setAttribute("href", url);
            link.setAttribute("download", `صفوف_دراسية_محددة_${new Date().toISOString().slice(0,10)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };

        // ── الحذف الجماعي الآمن عبر AJAX بالتزامن مع Laravel ──
        window.confirmDeleteMultiple = function() {
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            if (ids.length === 0) return;
            
            if (confirm(`تحذير: هل أنت متأكد من رغبتك في حذف ${ids.length} صفوف دراسية مختارة بالكامل؟ سيؤدي ذلك لحذف المقررات المرتبطة بها!`)) {
                dataGrid.style.opacity = '0.5';
                
                let promises = ids.map(id => {
                    return fetch(`/admin/grades/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ _method: 'DELETE' })
                    });
                });
                
                Promise.all(promises).then(() => {
                    window.location.reload();
                }).catch(err => {
                    alert('حدث خطأ أثناء تنفيذ الحذف الجماعي.');
                    dataGrid.style.opacity = '1';
                });
            }
        };
    });
</script>
@endpush
