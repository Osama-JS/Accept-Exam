@extends('layouts.admin')

@section('title', 'المواد الدراسية')
@section('page-title', 'المواد الدراسية')

@section('breadcrumb')
    <span style="color: var(--text-muted); font-weight: 500;">إدارة المحتوى</span>
    <span style="color: #cbd5e0; margin: 0 4px;"><i class="bi bi-chevron-left" style="font-size: 10px;"></i></span>
    <span style="color: var(--text-main); font-weight: 700;">المواد الدراسية</span>
@endsection

@push('styles')
<style>
    /* ── بنر مؤشرات المناهج والمقررات (Curriculum Insights Banner) ── */
    .curriculum-insights-banner {
        display: grid; grid-template-columns: 1.2fr 1.5fr; gap: 24px; margin-bottom: 32px;
    }
    @media (max-width: 992px) {
        .curriculum-insights-banner { grid-template-columns: 1fr; }
    }
    
    .curriculum-main-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: 24px; padding: 32px; color: #fff;
        display: flex; flex-direction: column; justify-content: space-between; gap: 20px;
        position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(118, 181, 27, 0.2);
    }
    .curriculum-main-card::after {
        content: '\F1BE'; font-family: 'bootstrap-icons'; position: absolute;
        left: -15px; bottom: -20px; font-size: 100px; opacity: 0.12; pointer-events: none;
    }
    .curriculum-main-content { display: flex; flex-direction: column; gap: 6px; }
    .curriculum-main-content .hero-lbl { font-size: 13px; font-weight: 800; opacity: 0.9; text-transform: uppercase; }
    .curriculum-main-content .hero-val { font-size: 30px; font-weight: 950; }
    
    .btn-create-sub {
        display: inline-flex; align-items: center; gap: 8px; background: #ffffff; color: var(--primary-dark);
        padding: 12px 24px; border-radius: 12px; font-size: 13.5px; font-weight: 850; text-decoration: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: all 0.2s; border: none; width: fit-content;
    }
    .btn-create-sub:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); color: var(--primary); }

    .curriculum-side-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
    }
    @media (max-width: 576px) {
        .curriculum-side-grid { grid-template-columns: 1fr; }
    }
    
    .curriculum-side-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 24px;
        display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .curriculum-side-card:hover { transform: translateY(-2px); box-shadow: 0 12px 20px rgba(0,0,0,0.02); }
    
    .side-card-icon {
        width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center;
        font-size: 24px; flex-shrink: 0;
    }
    .side-card-lbl { font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 4px; display: block; }
    .side-card-val { font-size: 20px; font-weight: 900; color: #1e293b; }

    /* ── شريط التحكم العلوي (Toolbar) ── */
    .page-toolbar {
        display: flex; flex-wrap: wrap; gap: 16px; align-items: center; justify-content: space-between;
        background: #fff; padding: 16px 24px; border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.02); border: 1px solid rgba(226, 232, 240, 0.6);
        margin-bottom: 28px;
    }

    .toolbar-filters { display: flex; gap: 12px; flex: 1; justify-content: flex-end; align-items: center; }
    
    /* مجموعة الحقل مع أيقونة */
    .input-icon-group {
        position: relative; width: 100%; max-width: 320px;
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
    
    .select-filter {
        padding: 11px 16px; border-radius: 12px; border: 1.5px solid #cbd5e1; 
        font-size: 13px; color: var(--text-main); background: #fff; cursor: pointer; outline: none;
        transition: all 0.2s; min-width: 140px;
    }
    .select-filter:focus { border-color: var(--primary); }

    /* ── شبكة البطاقات ── */
    .subjects-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; margin-bottom: 28px;
        transition: opacity 0.2s ease-in-out;
    }

    /* ── تصميم البطاقة الاحترافي الملون ديناميكياً ── */
    .subject-card {
        background: #fff; border-radius: 20px; border: 1px solid #e2e8f0;
        padding: 26px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex; flex-direction: column; position: relative;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01), 0 2px 4px -1px rgba(0,0,0,0.01);
    }
    .subject-card:hover { transform: translateY(-4px); box-shadow: 0 16px 32px rgba(0,0,0,0.05); border-color: var(--theme-color, var(--primary)); }

    /* رأس البطاقة */
    .card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
    
    .icon-wrapper {
        width: 52px; height: 52px; border-radius: 14px;
        background: var(--theme-grad);
        color: var(--theme-color); display: flex; align-items: center; justify-content: center;
        font-size: 20px; font-weight: 850; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.4);
    }
    
    /* القائمة المنسدلة (Options Menu) */
    .options-menu { position: relative; }
    .btn-options {
        background: transparent; border: none; color: #94a3b8; width: 34px; height: 34px; 
        border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;
    }
    .btn-options:hover { background: #f1f5f9; color: var(--text-main); }
    
    .dropdown-content {
        position: absolute; left: 0; top: 100%; min-width: 165px; background: #fff;
        border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0;
        z-index: 20; opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.2s;
    }
    .options-menu.active .dropdown-content { opacity: 1; visibility: visible; transform: translateY(4px); }
    
    .dropdown-item {
        display: flex; align-items: center; gap: 10px; padding: 10px 16px;
        color: var(--text-main); text-decoration: none; font-size: 13px; font-weight: 700;
        background: transparent; border: none; width: 100%; text-align: right; cursor: pointer; transition: background 0.2s;
    }
    .dropdown-item:first-child { border-radius: 12px 12px 0 0; }
    .dropdown-item:last-child { border-radius: 0 0 12px 12px; }
    .dropdown-item:hover { background: #f8fafc; color: var(--primary); }
    .dropdown-item.text-danger:hover { background: #fef2f2; color: var(--danger); }

    /* معلومات المادة */
    .subject-info { flex: 1; }
    .subject-info h3 { font-size: 18px; font-weight: 850; color: #1e293b; margin-bottom: 12px; line-height: 1.3; }
    
    .meta-tags { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
    .tag { padding: 5px 12px; border-radius: 8px; font-size: 11.5px; font-weight: 800; border: 1px solid transparent; display: inline-flex; align-items: center; gap: 4px; }

    /* وسوم الجاهزية */
    .badge-readiness { padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; }
    .badge-readiness.low { background: #fee2e2; color: #dc2626; }
    .badge-readiness.mid { background: #fef3c7; color: #d97706; }
    .badge-readiness.high { background: #dcfce7; color: #16a34a; }

    /* مؤشر تقدم بنك الأسئلة */
    .bank-progress-wrapper {
        margin-top: 14px; display: flex; flex-direction: column; gap: 6px;
    }
    .bank-progress-label {
        font-size: 11px; font-weight: 750; color: #64748b; display: flex; justify-content: space-between; align-items: center;
    }
    .bank-progress-track {
        height: 6px; background: #f1f5f9; border-radius: 4px; overflow: hidden; border: 1px solid #e2e8f0;
    }
    .bank-progress-bar {
        height: 100%; border-radius: 4px; background: var(--theme-color);
    }

    /* زر الأسئلة - أسلوب مميز (Ghost to Solid) */
    .card-action { margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
    .btn-manage {
        display: flex; align-items: center; justify-content: space-between; width: 100%;
        padding: 12px 18px; border-radius: 12px; font-size: 14px; font-weight: 800;
        color: var(--theme-color); background: transparent; border: 2px solid var(--theme-color-light);
        text-decoration: none; transition: all 0.3s;
    }
    .btn-manage i { font-size: 18px; transition: transform 0.3s; color: var(--theme-color); }
    .subject-card:hover .btn-manage { background: var(--theme-color); color: #fff; border-color: var(--theme-color); }
    .subject-card:hover .btn-manage i { transform: translateX(-4px); color: #fff; }

    /* ── الترقيم المطور المدمج مع لارافل (Pagination) ── */
    .pagination-wrapper {
        background: #ffffff; border: 1px solid rgba(226, 232, 240, 0.8);
        padding: 20px 28px; border-radius: 16px; margin-top: 24px;
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
</style>
@endpush

@section('content')

@php
    $totalSubjects = $subjects->count();
    $totalQuestions = $subjects->sum('questions_count');
    $averageQuestions = $totalSubjects > 0 ? round($totalQuestions / $totalSubjects) : 0;
@endphp

<!-- لوحة مؤشرات المناهج والمقررات الفاخرة -->
<div class="curriculum-insights-banner">
    <!-- البنر الرئيسي الأيمن للتحليلات -->
    <div class="curriculum-main-card">
        <div class="curriculum-main-content">
            <span class="hero-lbl"><i class="bi bi-book-half"></i> لوحة مؤشرات المناهج والمقررات</span>
            <span class="hero-val">{{ $totalCount ?? 0 }} <span style="font-size: 16px; font-weight: 700; opacity: 0.85;">مقررات دراسية نشطة</span></span>
        </div>
        <a href="{{ route('admin.subjects.create') }}" class="btn-create-sub">
            <i class="bi bi-plus-lg"></i> إضافة مادة دراسية جديدة
        </a>
    </div>
    
    <!-- مؤشرات بنك الأسئلة السريعة -->
    <div class="curriculum-side-grid">
        <div class="curriculum-side-card">
            <i class="bi bi-patch-question-fill side-card-icon" style="color: #2563eb; background: #eff6ff;"></i>
            <div>
                <span class="side-card-lbl">إجمالي الأسئلة المتاحة</span>
                <span class="side-card-val">{{ $totalQuestions }} سؤال</span>
            </div>
        </div>
        <div class="curriculum-side-card">
            <i class="bi bi-pie-chart-fill side-card-icon" style="color: #16a34a; background: #f0fdf4;"></i>
            <div>
                <span class="side-card-lbl">متوسط الأسئلة لكل مقرر</span>
                <span class="side-card-val">{{ $averageQuestions }} سؤال</span>
            </div>
        </div>
    </div>
</div>

<!-- شريط التحكم والتصفية الفورية بالـ AJAX -->
<div class="page-toolbar">
    <div style="font-size: 14.5px; font-weight: 850; color: #1e293b;"><i class="bi bi-funnel-fill text-primary"></i> أدوات التصفية الفورية</div>

    <form method="GET" action="{{ route('admin.subjects.index') }}" id="subjectsFilterForm" class="toolbar-filters" onsubmit="return false;">
        <!-- حقل البحث بالأيقونة المدمجة والـ focus state -->
        <div class="input-icon-group">
            <i class="bi bi-search"></i>
            <input type="text" name="search" id="searchInput" placeholder="ابحث باسم المادة..." value="{{ request('search') }}" autocomplete="off">
        </div>
        
        <select name="grade_id" id="gradeSelect" class="select-filter" onchange="fetchFilteredSubjects()">
            <option value="">كل الصفوف</option>
            @foreach($grades ?? [] as $grade)
                <option value="{{ $grade->id }}" {{ request('grade_id') == $grade->id ? 'selected' : '' }}>
                    {{ $grade->name }}
                </option>
            @endforeach
        </select>
        
        @if(request('search') || request('grade_id'))
            <a href="{{ route('admin.subjects.index') }}" class="btn-options" style="background: rgba(195,14,20,.08); color: var(--danger); border: 1.5px solid rgba(195,14,20,.15); width: 38px; height: 38px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;" title="مسح الفلاتر">
                <i class="bi bi-x-lg"></i>
            </a>
        @endif
    </form>
</div>

<!-- الحاوية المرنة لشبكة المواد المدعومة بالـ AJAX -->
<div class="subjects-grid" id="dataGrid">
    @if($subjects->isEmpty())
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #fff; border-radius: 20px; border: 1px dashed #cbd5e0;">
            <div style="font-size: 48px; color: #cbd5e0; margin-bottom: 16px;"><i class="bi bi-journal-x"></i></div>
            <h3 style="font-size: 18px; font-weight: 850; color: var(--text-main);">لم يتم العثور على مقررات دراسية</h3>
            <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 20px;">قم بإضافة المقررات الدراسية وربطها بالصفوف للبدء.</p>
        </div>
    @else
        <!-- إعداد لوحة ألوان الهوية البصرية للمواد ديناميكياً -->
        @php
            $colors = [
                'green'  => ['bg' => 'rgba(118,181,27,0.08)', 'text' => '#76b51b', 'grad' => 'linear-gradient(135deg, rgba(118,181,27,0.12) 0%, rgba(118,181,27,0.02) 100%)'],
                'blue'   => ['bg' => 'rgba(37,99,235,0.08)', 'text' => '#2563eb', 'grad' => 'linear-gradient(135deg, rgba(37,99,235,0.12) 0%, rgba(37,99,235,0.02) 100%)'],
                'purple' => ['bg' => 'rgba(139,92,246,0.08)', 'text' => '#8b5cf6', 'grad' => 'linear-gradient(135deg, rgba(139,92,246,0.12) 0%, rgba(139,92,246,0.02) 100%)'],
                'orange' => ['bg' => 'rgba(245,158,11,0.08)', 'text' => '#f59e0b', 'grad' => 'linear-gradient(135deg, rgba(245,158,11,0.12) 0%, rgba(245,158,11,0.02) 100%)'],
                'teal'   => ['bg' => 'rgba(13,148,136,0.08)', 'text' => '#0d9488', 'grad' => 'linear-gradient(135deg, rgba(13,148,136,0.12) 0%, rgba(13,148,136,0.02) 100%)']
            ];
            $colorKeys = array_keys($colors);
        @endphp

        @foreach($subjects as $subject)
            @php
                $colorKey = $colorKeys[$subject->id % count($colorKeys)];
                $theme = $colors[$colorKey];
                
                // حساب كفاءة وقوة بنك الأسئلة ديناميكياً
                $qCount = $subject->questions_count ?? 0;
                $pct = min(100, round(($qCount / 50) * 100)); // نعتبر أن 50 سؤالاً هي القوة المثالية للمادة
                
                // تصنيف جاهزية بنك الأسئلة
                if ($qCount < 10) {
                    $readinessText = 'شحيح';
                    $readinessClass = 'low';
                } elseif ($qCount >= 10 && $qCount < 30) {
                    $readinessText = 'مقبول';
                    $readinessClass = 'mid';
                } else {
                    $readinessText = 'ممتاز';
                    $readinessClass = 'high';
                }
            @endphp
            
            <div class="subject-card data-card" style="--theme-color: {{ $theme['text'] }}; --theme-grad: {{ $theme['grad'] }}; --theme-color-light: {{ $theme['bg'] }};">
                
                <div class="card-top">
                    <!-- أيقونة المادة بلون متدرج مخصص للمادة ومستخرج من أول حرفين من اسمها إن غابت الأيقونة الرسمية -->
                    <div class="icon-wrapper">
                        @if($subject->icon)
                            {!! $subject->icon !!}
                        @else
                            <span>{{ mb_substr($subject->name, 0, 2, 'utf-8') }}</span>
                        @endif
                    </div>
                    
                    <div class="options-menu">
                        <button class="btn-options toggle-menu"><i class="bi bi-three-dots-vertical"></i></button>
                        <div class="dropdown-content">
                            <a href="{{ route('admin.subjects.edit', $subject) }}" class="dropdown-item">
                                <i class="bi bi-pencil"></i> تعديل المادة
                            </a>
                            <form id="del-sub-{{ $subject->id }}" method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" style="margin: 0;">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('del-sub-{{ $subject->id }}', 'تحذير: هل أنت متأكد من حذف هذه المادة؟')" class="dropdown-item text-danger">
                                    <i class="bi bi-trash"></i> حذف المادة
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="subject-info">
                    <h3>{{ $subject->name }}</h3>
                    <div class="meta-tags">
                        @foreach($subject->grades as $g)
                            <span class="tag" style="background: {{ $theme['bg'] }}; color: {{ $theme['text'] }};"><i class="bi bi-layers"></i> {{ $g->name }}</span>
                        @endforeach
                        <span class="tag" style="background: #f8fafc; color: #64748b; border-color: #e2e8f0;"><i class="bi bi-patch-question-fill" style="color: {{ $theme['text'] }};"></i> {{ $qCount }} سؤال</span>
                    </div>

                    <!-- مؤشر قوة وبناء بنك الأسئلة للمادة -->
                    <div class="bank-progress-wrapper">
                        <div class="bank-progress-label">
                            <span>جاهزية بنك الأسئلة</span>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span class="badge-readiness {{ $readinessClass }}">{{ $readinessText }}</span>
                                <span style="font-family: 'Inter', sans-serif; font-weight: 800;">{{ $pct }}%</span>
                            </div>
                        </div>
                        <div class="bank-progress-track">
                            <div class="bank-progress-bar" style="width: {{ $pct }}%;"></div>
                        </div>
                    </div>
                </div>

                <div class="card-action">
                    <a href="{{ route('admin.questions.index', ['subject_id' => $subject->id]) }}" class="btn-manage">
                        <span>إدارة بنك الأسئلة</span>
                        <i class="bi bi-arrow-left-circle-fill"></i>
                    </a>
                </div>
                
            </div>
        @endforeach
    @endif
</div>

<!-- حاوية الترقيم للتصفية الفورية بالـ AJAX -->
<div id="pagination-container">
    @if($subjects->hasPages())
        <div class="pagination-wrapper">
            {{ $subjects->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    // ── تهيئة منطق القوائم المنسدلة (Three Dots Menu) ──
    function initDropdowns() {
        const menus = document.querySelectorAll('.options-menu');
        
        function closeAllMenus() {
            menus.forEach(menu => menu.classList.remove('active'));
        }

        menus.forEach(menu => {
            const btn = menu.querySelector('.toggle-menu');
            if (btn) {
                // تجنب تكرار المستمعين في الـ AJAX
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                
                newBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isActive = menu.classList.contains('active');
                    closeAllMenus();
                    if (!isActive) {
                        menu.classList.add('active');
                    }
                });
            }
        });

        document.addEventListener('click', closeAllMenus);
    }

    document.addEventListener('DOMContentLoaded', function() {
        initDropdowns();

        // ── محرك البحث الفوري وتصفية الفرز بالـ AJAX ──
        const searchInput = document.getElementById('searchInput');
        const filterForm = document.getElementById('subjectsFilterForm');
        const dataGrid = document.getElementById('dataGrid');
        const pagContainer = document.getElementById('pagination-container');
        
        function debounce(func, delay) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }

        window.fetchFilteredSubjects = function() {
            dataGrid.style.opacity = '0.5';
            
            const formData = new FormData(filterForm);
            const queryParams = new URLSearchParams(formData).toString();
            const url = `{{ route('admin.subjects.index') }}?${queryParams}`;
            
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
                
                initDropdowns();
                dataGrid.style.opacity = '1';
            })
            .catch(error => {
                console.error('Error fetching subjects:', error);
                dataGrid.style.opacity = '1';
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', debounce(fetchFilteredSubjects, 300));
        }

        // اعتراض أزرار صفحات Laravel وتمرير طلباتها بالـ AJAX
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
                        initDropdowns();
                        dataGrid.style.opacity = '1';
                        window.scrollTo({ top: dataGrid.offsetTop - 100, behavior: 'smooth' });
                    })
                    .catch(() => {
                        dataGrid.style.opacity = '1';
                    });
            }
        });
    });
</script>
@endpush
