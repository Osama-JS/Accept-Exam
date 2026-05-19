@extends('layouts.admin')

@section('title', 'بنك الأسئلة')

@section('breadcrumb')
    <span style="color: var(--text-muted); font-weight: 500;">إدارة المحتوى</span>
    <span style="color: #cbd5e0; margin: 0 4px;"><i class="bi bi-chevron-left" style="font-size: 10px;"></i></span>
    <span style="color: var(--text-main); font-weight: 700;">بنك الأسئلة</span>
@endsection

@section('page-title', 'بنك الأسئلة')

@push('styles')
<style>
    /* ── شريط التحكم العلوي (Toolbar) ── */
    .page-toolbar {
        display: flex; flex-wrap: wrap; gap: 16px; align-items: center; justify-content: space-between;
        background: #fff; padding: 16px 24px; border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.02); border: 1px solid rgba(226, 232, 240, 0.6);
        margin-bottom: 24px;
        animation: pageIn .4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .toolbar-actions { display: flex; gap: 12px; align-items: center; flex: 1; justify-content: flex-end; flex-wrap: wrap; }
    
    .search-box { position: relative; width: 100%; max-width: 300px; }
    .search-box i { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .search-box input { 
        width: 100%; padding: 10px 16px 10px 42px; border-radius: 10px; 
        border: 1px solid #e2e8f0; font-size: 13px; background: #f8fafc; transition: all 0.2s;
        padding-right: 42px; padding-left: 16px;
    }
    .search-box input:focus { background: #fff; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); outline: none; }
    
    .select-filter {
        padding: 10px 36px 10px 16px; border-radius: 10px; border: 1px solid #e2e8f0; 
        font-size: 13px; color: var(--text-main); background: #f8fafc; cursor: pointer; outline: none;
        min-width: 180px; appearance: none; 
        padding-right: 16px; padding-left: 36px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: left 14px center; background-size: 10px;
    }
    .select-filter:focus { border-color: var(--primary); }

    /* ── الإجراءات الجماعية (Bulk Actions) ── */
    .bulk-toolbar {
        background: rgba(195, 14, 20, 0.04); border: 1px solid rgba(195, 14, 20, 0.15);
        padding: 14px 20px; border-radius: 12px; margin-bottom: 20px;
        display: none; align-items: center; justify-content: space-between;
        animation: slideDown 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

    /* ── الجدول المتقدم للبيانات الكثيفة (Data Table) ── */
    .modern-table-wrapper { 
        background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; 
        overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        animation: pageIn .5s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .modern-table { width: 100%; border-collapse: collapse; text-align: right; }
    .modern-table th { 
        background: #f8fafc; padding: 14px 20px; font-size: 12px; font-weight: 700; 
        color: #64748b; border-bottom: 1px solid #e2e8f0; white-space: nowrap; 
    }
    .modern-table td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; transition: background 0.2s; }
    .modern-table tbody tr:hover { background: #fcfcfc; }
    
    /* تنسيقات خلايا الجدول */
    .q-text { font-size: 14.5px; font-weight: 700; color: var(--text-main); line-height: 1.6; margin-bottom: 6px; display: block; }
    .q-meta { font-size: 11px; color: #94a3b8; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
    
    .subject-badge { 
        display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; 
        background: #eff6ff; color: #1e40af; border-radius: 8px; font-size: 11.5px; font-weight: 700; margin-bottom: 4px;
        border: 1px solid rgba(30, 64, 175, 0.08);
    }
    .grade-text { font-size: 11.5px; color: #64748b; display: block; padding-right: 4px; font-weight: 600; }
    
    .choices-count {
        display: inline-flex; align-items: center; justify-content: center;
        width: 34px; height: 34px; background: #f1f5f9; color: #475569;
        border-radius: 10px; font-weight: 800; font-size: 13px; border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }
    .choices-count:hover { background: var(--primary-light); color: var(--primary); border-color: rgba(118, 181, 27, 0.2); }

    /* صناديق الاختيار */
    .custom-cb { width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer; border-radius: 4px; }

    /* أزرار الإجراءات */
    .action-btns { display: flex; gap: 8px; }
    .btn-icon-sm {
        width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;
        border-radius: 10px; border: 1px solid #e2e8f0; background: #ffffff; color: #64748b; cursor: pointer; transition: all 0.2s;
    }
    .btn-icon-sm:hover { transform: translateY(-2px); }
    .btn-icon-sm.preview:hover { background: #eff6ff; color: #3b82f6; border-color: rgba(59, 130, 246, 0.2); }
    .btn-icon-sm.edit:hover { background: #fef9c3; color: #ca8a04; border-color: rgba(202, 138, 4, 0.2); }
    .btn-icon-sm.delete:hover { background: #fef2f2; color: #ef4444; border-color: rgba(239, 68, 68, 0.2); }

    /* ── نافذة المعاينة المخصصة (Modal) ── */
    .custom-modal {
        display: none; position: fixed; top: 0; right: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);
        z-index: 1200; align-items: center; justify-content: center; opacity: 0;
        transition: opacity 0.25s ease;
    }
    .custom-modal.show { display: flex; opacity: 1; }
    .modal-content {
        background: #fff; border-radius: 20px; width: 90%; max-width: 600px;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        transform: scale(0.92); transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        overflow: hidden; border: 1.5px solid rgba(147, 51, 234, 0.15);
    }
    .custom-modal.show .modal-content { transform: scale(1); }
    .modal-header {
        padding: 20px 24px; background: #ffffff; border-bottom: 1.5px solid rgba(147, 51, 234, 0.1);
        display: flex; align-items: center; justify-content: space-between;
    }
    .modal-title { font-size: 16px; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 8px; }
    .modal-close {
        background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; color: var(--text-muted); cursor: pointer; transition: all 0.2s;
    }
    .modal-close:hover { background: #fef2f2; color: var(--danger); }
    .modal-body { padding: 24px; }

    /* ── الفلاتر المتقدمة (Advanced Filters Panel) ── */
    .advanced-filters-panel {
        background: #f8fafc; border-top: 1.5px dashed #e2e8f0;
        padding: 18px 24px; border-radius: 12px;
        display: none; /* مخفي افتراضياً */
        animation: slideDownPanel 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin-top: 14px;
        border: 1px solid #e2e8f0;
    }
    .advanced-filters-panel.show { display: block; }
    @keyframes slideDownPanel {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* أزرار اختيار مستوى الصعوبة (Difficulty Pills) */
    .difficulty-selector { display: flex; gap: 8px; flex-wrap: wrap; }
    .diff-radio { display: none; }
    .diff-label {
        padding: 8px 16px; border-radius: 20px; border: 1.5px solid #e2e8f0;
        font-size: 13px; font-weight: 700; color: #64748b; background: #fff;
        cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px;
    }
    .diff-label:hover { background: #f1f5f9; }

    /* تأثيرات التحديد (Checked States) */
    .diff-radio[value="all"]:checked + .diff-label { background: var(--text-main); color: #fff; border-color: var(--text-main); }
    .diff-radio[value="easy"]:checked + .diff-label { background: #f0fdf4; color: #16a34a; border-color: #16a34a; }
    .diff-radio[value="medium"]:checked + .diff-label { background: #fef9c3; color: #ca8a04; border-color: #ca8a04; }
    .diff-radio[value="hard"]:checked + .diff-label { background: #fef2f2; color: #dc2626; border-color: #dc2626; }

    .btn-toggle-filters {
        background: transparent; color: var(--text-muted); border: 1px solid #e2e8f0;
        padding: 10px 16px; border-radius: 12px; font-size: 13px; font-weight: 700;
        cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px;
        height: 38px;
    }
    .btn-toggle-filters:hover, .btn-toggle-filters.active {
        background: #f1f5f9; color: var(--primary); border-color: var(--primary-light);
    }

    /* ── حاوية القوائم المرنة (Flex-List) ── */
    .questions-list-container {
        display: flex; flex-direction: column; gap: 14px;
        animation: pageIn .5s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* ── تصميم بطاقة السؤال الأفقية ── */
    .q-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0,0,0,0.01);
        overflow: hidden; /* لاحتواء الانزلاق */
    }
    .q-card:hover {
        border-color: #cbd5e0;
        box-shadow: 0 12px 24px rgba(0,0,0,0.04);
        transform: translateY(-2px);
    }
    .q-card.is-open {
        border-color: var(--primary);
        box-shadow: 0 8px 32px rgba(118, 181, 27, 0.1);
    }

    /* ── الصف المرئي الرئيسي ── */
    .q-main-row {
        display: flex; align-items: flex-start; padding: 20px 24px; gap: 20px;
        position: relative;
    }

    /* الأيقونة ونوع السؤال */
    .q-type-icon {
        width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
        background: #f8fafc; color: var(--primary); border: 1px solid #e2e8f0;
        display: flex; align-items: center; justify-content: center; font-size: 20px;
        transition: all 0.2s;
    }
    .q-card.is-open .q-type-icon { background: var(--primary); color: #fff; border-color: var(--primary); }

    /* محتوى السؤال */
    .q-content { flex: 1; min-width: 0; }
    .q-title {
        font-size: 15.5px; font-weight: 800; color: #1e293b; 
        line-height: 1.6; margin-bottom: 10px; margin-top: 0;
        /* إخفاء النص الزائد بأناقة (3 أسطر كحد أقصى) */
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }

    /* وسوم المعلومات السفلية */
    .q-meta-badges { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .q-badge {
        display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px;
        border-radius: 8px; font-size: 11px; font-weight: 700;
    }
    .badge-id { background: #f1f5f9; color: #64748b; font-family: monospace; }
    .badge-subject { background: #eff6ff; color: #1e40af; border: 1px solid rgba(30, 64, 175, 0.08); }
    .badge-grade { background: transparent; border: 1px solid #e2e8f0; color: #475569; }

    /* ── منطقة الإجراءات اليسرى ── */
    .q-actions-area {
        display: flex; flex-direction: column; align-items: flex-end; gap: 12px; flex-shrink: 0;
    }
    .expand-btn {
        background: transparent; color: var(--text-muted); border: 1px solid #e2e8f0;
        padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 700;
        cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px;
        height: 32px;
        outline: none;
    }
    .expand-btn:hover { background: #f8fafc; color: var(--text-main); }
    .expand-btn.active { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }

    /* ── منطقة التوسيع (المحتوى المخفي) ── */
    .q-expanded-area {
        display: none; padding: 0 24px 24px 88px; /* محاذاة مع النص لجمالية الترتيب */
    }
    .q-expanded-area.show {
        display: block; animation: slideDownContent 0.3s ease-out;
    }
    @keyframes slideDownContent { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

    /* تصميم معاينة الخيارات كشكل ورقة الاختبار */
    .exam-preview-box {
        background: #f8fafc; border: 1px dashed #cbd5e0; border-radius: 12px; padding: 20px;
    }
    .full-text { font-size: 15px; font-weight: 700; color: #334155; margin-bottom: 16px; line-height: 1.8; text-align: right; }
    .options-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; }
    .option-item {
        display: flex; align-items: center; gap: 12px; padding: 10px 16px;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600;
        color: #475569; transition: all 0.2s;
    }
    .option-item.is-correct { background: #f0fdf4; border-color: #4ade80; color: #166534; box-shadow: 0 2px 8px rgba(74, 222, 128, 0.1); }

    /* ── الترقيم المطور المدمج مع لارافل (Pagination) ── */
    .pagination-wrapper {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        padding: 20px 28px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.015);
    }
    .pagination-wrapper nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .pagination-wrapper nav div:first-child {
        font-size: 13.5px;
        font-weight: 750;
        color: var(--text-muted);
    }
    .pagination-wrapper nav div:last-child {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pagination-wrapper nav a, 
    .pagination-wrapper nav span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        color: var(--text-main) !important;
        font-size: 13.5px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        background: #ffffff;
        box-shadow: none !important;
    }
    .pagination-wrapper nav a:first-child,
    .pagination-wrapper nav a:last-child,
    .pagination-wrapper nav span:first-child,
    .pagination-wrapper nav span:last-child {
        width: auto;
        padding: 0 16px;
        gap: 6px;
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

    /* ── بطاقات الفلترة السريعة (Quick Filter Cards) ── */
    .quick-filters-scroll {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding: 4px 4px 12px;
        margin-bottom: 20px;
        scrollbar-width: thin;
        scrollbar-color: rgba(118, 181, 27, 0.2) transparent;
        -webkit-overflow-scrolling: touch;
    }
    .quick-filters-scroll::-webkit-scrollbar {
        height: 6px;
    }
    .quick-filters-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .quick-filters-scroll::-webkit-scrollbar-thumb {
        background: rgba(118, 181, 27, 0.2);
        border-radius: 10px;
    }
    .quick-filters-scroll::-webkit-scrollbar-thumb:hover {
        background: var(--primary);
    }
    
    .quick-filter-card {
        flex: 0 0 auto;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.01);
    }
    .quick-filter-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(118, 181, 27, 0.08);
    }
    .quick-filter-card.active {
        background: linear-gradient(135deg, var(--primary), #5f9416);
        border-color: var(--primary);
        color: #ffffff !important;
        box-shadow: 0 6px 18px rgba(118, 181, 27, 0.25);
    }
    .quick-filter-card .card-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: rgba(118, 181, 27, 0.08);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: all 0.2s;
    }
    .quick-filter-card.active .card-icon {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }
    .quick-filter-card .card-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .quick-filter-card .card-grade {
        font-size: 10px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        transition: all 0.2s;
    }
    .quick-filter-card.active .card-grade {
        color: rgba(255, 255, 255, 0.8);
    }
    .quick-filter-card .card-subject {
        font-size: 13px;
        font-weight: 800;
        color: var(--text-main);
        transition: all 0.2s;
    }
    .quick-filter-card.active .card-subject {
        color: #ffffff;
    }
    .quick-filter-card .card-count {
        font-size: 11px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
        transition: all 0.2s;
    }
    .quick-filter-card.active .card-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }
</style>
@endpush

@section('content')

<div class="page-toolbar" style="padding-bottom: 16px; flex-direction: column; align-items: stretch; gap: 16px;">
    
    <!-- Row 1: Header + New Question Button -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 48px; height: 48px; background: rgba(147, 51, 234, 0.08); color: #9333ea; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="bi bi-patch-question-fill"></i>
            </div>
            <div>
                <h2 style="font-size: 18px; font-weight: 800; margin: 0; color: var(--text-main);">إدارة بنك الأسئلة</h2>
                <p style="color: var(--text-muted); font-size: 13px; margin: 4px 0 0; font-weight: 600;">
                    إجمالي الأسئلة المتاحة: <strong style="color: #9333ea; font-weight: 800;">{{ isset($questions) && method_exists($questions, 'total') ? $questions->total() : ($totalCount ?? 0) }}</strong>
                </p>
            </div>
        </div>
        <a href="{{ route('admin.questions.create') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #76b51b, #5f9416); border: none; box-shadow: 0 6px 20px rgba(118, 181, 27, 0.25); font-weight: 700; border-radius: 12px; padding: 8px 18px; display: inline-flex; align-items: center; gap: 8px; height: 38px; font-size: 13.5px;">
            <i class="bi bi-plus-lg"></i> سؤال جديد
        </a>
    </div>

    <!-- Row 2: Basic Filters & Toggles -->
    <form method="GET" action="{{ route('admin.questions.index') }}" id="filterForm" onsubmit="fetchFilteredQuestions(); return false;" style="display: flex; flex-direction: column; gap: 12px; border-top: 1.5px solid #f1f5f9; padding-top: 16px; margin: 0;">
        <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: flex-start; width: 100%;">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" name="search" id="searchInput" placeholder="ابحث في نص السؤال..." value="{{ request('search') }}" autocomplete="off">
            </div>

            <select name="grade_id" id="gradeSelect" class="select-filter" onchange="fetchFilteredQuestions()">
                <option value="">جميع الصفوف</option>
                @foreach($grades ?? [] as $g)
                    <option value="{{ $g->id }}" {{ request('grade_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                @endforeach
            </select>

            <select name="subject_id" id="subjectSelect" class="select-filter" onchange="fetchFilteredQuestions()">
                <option value="">جميع المواد</option>
                @foreach($subjects ?? [] as $s)
                    <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>

            <button type="button" class="btn-toggle-filters" id="toggleAdvancedBtn">
                <i class="bi bi-sliders"></i> فلاتر متقدمة
            </button>

            <select name="per_page" id="perPageSelect" class="select-filter" style="min-width: 130px;" onchange="fetchFilteredQuestions()">
                <option value="15" {{ request('per_page') == '15' || !request('per_page') ? 'selected' : '' }}>15 سؤال/صفحة</option>
                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 أسئلة/صفحة</option>
                <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 سؤال/صفحة</option>
                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 سؤال/صفحة</option>
                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 سؤال/صفحة</option>
            </select>
            
            <div style="margin-right: auto; display: flex; gap: 10px; align-items: center;">
                @if(request()->hasAny(['grade_id', 'subject_id', 'search', 'difficulty', 'type']))
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary" style="border: 1.5px solid var(--border); background: #ffffff; color: var(--text-main); font-weight: 700; border-radius: 12px; width: 38px; height: 38px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="مسح الفلاتر">
                        <i class="bi bi-x-lg" style="color: var(--danger); font-size: 14px;"></i>
                    </a>
                @endif
                <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #76b51b, #5f9416); border: none; font-weight: 700; border-radius: 12px; padding: 8px 20px; height: 38px; font-size: 13px; box-shadow: 0 4px 12px rgba(118, 181, 27, 0.15); display: inline-flex; align-items: center; justify-content: center;">تطبيق الفرز</button>
            </div>
        </div>

        <!-- Collapsible Panel inside form -->
        <div class="advanced-filters-panel" id="advancedFiltersPanel">
            <div style="display: flex; flex-wrap: wrap; gap: 24px; align-items: center;">
                
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: #94a3b8; margin-bottom: 8px; display: block;">مستوى الصعوبة</label>
                    <div class="difficulty-selector">
                        <input type="radio" name="difficulty" id="diff_all" class="diff-radio" value="all" {{ !request('difficulty') || request('difficulty') == 'all' ? 'checked' : '' }}>
                        <label for="diff_all" class="diff-label">الكل</label>

                        <input type="radio" name="difficulty" id="diff_easy" class="diff-radio" value="easy" {{ request('difficulty') == 'easy' ? 'checked' : '' }}>
                        <label for="diff_easy" class="diff-label"><i class="bi bi-circle-fill" style="font-size: 8px;"></i> سهل</label>

                        <input type="radio" name="difficulty" id="diff_medium" class="diff-radio" value="medium" {{ request('difficulty') == 'medium' ? 'checked' : '' }}>
                        <label for="diff_medium" class="diff-label"><i class="bi bi-circle-fill" style="font-size: 8px;"></i> متوسط</label>

                        <input type="radio" name="difficulty" id="diff_hard" class="diff-radio" value="hard" {{ request('difficulty') == 'hard' ? 'checked' : '' }}>
                        <label for="diff_hard" class="diff-label"><i class="bi bi-circle-fill" style="font-size: 8px;"></i> صعب</label>
                    </div>
                </div>

                <div style="min-width: 220px;">
                    <label style="font-size: 12px; font-weight: 700; color: #94a3b8; margin-bottom: 8px; display: block;">نوع السؤال</label>
                    <select name="type" id="typeSelect" class="select-filter" style="width: 100%;" onchange="fetchFilteredQuestions()">
                        <option value="">جميع الأنواع</option>
                        <option value="mcq" {{ request('type') == 'mcq' ? 'selected' : '' }}>🟢 اختيار من متعدد</option>
                        <option value="tf" {{ request('type') == 'tf' ? 'selected' : '' }}>⚖️ صح أو خطأ</option>
                        <option value="essay" {{ request('type') == 'essay' ? 'selected' : '' }}>✍️ سؤال مقالي</option>
                        <option value="matching" {{ request('type') == 'matching' ? 'selected' : '' }}>🔗 سؤال توصيل</option>
                    </select>
                </div>
                
            </div>
        </div>
    </form>
</div>

<!-- بطاقات الفلترة السريعة -->
<div id="quick-filters-container">
    @if(!empty($quickFilters))
    <div class="quick-filters-scroll">
        @foreach($quickFilters as $filter)
            @php 
                $isActive = (request('grade_id') == $filter['grade_id'] && request('subject_id') == $filter['subject_id']);
            @endphp
            <div class="quick-filter-card {{ $isActive ? 'active' : '' }}" 
                 onclick="applyQuickFilter(this, '{{ $filter['grade_id'] }}', '{{ $filter['subject_id'] }}')">
                <div class="card-icon">
                    <i class="bi bi-bookmark-star-fill"></i>
                </div>
                <div class="card-info">
                    <span class="card-grade">{{ $filter['grade_name'] }}</span>
                    <span class="card-subject">{{ $filter['subject_name'] }}</span>
                </div>
                <span class="card-count">{{ $filter['count'] }}</span>
            </div>
        @endforeach
    </div>
    @endif
</div>

<!-- شريط الإجراءات الجماعية -->
<div class="bulk-toolbar" id="bulkToolbar">
    <div style="color: var(--danger); font-weight: 800; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
        <i class="bi bi-check2-square" style="font-size: 18px;"></i> تم تحديد <span id="selectedCount" style="text-decoration: underline;">0</span> أسئلة
    </div>
    <div style="display: flex; gap: 10px;">
        <button class="btn btn-sm btn-secondary" style="border: 1.5px solid var(--border); background: #ffffff; color: var(--text-main); font-weight: 700; border-radius: 10px; padding: 6px 14px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;" onclick="alert('سيتم إتاحة النقل الجماعي بالنسخة القادمة.')">
            <i class="bi bi-folder-symlink" style="color: var(--primary);"></i> نقل إلى مادة أخرى
        </button>
        <button class="btn btn-sm btn-danger" style="background: var(--danger); border: none; color: #fff; font-weight: 700; border-radius: 10px; padding: 6px 14px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;" onclick="confirmBulkDelete()">
            <i class="bi bi-trash"></i> حذف المحدد
        </button>
    </div>
</div>

<!-- فورم الحذف الجماعي المخفي -->
<form id="bulkDeleteForm" method="POST" action="{{ route('admin.questions.bulk-destroy') }}" style="display: none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="ids" id="bulkDeleteIds">
</form>

<!-- Select All / Controls Header Card -->
<div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 14px 24px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; animation: pageIn .4s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 2px 8px rgba(0,0,0,0.01);">
    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13.5px; font-weight: 700; color: var(--text-main); margin: 0; user-select: none;">
        <input type="checkbox" id="selectAll" class="custom-cb">
        <span>تحديد جميع الأسئلة في هذه الصفحة</span>
    </label>
    
    <div style="display: flex; gap: 10px;">
        <button type="button" onclick="expandAllQCards()" class="btn btn-secondary" style="border: 1px solid #e2e8f0; background: #ffffff; color: var(--text-main); font-weight: 700; border-radius: 12px; padding: 8px 16px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
            <i class="bi bi-arrows-expand" style="color: var(--primary);"></i> توسيع الكل
        </button>
        <button type="button" onclick="collapseAllQCards()" class="btn btn-secondary" style="border: 1px solid #e2e8f0; background: #ffffff; color: var(--text-main); font-weight: 700; border-radius: 12px; padding: 8px 16px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
            <i class="bi bi-arrows-collapse" style="color: #64748b;"></i> طي الكل
        </button>
    </div>
</div>

<div class="questions-list-container" id="dataGrid">
    @if($questions->isEmpty())
        <div style="text-align: center; padding: 80px 20px; background: #fff; border-radius: 16px; border: 1px solid #e2e8f0;">
            <div style="font-size: 56px; color: #cbd5e0; margin-bottom: 16px;"><i class="bi bi-inboxes"></i></div>
            <h3 style="font-size: 18px; font-weight: 800; color: var(--text-main);">بنك الأسئلة فارغ</h3>
            <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 24px;">لم يتم العثور على أي أسئلة مطابقة لشروط البحث.</p>
        </div>
    @else
        @foreach($questions as $q)
        <div class="q-card" id="card-{{ $q->id }}">
            
            <div class="q-main-row">
                <div style="padding-top: 12px;">
                    <input type="checkbox" class="custom-cb row-cb" value="{{ $q->id }}">
                </div>

                <div class="q-type-icon" title="
                    @if($q->type == 'mcq')
                        اختيار من متعدد
                    @elseif($q->type == 'tf')
                        صح أو خطأ
                    @elseif($q->type == 'essay')
                        سؤال مقالي
                    @elseif($q->type == 'matching')
                        سؤال توصيل
                    @else
                        سؤال عام
                    @endif
                ">
                    @if($q->type == 'mcq')
                        <i class="bi bi-ui-radios"></i>
                    @elseif($q->type == 'tf')
                        <i class="bi bi-patch-check"></i>
                    @elseif($q->type == 'essay')
                        <i class="bi bi-blockquote-left"></i>
                    @elseif($q->type == 'matching')
                        <i class="bi bi-distribute-vertical"></i>
                    @else
                        <i class="bi bi-question-lg"></i>
                    @endif
                </div>

                <div class="q-content">
                    <h4 class="q-title" onclick="toggleQCard('{{ $q->id }}')" style="cursor: pointer; user-select: none;">
                        {{ $q->text }}
                    </h4>
                    
                    <div class="q-meta-badges">
                        <span class="q-badge badge-id">#{{ $q->id }}</span>
                        <span class="q-badge badge-subject"><i class="bi bi-book"></i> {{ $q->subject->name ?? 'عام' }}</span>
                        <span class="q-badge badge-grade">{{ $q->grade->name ?? 'عام' }}</span>
                        
                        @if($q->difficulty)
                            @if($q->difficulty == 'easy')
                                <span class="q-badge" style="color: #16a34a; background: #f0fdf4;"><i class="bi bi-circle-fill" style="font-size: 6px;"></i> سهل</span>
                            @elseif($q->difficulty == 'medium')
                                <span class="q-badge" style="color: #ca8a04; background: #fef9c3;"><i class="bi bi-circle-fill" style="font-size: 6px;"></i> متوسط</span>
                            @elseif($q->difficulty == 'hard')
                                <span class="q-badge" style="color: #dc2626; background: #fef2f2;"><i class="bi bi-circle-fill" style="font-size: 6px;"></i> صعب</span>
                            @endif
                        @endif

                        @if($q->type)
                            <span class="q-badge" style="color: #475569; background: #f1f5f9;">
                            @if($q->type == 'mcq')
                                🟢 اختيار من متعدد
                            @elseif($q->type == 'tf')
                                ⚖️ صح أو خطأ
                            @elseif($q->type == 'essay')
                                ✍️ مقالي
                            @elseif($q->type == 'matching')
                                🔗 توصيل
                            @endif
                            </span>
                        @endif
                    </div>
                </div>

                <div class="q-actions-area">
                    <button type="button" class="expand-btn" id="btn-{{ $q->id }}" onclick="toggleQCard('{{ $q->id }}')">
                        <span>استعراض</span> <i class="bi bi-chevron-down"></i>
                    </button>
                    
                    <div class="action-btns">
                        <a href="{{ route('admin.questions.edit', $q) }}" class="btn-icon-sm edit" title="تعديل"><i class="bi bi-pencil"></i></a>
                        <form id="del-q-{{ $q->id }}" method="POST" action="{{ route('admin.questions.destroy', $q) }}" style="margin:0;">
                            @csrf @method('DELETE')
                        </form>
                        <button onclick="confirmDelete('del-q-{{ $q->id }}', 'هل تريد بالتأكيد حذف هذا السؤال نهائياً؟')" class="btn-icon-sm delete" title="حذف"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            </div>

            <div class="q-expanded-area" id="expand-{{ $q->id }}">
                <div class="exam-preview-box">
                    <div class="full-text">{{ $q->text }}</div>
                    
                    <div class="options-grid">
                        @php $letters = ['أ', 'ب', 'ج', 'د', 'هـ', 'و']; @endphp
                        @foreach($q->choices as $index => $choice)
                            <div class="option-item {{ $choice->is_correct ? 'is-correct' : '' }}">
                                <div style="width: 24px; height: 24px; border-radius: 6px; background: {{ $choice->is_correct ? '#dcfce7' : '#f1f5f9' }}; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; color: {{ $choice->is_correct ? '#166534' : '#64748b' }}; flex-shrink: 0;">
                                    {{ $letters[$index] ?? '-' }}
                                </div>
                                <span>{{ $choice->text }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
        @endforeach
    @endif
</div>

<div id="pagination-container">
    @if($questions->hasPages())
    <div class="pagination-wrapper" style="animation: pageIn .4s cubic-bezier(0.16, 1, 0.3, 1); margin-top: 24px;">
        {{ $questions->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    // ── تهيئة وتحديث مستمعي العمليات الجماعية (Bulk Actions Initialization) ──
    function initBulkActions() {
        const selectAll = document.getElementById('selectAll');
        const rowCbs = document.querySelectorAll('.row-cb');
        const bulkToolbar = document.getElementById('bulkToolbar');
        const countSpan = document.getElementById('selectedCount');

        function updateBulk() {
            const checkedCount = document.querySelectorAll('.row-cb:checked').length;
            const totalCbs = document.querySelectorAll('.row-cb').length;
            if(checkedCount > 0) {
                bulkToolbar.style.display = 'flex';
                countSpan.textContent = checkedCount;
            } else {
                bulkToolbar.style.display = 'none';
            }
            if(selectAll) {
                selectAll.checked = checkedCount === totalCbs && totalCbs > 0;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < totalCbs;
            }
        }

        if(selectAll) {
            // تجنب تكرار المستمعين في الـ AJAX
            const newSelectAll = selectAll.cloneNode(true);
            selectAll.parentNode.replaceChild(newSelectAll, selectAll);
            
            newSelectAll.addEventListener('change', function() {
                const freshCbs = document.querySelectorAll('.row-cb');
                freshCbs.forEach(cb => cb.checked = this.checked);
                updateBulk();
            });
        }

        rowCbs.forEach(cb => {
            const newCb = cb.cloneNode(true);
            cb.parentNode.replaceChild(newCb, cb);
            newCb.addEventListener('change', updateBulk);
        });
        
        updateBulk();
    }

    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggleAdvancedBtn');
        const advancedPanel = document.getElementById('advancedFiltersPanel');
        const searchInput = document.getElementById('searchInput');
        
        initBulkActions();

        // التحقق مما إذا كان هناك فلتر متقدم نشط لفتح اللوحة تلقائياً عند تحميل الصفحة
        const urlParams = new URLSearchParams(window.location.search);
        if((urlParams.has('difficulty') && urlParams.get('difficulty') !== 'all') || (urlParams.has('type') && urlParams.get('type') !== '')) {
            advancedPanel.classList.add('show');
            toggleBtn.classList.add('active');
        }

        // فتح وإغلاق لوحة الفلاتر
        toggleBtn.addEventListener('click', function() {
            advancedPanel.classList.toggle('show');
            toggleBtn.classList.toggle('active');
        });

        // الاستماع لتغيير مستوى الصعوبة
        const diffRadios = document.querySelectorAll('.diff-radio');
        diffRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                fetchFilteredQuestions();
            });
        });

        // محرك البحث الفوري بالـ AJAX
        if (searchInput) {
            searchInput.addEventListener('input', debounce(fetchFilteredQuestions, 350));
        }

        // الاستماع لنقرات ترقيم الصفحات عبر AJAX
        document.addEventListener('click', function(e) {
            const pageLink = e.target.closest('.pagination a');
            const pagContainer = document.getElementById('pagination-container');
            if (pageLink && pagContainer.contains(e.target)) {
                e.preventDefault();
                const url = pageLink.getAttribute('href');
                
                const dataGrid = document.getElementById('dataGrid');
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
    });

    window.fetchFilteredQuestions = function() {
        const dataGrid = document.getElementById('dataGrid');
        const pagContainer = document.getElementById('pagination-container');
        const filterForm = document.getElementById('filterForm');
        const subjectSelect = document.getElementById('subjectSelect');
        
        dataGrid.style.opacity = '0.5';
        
        const formData = new FormData(filterForm);
        const queryParams = new URLSearchParams(formData).toString();
        const url = `{{ route('admin.questions.index') }}?${queryParams}`;
        
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
            const newSubjectSelect = doc.getElementById('subjectSelect');
            
            if (newGrid) {
                dataGrid.innerHTML = newGrid.innerHTML;
            }
            if (pagContainer) {
                pagContainer.innerHTML = newPag ? newPag.innerHTML : '';
            }
            if (subjectSelect && newSubjectSelect) {
                const currentValue = subjectSelect.value;
                subjectSelect.innerHTML = newSubjectSelect.innerHTML;
                if (Array.from(subjectSelect.options).some(opt => opt.value === currentValue)) {
                    subjectSelect.value = currentValue;
                } else {
                    subjectSelect.value = '';
                }
            }
            
            const newPerPageSelect = doc.getElementById('perPageSelect');
            const perPageSelect = document.getElementById('perPageSelect');
            if (perPageSelect && newPerPageSelect) {
                perPageSelect.value = newPerPageSelect.value;
            }

            // تحديث بطاقات الفلترة السريعة
            const newQuickFilters = doc.getElementById('quick-filters-container');
            const quickFiltersContainer = document.getElementById('quick-filters-container');
            if (quickFiltersContainer && newQuickFilters) {
                quickFiltersContainer.innerHTML = newQuickFilters.innerHTML;
            }
            
            initBulkActions();
            dataGrid.style.opacity = '1';
        })
        .catch(error => {
            console.error('Error fetching questions:', error);
            dataGrid.style.opacity = '1';
        });
    };

    // تطبيق الفلتر السريع عند النقر على البطاقة
    window.applyQuickFilter = function(cardElement, gradeId, subjectId) {
        const gradeSelect = document.getElementById('gradeSelect');
        const subjectSelect = document.getElementById('subjectSelect');
        
        if (cardElement.classList.contains('active')) {
            gradeSelect.value = '';
            subjectSelect.value = '';
        } else {
            gradeSelect.value = gradeId;
            // حجز الخيار مؤقتاً لتفادي وميض القائمة
            subjectSelect.innerHTML = `<option value="${subjectId}" selected>جاري التحميل...</option>`;
            subjectSelect.value = subjectId;
        }
        
        fetchFilteredQuestions();
    };

    // دالة فتح وإغلاق بطاقة السؤال
    window.toggleQCard = function(id) {
        const card = document.getElementById('card-' + id);
        const expandArea = document.getElementById('expand-' + id);
        const btn = document.getElementById('btn-' + id);
        const icon = btn.querySelector('.bi');

        // خيار: إغلاق البطاقات الأخرى عند فتح واحدة جديدة (Accordion)
        document.querySelectorAll('.q-card.is-open').forEach(c => {
            if(c.id !== 'card-' + id) {
                c.classList.remove('is-open');
                c.querySelector('.q-expanded-area').classList.remove('show');
                const oldBtn = c.querySelector('.expand-btn');
                if(oldBtn) {
                    oldBtn.classList.remove('active');
                    oldBtn.querySelector('span').textContent = 'استعراض';
                    oldBtn.querySelector('.bi').className = 'bi bi-chevron-down';
                }
            }
        });

        if (card.classList.contains('is-open')) {
            card.classList.remove('is-open');
            expandArea.classList.remove('show');
            btn.classList.remove('active');
            btn.querySelector('span').textContent = 'استعراض';
            icon.className = 'bi bi-chevron-down';
        } else {
            card.classList.add('is-open');
            expandArea.classList.add('show');
            btn.classList.add('active');
            btn.querySelector('span').textContent = 'إغلاق';
            icon.className = 'bi bi-chevron-up';
        }
    }

    // دالة توسيع الكل
    window.expandAllQCards = function() {
        document.querySelectorAll('.q-card').forEach(card => {
            const id = card.id.replace('card-', '');
            const expandArea = document.getElementById('expand-' + id);
            const btn = document.getElementById('btn-' + id);
            const icon = btn.querySelector('.bi');

            card.classList.add('is-open');
            expandArea.classList.add('show');
            btn.classList.add('active');
            btn.querySelector('span').textContent = 'إغلاق';
            icon.className = 'bi bi-chevron-up';
        });
    }

    // دالة طي الكل
    window.collapseAllQCards = function() {
        document.querySelectorAll('.q-card').forEach(card => {
            const id = card.id.replace('card-', '');
            const expandArea = document.getElementById('expand-' + id);
            const btn = document.getElementById('btn-' + id);
            const icon = btn.querySelector('.bi');

            card.classList.remove('is-open');
            expandArea.classList.remove('show');
            btn.classList.remove('active');
            btn.querySelector('span').textContent = 'استعراض';
            icon.className = 'bi bi-chevron-down';
        });
    }

    // منطق الحذف الجماعي الفعلي
    window.confirmBulkDelete = function() {
        const checkedCbs = document.querySelectorAll('.row-cb:checked');
        const checkedIds = Array.from(checkedCbs).map(cb => cb.value);
        
        if (checkedIds.length === 0) {
            Swal.fire({
                title: 'تنبيه',
                text: 'يرجى تحديد سؤال واحد على الأقل للحذف الجماعي.',
                icon: 'info',
                confirmButtonText: 'حسناً',
                confirmButtonColor: 'var(--primary)'
            });
            return;
        }

        Swal.fire({
            title: 'هل أنت متأكد من الحذف الجماعي؟',
            text: `تحذير هام: أنت على وشك حذف عدد (${checkedIds.length}) من الأسئلة المحددة نهائياً! لا يمكن التراجع عن هذا الإجراء.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c30e14',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'نعم، احذف الكل!',
            cancelButtonText: 'تراجع',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'جاري حذف الأسئلة المحددة...',
                    html: 'يرجى عدم إغلاق الصفحة حتى انتهاء الحذف الجماعي.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                document.getElementById('bulkDeleteIds').value = checkedIds.join(',');
                document.getElementById('bulkDeleteForm').submit();
            }
        });
    }
</script>
@endpush
