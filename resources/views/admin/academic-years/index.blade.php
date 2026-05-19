@extends('layouts.admin')

@section('title', 'السنوات الدراسية')
@section('page-title', 'السنوات الدراسية')

@section('breadcrumb')
    <span style="color: var(--text-muted); font-weight: 500;">الامتحانات والقبول</span>
    <span style="color: #cbd5e0; margin: 0 4px;"><i class="bi bi-chevron-left" style="font-size: 10px;"></i></span>
    <span style="color: var(--text-main); font-weight: 700;">السنوات الدراسية</span>
@endsection

@push('styles')
<style>
    /* ── بطاقة السنة الحالية (Hero Banner) ── */
    .hero-active-year {
        background: linear-gradient(135deg, var(--sidebar-bg) 0%, #1e293b 100%);
        border-radius: 24px; padding: 36px; color: #fff; margin-bottom: 32px;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 24px;
        box-shadow: var(--shadow-lg); position: relative; overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .hero-active-year::after {
        content: '\F22A'; font-family: 'bootstrap-icons'; position: absolute;
        left: -20px; bottom: -40px; font-size: 200px; opacity: 0.06; line-height: 1; pointer-events: none;
    }
    .hero-title { font-size: 13px; font-weight: 800; opacity: 0.75; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; display: block; }
    .hero-year { font-size: 38px; font-weight: 950; line-height: 1; margin: 0; font-family: 'Inter', sans-serif !important; }
    
    .hero-badge {
        background: rgba(118, 181, 27, 0.15); border: 1.5px solid rgba(118, 181, 27, 0.3);
        color: var(--primary); padding: 8px 18px; border-radius: 50px; font-size: 13.5px; font-weight: 850;
        display: inline-flex; align-items: center; gap: 8px; backdrop-filter: blur(4px);
    }
    
    .hero-stats { display: flex; gap: 20px; }
    .hero-stat-item {
        text-align: center; background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 16px 28px; border-radius: 16px; min-width: 140px;
    }
    .hero-stat-val { font-size: 28px; font-weight: 950; display: block; margin-bottom: 4px; color: var(--primary); font-family: 'Inter', sans-serif !important; }
    .hero-stat-lbl { font-size: 12px; font-weight: 750; opacity: 0.8; }

    /* ── شريط التصفية والتحكم ── */
    .years-toolbar-wrapper {
        background: #fff; padding: 20px; border-radius: 20px;
        box-shadow: var(--shadow-sm); border: 1px solid rgba(226, 232, 240, 0.8);
        margin-bottom: 28px; display: flex; flex-direction: column; gap: 16px;
    }
    
    .toolbar-top-row {
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;
    }
    .toolbar-top-row h3 { font-size: 18px; font-weight: 900; color: #0f172a; margin: 0; }

    /* حقل بحث متميز */
    .input-search-group {
        position: relative; width: 100%; flex: 1; max-width: 500px;
    }
    .input-search-group i {
        position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
        color: #94a3b8; font-size: 16px; pointer-events: none; transition: color 0.2s; z-index: 10;
    }
    .input-search-group input { 
        width: 100%; padding: 11px 16px 11px 40px; border-radius: 12px; 
        border: 1.5px solid #cbd5e1; font-size: 13.5px; background: #fff; transition: all 0.2s;
        outline: none;
    }
    .input-search-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-light); }
    .input-search-group:focus-within i { color: var(--primary); }

    /* ── شبكة السنوات السابقة والقادمة (Card Grid) ── */
    .years-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 24px; transition: opacity 0.2s ease-in-out; }
    
    .year-card {
        background: #fff; border-radius: 24px; border: 1px solid #e2e8f0;
        padding: 28px; display: flex; flex-direction: column; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);
    }
    .year-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05); border-color: var(--theme-color, var(--primary)); }
    
    /* رأس البطاقة */
    .yc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
    .yc-icon {
        width: 52px; height: 52px; border-radius: 14px; background: var(--theme-color-light); color: var(--theme-color);
        display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
    }
    .yc-title { font-size: 24px; font-weight: 950; color: #1e293b; margin: 0; font-family: 'Inter', sans-serif !important; }
    
    /* ── وسوم دورة حياة السنة الدراسية (Lifecycle Badges) ── */
    .lifecycle-badge {
        padding: 6px 14px; border-radius: 30px; font-size: 12px; font-weight: 800; 
        display: inline-flex; align-items: center; gap: 6px; border: 1.5px solid;
        width: fit-content; margin-top: 10px;
    }

    /* 🟢 السنة الحالية (Active) */
    .badge-active { background: rgba(16, 185, 129, 0.06); color: #10b981; border-color: rgba(16, 185, 129, 0.15); }
    /* 🟡 قادمة / قيد التجهيز (Upcoming) */
    .badge-upcoming { background: rgba(245, 158, 11, 0.06); color: #f59e0b; border-color: rgba(245, 158, 11, 0.15); }
    /* 🔘 مؤرشفة / منتهية (Archived) */
    .badge-archived { background: #f8fafc; color: #64748b; border-color: #e2e8f0; }

    /* معلومات البطاقة (عدادات) */
    .yc-info { display: flex; gap: 16px; margin-bottom: 24px; }
    .yc-info-item { flex: 1; display: flex; flex-direction: column; gap: 4px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 12px; }
    .yc-info-val { font-size: 18px; font-weight: 900; color: var(--text-main); font-family: 'Inter', sans-serif !important; }
    .yc-info-lbl { font-size: 11.5px; font-weight: 750; color: #94a3b8; }

    /* أزرار الإجراءات السفلية */
    .yc-actions {
        display: flex; gap: 8px; margin-top: auto; padding-top: 20px;
        border-top: 1px dashed #e2e8f0; align-items: center;
    }
    .btn-set-active {
        flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;
        background: transparent; color: var(--theme-color); border: 2px solid var(--theme-color-light);
        padding: 11px; border-radius: 12px; font-size: 13px; font-weight: 850; cursor: pointer; transition: all 0.3s;
    }
    .year-card:hover .btn-set-active { background: var(--theme-color); color: #fff; border-color: var(--theme-color); }
    
    .btn-edit-year {
        width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;
        background: rgba(8, 145, 178, 0.06); color: #0891b2; border: 1.5px solid rgba(8, 145, 178, 0.12); border-radius: 12px;
        cursor: pointer; transition: all 0.2s; text-decoration: none; font-size: 16px;
    }
    .btn-edit-year:hover { background: #0891b2; color: #fff; }

    .btn-del-year {
        width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;
        background: rgba(195, 14, 20, 0.06); color: var(--danger); border: 1.5px solid rgba(195, 14, 20, 0.12); border-radius: 12px;
        cursor: pointer; transition: all 0.2s; font-size: 16px;
    }
    .btn-del-year:hover:not(:disabled) { background: var(--danger); color: #fff; }
    .btn-del-year:disabled { cursor: not-allowed; opacity: 0.35; background: #f1f5f9; border-color: #e2e8f0; color: #94a3b8; }

    /* ── نافذة التأكيد والإنشاء المنبثقة الفاخرة (Glassmorphic Modals) ── */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
        z-index: 1000; display: none; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-card {
        background: #fff; width: 100%; max-width: 460px; border-radius: 24px;
        padding: 32px; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15); border: 1px solid rgba(226, 232, 240, 0.8);
        transform: scale(0.95) translateY(10px); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .modal-overlay.show .modal-card { transform: scale(1) translateY(0); }

    .create-modal-overlay {
        position: fixed; inset: 0; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
        z-index: 1050; display: none; align-items: center; justify-content: center;
        opacity: 0; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .create-modal-overlay.show { display: flex; opacity: 1; }

    .create-modal-card {
        background: #fff; width: 100%; max-width: 480px; border-radius: 24px;
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.15); overflow: hidden;
        border: 1px solid rgba(226, 232, 240, 0.8);
        transform: translateY(20px) scale(0.95); transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .create-modal-overlay.show .create-modal-card { transform: translateY(0) scale(1); }

    /* ترويسة النافذة */
    .cm-header {
        padding: 24px 32px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;
    }
    .cm-title { display: flex; align-items: center; gap: 12px; font-size: 18px; font-weight: 900; color: #0f172a; margin: 0; }
    .cm-icon { width: 44px; height: 44px; background: var(--primary-light); color: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
    .btn-close-modal { background: #f8fafc; border: none; width: 34px; height: 34px; border-radius: 50%; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; transition: all 0.2s; }
    .btn-close-modal:hover { background: #fef2f2; color: #ef4444; }

    /* جسم النافذة */
    .cm-body { padding: 32px; }

    /* حقل إدخال السنة الذكي */
    .smart-year-input {
        display: flex; align-items: center; gap: 16px; background: #f8fafc; border: 2px solid #cbd5e1;
        padding: 18px; border-radius: 16px; transition: all 0.2s;
    }
    .smart-year-input:focus-within { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px var(--primary-light); }
    .year-field { flex: 1; display: flex; flex-direction: column; gap: 6px; }
    .year-field label { font-size: 11.5px; font-weight: 800; color: #64748b; text-transform: uppercase; }
    .year-field input { border: none; background: transparent; font-size: 26px; font-weight: 950; color: #1e293b; outline: none; font-family: 'Inter', monospace; padding: 0; width: 100%; text-align: center; }
    .year-divider { font-size: 26px; color: #cbd5e0; font-weight: 300; }

    /* المعاينة الحية */
    .year-preview { text-align: center; margin-top: 18px; font-size: 13px; color: #64748b; font-weight: 750; }
    .year-preview span { color: var(--primary); font-weight: 900; background: var(--primary-light); padding: 3px 10px; border-radius: 8px; font-family: 'Inter', sans-serif !important; }

    /* زر التبديل (Toggle Switch) */
    .custom-toggle { display: flex; align-items: center; justify-content: space-between; margin-top: 24px; padding: 18px; border: 1.5px dashed #cbd5e0; border-radius: 16px; cursor: pointer; transition: all 0.2s; }
    .custom-toggle:hover { border-color: var(--primary); background: var(--primary-light); }
    .toggle-info strong { display: block; font-size: 14px; font-weight: 850; color: #1e293b; margin-bottom: 2px; }
    .toggle-info span { font-size: 12px; color: #94a3b8; }
    .switch { position: relative; display: inline-block; width: 48px; height: 26px; flex-shrink: 0; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e0; transition: .3s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
    input:checked + .slider { background-color: var(--primary); }
    input:checked + .slider:before { transform: translateX(22px); }

    /* تذييل النافذة */
    .cm-footer { padding: 20px 32px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; gap: 12px; }
    .cm-footer .btn { flex: 1; padding: 12px; border-radius: 12px; font-size: 14px; font-weight: 800; height: 44px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
</style>
@endpush

@section('content')

@if($currentYear)
<div class="hero-active-year">
    <div>
        <span class="hero-title"><i class="bi bi-broadcast text-success"></i> السياق الزمني النشط لنظام القبول</span>
        <h2 class="hero-year">{{ $currentYear->name }}</h2>
        <div style="margin-top: 16px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <span class="hero-badge"><i class="bi bi-check-circle-fill"></i> هذه هي السنة الحالية الفعالة</span>
            <button type="button" onclick="openEditModal('{{ $currentYear->id }}', '{{ $currentYear->name }}')" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 7px 16px; border-radius: 50px; font-size: 12.5px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; transition: all 0.2s;">
                <i class="bi bi-pencil-square"></i> تعديل اسم السنة
            </button>
        </div>
    </div>
    
    <div class="hero-stats">
        <div class="hero-stat-item">
            <span class="hero-stat-val">{{ $currentYear->exams_count }}</span> 
            <span class="hero-stat-lbl">الامتحانات المسجلة</span>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning" style="border-radius: 20px; padding: 24px; margin-bottom: 32px; display: flex; align-items: center; gap: 16px; background: #fffbeb; border: 1.5px solid #fde68a; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.05);">
    <i class="bi bi-exclamation-triangle-fill" style="font-size: 32px; color: #d97706;"></i>
    <div>
        <strong style="color: #92400e; display: block; font-size: 16px; font-weight: 850;">لا توجد سنة دراسية نشطة للقبول!</strong>
        <span style="color: #b45309; font-size: 13.5px; line-height: 1.5; display: block; margin-top: 4px;">يرجى تعيين إحدى السنوات لتكون السنة الحالية ليتمكن النظام من ربط بيانات الطلاب والامتحانات بها بشكل صحيح.</span>
    </div>
</div>
@endif

<!-- شريط الأدوات المطور -->
<div class="years-toolbar-wrapper">
    <div class="toolbar-top-row">
        <h3>سجل السنوات الدراسية</h3>
        
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" class="btn btn-secondary" onclick="exportYearsExcel()" style="border-radius: 12px; padding: 10px 20px; font-weight: 800; display: inline-flex; align-items: center; gap: 8px; height: 44px; cursor: pointer; border: 1.5px solid #cbd5e1;">
                <i class="bi bi-file-earmark-arrow-down-fill" style="color: #16a34a; font-size: 16px;"></i> تصدير التقرير (Excel)
            </button>
            <button type="button" class="btn btn-primary" onclick="openCreateModal()" style="border-radius: 12px; padding: 10px 22px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border: none; box-shadow: var(--shadow-primary); display: inline-flex; align-items: center; gap: 8px; height: 44px;">
                <i class="bi bi-plus-lg"></i> إضافة سنة جديدة
            </button>
        </div>
    </div>

    <!-- حقل بحث بالـ AJAX والترشيح التلقائي -->
    <form method="GET" action="{{ route('admin.academic-years.index') }}" id="yearsFilterForm" style="display: flex; gap: 12px; align-items: center;" onsubmit="return false;">
        <div class="input-search-group">
            <i class="bi bi-search"></i>
            <input type="text" name="search" id="yearsSearchInput" placeholder="ابحث باسم السنة الدراسية (مثال: 2026-2027)..." value="{{ request('search') }}" autocomplete="off">
            <!-- سبنر البحث الفوري -->
            <div class="spinner-border text-primary" id="yearsSearchSpinner" role="status" style="position: absolute; left: 14px; top: 30%; width: 18px; height: 18px; display: none;"></div>
        </div>
        <button type="button" onclick="clearSearch()" id="resetSearchBtn" class="btn btn-secondary" style="height: 44px; border-radius: 12px; padding: 0 20px; font-weight: 800; display: {{ request('search') ? 'inline-flex' : 'none' }}; align-items: center; border: 1.5px solid #cbd5e1;">إلغاء البحث</button>
    </form>
</div>

@php
    $otherYears = $years->where('is_current', false);
@endphp

<!-- شبكة السنوات المقرونة بالـ AJAX -->
<div class="years-grid" id="dataGrid">
    @forelse($years as $year)
        @php
            $status = $year->lifecycle_status;
            $statusText = '';
            $statusIcon = '';
            $badgeClass = '';
            $themeColor = '#64748b';
            $themeColorLight = '#f8fafc';
            
            if ($year->is_current) {
                $statusText = 'السنة الحالية الفعالة';
                $statusIcon = 'bi-broadcast';
                $badgeClass = 'badge-active';
                $themeColor = '#10b981';
                $themeColorLight = 'rgba(16, 185, 129, 0.08)';
            } elseif ($status === 'archived') {
                $statusText = 'مؤرشفة ومغلقة';
                $statusIcon = 'bi-archive-fill';
                $badgeClass = 'badge-archived';
                $themeColor = '#64748b';
                $themeColorLight = '#f1f5f9';
            } else {
                $statusText = 'قادمة (قيد التجهيز)';
                $statusIcon = 'bi-calendar-plus-fill';
                $badgeClass = 'badge-upcoming';
                $themeColor = '#f59e0b';
                $themeColorLight = 'rgba(245, 158, 11, 0.08)';
            }
            
            // قرار الحذف الآمن
            $hasExams = $year->exams_count > 0;
            $canDelete = !$year->is_current && ($status !== 'archived') && !$hasExams;
        @endphp

        <div class="year-card" style="--theme-color: {{ $themeColor }}; --theme-color-light: {{ $themeColorLight }};">
            
            <div class="yc-header">
                <div>
                    <h4 class="yc-title">{{ $year->name }}</h4>
                    <span class="lifecycle-badge {{ $badgeClass }}">
                        <i class="bi {{ $statusIcon }}"></i> {{ $statusText }}
                    </span>
                </div>
                <div class="yc-icon"><i class="bi bi-calendar-event"></i></div>
            </div>

            <div class="yc-info">
                <div class="yc-info-item">
                    <span class="yc-info-val">{{ $year->exams_count }}</span>
                    <span class="yc-info-lbl">الامتحانات المدرجة</span>
                </div>
            </div>

            <div class="yc-actions">
                @if(!$year->is_current)
                    <button type="button" class="btn-set-active" onclick="openActivationModal('{{ $year->id }}', '{{ $year->name }}')">
                        <i class="bi bi-check-circle-fill"></i> تعيين كالحالية
                    </button>
                @else
                    <span class="badge badge-success" style="flex: 1; padding: 12px; border-radius: 12px; font-size: 13px; font-weight: 850; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="bi bi-check-all"></i> نشطة حالياً
                    </span>
                @endif
                
                <button type="button" class="btn-edit-year" onclick="openEditModal('{{ $year->id }}', '{{ $year->name }}')" title="تعديل السنة">
                    <i class="bi bi-pencil-fill"></i>
                </button>
                
                <form id="del-form-{{ $year->id }}" method="POST" action="{{ route('admin.academic-years.destroy', $year) }}" style="margin: 0; display: inline-block;">
                    @csrf @method('DELETE')
                    <button type="button" 
                            onclick="confirmDelete('del-form-{{ $year->id }}')" 
                            class="btn-del-year" 
                            title="{{ $canDelete ? 'حذف السنة' : 'لا يمكن حذف سنة نشطة، مؤرشفة، أو تحتوي على بيانات' }}" 
                            {{ $canDelete ? '' : 'disabled' }}>
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </form>
            </div>
            
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #fff; border-radius: 24px; border: 1.5px dashed #cbd5e0; color: var(--text-muted);">
            <i class="bi bi-calendar-x" style="font-size: 48px; color: #cbd5e0; display: block; margin-bottom: 16px;"></i>
            <h3 style="font-size: 18px; font-weight: 850; color: var(--text-main);">لم يتم العثور على سنوات دراسية</h3>
            <p style="font-size: 14px; margin-top: 6px;">لم نعثر على أي سنوات مطابقة لمعيار البحث المكتوب.</p>
        </div>
    @endforelse
</div>

<!-- حاوية الترقيم المقرونة بالـ AJAX -->
<div id="pagination-container">
    @if($years->hasPages())
        <div style="margin-top: 24px;">
            {{ $years->appends(request()->query())->links() }}
        </div>
    @endif
</div>

{{-- نافذة تأكيد تعيين السنة النشطة --}}
<div class="modal-overlay" id="activationModal">
    <div class="modal-card">
        <div style="width: 64px; height: 64px; background: rgba(16, 185, 129, 0.08); color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px;">
            <i class="bi bi-arrow-repeat"></i>
        </div>
        <h3 style="text-align: center; font-size: 20px; font-weight: 900; color: #1e293b; margin-bottom: 12px;">تغيير السنة النشطة للنظام</h3>
        <p style="text-align: center; color: #64748b; font-size: 14px; line-height: 1.6; margin-bottom: 24px;">
            هل أنت متأكد من رغبتك في تحويل سياق نظام "مدارس القيم الأهلية" ليعمل على السنة الدراسية <strong id="modalYearName" style="color: var(--primary);"></strong>؟
            <br><small style="display: block; margin-top: 12px; color: var(--danger); font-weight: 800;"><i class="bi bi-exclamation-triangle-fill"></i> تنبيه: سيتم ربط كافة الامتحانات والتسجيلات ونتائج الطلاب الجدد ضمن هذه السنة تلقائياً.</small>
        </p>
        
        <form id="activationForm" method="POST" action="">
            @csrf
            <div style="display: flex; gap: 12px;">
                <button type="button" class="btn btn-secondary" style="flex: 1; padding: 12px; border-radius: 12px; font-weight: 800; border: 1.5px solid #cbd5e1;" onclick="closeModal()">إلغاء الأمر</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px; border-radius: 12px; font-weight: 800; background: var(--primary);"><i class="bi bi-check-lg"></i> تأكيد التعيين</button>
            </div>
        </form>
    </div>
</div>

{{-- نافذة إضافة سنة دراسية جديدة (Premium Modal) --}}
<div class="create-modal-overlay" id="createYearModal">
    <div class="create-modal-card">
        <div class="cm-header">
            <h3 class="cm-title"><div class="cm-icon"><i class="bi bi-calendar-plus"></i></div> إضافة سنة دراسية</h3>
            <button type="button" class="btn-close-modal" onclick="closeCreateModal()"><i class="bi bi-x-lg"></i></button>
        </div>
        
        <form method="POST" action="{{ route('admin.academic-years.store') }}" id="createYearForm">
            @csrf
            <div class="cm-body">
                
                <div class="smart-year-input">
                    <div class="year-field">
                        <label>سنة البداية</label>
                        <input type="number" id="startYearInput" name="start_year" value="{{ date('Y') }}" min="2020" max="2050" required autocomplete="off">
                    </div>
                    
                    <div class="year-divider">/</div>
                    
                    <div class="year-field">
                        <label>سنة النهاية</label>
                        <input type="number" id="endYearInput" name="end_year" value="{{ date('Y') + 1 }}" readonly style="color: #94a3b8; background: transparent;">
                    </div>
                </div>

                <div class="year-preview">
                    سيتم تخزين السنة بالتنسيق التالي: <span id="finalNamePreview">{{ date('Y') }}-{{ date('Y') + 1 }}</span>
                </div>
                <input type="hidden" id="finalNameInput" name="name" value="{{ date('Y') }}-{{ date('Y') + 1 }}">

                <label class="custom-toggle">
                    <div class="toggle-info">
                        <strong>تعيين كـ "السنة الحالية" فوراً</strong>
                        <span>نقل النظام للعمل على هذه السنة بمجرد حفظها بنجاح.</span>
                    </div>
                    <div class="switch">
                        <input type="checkbox" name="is_current" value="1">
                        <span class="slider"></span>
                    </div>
                </label>

            </div>
            
            <div class="cm-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCreateModal()">إلغاء</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> حفظ السنة وإضافتها</button>
            </div>
        </form>
    </div>
</div>

{{-- نافذة تعديل سنة دراسية (Premium Modal) --}}
<div class="create-modal-overlay" id="editYearModal">
    <div class="create-modal-card">
        <div class="cm-header">
            <h3 class="cm-title"><div class="cm-icon" style="background: rgba(8, 145, 178, 0.08); color: #0891b2;"><i class="bi bi-pencil-square"></i></div> تعديل اسم السنة</h3>
            <button type="button" class="btn-close-modal" onclick="closeEditModal()"><i class="bi bi-x-lg"></i></button>
        </div>
        
        <form method="POST" action="" id="editYearForm">
            @csrf
            @method('PUT')
            <div class="cm-body">
                
                <div class="smart-year-input">
                    <div class="year-field">
                        <label>سنة البداية</label>
                        <input type="number" id="editStartYearInput" name="start_year" min="2020" max="2050" required autocomplete="off">
                    </div>
                    
                    <div class="year-divider">/</div>
                    
                    <div class="year-field">
                        <label>سنة النهاية</label>
                        <input type="number" id="editEndYearInput" name="end_year" readonly style="color: #94a3b8; background: transparent;">
                    </div>
                </div>

                <div class="year-preview">
                    الاسم المقترح بعد التعديل: <span id="editFinalNamePreview"></span>
                </div>
                <input type="hidden" id="editFinalNameInput" name="name" value="">

            </div>
            
            <div class="cm-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">إلغاء</button>
                <button type="submit" class="btn btn-primary" style="background: #0891b2; border-color: #0891b2; box-shadow: 0 4px 12px rgba(8, 145, 178, 0.2);"><i class="bi bi-check-lg"></i> حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ── دوال التحكم بالنافذة المنبثقة للنشطة (Activation Modal) ──
        const modal = document.getElementById('activationModal');
        const modalYearName = document.getElementById('modalYearName');
        const activationForm = document.getElementById('activationForm');
        
        window.openActivationModal = function(yearId, yearName) {
            modalYearName.textContent = yearName;
            activationForm.action = `{{ url('admin/academic-years') }}/${yearId}/set-current`;
            modal.classList.add('show');
        }

        window.closeModal = function() {
            modal.classList.remove('show');
        }

        modal.addEventListener('click', function(e) {
            if(e.target === modal) { closeModal(); }
        });

        // ── التحكم بنافذة إضافة سنة جديدة ──
        const createModal = document.getElementById('createYearModal');
        
        window.openCreateModal = function() {
            createModal.classList.add('show');
            document.getElementById('startYearInput').focus();
        }
        
        window.closeCreateModal = function() {
            createModal.classList.remove('show');
        }

        createModal.addEventListener('click', function(e) {
            if(e.target === createModal) { closeCreateModal(); }
        });

        // منطق الإدخال الذكي للإضافة
        const startYearInput = document.getElementById('startYearInput');
        const endYearInput = document.getElementById('endYearInput');
        const finalNamePreview = document.getElementById('finalNamePreview');
        const finalNameInput = document.getElementById('finalNameInput');

        startYearInput.addEventListener('input', function() {
            let startVal = parseInt(this.value);
            if(startVal && startVal > 1900 && startVal < 2100) {
                let endVal = startVal + 1;
                endYearInput.value = endVal;
                let formattedName = `${startVal}-${endVal}`;
                finalNamePreview.textContent = formattedName;
                finalNameInput.value = formattedName;
            } else {
                endYearInput.value = '';
                finalNamePreview.textContent = 'تنسيق غير صالح';
            }
        });

        // ── التحكم بنافذة تعديل السنة الدراسية (Edit Modal) ──
        const editModal = document.getElementById('editYearModal');
        const editYearForm = document.getElementById('editYearForm');
        const editStartYearInput = document.getElementById('editStartYearInput');
        const editEndYearInput = document.getElementById('editEndYearInput');
        const editFinalNamePreview = document.getElementById('editFinalNamePreview');
        const editFinalNameInput = document.getElementById('editFinalNameInput');

        window.openEditModal = function(yearId, yearName) {
            editYearForm.action = `{{ url('admin/academic-years') }}/${yearId}`;
            
            const parts = yearName.split('-');
            if (parts.length === 2) {
                editStartYearInput.value = parts[0];
                editEndYearInput.value = parts[1];
                editFinalNamePreview.textContent = yearName;
                editFinalNameInput.value = yearName;
            }
            
            editModal.classList.add('show');
            editStartYearInput.focus();
        }

        window.closeEditModal = function() {
            editModal.classList.remove('show');
        }

        editModal.addEventListener('click', function(e) {
            if(e.target === editModal) { closeEditModal(); }
        });

        // منطق الإدخال الذكي للتعديل
        editStartYearInput.addEventListener('input', function() {
            let startVal = parseInt(this.value);
            if(startVal && startVal > 1900 && startVal < 2100) {
                let endVal = startVal + 1;
                editEndYearInput.value = endVal;
                let formattedName = `${startVal}-${endVal}`;
                editFinalNamePreview.textContent = formattedName;
                editFinalNameInput.value = formattedName;
            } else {
                editEndYearInput.value = '';
                editFinalNamePreview.textContent = 'تنسيق غير صالح';
            }
        });

        // ── دالة الحذف الآمن ──
        window.confirmDelete = function(formId) {
            if(confirm('هل أنت متأكد من رغبتك في حذف هذه السنة الدراسية نهائياً؟')) {
                document.getElementById(formId).submit();
            }
        }

        // ── محرك البحث الفوري بالـ AJAX ──
        const searchInput = document.getElementById('yearsSearchInput');
        const searchSpinner = document.getElementById('yearsSearchSpinner');
        const resetSearchBtn = document.getElementById('resetSearchBtn');
        const filterForm = document.getElementById('yearsFilterForm');
        const dataGrid = document.getElementById('dataGrid');
        const pagContainer = document.getElementById('pagination-container');

        function debounce(func, delay) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }

        window.fetchFilteredYears = function() {
            if (searchSpinner) searchSpinner.style.display = 'inline-block';
            dataGrid.style.opacity = '0.5';
            
            const searchValue = searchInput.value.trim();
            if (searchValue) {
                resetSearchBtn.style.display = 'inline-flex';
            } else {
                resetSearchBtn.style.display = 'none';
            }
            
            const queryParams = new URLSearchParams(new FormData(filterForm)).toString();
            const url = `{{ route('admin.academic-years.index') }}?${queryParams}`;
            
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newGrid = doc.getElementById('dataGrid');
                const newPag = doc.getElementById('pagination-container');
                
                if (newGrid) dataGrid.innerHTML = newGrid.innerHTML;
                if (pagContainer) pagContainer.innerHTML = newPag ? newPag.innerHTML : '';
                
                dataGrid.style.opacity = '1';
                if (searchSpinner) searchSpinner.style.display = 'none';
            })
            .catch(error => {
                console.error('Error searching academic years:', error);
                dataGrid.style.opacity = '1';
                if (searchSpinner) searchSpinner.style.display = 'none';
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', debounce(fetchFilteredYears, 350));
        }

        window.clearSearch = function() {
            if (searchInput) {
                searchInput.value = '';
                fetchFilteredYears();
            }
        }

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
                        if (newGrid) dataGrid.innerHTML = newGrid.innerHTML;
                        if (pagContainer) pagContainer.innerHTML = newPag ? newPag.innerHTML : '';
                        dataGrid.style.opacity = '1';
                        window.scrollTo({ top: dataGrid.offsetTop - 100, behavior: 'smooth' });
                    })
                    .catch(() => {
                        dataGrid.style.opacity = '1';
                    });
            }
        });

        // ── تصدير السنوات الدراسية إلى ملف Excel CSV ──
        window.exportYearsExcel = function() {
            let csvContent = "\uFEFF";
            csvContent += "اسم السنة الدراسية,الحالة,الامتحانات المدرجة\n";
            
            const cards = document.querySelectorAll('.year-card');
            if (cards.length === 0) return;
            
            cards.forEach(card => {
                const name = card.querySelector('.yc-title').textContent.trim();
                const status = card.querySelector('.lifecycle-badge').textContent.trim();
                const exams = card.querySelector('.yc-info-val').textContent.trim();
                
                csvContent += `"${name}","${status}","${exams}"\n`;
            });
            
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement("a");
            link.setAttribute("href", url);
            link.setAttribute("download", `تقرير_السنوات_الدراسية_${new Date().toISOString().slice(0,10)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };
    });
</script>
@endpush
