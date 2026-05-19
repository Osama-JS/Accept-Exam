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
        background: #ffffff; 
        border: 1px solid rgba(226, 232, 240, 0.8);
        padding: 16px 24px; 
        border-radius: 16px; 
        margin-top: 32px;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }
    
    /* تنسيق أسلوب Bootstrap Pagination إن وجد */
    .pagination-wrapper .pagination {
        display: flex;
        gap: 6px;
        list-style: none;
        padding: 0;
        margin: 0;
        align-items: center;
    }
    .pagination-wrapper .page-item .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 14px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #475569;
        font-weight: 750;
        font-size: 13.5px;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pagination-wrapper .page-item.active .page-link {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
        box-shadow: 0 4px 12px var(--primary-light);
    }
    .pagination-wrapper .page-item:not(.active):not(.disabled) .page-link:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-light);
        transform: translateY(-1px);
    }
    .pagination-wrapper .page-item.disabled .page-link {
        color: #cbd5e1;
        background: #f8fafc;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }

    /* تنسيق أسلوب Tailwind Pagination الافتراضي من لارافل */
    .pagination-wrapper nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }
    .pagination-wrapper nav > div:first-child {
        font-size: 13px;
        color: #64748b;
        font-weight: 700;
    }
    .pagination-wrapper nav > div:last-child {
        display: flex;
        gap: 4px;
        align-items: center;
    }
    .pagination-wrapper nav a, 
    .pagination-wrapper nav span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0 12px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #475569;
        font-weight: 750;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: none !important;
    }
    .pagination-wrapper nav span[aria-current="page"] {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff !important;
        box-shadow: 0 4px 12px var(--primary-light) !important;
    }
    .pagination-wrapper nav a:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-light);
        transform: translateY(-1px);
    }
    .pagination-wrapper nav span[aria-disabled="true"] {
        color: #cbd5e1 !important;
        background: #f8fafc;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
    .pagination-wrapper nav svg {
        width: 16px;
        height: 16px;
    }

    /* ── نافذة إدارة المواد التفاعلية (Manage Subjects Modal Styling) ── */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
        z-index: 1100; display: flex; align-items: center; justify-content: center;
        padding: 20px; animation: fadeInModal 0.2s ease-out;
    }
    @keyframes fadeInModal {
        from { opacity: 0; } to { opacity: 1; }
    }
    
    .modal-card {
        background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0;
        width: 100%; max-width: 600px; display: flex; flex-direction: column;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        animation: slideInModal 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        overflow: hidden;
    }
    @keyframes slideInModal {
        from { transform: translateY(30px) scale(0.95); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
    }
    
    .modal-header {
        padding: 24px; border-bottom: 1px solid #f1f5f9; display: flex;
        justify-content: space-between; align-items: center; background: #f8fafc;
    }
    .modal-title {
        font-size: 18px; font-weight: 900; color: #1e293b; margin: 0;
    }
    .modal-subtitle {
        font-size: 13px; font-weight: 700; color: var(--primary-dark); margin-top: 4px;
    }
    .modal-close-btn {
        background: none; border: none; font-size: 28px; color: #94a3b8;
        cursor: pointer; transition: color 0.2s; padding: 0 8px; line-height: 1;
    }
    .modal-close-btn:hover { color: var(--danger); }
    
    .modal-body {
        padding: 24px; max-height: 380px; overflow-y: auto; position: relative;
    }
    .modal-loader {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 40px 0; gap: 16px; color: #64748b; font-weight: 800; font-size: 14px;
    }
    .modal-loader .spinner {
        width: 40px; height: 40px; border: 4px solid rgba(118, 181, 27, 0.1);
        border-top-color: var(--primary); border-radius: 50%; animation: spinLoader 0.8s linear infinite;
    }
    @keyframes spinLoader {
        to { transform: rotate(360deg); }
    }
    
    .modal-subjects-grid {
        display: flex; flex-direction: column; gap: 12px;
    }
    
    /* سطر المادة داخل المودال */
    .modal-subject-row {
        background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 16px;
        padding: 16px 20px; display: flex; align-items: center; justify-content: space-between;
        transition: all 0.2s; cursor: pointer; user-select: none;
    }
    .modal-subject-row:hover {
        background: #ffffff; border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(118, 181, 27, 0.05);
    }
    .modal-subject-row.associated {
        background: rgba(118, 181, 27, 0.03); border-color: rgba(118, 181, 27, 0.3);
    }
    
    .subject-info-box {
        display: flex; align-items: center; gap: 12px;
    }
    .subject-title-label {
        font-size: 14.5px; font-weight: 850; color: #1e293b; cursor: pointer;
    }
    
    .questions-count-badge {
        font-size: 11.5px; font-weight: 800; padding: 6px 12px; border-radius: 30px;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .badge-has-questions {
        background: rgba(118, 181, 27, 0.1); color: var(--primary-dark);
        border: 1px solid rgba(118, 181, 27, 0.2);
    }
    .badge-no-questions {
        background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0;
    }
    
    .modal-footer {
        padding: 20px 24px; border-top: 1px solid #f1f5f9; display: flex;
        justify-content: flex-end; gap: 12px; background: #f8fafc;
    }
    .btn-cancel {
        background: #ffffff; border: 1.5px solid #cbd5e1; color: #475569;
        font-weight: 800; font-size: 13.5px; padding: 12px 24px; border-radius: 12px;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-cancel:hover { background: #f8fafc; color: #1e293b; border-color: #94a3b8; }
    
    .btn-submit-save {
        background: var(--primary); border: none; color: #ffffff;
        font-weight: 900; font-size: 13.5px; padding: 12px 28px; border-radius: 12px;
        cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
        box-shadow: var(--shadow-primary); transition: all 0.2s;
    }
    .btn-submit-save:hover {
        background: var(--primary-dark); transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(118, 181, 27, 0.35);
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
                    <button type="button" class="btn-manage-subjects" onclick="openManageSubjectsModal({{ $grade->id }}, '{{ $grade->name }}')">
                        <span>إدارة المواد</span>
                        <i class="bi bi-arrow-left-circle-fill"></i>
                    </button>
                    
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

<!-- ── نافذة إدارة المواد الدراسية الفاخرة (Manage Subjects Modal) ── -->
<div id="manageSubjectsModal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">إدارة المواد الدراسية</h3>
                <p class="modal-subtitle" id="modalGradeName">تحديد المواد المرتبطة بهذا الصف</p>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeManageSubjectsModal()">&times;</button>
        </div>
        
        <form id="manageSubjectsForm" onsubmit="submitManageSubjectsForm(event)">
            @csrf
            <input type="hidden" name="grade_id" id="modalGradeId">
            
            <div class="modal-body">
                <!-- loading spinner -->
                <div id="modalLoading" class="modal-loader">
                    <div class="spinner"></div>
                    <p>جاري تحميل المواد الدراسية والأسئلة...</p>
                </div>
                
                <!-- subjects list container -->
                <div id="modalSubjectsList" class="modal-subjects-grid" style="display: none;">
                    <!-- loaded dynamically via js -->
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeManageSubjectsModal()">إلغاء</button>
                <button type="submit" class="btn-submit-save" id="btnSaveSubjects">
                    <span>حفظ التغييرات</span>
                    <i class="bi bi-check-circle-fill"></i>
                </button>
            </div>
        </form>
    </div>
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

        // ── الحذف الجماعي الآمن عبر AJAX بالتزامن مع Laravel باستخدام SweetAlert2 ──
        window.confirmDeleteMultiple = function() {
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            if (ids.length === 0) return;
            
            Swal.fire({
                title: 'هل أنت متأكد من الحذف الجماعي؟',
                text: `تحذير: أنت على وشك حذف عدد (${ids.length}) صفوف دراسية مختارة بالكامل مع المقررات المرتبطة بها!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c30e14',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'نعم، احذف الكل!',
                cancelButtonText: 'تراجع',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    dataGrid.style.opacity = '0.5';
                    Swal.fire({
                        title: 'جاري حذف الصفوف المحددة...',
                        html: 'يرجى عدم غلق الصفحة حتى انتهاء الحذف الجماعي.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    const deleteBaseUrl = "{{ route('admin.grades.destroy', ['grade' => ':gradeId']) }}";
                    let promises = ids.map(id => {
                        const deleteUrl = deleteBaseUrl.replace(':gradeId', id);
                        return fetch(deleteUrl, {
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
                        Swal.fire({
                            title: 'خطأ!',
                            text: 'حدث خطأ غير متوقع أثناء تنفيذ الحذف الجماعي.',
                            icon: 'error',
                            confirmButtonColor: '#c30e14',
                            confirmButtonText: 'حسناً'
                        });
                        dataGrid.style.opacity = '1';
                    });
                }
            });
        };

        // ── دالة فتح نافذة إدارة المواد الدراسية الفورية ──
        window.openManageSubjectsModal = function(gradeId, gradeName) {
            const modal = document.getElementById('manageSubjectsModal');
            const modalTitleGrade = document.getElementById('modalGradeName');
            const modalIdInput = document.getElementById('modalGradeId');
            const loader = document.getElementById('modalLoading');
            const listContainer = document.getElementById('modalSubjectsList');
            
            if(!modal) return;
            
            // ضبط البيانات المبدئية
            modalIdInput.value = gradeId;
            modalTitleGrade.textContent = `مقررات ومواد: ${gradeName}`;
            
            // إظهار المودال واللودر
            modal.style.display = 'flex';
            loader.style.display = 'flex';
            listContainer.style.display = 'none';
            listContainer.innerHTML = '';
            
            // جلب البيانات بالـ AJAX باستخدام مسار لارافل الديناميكي المتوافق مع المجلدات الفرعية
            const fetchBaseUrl = "{{ route('admin.grades.subjects', ['grade' => ':gradeId']) }}";
            const fetchUrl = fetchBaseUrl.replace(':gradeId', gradeId);
            
            fetch(fetchUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                loader.style.display = 'none';
                listContainer.style.display = 'flex';
                
                if (data.subjects.length === 0) {
                    listContainer.innerHTML = `
                        <div style="text-align: center; padding: 30px 10px; color: #94a3b8; width: 100%;">
                            <i class="bi bi-book" style="font-size: 32px; display: block; margin-bottom: 8px;"></i>
                            <span>لا توجد أي مواد دراسية مضافة في النظام حالياً.</span>
                        </div>
                    `;
                    return;
                }
                
                data.subjects.forEach(subject => {
                    const isChecked = subject.is_associated ? 'checked' : '';
                    const rowClass = subject.is_associated ? 'modal-subject-row associated' : 'modal-subject-row';
                    const badgeClass = subject.questions_count > 0 ? 'questions-count-badge badge-has-questions' : 'questions-count-badge badge-no-questions';
                    const badgeText = subject.questions_count > 0 
                        ? `<i class="bi bi-patch-question-fill"></i> ${subject.questions_count} سؤالاً`
                        : `<i class="bi bi-question-circle"></i> لا توجد أسئلة`;
                        
                    const rowHtml = `
                        <div class="${rowClass}" onclick="toggleSubjectRowCheckbox(this, '${subject.id}')">
                            <div class="subject-info-box">
                                <input type="checkbox" name="subject_ids[]" value="${subject.id}" 
                                    id="chk-subj-${subject.id}" class="custom-checkbox" ${isChecked}
                                    onclick="event.stopPropagation(); handleCheckboxChange(this)">
                                <label for="chk-subj-${subject.id}" class="subject-title-label" onclick="event.stopPropagation()">${subject.name}</label>
                            </div>
                            <span class="${badgeClass}">${badgeText}</span>
                        </div>
                    `;
                    listContainer.insertAdjacentHTML('beforeend', rowHtml);
                });
            })
            .catch(err => {
                loader.style.display = 'none';
                listContainer.style.display = 'flex';
                listContainer.innerHTML = `
                    <div style="text-align: center; padding: 20px; color: var(--danger); font-weight: 800; width: 100%;">
                        <i class="bi bi-exclamation-triangle" style="font-size: 30px; display: block; margin-bottom: 8px;"></i>
                        <span>حدث خطأ أثناء جلب المواد الدراسية.</span>
                    </div>
                `;
            });
        };
        
        window.closeManageSubjectsModal = function() {
            const modal = document.getElementById('manageSubjectsModal');
            if(modal) modal.style.display = 'none';
        };
        
        window.toggleSubjectRowCheckbox = function(rowDiv, subjectId) {
            const checkbox = document.getElementById(`chk-subj-${subjectId}`);
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                handleCheckboxChange(checkbox);
            }
        };
        
        window.handleCheckboxChange = function(checkbox) {
            const row = checkbox.closest('.modal-subject-row');
            if (row) {
                if (checkbox.checked) {
                    row.classList.add('associated');
                } else {
                    row.classList.remove('associated');
                }
            }
        };
        
        window.submitManageSubjectsForm = function(event) {
            event.preventDefault();
            const btn = document.getElementById('btnSaveSubjects');
            const gradeId = document.getElementById('modalGradeId').value;
            const form = document.getElementById('manageSubjectsForm');
            const formData = new FormData(form);
            
            // تعطيل الزر مؤقتاً
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<span>جاري حفظ التعديلات...</span> <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width:16px; height:16px; border: 2.5px solid rgba(255,255,255,0.25); border-top-color: #fff; border-radius: 50%; display: inline-block; animation: spinLoader 0.6s linear infinite;"></span>`;
            
            const syncBaseUrl = "{{ route('admin.grades.subjects.sync', ['grade' => ':gradeId']) }}";
            const syncUrl = syncBaseUrl.replace(':gradeId', gradeId);
            
            fetch(syncUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                
                if (data.success) {
                    closeManageSubjectsModal();
                    Swal.fire({
                        title: 'عملية ناجحة!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#76b51b',
                        confirmButtonText: 'ممتاز'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء حفظ ومزامنة المواد.',
                        icon: 'error',
                        confirmButtonColor: '#c30e14',
                        confirmButtonText: 'حسناً'
                    });
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                Swal.fire({
                    title: 'فشل في الشبكة!',
                    text: 'حدث خطأ في الاتصال أثناء إرسال البيانات، يرجى المحاولة لاحقاً.',
                    icon: 'error',
                    confirmButtonColor: '#c30e14',
                    confirmButtonText: 'حسناً'
                });
            });
        };
    });
</script>
@endpush
