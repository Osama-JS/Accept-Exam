@extends('layouts.admin')

@section('title', 'تفاصيل الاختبار')
@section('page-title', 'تفاصيل الاختبار')

@section('breadcrumb')
    <a href="{{ route('admin.exams.index') }}">إدارة الاختبارات</a>
    <span>/</span>
    <span style="color: var(--text-main); font-weight: 700;">تفاصيل الاختبار</span>
@endsection

@push('styles')
<style>
    /* ── بنر تفاصيل الاختبار (Hero Section) ── */
    .exam-hero {
        background: linear-gradient(135deg, #223422 0%, #6aa418 100%);
        border-radius: 20px; padding: 32px; color: #fff; margin-bottom: 28px;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 24px;
        position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
    }
    .exam-hero::after {
        content: '\F3FC'; font-family: 'bootstrap-icons'; position: absolute;
        left: -20px; bottom: -40px; font-size: 180px; opacity: 0.05; line-height: 1; pointer-events: none;
    }
    .hero-title { font-size: 24px; font-weight: 800; margin: 0 0 12px 0; line-height: 1.3; }
    .hero-badges { display: flex; gap: 10px; flex-wrap: wrap; }
    .hero-badge {
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);
        padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700;
        display: inline-flex; align-items: center; gap: 6px; backdrop-filter: blur(4px);
    }

    /* ── شبكة الإحصائيات التحليلية (Stats Grid) ── */
    .stats-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 28px;
    }
    .stat-card {
        background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px;
        display: flex; flex-direction: column; gap: 6px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);
        position: relative; overflow: hidden;
    }
    .stat-card::after {
        font-family: 'bootstrap-icons'; position: absolute; left: 16px; bottom: 12px; font-size: 24px; opacity: 0.06;
    }
    .stat-card.total::after { content: '\F188'; color: var(--primary); }
    .stat-card.pass::after { content: '\F2E6'; color: #16a34a; }
    .stat-card.questions::after { content: '\F3FB'; color: #2563eb; }
    .stat-card.mark::after { content: '\F1B4'; color: #d97706; }

    .stat-card-val { font-size: 24px; font-weight: 900; color: #1e293b; }
    .stat-card-lbl { font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }

    /* ── تخطيط مساحة العمل الثنائية ── */
    .show-grid {
        display: grid; grid-template-columns: 1fr 1.6fr; gap: 24px;
        align-items: start; margin-bottom: 40px;
    }
    @media (max-width: 992px) {
        .show-grid { grid-template-columns: 1fr; }
    }

    /* البطاقات العامة */
    .ws-card {
        background: #fff; border-radius: 16px; border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); overflow: hidden;
    }
    .ws-card-header {
        padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;
    }
    .ws-card-title { font-size: 16px; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px; }
    .ws-card-body { padding: 24px; }

    /* ── بطاقات مصادر الأسئلة (Sources) ── */
    .source-item {
        display: flex; justify-content: space-between; align-items: center; padding: 16px;
        background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; margin-bottom: 12px;
        transition: all 0.2s;
    }
    .source-item:hover { border-color: #cbd5e0; background: #fff; }
    .source-icon {
        width: 42px; height: 42px; border-radius: 10px; background: var(--primary-light); color: var(--primary-dark);
        display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 800;
    }
    .source-label { font-size: 14px; font-weight: 800; color: #1e293b; }
    .source-sublbl { font-size: 11px; font-weight: 600; color: #94a3b8; margin-top: 2px; }

    /* ── وسوم الطلاب ── */
    .badge-pass { background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
    .badge-fail { background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }

    /* أزرار التحكم الفاخرة */
    .btn-action-icon {
        width: 32px; height: 32px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0;
        color: #64748b; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; text-decoration: none;
    }
    .btn-action-icon:hover { background: #eff6ff; color: #3b82f6; border-color: #bfdbfe; }
</style>
@endpush

@section('content')

<!-- بنر تفاصيل الاختبار (Hero Section) -->
<div class="exam-hero">
    <div>
        <h2 class="hero-title">{{ $exam->title }}</h2>
        <div class="hero-badges">
            <span class="hero-badge"><i class="bi bi-layers"></i> {{ $exam->grade->name }}</span>
            <span class="hero-badge"><i class="bi bi-calendar3"></i> {{ $exam->academicYear->name }}</span>
            <span class="hero-badge">
                @if($exam->is_active)
                    <i class="bi bi-unlock-fill text-success"></i> مفعل ومتاح للطلاب
                @else
                    <i class="bi bi-lock-fill text-danger"></i> موقوف / مغلق للطلاب
                @endif
            </span>
        </div>
    </div>
    
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="{{ route('admin.exams.index') }}" class="btn" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); color: #fff; border-radius: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px;">
            <i class="bi bi-arrow-right"></i> رجوع للقائمة
        </a>
        
        <a href="{{ route('admin.exams.toggle', $exam) }}" class="btn" style="background: {{ $exam->is_active ? '#ef4444' : '#22c55e' }}; border: none; color: #fff; border-radius: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; box-shadow: 0 6px 20px {{ $exam->is_active ? 'rgba(239, 68, 68, 0.25)' : 'rgba(34, 197, 94, 0.25)' }};">
            <i class="bi bi-toggle-{{ $exam->is_active ? 'on' : 'off' }}"></i>
            {{ $exam->is_active ? 'إيقاف الاختبار' : 'تفعيل الاختبار' }}
        </a>
    </div>
</div>

<!-- شبكة الإحصائيات التحليلية -->
<div class="stats-grid">
    <div class="stat-card total">
        <span class="stat-card-val">{{ $exam->total_marks }}</span>
        <span class="stat-card-lbl">الدرجة الكلية</span>
    </div>
    <div class="stat-card pass">
        <span class="stat-card-val" style="color: #16a34a;">{{ $exam->pass_marks }}</span>
        <span class="stat-card-lbl">درجة النجاح</span>
    </div>
    <div class="stat-card questions">
        <span class="stat-card-val" style="color: #2563eb;">{{ $exam->totalQuestionsCount() }}</span>
        <span class="stat-card-lbl">إجمالي الأسئلة</span>
    </div>
    <div class="stat-card mark">
        <span class="stat-card-val" style="color: #d97706;">{{ number_format($exam->markPerQuestion(), 2) }}</span>
        <span class="stat-card-lbl">درجة كل سؤال</span>
    </div>
</div>

<!-- تخطيط مساحة العمل الثنائية -->
<div class="show-grid">
    
    <!-- العمود الأيمن: مصادر وطرق سحب الأسئلة -->
    <div class="ws-card">
        <div class="ws-card-header">
            <h3 class="ws-card-title"><i class="bi bi-diagram-3 text-success" style="font-size: 20px;"></i> مصادر وتوزيع الأسئلة</h3>
        </div>
        
        <div class="ws-card-body">
            @forelse($exam->subjectConfigs as $config)
                <div class="source-item">
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <div class="source-icon">
                            {{ $config->subject->icon ?? '📖' }}
                        </div>
                        <div>
                            <span class="source-label">{{ $config->subject->name }}</span>
                            <div class="source-sublbl"><i class="bi bi-layers-half"></i> {{ $config->grade->name ?? 'غير محدد' }}</div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <span class="badge" style="border-radius: 8px; font-weight: 700; padding: 6px 12px; background: #f1f5f9; color: #475569; font-size: 12px;"><i class="bi bi-sort-numeric-down"></i> الترتيب: {{ $config->sort_order }}</span>
                        <span class="badge badge-info" style="border-radius: 8px; font-weight: 700; padding: 6px 12px;">{{ $config->question_count }} سؤال</span>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 24px; color: var(--text-muted);">لا توجد مصادر محددة.</div>
            @endforelse
        </div>
    </div>
    
    <!-- العمود الأيسر: الطلاب المتقدمون وسجل النتائج -->
    <div class="ws-card">
        <div class="ws-card-header">
            <h3 class="ws-card-title"><i class="bi bi-people text-primary" style="font-size: 20px;"></i> سجل الطلاب المتقدمين ({{ $exam->studentExams->count() }})</h3>
            <a href="{{ route('admin.results.index') }}" class="btn btn-secondary btn-sm" style="border-radius: 10px; font-weight: 700;">كل النتائج</a>
        </div>
        
        <div class="table-wrapper">
            @if($exam->studentExams->isEmpty())
                <div class="empty-state" style="padding: 48px 24px;">
                    <i class="bi bi-person-x" style="font-size: 40px; color: #cbd5e0; display: block; margin-bottom: 12px;"></i>
                    <h3 style="font-size: 16px; font-weight: 700; color: var(--text-muted);">لم يتقدم أي طالب لأداء هذا الاختبار بعد.</h3>
                </div>
            @else
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>الطالب</th>
                            <th>الدرجة المحرزة</th>
                            <th>النتيجة</th>
                            <th>تاريخ تقديم الإجابة</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exam->studentExams as $se)
                            <tr>
                                <td class="fw-bold" style="color: #1e293b;">{{ $se->student->name }}</td>
                                <td>{{ $se->score }} / {{ $se->total_marks }}</td>
                                <td>
                                    @if($se->isPassed())
                                        <span class="badge-pass"><i class="bi bi-check-circle-fill"></i> ناجح</span>
                                    @else
                                        <span class="badge-fail"><i class="bi bi-x-circle-fill"></i> راسب</span>
                                    @endif
                                </td>
                                <td class="text-muted" style="font-size: 12px;">{{ $se->submitted_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.results.show', $se) }}" class="btn-action-icon" title="معاينة إجابات الطالب بالتفصيل">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>

@endsection
