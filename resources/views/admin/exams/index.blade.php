@extends('layouts.admin')

@section('title', 'إدارة الاختبارات')
@section('page-title', 'قائمة الاختبارات')

@section('breadcrumb')
    <span style="color: var(--text-main); font-weight: 700;">الاختبارات</span>
@endsection

@push('styles')
<style>
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

    /* ── شريط الأدوات والفلاتر المتقدمة ── */
    .smart-toolbar {
        display: flex; flex-wrap: wrap; gap: 16px; align-items: center; justify-content: space-between;
        margin-bottom: 24px;
    }
    .toolbar-form { display: flex; flex-wrap: wrap; gap: 12px; flex: 1; align-items: center; }
    .search-wrapper { position: relative; flex: 1; min-width: 260px; max-width: 400px; }
    .search-wrapper i { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .search-wrapper input {
        width: 100%; padding: 12px 16px 12px 48px; border-radius: 12px; border: 1.5px solid #cbd5e1;
        font-size: 13px; background: #fff; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.01);
        outline: none;
    }
    .search-wrapper input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-light); }
    
    .select-filter {
        padding: 12px 20px; border-radius: 12px; border: 1.5px solid #cbd5e1;
        font-size: 13px; color: #475569; background: #fff; cursor: pointer;
        outline: none; transition: all 0.2s; min-width: 150px;
    }
    .select-filter:focus { border-color: var(--primary); }

    /* ── تصميم بطاقة الاختبار الاحترافية ── */
    .exam-grid { display: flex; flex-direction: column; gap: 16px; }
    .ex-card {
        background: #fff; border-radius: 16px; border: 1px solid #e2e8f0;
        padding: 24px; display: grid; grid-template-columns: 2.5fr 1.5fr auto; gap: 24px;
        align-items: center; transition: all 0.3s; position: relative; overflow: visible;
    }
    .ex-card:hover { transform: translateY(-3px); box-shadow: 0 12px 24px rgba(0,0,0,0.05); border-color: #cbd5e0; }
    
    @media (max-width: 992px) {
        .ex-card { grid-template-columns: 1fr; gap: 16px; }
    }

    /* مؤشر جانبي للحالة */
    .ex-card::before { content: ''; position: absolute; right: 0; top: 0; bottom: 0; width: 4px; background: #e2e8f0; transition: background 0.2s; }
    .ex-card.active::before { background: var(--primary); }
    .ex-card.is-locked::before { background: #ef4444; }

    /* القسم الأول: معلومات الاختبار */
    .ex-info-col { display: flex; gap: 16px; align-items: flex-start; }
    .ex-icon {
        width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
        font-size: 24px; flex-shrink: 0; background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;
    }
    .ex-card.active .ex-icon { background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #166534; border: none; }
    
    .ex-title-wrap h4 { font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 6px 0; }
    .ex-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 12px; font-weight: 600; color: #64748b; }
    .ex-meta span { display: flex; align-items: center; gap: 4px; }
    .ex-meta i { color: #cbd5e0; font-size: 14px; }

    /* القسم الثاني: شريط التقدم (الإنجاز) */
    .ex-progress-col { padding: 0 24px; border-right: 1px dashed #e2e8f0; border-left: 1px dashed #e2e8f0; }
    @media (max-width: 992px) { .ex-progress-col { padding: 16px 0; border: none; border-top: 1px dashed #e2e8f0; border-bottom: 1px dashed #e2e8f0; } }
    .progress-header { display: flex; justify-content: space-between; font-size: 12px; font-weight: 700; margin-bottom: 8px; }
    .progress-header .lbl { color: #64748b; }
    .progress-header .val { color: #1e293b; }
    .progress-track { height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; position: relative; }
    .progress-bar { height: 100%; border-radius: 4px; background: var(--primary); transition: width 0.5s ease; }
    .progress-bar.low { background: #eab308; } /* أصفر إذا كانت النسبة قليلة */

    /* القسم الثالث: الإجراءات */
    .ex-actions-col { display: flex; align-items: center; gap: 16px; justify-content: flex-end; }
    
    /* زر التفعيل الأنيق */
    .status-toggle { display: flex; align-items: center; gap: 8px; cursor: pointer; }
    .status-lbl { font-size: 12px; font-weight: 700; color: #94a3b8; width: 75px; text-align: left; transition: color 0.2s; }
    .ex-card.active .status-lbl { color: var(--primary); }
    .switch-ui { position: relative; width: 44px; height: 24px; background: #cbd5e0; border-radius: 20px; transition: 0.3s; flex-shrink: 0; }
    .switch-ui::after { content: ''; position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; background: #fff; border-radius: 50%; transition: 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .status-toggle input { display: none; }
    .status-toggle input:checked + .switch-ui { background: var(--primary); }
    .status-toggle input:checked + .switch-ui::after { transform: translateX(20px); }

    /* زر النتائج الرئيسي */
    .btn-results {
        background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; padding: 10px 16px;
        border-radius: 10px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 6px;
        text-decoration: none; transition: all 0.2s;
    }
    .btn-results:hover { background: #fff; border-color: var(--primary); color: var(--primary); box-shadow: 0 4px 12px rgba(118, 181, 27, 0.1); }

    /* قائمة الثلاث نقاط (Dropdown) */
    .options-dropdown { position: relative; }
    .btn-dots {
        width: 38px; height: 38px; border-radius: 10px; background: transparent; border: 1px solid transparent;
        color: #94a3b8; display: flex; align-items: center; justify-content: center; font-size: 18px; cursor: pointer; transition: 0.2s;
    }
    .btn-dots:hover, .options-dropdown.open .btn-dots { background: #f1f5f9; color: #1e293b; border-color: #e2e8f0; }
    
    .dropdown-menu {
        position: absolute; left: 0; top: calc(100% + 8px); width: 200px; background: #fff;
        border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;
        opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.2s; z-index: 50;
    }
    .options-dropdown.open .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
    .dropdown-item {
        display: flex; align-items: center; gap: 10px; padding: 12px 16px; font-size: 13px; font-weight: 600;
        color: #475569; text-decoration: none; transition: 0.2s; border: none; width: 100%; text-align: right; background: transparent; cursor: pointer;
    }
    .dropdown-item:hover { background: #f8fafc; color: var(--primary); }
    .dropdown-item.danger:hover { background: #fef2f2; color: #ef4444; }
    .dropdown-divider { height: 1px; background: #f1f5f9; margin: 4px 0; }

    /* تأخر خفيف للمظهر المتجاوب للـ Ajax */
    #exams-list-container {
        transition: opacity 0.2s ease-in-out;
    }
</style>
@endpush

@section('content')

<div class="exam-tabs">
    <a href="javascript:void(0)" onclick="filterStatus(event, '')" class="tab-item {{ !request('status') ? 'active' : '' }}">
        الكل <span class="tab-badge">{{ $totalCount }}</span>
    </a>
    <a href="javascript:void(0)" onclick="filterStatus(event, 'active')" class="tab-item {{ request('status') === 'active' ? 'active' : '' }}">
        مفعلة (متاحة) <span class="tab-badge" style="background:#dcfce7; color:#166534;">{{ $activeCount }}</span>
    </a>
    <a href="javascript:void(0)" onclick="filterStatus(event, 'locked')" class="tab-item {{ request('status') === 'locked' ? 'active' : '' }}">
        مغلقة <span class="tab-badge">{{ max(0, $totalCount - $activeCount) }}</span>
    </a>
</div>

<div class="smart-toolbar">
    <form method="GET" action="{{ route('admin.exams.index') }}" id="toolbarFilterForm" class="toolbar-form" onsubmit="return false;">
        <input type="hidden" name="status" id="statusFilterInput" value="{{ request('status') }}">
        
        <div class="search-wrapper">
            <i class="bi bi-search"></i>
            <input type="text" name="search" id="instantSearchInput" placeholder="بحث فوري سريع باسم الاختبار..." value="{{ request('search') }}" autocomplete="off">
        </div>

        <select name="grade_id" class="select-filter" onchange="fetchFilteredExams()">
            <option value="">كل الصفوف</option>
            @foreach($grades as $grade)
                <option value="{{ $grade->id }}" {{ request('grade_id') == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
            @endforeach
        </select>
        
        <select name="academic_year_id" class="select-filter" onchange="fetchFilteredExams()">
            <option value="">كل السنوات</option>
            @foreach($academicYears as $year)
                <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
            @endforeach
        </select>

        @if(request()->hasAny(['search', 'grade_id', 'academic_year_id', 'status']))
            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary" style="padding: 12px; border-radius: 12px; display: inline-flex; align-items: center;" title="إعادة تعيين"><i class="bi bi-x-lg"></i></a>
        @endif
    </form>
    
    <a href="{{ route('admin.exams.create') }}" class="btn btn-primary" style="padding: 12px 24px; border-radius: 12px; font-size: 14px; background: linear-gradient(135deg, #76b51b, #5f9416); border: none; box-shadow: 0 6px 20px rgba(118, 181, 27, 0.25);">
        <i class="bi bi-plus-lg"></i> إنشاء اختبار جديد
    </a>
</div>

<!-- الحاوية المرنة المستهدفة بالـ AJAX للبحث الفوري -->
<div id="exams-list-container">
    <div class="exam-grid">
        @forelse($exams as $exam)
        @php
            $submitted = $exam->student_exams_count;
            $totalStudents = $exam->grade->students_count;
            $percent = $totalStudents > 0 ? round(($submitted / $totalStudents) * 100) : 0;
            
            // تحديد لون شريط التقدم بناءً على النسبة
            $barClass = $percent < 50 ? 'low' : '';
        @endphp

        <div class="ex-card {{ $exam->is_active ? 'active' : 'is-locked' }}">
            <div class="ex-info-col">
                <div class="ex-icon">
                    <i class="bi {{ $exam->is_active ? 'bi-file-earmark-check' : 'bi-lock-fill' }}"></i>
                </div>
                <div class="ex-title-wrap">
                    <h4>{{ $exam->title }}</h4>
                    <div class="ex-meta">
                        <span><i class="bi bi-layers"></i> {{ $exam->grade->name }}</span>
                        <span><i class="bi bi-calendar3"></i> {{ $exam->academicYear->name }}</span>
                        <span><i class="bi bi-bullseye"></i> {{ $exam->pass_marks }} / {{ $exam->total_marks }} درجة</span>
                    </div>
                </div>
            </div>

            <div class="ex-progress-col">
                <div class="progress-header">
                    <span class="lbl">معدل تسليم الطلاب</span>
                    <span class="val">{{ $submitted }} / {{ $totalStudents }} ({{ $percent }}%)</span>
                </div>
                <div class="progress-track">
                    <div class="progress-bar {{ $barClass }}" style="width: {{ $percent }}%;"></div>
                </div>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 6px;">
                    @if($percent == 100 && $totalStudents > 0)
                        <i class="bi bi-check-all text-success"></i> اكتمل تسليم جميع الطلاب
                    @else
                        <i class="bi bi-clock-history"></i> يتبقى {{ max(0, $totalStudents - $submitted) }} طالب لم يختبر بعد
                    @endif
                </div>
            </div>

            <div class="ex-actions-col">
                <label class="status-toggle">
                    <span class="status-lbl">{{ $exam->is_active ? 'متاح الآن' : 'مغلق' }}</span>
                    <input type="checkbox" {{ $exam->is_active ? 'checked' : '' }} onchange="toggleStatus('{{ $exam->id }}', this)">
                    <div class="switch-ui"></div>
                </label>

                <a href="{{ route('admin.exams.show', $exam) }}" class="btn-results"><i class="bi bi-bar-chart-fill"></i> لوحة النتائج</a>

                <div class="options-dropdown">
                    <button type="button" class="btn-dots" onclick="toggleDropdown(event, this)"><i class="bi bi-three-dots-vertical"></i></button>
                    <div class="dropdown-menu">
                        <a href="{{ route('admin.exams.show', $exam) }}" class="dropdown-item"><i class="bi bi-eye"></i> معاينة تفصيلية للأسئلة</a>
                        <div class="dropdown-divider"></div>
                        <form id="del-form-{{ $exam->id }}" action="{{ route('admin.exams.destroy', $exam) }}" method="POST" style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="button" class="dropdown-item danger" onclick="confirmDelete('{{ $exam->id }}')"><i class="bi bi-trash3"></i> حذف الاختبار نهائياً</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
            <div style="text-align: center; padding: 48px 24px; background: #fff; border-radius: 16px; border: 1.5px dashed #cbd5e0; color: var(--text-muted); width: 100%;">
                <i class="bi bi-journal-x" style="font-size: 40px; color: #cbd5e0; display: block; margin-bottom: 12px;"></i>
                <p style="font-weight: 700; margin: 0;">لا توجد اختبارات تطابق معايير البحث والفلترة حالياً.</p>
            </div>
        @endforelse
    </div>

    @if($exams->hasPages())
    <div style="margin-top: 24px;">
        {{ $exams->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    // ── منطق تغيير حالة الاختبار ──
    window.toggleStatus = function(examId, checkbox) {
        const card = checkbox.closest('.ex-card');
        const lbl = card.querySelector('.status-lbl');
        const icon = card.querySelector('.ex-icon i');
        const isChecked = checkbox.checked;

        if(isChecked) {
            card.classList.remove('is-locked');
            card.classList.add('active');
            lbl.textContent = 'متاح الآن';
            icon.className = 'bi bi-file-earmark-check';
        } else {
            card.classList.remove('active');
            card.classList.add('is-locked');
            lbl.textContent = 'مغلق';
            icon.className = 'bi bi-lock-fill';
        }

        // إرسال طلب التبديل للسيرفر عبر fetch
        fetch(`{{ url('admin/exams') }}/${examId}/toggle`)
            .then(response => {
                if(!response.ok) {
                    alert('حدث خطأ أثناء تحديث حالة الاختبار.');
                    checkbox.checked = !isChecked;
                    toggleExamStatusVisual(examId, !isChecked, card, lbl, icon);
                }
            })
            .catch(() => {
                alert('حدث خطأ في الاتصال بالسيرفر.');
                checkbox.checked = !isChecked;
                toggleExamStatusVisual(examId, !isChecked, card, lbl, icon);
            });
    }

    function toggleExamStatusVisual(examId, isChecked, card, lbl, icon) {
        if(isChecked) {
            card.classList.remove('is-locked');
            card.classList.add('active');
            lbl.textContent = 'متاح الآن';
            icon.className = 'bi bi-file-earmark-check';
        } else {
            card.classList.remove('active');
            card.classList.add('is-locked');
            lbl.textContent = 'مغلق';
            icon.className = 'bi bi-lock-fill';
        }
    }

    // ── دالة لتأكيد الحذف ──
    window.confirmDelete = function(examId) {
        if(confirm('هل أنت متأكد من حذف هذا الاختبار؟ سيتم حذف جميع إجابات الطلاب المرتبطة به نهائياً.')) {
            document.getElementById('del-form-' + examId).submit();
        }
    }

    // ── منطق القوائم المنسدلة (Three Dots Menu) ──
    window.toggleDropdown = function(event, btn) {
        event.stopPropagation();
        const dropdown = btn.closest('.options-dropdown');
        const isOpen = dropdown.classList.contains('open');
        
        // إغلاق جميع القوائم الأخرى المفتوحة
        document.querySelectorAll('.options-dropdown.open').forEach(d => {
            if (d !== dropdown) {
                d.classList.remove('open');
            }
        });
        
        // فتح أو إغلاق القائمة الحالية
        if(isOpen) {
            dropdown.classList.remove('open');
        } else {
            dropdown.classList.add('open');
        }
    }

    // إغلاق القائمة عند النقر خارجها
    document.addEventListener('click', function(e) {
        if(!e.target.closest('.options-dropdown')) {
            document.querySelectorAll('.options-dropdown.open').forEach(d => d.classList.remove('open'));
        }
    });

    // ── منطق التبويبات العلوية (Quick Tabs) ──
    window.filterStatus = function(event, val) {
        document.getElementById('statusFilterInput').value = val;
        
        // تحديث التبويبات النشطة بصرياً
        const tabs = document.querySelectorAll('.tab-item');
        tabs.forEach(t => t.classList.remove('active'));
        event.currentTarget.classList.add('active');
        
        fetchFilteredExams();
    }

    // ── محرك البحث الفوري والفلترة بالـ AJAX ──
    const filterForm = document.getElementById('toolbarFilterForm');
    const searchInput = document.getElementById('instantSearchInput');
    const listContainer = document.getElementById('exams-list-container');
    
    // دالة لتأخير طلبات البحث (Debounce) لضمان عدم إرهاق السيرفر
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // دالة جلب وتحديث الاختبارات
    window.fetchFilteredExams = function() {
        listContainer.style.opacity = '0.5';
        
        const formData = new FormData(filterForm);
        const queryParams = new URLSearchParams(formData).toString();
        const url = `{{ route('admin.exams.index') }}?${queryParams}`;
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.getElementById('exams-list-container');
            
            if (newContent) {
                listContainer.innerHTML = newContent.innerHTML;
            }
            listContainer.style.opacity = '1';
        })
        .catch(error => {
            console.error('Error fetching exams:', error);
            listContainer.style.opacity = '1';
        });
    }

    // ربط الكتابة بالبحث الفوري مع تأخير 300 مللي ثانية
    searchInput.addEventListener('input', debounce(fetchFilteredExams, 300));

    // اعتراض ضغطات أزرار الصفحات لجعل الترقيم يعمل بالـ AJAX أيضاً
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
                    const newContent = doc.getElementById('exams-list-container');
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
