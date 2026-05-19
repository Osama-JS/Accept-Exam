@extends('layouts.student')
@section('title', 'مدارس القيم الأهلية - نظام القبول والامتحانات الإلكتروني')

@push('styles')
<style>
    /* ── الخلفية العامة والأنماط التجميلية للشبكة ── */
    body {
        background: #f8fafc;
        overflow-x: hidden;
    }
    
    .glow-blob {
        position: absolute; width: 400px; height: 400px; border-radius: 50%;
        background: radial-gradient(circle, rgba(118, 181, 27, 0.08) 0%, rgba(255,255,255,0) 70%);
        filter: blur(40px); z-index: -1; pointer-events: none;
    }

    /* ── Hero Section (الواجهة الرئيسية الفاخرة) ── */
    .hero-container-wrapper {
        position: relative;
        padding: 80px 0 100px;
        background: radial-gradient(100% 100% at 50% 0%, rgba(118, 181, 27, 0.04) 0%, rgba(248, 250, 252, 0) 100%);
    }

    .hero {
        display: flex; align-items: center; justify-content: space-between;
        gap: 60px; max-width: 1400px; margin: 0 auto; padding: 0 40px;
    }

    .hero-content {
        flex: 1; display: flex; flex-direction: column; justify-content: center;
        text-align: right; z-index: 5;
    }

    .hero-badge-active {
        display: inline-flex; align-items: center; gap: 8px; width: fit-content;
        background: rgba(16, 185, 129, 0.08); border: 1.5px solid rgba(16, 185, 129, 0.15);
        color: #10b981; padding: 6px 14px; border-radius: 30px; font-size: 13px; font-weight: 850;
        margin-bottom: 24px; animation: pulseGlow 2s infinite ease-in-out;
    }
    
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 0 0px rgba(16, 185, 129, 0.2); }
        50% { box-shadow: 0 0 12px 4px rgba(16, 185, 129, 0.1); }
    }

    .hero-content h1 {
        font-size: 46px; font-weight: 950; margin-bottom: 16px; line-height: 1.25; color: #0f172a;
    }
    
    .hero-content h1 span {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }

    .hero-content .subtitle {
        font-size: 26px; font-weight: 800; margin-bottom: 16px; color: #334155;
    }

    .hero-content .slogan {
        font-size: 16px; font-weight: 600; color: #64748b; margin-bottom: 40px;
        line-height: 1.7; max-width: 580px;
    }

    .hero-actions-row {
        display: flex; gap: 16px; flex-wrap: wrap;
    }

    .btn-hero-primary {
        display: inline-flex; align-items: center; gap: 10px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #ffffff; padding: 16px 36px; border-radius: 16px;
        font-family: 'Cairo', sans-serif; font-size: 16px; font-weight: 850;
        text-decoration: none; cursor: pointer; transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--shadow-primary);
    }
    .btn-hero-primary:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(118, 181, 27, 0.4);
    }

    .btn-hero-secondary {
        display: inline-flex; align-items: center; gap: 10px;
        background: #ffffff; border: 2px solid #e2e8f0;
        color: #475569; padding: 16px 32px; border-radius: 16px;
        font-family: 'Cairo', sans-serif; font-size: 16px; font-weight: 850;
        text-decoration: none; cursor: pointer; transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-hero-secondary:hover {
        background: #f8fafc; border-color: #cbd5e1; color: #1e293b;
        transform: translateY(-4px);
    }

    /* ── Live Mock Dashboard Preview (الواجهة الرقمية التفاعلية) ── */
    .hero-image-container {
        flex: 1.1; display: flex; justify-content: center; align-items: center;
        position: relative; z-index: 5;
    }

    .mock-portal-card {
        background: #ffffff; width: 100%; max-width: 520px; border-radius: 24px;
        border: 1px solid rgba(226, 232, 240, 0.8); padding: 24px;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.08);
        position: relative; overflow: hidden;
        transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .mock-portal-card:hover {
        transform: translateY(-8px) scale(1.01);
    }

    /* رأس لوحة المعاينة */
    .mp-header {
        display: flex; justify-content: space-between; align-items: center;
        border-bottom: 1.5px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 20px;
    }
    .mp-dots { display: flex; gap: 6px; }
    .mp-dot { width: 10px; height: 10px; border-radius: 50%; }
    .mp-dot-red { background: #ef4444; }
    .mp-dot-yellow { background: #f59e0b; }
    .mp-dot-green { background: #10b981; }
    
    .mp-title {
        font-size: 13px; font-weight: 850; color: #64748b; background: #f1f5f9;
        padding: 4px 12px; border-radius: 20px; font-family: 'Inter', sans-serif !important;
    }

    /* محتويات لوحة المعاينة */
    .mp-body { display: flex; flex-direction: column; gap: 14px; }
    
    .mp-welcome-capsule {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 16px; padding: 18px; color: #fff;
        display: flex; align-items: center; justify-content: space-between;
    }
    .mpw-name { font-size: 15px; font-weight: 800; display: block; margin-bottom: 4px; }
    .mpw-desc { font-size: 11.5px; opacity: 0.75; }

    .mp-exam-row {
        background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px;
        padding: 14px 16px; display: flex; align-items: center; justify-content: space-between;
        transition: border-color 0.2s;
    }
    .mp-exam-row.active { border-color: var(--primary-dark); background: rgba(118,181,27,0.02); }
    
    .mpe-meta { display: flex; align-items: center; gap: 12px; }
    .mpe-icon {
        width: 38px; height: 38px; border-radius: 10px; background: rgba(118, 181, 27, 0.1);
        color: var(--primary); display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }
    .mpe-name { font-size: 13.5px; font-weight: 800; color: #1e293b; }
    .mpe-duration { font-size: 11px; color: #94a3b8; margin-top: 2px; }

    .mpe-btn {
        background: var(--primary); color: #fff; border: none; font-size: 12px;
        font-weight: 800; padding: 7px 14px; border-radius: 8px; cursor: pointer;
    }
    .mpe-btn-done {
        background: rgba(16, 185, 129, 0.1); color: #10b981; font-weight: 850;
        font-size: 12px; padding: 6px 12px; border-radius: 8px; display: inline-flex; align-items: center; gap: 4px;
    }

    @media (max-width: 992px) {
        .hero { flex-direction: column-reverse; text-align: center; gap: 40px; padding: 0 20px; }
        .hero-content { align-items: center; text-align: center; }
        .hero-content h1 { font-size: 34px; }
        .hero-content .subtitle { font-size: 22px; }
        .hero-content .slogan { font-size: 14.5px; margin-bottom: 28px; }
        .hero-image-container { width: 100%; }
        .mock-portal-card { max-width: 460px; }
        .hero-actions-row { justify-content: center; }
    }

    /* ── Steps Section (خريطة التقديم الاحترافية) ── */
    .steps-section {
        background: #ffffff; padding: 100px 24px;
        border-top: 1px solid #e2e8f0; position: relative;
    }
    
    .section-header {
        text-align: center; max-width: 600px; margin: 0 auto 60px;
    }
    .section-header h2 { font-size: 32px; font-weight: 950; color: #0f172a; margin-bottom: 12px; }
    .section-header p { font-size: 14.5px; color: #64748b; }

    .steps-container {
        position: relative; max-width: 1200px; margin: 0 auto;
    }
    
    /* خط الربط التفاعلي */
    .steps-line {
        position: absolute; top: 50px; left: 10%; right: 10%; height: 3px;
        background: repeating-linear-gradient(to right, #cbd5e1 0px, #cbd5e1 8px, transparent 8px, transparent 16px);
        z-index: 1;
    }
    
    .steps { display: flex; justify-content: space-between; position: relative; z-index: 2; gap: 20px; }
    
    .step {
        text-align: center; flex: 1; padding: 0 10px; transition: all 0.3s ease;
    }
    
    .step-circle {
        width: 100px; height: 100px; background: #ffffff;
        border: 4px solid #e2e8f0; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 36px; color: #5f9416; margin: 0 auto 24px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.02);
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
    }
    .step-badge {
        position: absolute; top: -4px; right: -4px; width: 28px; height: 28px;
        background: var(--primary); color: #fff; border-radius: 50%;
        font-size: 12px; font-weight: 900; display: flex; align-items: center; justify-content: center;
        border: 3px solid #fff; font-family: 'Inter', sans-serif !important;
    }

    .step:hover .step-circle {
        transform: translateY(-8px) scale(1.05);
        border-color: var(--primary);
        box-shadow: 0 15px 30px rgba(118, 181, 27, 0.2);
    }
    
    .step h3 { font-size: 18px; font-weight: 850; margin-bottom: 12px; color: #1e293b; }
    .step p { font-size: 13.5px; color: #64748b; line-height: 1.6; max-width: 240px; margin: 0 auto; }
    
    @media (max-width: 868px) {
        .steps-line { display: none; }
        .steps { flex-direction: column; gap: 40px; }
        .step p { max-width: 100%; }
    }

    /* ── Grades Section (شبكة الصفوف الدراسية الفاخرة) ── */
    .grades-section {
        max-width: 1200px; margin: 80px auto; padding: 0 24px;
    }
    
    .grades-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;
    }
    
    .grade-card {
        background: #ffffff; border: 1.5px solid #e2e8f0;
        border-radius: 20px; padding: 28px;
        text-decoration: none; color: var(--text-main);
        display: flex; flex-direction: column; gap: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative; overflow: hidden;
    }
    .grade-card::before {
        content: ''; position: absolute; top: 0; right: 0;
        width: 100%; height: 4px; background: var(--primary);
        transform: scaleX(0); transform-origin: right; transition: transform .3s;
    }
    .grade-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 25px -5px rgba(118, 181, 27, 0.08);
        border-color: rgba(118, 181, 27, 0.3);
    }
    .grade-card:hover::before { transform: scaleX(1); }
    
    .grade-header-row { display: flex; align-items: center; justify-content: space-between; }
    
    .grade-icon {
        width: 52px; height: 52px; background: var(--primary-light);
        border-radius: 14px; display: flex; align-items: center; justify-content: center;
        font-size: 24px; color: var(--primary);
        transition: transform .3s;
    }
    .grade-card:hover .grade-icon { transform: scale(1.1) rotate(-4deg); }
    
    .grade-badge-count {
        background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a;
        font-size: 11.5px; font-weight: 850; padding: 4px 10px; border-radius: 30px;
    }
    
    .grade-name { font-size: 17px; font-weight: 900; color: #1e293b; margin-top: 4px; }
    
    .grade-meta {
        font-size: 13px; color: #64748b; display: flex; align-items: center; gap: 6px;
        background: #f8fafc; padding: 10px; border-radius: 10px;
    }

    .grade-action-btn-row {
        display: flex; align-items: center; justify-content: space-between;
        margin-top: 8px; border-top: 1px solid #f1f5f9; padding-top: 14px;
    }
    .grade-arrow {
        width: 36px; height: 36px; background: #f1f5f9; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: var(--primary); transition: all .3s;
    }
    .grade-card:hover .grade-arrow { background: var(--primary); color: #fff; transform: translateX(-4px); }

    /* ── FAQ Section (قسم الأسئلة الشائعة اللذيذ) ── */
    .faq-section {
        max-width: 800px; margin: 100px auto; padding: 0 24px;
    }
    .faq-wrapper { display: flex; flex-direction: column; gap: 16px; }
    .faq-item {
        background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 16px;
        overflow: hidden; transition: all 0.3s;
    }
    .faq-item.active { border-color: var(--primary); box-shadow: 0 10px 20px rgba(118, 181, 27, 0.04); }
    
    .faq-question {
        padding: 20px 24px; display: flex; justify-content: space-between; align-items: center;
        font-weight: 850; font-size: 15.5px; color: #1e293b; cursor: pointer; user-select: none;
    }
    .faq-answer {
        max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out;
        padding: 0 24px; color: #64748b; font-size: 14px; line-height: 1.6;
    }
    .faq-item.active .faq-answer { padding-bottom: 20px; }
    .faq-chevron { transition: transform 0.3s; color: #94a3b8; }
    .faq-item.active .faq-chevron { transform: rotate(180deg); color: var(--primary); }
</style>
@endpush

@section('content')
<div class="glow-blob" style="top: 10%; right: 5%;"></div>
<div class="glow-blob" style="top: 50%; left: 5%;"></div>

<!-- Hero Area -->
<div class="hero-container-wrapper" id="home">
    <div class="hero">
        
        <!-- Live Mock Portal Preview (لوحة المعاينة التفاعلية) -->
        <div class="hero-image-container">
            <div class="mock-portal-card">
                <div class="mp-header">
                    <div class="mp-dots">
                        <div class="mp-dot mp-dot-red"></div>
                        <div class="mp-dot mp-dot-yellow"></div>
                        <div class="mp-dot mp-dot-green"></div>
                    </div>
                    <span class="mp-title">Admission Portal v2.0</span>
                </div>
                
                <div class="mp-body">
                    <div class="mp-welcome-capsule">
                        <div>
                            <span class="mpw-name">أهلاً بك في مدرسة القيم الأهلية 🎓</span>
                            <span class="mpw-desc">يرجى اختيار أحد الاختبارات النشطة بالأسفل</span>
                        </div>
                        <i class="bi bi-person-badge" style="font-size: 26px; opacity: 0.85;"></i>
                    </div>
                    
                    <div class="mp-exam-row active">
                        <div class="mpe-meta">
                            <div class="mpe-icon"><i class="bi bi-translate"></i></div>
                            <div>
                                <span class="mpe-name">اختبار اللغة العربية للقبول</span>
                                <div class="mpe-duration"><i class="bi bi-clock"></i> 30 دقيقة | 15 سؤالاً</div>
                            </div>
                        </div>
                        <button type="button" class="mpe-btn">بدء التقديم</button>
                    </div>

                    <div class="mp-exam-row">
                        <div class="mpe-meta">
                            <div class="mpe-icon" style="background: rgba(195, 14, 20, 0.08); color: var(--danger);"><i class="bi bi-calculator"></i></div>
                            <div>
                                <span class="mpe-name">اختبار الرياضيات والذكاء</span>
                                <div class="mpe-duration"><i class="bi bi-clock"></i> 45 دقيقة | 20 سؤالاً</div>
                            </div>
                        </div>
                        <span class="mpe-btn-done"><i class="bi bi-check-circle-fill"></i> اكتمل بنجاح</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero Content Text Area -->
        <div class="hero-content">
            <div class="hero-badge-active">
                <i class="bi bi-circle-fill" style="font-size: 8px;"></i>
                بوابة التقديم والقبول نشطة حالياً
            </div>
            <h1>بوابتك نحو <span>التميز الأكاديمي</span> والتعليم الريادي المستقبلي</h1>
            <div class="subtitle">نظام القبول والامتحانات الإلكتروني الذكي</div>
            <p class="slogan">
                تلتزم مدارس القيم الأهلية بتقديم أرقى المستويات التعليمية. تتيح لكم هذه البوابة التفاعلية المتطورة تقديم طلبات الالتحاق وإجراء امتحانات القبول الرقمية بشكل مؤمن وتفاعلي وفي وقت قياسي.
            </p>
            <div class="hero-actions-row">
                <a href="#grades" class="btn-hero-primary">
                    <i class="bi bi-rocket-takeoff-fill"></i> ابدأ خطوات التسجيل والقبول
                </a>
                <a href="#about" class="btn-hero-secondary">
                    <i class="bi bi-journal-text"></i> استكشف معايير القبول
                </a>
            </div>
        </div>

    </div>
</div>

<!-- Steps Section (خريطة خطوات التقديم الحقيقية) -->
<div class="steps-section" id="about">
    <div class="section-header">
        <h2>خطوات رحلة القبول الإلكتروني</h2>
        <p>قمنا بتبسيط وتأمين خطوات التقديم لتضمن مقعدك الدراسي في بضع دقائق ومن أي جهاز</p>
    </div>
    
    <div class="steps-container">
        <div class="steps-line"></div>
        <div class="steps">
            <!-- Step 1 -->
            <div class="step">
                <div class="step-circle">
                    🎓
                    <span class="step-badge">1</span>
                </div>
                <h3>1. اختر الصف الدراسي</h3>
                <p>تصفح الصفوف الأكاديمية النشطة بالأسفل، وحدد الصف الدراسي الذي ترغب في التسجيل والالتحاق به.</p>
            </div>
            <!-- Step 2 -->
            <div class="step">
                <div class="step-circle">
                    📋
                    <span class="step-badge">2</span>
                </div>
                <h3>2. سجل بياناتك الأساسية</h3>
                <p>أدخل اسم الطالب رباعياً، ورقم هاتف ولي الأمر لتأمين طلب الالتحاق وإثبات الحضور والجلسة.</p>
            </div>
            <!-- Step 3 -->
            <div class="step">
                <div class="step-circle">
                    ⏱️
                    <span class="step-badge">3</span>
                </div>
                <h3>3. أدّ اختبار القبول</h3>
                <p>ابدأ الإجابة عن الأسئلة التفاعلية المتنوعة بكل سهولة وبشكل مؤمن داخل الوقت الزمني المخصص.</p>
            </div>
            <!-- Step 4 -->
            <div class="step">
                <div class="step-circle">
                    🏆
                    <span class="step-badge">4</span>
                </div>
                <h3>4. احصل على نتيجتك</h3>
                <p>فور الانتهاء من تأدية الامتحان، يصدر النظام تقرير أداء فوري ومؤشرات النجاح والقبول للطلب.</p>
            </div>
        </div>
    </div>
</div>

<!-- Grades Grid Area -->
<div class="grades-section" id="grades">
    <div class="section-header">
        <h2>الصفوف الدراسية المتاحة للقبول</h2>
        <p>يرجى اختيار الصف الأكاديمي المستهدف لاستكشاف امتحانات القبول النشطة والمتاحة حالياً</p>
    </div>

    @if($grades->isEmpty())
        <div style="text-align:center; padding:60px 20px; background:#fff; border-radius:24px; border: 1.5px dashed #cbd5e0; color:var(--text-muted)">
            <i class="bi bi-clipboard-x" style="font-size:48px; color:#cbd5e0; display:block; margin-bottom:16px"></i>
            <h3 style="font-weight: 850; color: #1e293b;">لا توجد اختبارات متاحة حالياً</h3>
            <p style="font-size: 14px; margin-top: 6px;">يرجى مراجعة إدارة التسجيل والقبول بالمدارس لتفعيل الامتحانات قريباً.</p>
        </div>
    @else
        <div class="grades-grid">
            @foreach($grades as $grade)
            <a href="{{ route('student.exams', $grade) }}" class="grade-card">
                <div class="grade-header-row">
                    <div class="grade-icon"><i class="bi bi-mortarboard-fill"></i></div>
                    <span class="grade-badge-count">{{ $grade->exams_count }} {{ $grade->exams_count == 1 ? 'اختبار متاح' : 'اختبارات متاحة' }}</span>
                </div>
                <div>
                    <div class="grade-name">{{ $grade->name }}</div>
                </div>
                <div class="grade-meta">
                    <i class="bi bi-shield-check" style="color: var(--primary); font-size: 15px;"></i>
                    <span>امتحان قبول رقمي معتمد وموقوت</span>
                </div>
                <div class="grade-action-btn-row">
                    <span style="font-size:13.5px; font-weight:800; color: #64748b;">استعراض الاختبارات</span>
                    <div class="grade-arrow"><i class="bi bi-arrow-left"></i></div>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>

<!-- FAQ Section -->
<div class="faq-section" id="programs">
    <div class="section-header">
        <h2>الأسئلة الشائعة حول القبول</h2>
        <p>كل ما تود معرفته عن امتحانات القبول الإلكترونية بمدارس القيم الأهلية</p>
    </div>

    <div class="faq-wrapper">
        <div class="faq-item">
            <div class="faq-question">
                <span>ما هي مدة الاختبار المحددة لقبول الطلاب؟</span>
                <i class="bi bi-chevron-down faq-chevron"></i>
            </div>
            <div class="faq-answer">
                تختلف مدة الاختبار حسب الصف الدراسي، وتتراوح عادةً بين 30 دقيقة إلى 60 دقيقة. يعرض النظام مؤقتاً تنازلياً دقيقاً في أعلى شاشة الامتحان لتنبيه الطالب.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>هل تظهر نتيجة الاختبار للطالب فور الانتهاء؟</span>
                <i class="bi bi-chevron-down faq-chevron"></i>
            </div>
            <div class="faq-answer">
                نعم، تظهر النتيجة وتفاصيل الأداء التقييمي للطالب بشكل فوري وتلقائي بمجرد النقر على زر "تسليم الإجابات"، مع إمكانية طباعة التقرير أو الاحتفاظ بالرابط.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>ماذا يحدث إذا انقطع الاتصال بالإنترنت أثناء تأدية الامتحان؟</span>
                <i class="bi bi-chevron-down faq-chevron"></i>
            </div>
            <div class="faq-answer">
                يقوم النظام بحفظ آخر إجابات مدخلة للطالب بشكل تلقائي. وفي حال انقطاع الخدمة، يرجى إعادة تحميل الصفحة بعد التحقق من الشبكة لمواصلة حل الامتحان من حيث توقفت.
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ── محرك الأسئلة الشائعة الأكورديون ──
        const faqItems = document.querySelectorAll('.faq-item');
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            const answer = item.querySelector('.faq-answer');
            
            question.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                
                // إغلاق المفتوح سابقاً
                faqItems.forEach(other => {
                    other.classList.remove('active');
                    other.querySelector('.faq-answer').style.maxHeight = '0';
                });
                
                if (!isActive) {
                    item.classList.add('active');
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                }
            });
        });
    });
</script>
@endsection
