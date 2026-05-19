@extends('layouts.student')
@section('title', 'تفاصيل نتيجة القبول')

@push('styles')
<style>
    /* ── تأثيرات الحركة والعمق البصري للنتائج ── */
    .result-page {
        max-width: 780px; margin: 48px auto 100px; padding: 0 24px;
        animation: resultIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
    }
    @keyframes resultIn {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .glow-blob {
        position: absolute; width: 350px; height: 350px; border-radius: 50%;
        background: radial-gradient(circle, rgba(118, 181, 27, 0.08) 0%, rgba(255,255,255,0) 70%);
        filter: blur(50px); z-index: -1; pointer-events: none;
    }

    /* ── بطاقة النتيجة البانورامية الكبرى (Hero Result Banner) ── */
    .result-hero {
        border-radius: 28px; padding: 56px 40px; text-align: center; color: #fff;
        margin-bottom: 32px; position: relative; overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    /* 🟢 حالة النجاح المشرقة */
    .result-hero.pass {
        background: linear-gradient(135deg, #10b981 0%, #059669 50%, var(--primary) 100%);
        box-shadow: 0 20px 40px -10px rgba(16, 185, 129, 0.3);
    }
    
    /* 🔴 حالة الإعادة والتعثر القرمزية */
    .result-hero.fail {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, var(--danger) 100%);
        box-shadow: 0 20px 40px -10px rgba(220, 38, 38, 0.3);
    }
    
    .result-hero::before {
        content: ''; position: absolute; inset: 0;
        background: radial-gradient(circle at 50% 20%, rgba(255,255,255,0.2) 0%, transparent 60%);
        pointer-events: none;
    }
    
    .result-hero .content { position: relative; z-index: 2; }
    
    /* الأيقونة النابضة الكبرى */
    .result-hero .icon-badge {
        width: 90px; height: 90px; background: rgba(255, 255, 255, 0.18);
        border: 2px solid rgba(255, 255, 255, 0.3); border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 42px; margin: 0 auto 20px;
        animation: scalePop 1.2s infinite ease-in-out;
    }
    @keyframes scalePop {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.08); }
    }

    .result-hero h2 { font-size: 32px; font-weight: 950; margin-bottom: 8px; letter-spacing: -0.5px; }
    .result-hero .sub { font-size: 16px; opacity: .9; font-weight: 600; max-width: 500px; margin: 0 auto; }
    
    /* حقل عرض الدرجة الدائري المضيء */
    .score-display {
        margin: 30px auto 0; background: rgba(255, 255, 255, 0.1);
        border: 1.5px solid rgba(255, 255, 255, 0.2); width: fit-content;
        padding: 14px 36px; border-radius: 20px;
        display: flex; align-items: baseline; gap: 8px;
    }
    .score-num { font-size: 56px; font-weight: 950; line-height: 1; }
    .score-denom { font-size: 26px; opacity: .8; font-weight: 700; }
    .score-pct { font-size: 16px; background: #ffffff; color: #0f172a; padding: 4px 12px; border-radius: 30px; font-weight: 850; margin-right: 12px; }

    /* ── جدول وجدول تفاصيل البيانات (Grid Info Cards) ── */
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
    
    .info-card {
        background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 20px;
        padding: 24px; transition: all .3s ease; box-shadow: var(--shadow-sm);
    }
    .info-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }
    
    .info-card h4 {
        font-size: 14.5px; font-weight: 900; color: #0f172a; margin-bottom: 18px;
        display: flex; align-items: center; gap: 8px; border-bottom: 1.5px solid #f1f5f9;
        padding-bottom: 12px;
    }
    .info-card h4 i { color: var(--primary); font-size: 18px; }
    
    .info-card table { width: 100%; border-collapse: collapse; }
    .info-card td { padding: 10px 0; font-size: 13.5px; border-bottom: 1px solid #f8fafc; }
    .info-card tr:last-child td { border-bottom: none; }
    .info-card td.label-td { color: #64748b; font-weight: 600; width: 45%; }
    .info-card td.value-td { color: #1e293b; font-weight: 800; text-align: left; }

    /* ── مقياس النجاح والقبول المطور (Threshold Gauge) ── */
    .gauge-wrapper {
        background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 20px;
        padding: 28px; margin-bottom: 36px; box-shadow: var(--shadow-sm);
    }
    .gauge-header { display: flex; justify-content: space-between; font-size: 13px; color: #64748b; font-weight: 800; margin-bottom: 12px; }
    
    .gauge-bar-container {
        height: 18px; background: #e2e8f0; border-radius: 20px; overflow: hidden;
        position: relative; border: 1.5px solid #e2e8f0;
    }
    /* خط النجاح والرسوب المنقط */
    .gauge-pass-marker {
        position: absolute; right: 0; top: 0; bottom: 0;
        width: {{ ($studentExam->pass_marks / $studentExam->total_marks) * 100 }}%;
        background: rgba(245, 158, 11, 0.08); border-left: 2px dashed #f59e0b;
        z-index: 2; pointer-events: none;
    }
    /* مؤشر درجة الطالب الفعلي */
    .gauge-fill {
        height: 100%; width: {{ $studentExam->percentage() }}%;
        border-radius: 20px; transition: width 1.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        z-index: 3; position: relative;
    }
    .gauge-fill.pass { background: linear-gradient(90deg, var(--primary), #10b981); }
    .gauge-fill.fail { background: linear-gradient(90deg, #f87171, var(--danger)); }
    
    .gauge-footer {
        display: flex; justify-content: space-between; align-items: center; margin-top: 14px;
    }
    .gauge-pass-lbl { font-size: 12px; font-weight: 800; color: #d97706; display: flex; align-items: center; gap: 4px; }
    .gauge-score-lbl { font-size: 15px; font-weight: 900; }
    .gauge-score-lbl.pass { color: #10b981; }
    .gauge-score-lbl.fail { color: var(--danger); }

    /* ── إجراءات التنقل والطباعة ── */
    .actions-row { display: flex; gap: 16px; flex-wrap: wrap; }
    
    .action-btn {
        flex: 1; min-width: 180px; padding: 14px 28px; border-radius: 16px;
        font-family: 'Cairo', sans-serif; font-size: 15px; font-weight: 850;
        cursor: pointer; border: none; display: flex; align-items: center;
        justify-content: center; gap: 8px; text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .action-btn.primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #fff; box-shadow: var(--shadow-primary);
    }
    .action-btn.primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(118, 181, 27, 0.35);
    }
    
    .action-btn.secondary {
        background: #ffffff; color: #475569; border: 1.5px solid #e2e8f0;
    }
    .action-btn.secondary:hover {
        background: #f8fafc; border-color: #cbd5e1; color: #1e293b;
        transform: translateY(-3px);
    }

    @media (max-width: 580px) {
        .info-grid { grid-template-columns: 1fr; gap: 16px; }
        .result-hero { padding: 40px 20px; }
        .score-display { padding: 10px 24px; }
    }
    
    /* تنسيق للطباعة */
    @media print {
        body { background: #fff; color: #000; }
        .site-header, .site-footer, .actions-row, .breadcrumb-row { display: none !important; }
        .result-page { margin: 0; padding: 0; max-width: 100%; }
        .result-hero { background: #f8fafc !important; color: #000 !important; border: 1px solid #ccc !important; box-shadow: none !important; }
        .result-hero .icon-badge { border-color: #ccc !important; color: #000 !important; }
        .score-pct { border: 1px solid #000 !important; }
        .info-card { box-shadow: none !important; border: 1px solid #ccc !important; }
        .gauge-wrapper { border: 1px solid #ccc !important; }
    }
</style>
@endpush

@section('content')
<div class="glow-blob" style="top: 15%; right: 10%;"></div>
<div class="glow-blob" style="top: 60%; left: 10%;"></div>

<div class="result-page">
    
    <!-- كارت الإنجاز البانورامي الكروي (Result Hero Banner) -->
    <div class="result-hero {{ $studentExam->isPassed() ? 'pass' : 'fail' }}">
        <div class="content">
            @if($studentExam->isPassed())
                <div class="icon-badge">🎉</div>
                <h2>تهانينا، لقد تم قبولك!</h2>
                <p class="sub">رائع جداً! لقد نجحت واجتزت اختبار القبول بجدارة وتفوّق أكاديمي.</p>
            @else
                <div class="icon-badge">💪</div>
                <h2>حظ أوفر في المرة القادمة</h2>
                <p class="sub">مجهود رائع! لم توفّق في تخطي عتبة النجاح المطلوبة هذه المرة، نأمل لك التوفيق في المرة القادمة.</p>
            @endif

            <div class="score-display">
                <span class="score-num">{{ $studentExam->score }}</span>
                <span class="score-denom">/ {{ $studentExam->total_marks }}</span>
                <span class="score-pct">{{ $studentExam->percentage() }}%</span>
            </div>
        </div>
    </div>

    <!-- شبكة التفاصيل الرقمية للبيانات -->
    <div class="info-grid">
        <!-- كارت تفاصيل الطالب -->
        <div class="info-card">
            <h4><i class="bi bi-person-badge-fill"></i> بيانات الطالب المتقدم</h4>
            <table>
                <tr>
                    <td class="label-td">الاسم بالكامل</td>
                    <td class="value-td">{{ $studentExam->student->name }}</td>
                </tr>
                <tr>
                    <td class="label-td">الصف المستهدف</td>
                    <td class="value-td">{{ $studentExam->student->applyingGrade->name }}</td>
                </tr>
                <tr>
                    <td class="label-td">المدرسة السابقة</td>
                    <td class="value-td">{{ $studentExam->student->previous_school }}</td>
                </tr>
                <tr>
                    <td class="label-td">معدل الصف السابق</td>
                    <td class="value-td">{{ $studentExam->student->last_grade_average }}%</td>
                </tr>
            </table>
        </div>

        <!-- كارت نتائج الاختبار والتقييم -->
        <div class="info-card">
            <h4><i class="bi bi-award-fill"></i> تفاصيل درجات التقييم</h4>
            <table>
                <tr>
                    <td class="label-td">درجة القبول المحرزة</td>
                    <td class="value-td {{ $studentExam->isPassed() ? 'text-success' : 'text-danger' }}">
                        {{ $studentExam->score }} / {{ $studentExam->total_marks }}
                    </td>
                </tr>
                <tr>
                    <td class="label-td">الحد الأدنى للقبول</td>
                    <td class="value-td" style="color: #d97706;">{{ $studentExam->pass_marks }} درجة</td>
                </tr>
                <tr>
                    <td class="label-td">الإجابات الصحيحة</td>
                    <td class="value-td text-success">
                        <i class="bi bi-check-circle-fill"></i>
                        {{ $studentExam->correct_answers }} من {{ $studentExam->total_questions }}
                    </td>
                </tr>
                <tr>
                    <td class="label-td">الإجابات الخاطئة / المتروكة</td>
                    <td class="value-td text-danger">
                        <i class="bi bi-x-circle-fill"></i>
                        {{ $studentExam->total_questions - $studentExam->correct_answers }} أسئلة
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- مقياس النجاح التفاعلي التخطيطي (Threshold Gauge) -->
    <div class="gauge-wrapper">
        <div class="gauge-header">
            <span>مقياس أداء التقديم الأكاديمي</span>
            <span>حد النجاح المطلوب: {{ $studentExam->pass_marks }} / {{ $studentExam->total_marks }}</span>
        </div>
        
        <div class="gauge-bar-container">
            <!-- خط حد النجاح -->
            <div class="gauge-pass-marker"></div>
            <!-- درجة الطالب -->
            <div class="gauge-fill {{ $studentExam->isPassed() ? 'pass' : 'fail' }}"></div>
        </div>
        
        <div class="gauge-footer">
            <span class="gauge-pass-lbl">
                <i class="bi bi-info-circle-fill"></i>
                الخط المتقطع يمثّل الحد الأدنى للقبول بالمدارس
            </span>
            <span class="gauge-score-lbl {{ $studentExam->isPassed() ? 'pass' : 'fail' }}">
                النسبة المكتسبة: {{ $studentExam->percentage() }}%
            </span>
        </div>
    </div>

    <!-- الإجراءات المتاحة للطباعة والرجوع -->
    <div class="actions-row">
        <a href="{{ route('home') }}" class="action-btn secondary">
            <i class="bi bi-house-door-fill"></i> العودة للرئيسية
        </a>
        <button type="button" onclick="window.print()" class="action-btn primary">
            <i class="bi bi-printer-fill"></i> طباعة وثيقة القبول
        </button>
    </div>
</div>
@endsection
