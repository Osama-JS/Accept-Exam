@extends('layouts.admin')

@section('title', 'لوحة النتائج والقبول')
@section('page-title', 'نتائج الطلاب')

@section('breadcrumb')
    <span style="color: var(--text-main); font-weight: 700;">نتائج المتقدمين</span>
@endsection

@push('styles')
<style>
    /* ── لوحة مؤشرات القبول والتحليلات (Metrics Dashboard Showcase) ── */
    .metrics-dashboard {
        display: grid; grid-template-columns: 1.1fr 1.5fr; gap: 24px; margin-bottom: 32px;
    }
    @media (max-width: 992px) {
        .metrics-dashboard { grid-template-columns: 1fr; }
    }
    
    .metric-main-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: 24px; padding: 32px; color: #fff;
        display: flex; align-items: center; justify-content: space-between;
        position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(118, 181, 27, 0.25);
    }
    .metric-main-card .main-icon {
        font-size: 80px; opacity: 0.12; position: absolute; left: -10px; bottom: -20px; line-height: 1; pointer-events: none;
    }
    .metric-main-content { display: flex; flex-direction: column; gap: 8px; }
    .metric-main-content .hero-title { font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; }
    .metric-main-content .hero-value { font-size: 32px; font-weight: 950; }
    
    .metric-side-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
    }
    @media (max-width: 576px) {
        .metric-side-grid { grid-template-columns: 1fr; }
    }
    
    .metric-side-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 24px;
        display: flex; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .metric-side-card:hover { transform: translateY(-2px); box-shadow: 0 12px 20px rgba(0,0,0,0.03); }
    .side-card-lbl { font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block; }
    .side-card-val { font-size: 24px; font-weight: 900; }
    
    .side-card-circle {
        width: 54px; height: 54px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 13.5px; font-weight: 800; flex-shrink: 0;
    }
    .side-card-circle.success { background: #dcfce7; color: #166534; }
    .side-card-circle.danger { background: #fee2e2; color: #991b1b; }

    /* ── التبويبات العلوية السريعة (Quick Tabs) ── */
    .exam-tabs {
        display: flex; gap: 8px; border-bottom: 2px solid #e2e8f0; margin-bottom: 24px; padding-bottom: 0;
        flex-wrap: wrap;
    }
    .tab-item {
        padding: 12px 24px; font-size: 14px; font-weight: 700; color: #64748b;
        cursor: pointer; position: relative; transition: all 0.2s; border-radius: 12px 12px 0 0;
        display: flex; align-items: center; gap: 8px; text-decoration: none;
    }
    .tab-item:hover { color: var(--primary); background: rgba(118, 181, 27, 0.05); }
    .tab-item.active { color: var(--primary); background: transparent; }
    .tab-item.active::after {
        content: ''; position: absolute; bottom: -2px; left: 0; right: 0;
        height: 3px; background: var(--primary); border-radius: 4px 4px 0 0;
    }
    .tab-badge { background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 20px; font-size: 11px; }
    .tab-item.active .tab-badge { background: var(--primary-light); color: var(--primary-dark); }

    /* ── شريط الأدوات المتقدم مع أيقونات الحقول ── */
    .smart-toolbar {
        display: flex; flex-wrap: wrap; gap: 16px; align-items: center; justify-content: space-between;
        margin-bottom: 24px;
    }
    .toolbar-form { display: flex; flex-wrap: wrap; gap: 12px; flex: 1; align-items: center; }
    
    /* مجموعة الحقل مع أيقونة */
    .input-icon-group {
        position: relative; flex: 1; min-width: 260px; max-width: 400px;
    }
    .input-icon-group i {
        position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
        color: #94a3b8; font-size: 16px; pointer-events: none; transition: color 0.2s; z-index: 10;
    }
    .input-icon-group input {
        width: 100%; padding: 12px 16px 12px 48px; border-radius: 12px; border: 1.5px solid #cbd5e1;
        font-size: 13px; background: #fff; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.01);
        outline: none;
    }
    .input-icon-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-light); }
    .input-icon-group:focus-within i { color: var(--primary); }
    
    .select-filter {
        padding: 12px 20px; border-radius: 12px; border: 1.5px solid #cbd5e1;
        font-size: 13px; color: #475569; background: #fff; cursor: pointer;
        outline: none; transition: all 0.2s; min-width: 155px;
    }
    .select-filter:focus { border-color: var(--primary); }

    /* البطاقات العامة */
    .ws-card {
        background: #fff; border-radius: 20px; border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.01), 0 8px 10px -6px rgba(0,0,0,0.01); overflow: hidden;
    }
    .ws-card-header {
        padding: 22px 28px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;
    }
    .ws-card-title { font-size: 16px; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px; }

    /* وسوم الطلاب */
    .badge-pass { background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
    .badge-fail { background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }

    /* أزرار الإجراءات الفاخرة */
    .btn-action-icon {
        width: 36px; height: 36px; border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0;
        color: #64748b; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; text-decoration: none;
    }
    .btn-action-icon:hover { background: #eff6ff; color: #3b82f6; border-color: #bfdbfe; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1); }
    .btn-action-icon.print:hover { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.1); }

    #results-list-container {
        transition: opacity 0.2s ease-in-out;
    }
</style>
@endpush

@section('content')

@php
    $totalCountVal = max(1, $stats['total']);
    $successRate = round(($stats['passed'] / $totalCountVal) * 100);
    $failRate = 100 - $successRate;
@endphp

<!-- لوحة مؤشرات القبول والتحليلات المتكاملة -->
<div class="metrics-dashboard">
    <!-- البنر الرئيسي للمتقدمين -->
    <div class="metric-main-card">
        <div class="metric-main-content">
            <span class="hero-title"><i class="bi bi-people-fill"></i> إجمالي المتقدمين للقبول</span>
            <span class="hero-value">{{ $stats['total'] }} <span style="font-size: 18px; font-weight: 700; opacity: 0.8;">طالب وطالبة</span></span>
        </div>
        <i class="bi bi-people main-icon"></i>
    </div>
    
    <!-- بطاقات تحليلات النجاح والرسوب الدائرية -->
    <div class="metric-side-grid">
        <div class="metric-side-card">
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <div>
                    <span class="side-card-lbl">الطلاب الناجحون</span>
                    <span class="side-card-val" style="color: #16a34a;">{{ $stats['passed'] }} طالب</span>
                </div>
                <div class="side-card-circle success" title="نسبة نجاح الطلاب المتقدمين">
                    <span>{{ $successRate }}%</span>
                </div>
            </div>
        </div>
        
        <div class="metric-side-card">
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <div>
                    <span class="side-card-lbl">الطلاب غير المجتازين</span>
                    <span class="side-card-val" style="color: #ef4444;">{{ $stats['failed'] }} طالب</span>
                </div>
                <div class="side-card-circle danger" title="نسبة الطلاب الذين تعثروا">
                    <span>{{ $failRate }}%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نظام التبويبات السريعة (Quick Status Tabs) -->
<div class="exam-tabs">
    <a href="javascript:void(0)" onclick="filterResultsStatus(event, '')" class="tab-item {{ !request('status') ? 'active' : '' }}">
        جميع النتائج <span class="tab-badge">{{ $stats['total'] }}</span>
    </a>
    <a href="javascript:void(0)" onclick="filterResultsStatus(event, 'pass')" class="tab-item {{ request('status') === 'pass' ? 'active' : '' }}">
        الناجحون <span class="tab-badge" style="background:#dcfce7; color:#166534;">{{ $stats['passed'] }}</span>
    </a>
    <a href="javascript:void(0)" onclick="filterResultsStatus(event, 'fail')" class="tab-item {{ request('status') === 'fail' ? 'active' : '' }}">
        غير المجتازين <span class="tab-badge" style="background:#fee2e2; color:#991b1b;">{{ $stats['failed'] }}</span>
    </a>
</div>

<!-- شريط الفلاتر والأدوات المتقدم للبحث الفوري -->
<div class="smart-toolbar">
    <form method="GET" action="{{ route('admin.results.index') }}" id="resultsFilterForm" class="toolbar-form" onsubmit="return false;">
        <input type="hidden" name="status" id="resultsStatusInput" value="{{ request('status') }}">
        
        <!-- حقل البحث مع الأيقونة المدمجة والـ focus state -->
        <div class="input-icon-group">
            <i class="bi bi-search"></i>
            <input type="text" name="search" id="resultsSearchInput" placeholder="بحث فوري سريع باسم الطالب..." value="{{ request('search') }}" autocomplete="off">
        </div>

        <select name="grade_id" class="select-filter" onchange="fetchFilteredResults()">
            <option value="">كل الصفوف الدراسية</option>
            @foreach($grades as $g)
                <option value="{{ $g->id }}" {{ request('grade_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
            @endforeach
        </select>

        @if(request()->hasAny(['search', 'grade_id', 'status']))
            <a href="{{ route('admin.results.index') }}" class="btn btn-secondary" style="padding: 12px; border-radius: 12px; display: inline-flex; align-items: center;" title="إعادة تعيين"><i class="bi bi-x-lg"></i></a>
        @endif
    </form>
    
    <a href="{{ route('admin.results.export') }}?{{ http_build_query(request()->all()) }}" class="btn btn-success" style="padding: 12px 24px; border-radius: 12px; font-size: 14px; background: #16a34a; border: none; box-shadow: 0 6px 20px rgba(22, 163, 74, 0.25); display: inline-flex; align-items: center; gap: 8px;">
        <i class="bi bi-file-earmark-excel"></i> تصدير البيانات لـ Excel
    </a>
</div>

<!-- الحاوية المرنة المستهدفة بالـ AJAX للبحث اللحظي -->
<div id="results-list-container">
    <div class="ws-card">
        <div class="ws-card-header">
            <h3 class="ws-card-title"><i class="bi bi-bar-chart-fill text-primary" style="font-size: 20px;"></i> سجل نتائج المتقدمين للقبول</h3>
        </div>
        
        <div class="table-wrapper">
            @if($results->isEmpty())
                <div class="empty-state" style="padding: 48px 24px;">
                    <i class="bi bi-search" style="font-size: 40px; color: #cbd5e0; display: block; margin-bottom: 12px;"></i>
                    <h3 style="font-size: 16px; font-weight: 700; color: var(--text-muted);">لا توجد نتائج تطابق معايير الفلترة والبحث حالياً.</h3>
                </div>
            @else
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>الطالب والمدرسة السابقة</th>
                            <th>اسم الاختبار المتقدم إليه</th>
                            <th>الصف المتقدم</th>
                            <th>الدرجة المحرزة</th>
                            <th style="width: 200px;">مؤشر النسبة المئوية</th>
                            <th>التقييم</th>
                            <th>تاريخ التقديم</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $r)
                        <tr>
                            <!-- عمود الطالب الاحترافي مع Avatar دائري والمدرسة السابقة -->
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 42px; height: 42px; border-radius: 50%; background: var(--primary-light); color: var(--primary-dark); display: flex; align-items: center; justify-content: center; font-weight: 850; font-size: 16px;">
                                        {{ mb_substr($r->student->name, 0, 1, 'utf-8') }}
                                    </div>
                                    <div>
                                        <div style="color: #1e293b; font-size: 14px; font-weight: 850;">{{ $r->student->name }}</div>
                                        <div style="color: #94a3b8; font-size: 11px; font-weight: 600;"><i class="bi bi-building"></i> {{ $r->student->previous_school ?? 'غير محدد' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="color: #1e293b; font-weight: 800;">{{ Str::limit($r->exam->title, 40) }}</div>
                                <div style="color: #94a3b8; font-size: 11px; font-weight: 600;"><i class="bi bi-calendar3"></i> {{ $r->exam->academicYear->name }}</div>
                            </td>
                            <td><span class="badge badge-primary" style="font-size: 11px; padding: 4px 8px; border-radius: 6px;">{{ $r->exam->grade->name }}</span></td>
                            <td class="fw-bold" style="font-size: 14px;">{{ $r->score }} / {{ $r->total_marks }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="flex: 1; height: 6px; background: #f1f5f9; border-radius: 4px; overflow: hidden; border: 1px solid #e2e8f0;">
                                        <div style="height: 100%; width: {{ $r->percentage() }}%; background: {{ $r->isPassed() ? '#16a34a' : '#dc2626' }}; border-radius: 4px;"></div>
                                    </div>
                                    <span style="font-size: 12.5px; font-weight: 800; color: #1e293b;">{{ $r->percentage() }}%</span>
                                </div>
                            </td>
                            <td>
                                @if($r->isPassed())
                                    <span class="badge-pass"><i class="bi bi-check-circle-fill"></i> ناجح</span>
                                @else
                                    <span class="badge-fail"><i class="bi bi-x-circle-fill"></i> راسب</span>
                                @endif
                            </td>
                            <td class="text-muted" style="font-size: 12px;">{{ $r->submitted_at?->format('Y-m-d') }}</td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('admin.results.show', $r) }}" class="btn-action-icon" title="معاينة إجابات الطالب بالتفصيل">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.results.print', $r) }}" target="_blank" class="btn-action-icon print" title="طباعة شهادة النتيجة للقبول">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    @if($results->hasPages())
    <div style="margin-top: 24px;">
        {{ $results->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    // ── محرك البحث الفوري وتصفية النتائج اللحظية عبر الـ AJAX ──
    const filterForm = document.getElementById('resultsFilterForm');
    const searchInput = document.getElementById('resultsSearchInput');
    const listContainer = document.getElementById('results-list-container');
    
    // دالة لتأخير الاستعلام (Debounce) لتقليل العبء على السيرفر
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // دالة جلب البيانات وتحديث اللوحة بالـ AJAX
    window.fetchFilteredResults = function() {
        listContainer.style.opacity = '0.5';
        
        const formData = new FormData(filterForm);
        const queryParams = new URLSearchParams(formData).toString();
        const url = `{{ route('admin.results.index') }}?${queryParams}`;
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.getElementById('results-list-container');
            
            if (newContent) {
                listContainer.innerHTML = newContent.innerHTML;
            }
            listContainer.style.opacity = '1';
        })
        .catch(error => {
            console.error('Error fetching results:', error);
            listContainer.style.opacity = '1';
        });
    }

    // ربط كتابة الحقل بالبحث اللحظي مع debounce 300ms
    searchInput.addEventListener('input', debounce(fetchFilteredResults, 300));

    // دالة الانتقال بين التبويبات وحفظ حالتها في الحقل المخفي
    window.filterResultsStatus = function(event, val) {
        document.getElementById('resultsStatusInput').value = val;
        
        // تحديث المظهر النشط للتبويبات
        const tabs = document.querySelectorAll('.tab-item');
        tabs.forEach(t => t.classList.remove('active'));
        event.currentTarget.classList.add('active');
        
        fetchFilteredResults();
    }

    // اعتراض أزرار صفحات Laravel وتمرير طلباتها بالـ AJAX
    listContainer.addEventListener('click', function(e) {
        const pageLink = e.target.closest('.pagination a');
        if (pageLink) {
            e.preventDefault();
            const url = pageLink.getAttribute('href');
            
            listContainer.style.opacity = '0.5';
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('results-list-container');
                    if (newContent) {
                        listContainer.innerHTML = newContent.innerHTML;
                    }
                    listContainer.style.opacity = '1';
                    window.scrollTo({ top: listContainer.offsetTop - 100, behavior: 'smooth' });
                })
                .catch(() => {
                    listContainer.style.opacity = '1';
                });
        }
    });
</script>
@endpush
