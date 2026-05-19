@extends('layouts.student')
@section('title', 'من نحن | مدارس القيم الأهلية')

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

    /* ── Hero Section (الواجهة الرئيسية الفاخرة لصفحة من نحن) ── */
    .about-hero {
        position: relative;
        padding: 100px 24px 80px;
        background: radial-gradient(100% 100% at 50% 0%, rgba(118, 181, 27, 0.05) 0%, rgba(248, 250, 252, 0) 100%);
        text-align: center;
    }

    .about-hero-content {
        max-width: 900px;
        margin: 0 auto;
    }

    .about-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(118, 181, 27, 0.08);
        border: 1.5px solid rgba(118, 181, 27, 0.15);
        color: var(--primary-dark);
        padding: 6px 16px;
        border-radius: 30px;
        font-size: 14px;
        font-weight: 850;
        margin-bottom: 24px;
    }

    .about-hero h1 {
        font-size: 44px;
        font-weight: 950;
        margin-bottom: 20px;
        line-height: 1.3;
        color: #0f172a;
    }

    .about-hero h1 span {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .about-hero .lead-text {
        font-size: 18px;
        font-weight: 600;
        color: #475569;
        line-height: 1.8;
        margin-bottom: 32px;
    }

    /* ── قسم الرؤية والرسالة والقيم الجوهرية ── */
    .pillar-section {
        max-width: 1200px;
        margin: 0 auto 100px;
        padding: 0 24px;
    }

    .pillar-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 32px;
    }

    @media (max-width: 992px) {
        .pillar-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }
        .about-hero h1 {
            font-size: 32px;
        }
        .about-hero .lead-text {
            font-size: 16px;
        }
    }

    .pillar-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 24px;
        padding: 36px 30px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.02);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .pillar-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top left, rgba(118, 181, 27, 0.04) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .pillar-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
        border-color: rgba(118, 181, 27, 0.3);
    }

    .pillar-card:hover::before {
        opacity: 1;
    }

    .pillar-icon {
        width: 72px;
        height: 72px;
        background: var(--primary-light);
        color: var(--primary);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin: 0 auto 24px;
        transition: all 0.3s ease;
    }

    .pillar-card:hover .pillar-icon {
        transform: scale(1.1) rotate(-5deg);
        background: var(--primary);
        color: #ffffff;
        box-shadow: var(--shadow-primary);
    }

    .pillar-card h2 {
        font-size: 22px;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 16px;
    }

    .pillar-card p {
        font-size: 14.5px;
        color: #64748b;
        line-height: 1.8;
    }

    /* ── قسم التعريف التفصيلي وتاريخ المدرسة ── */
    .history-section {
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        padding: 100px 24px;
    }

    .history-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        gap: 60px;
    }

    .history-content {
        flex: 1.2;
        text-align: right;
    }

    .history-image-area {
        flex: 0.8;
        position: relative;
    }

    @media (max-width: 992px) {
        .history-container {
            flex-direction: column;
            gap: 40px;
        }
        .history-image-area {
            width: 100%;
            max-width: 480px;
        }
    }

    .history-content h2 {
        font-size: 32px;
        font-weight: 950;
        color: #0f172a;
        margin-bottom: 24px;
    }

    .history-content p {
        font-size: 15.5px;
        color: #475569;
        line-height: 1.9;
        margin-bottom: 20px;
    }

    /* كارت إحصائي فاخر */
    .stats-card-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-top: 36px;
    }

    .about-stat-item {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
    }

    .about-stat-num {
        font-family: 'Inter', sans-serif !important;
        font-size: 28px;
        font-weight: 900;
        color: var(--primary-dark);
        display: block;
        margin-bottom: 4px;
    }

    .about-stat-label {
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
    }

    /* ── الشعار التفاعلي الجميل في الخلفية ── */
    .about-emblem-showcase {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 30px;
        padding: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 15px 35px rgba(0,0,0,0.02);
    }
    
    .about-emblem-showcase img {
        width: 100%;
        max-width: 240px;
        height: auto;
        filter: drop-shadow(0 10px 20px rgba(118, 181, 27, 0.15));
        animation: floatImg 6s ease-in-out infinite;
    }

    @keyframes floatImg {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    /* ── لماذا تختار مدارسنا ── */
    .features-section {
        max-width: 1200px;
        margin: 100px auto;
        padding: 0 24px;
    }

    .section-title-center {
        text-align: center;
        max-width: 700px;
        margin: 0 auto 60px;
    }

    .section-title-center h2 {
        font-size: 32px;
        font-weight: 950;
        color: #0f172a;
        margin-bottom: 12px;
    }

    .section-title-center p {
        font-size: 15px;
        color: #64748b;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 32px;
    }

    @media (max-width: 768px) {
        .features-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }
    }

    .feature-item-box {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 20px;
        padding: 28px;
        display: flex;
        gap: 20px;
        transition: all 0.3s ease;
    }

    .feature-item-box:hover {
        border-color: rgba(118, 181, 27, 0.3);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.04);
        transform: translateY(-4px);
    }

    .feature-icon-wrapper {
        width: 48px;
        height: 48px;
        background: var(--primary-light);
        color: var(--primary-dark);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .feature-item-box:hover .feature-icon-wrapper {
        background: var(--primary);
        color: #ffffff;
    }

    .feature-desc-area h3 {
        font-size: 17px;
        font-weight: 850;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .feature-desc-area p {
        font-size: 13.5px;
        color: #64748b;
        line-height: 1.7;
    }

    /* ── دعوة للتسجيل والقبول ── */
    .about-cta-section {
        max-width: 1200px;
        margin: 0 auto 100px;
        padding: 0 24px;
    }

    .about-cta-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 28px;
        padding: 60px 40px;
        text-align: center;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.06);
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.15);
    }

    .about-cta-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 80% 20%, rgba(118, 181, 27, 0.12) 0%, transparent 60%);
        pointer-events: none;
    }

    .about-cta-card h2 {
        font-size: 32px;
        font-weight: 900;
        margin-bottom: 16px;
        color: #ffffff;
    }

    .about-cta-card p {
        color: #94a3b8;
        font-size: 16px;
        max-width: 650px;
        margin: 0 auto 36px;
        line-height: 1.8;
    }

    .cta-btn-main {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--primary);
        color: #ffffff;
        padding: 16px 36px;
        border-radius: 14px;
        font-size: 16px;
        font-weight: 800;
        text-decoration: none;
        box-shadow: var(--shadow-primary);
        transition: all 0.3s ease;
    }

    .cta-btn-main:hover {
        background: var(--primary-dark);
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(118, 181, 27, 0.4);
    }
</style>
@endpush

@section('content')
<div class="glow-blob" style="top: 15%; right: 5%;"></div>
<div class="glow-blob" style="top: 55%; left: 5%;"></div>

<!-- Hero Area -->
<div class="about-hero">
    <div class="about-hero-content">
        <div class="about-badge">
            <i class="bi bi-info-circle-fill"></i> من نحن
        </div>
        <h1>عن <span>{{ \App\Models\Setting::get('school_name', 'مدارس القيم الأهلية') }}</span></h1>
        <p class="lead-text">
            {{ \App\Models\Setting::get('about_description', 'مؤسسة تعليمية رائدة تكرس جهودها لبناء جيل مسلح بالعلم، ومستمسك بالقيم الأخلاقية، وقادر على الابتكار والتميز والمنافسة محلياً وعالمياً.') }}
        </p>
    </div>
</div>

<!-- Vision, Mission & Values Grid -->
<div class="pillar-section">
    <div class="pillar-grid">
        <!-- الرؤية -->
        <div class="pillar-card">
            <div class="pillar-icon">
                <i class="bi bi-eye-fill"></i>
            </div>
            <h2>رؤيتنا</h2>
            <p>
                {{ \App\Models\Setting::get('about_vision', 'الريادة في تقديم تعليم إبداعي ذي جودة عالية يدمج بين القيم والتميز الأكاديمي، لنكون الخيار الأول في صناعة قادة المستقبل وتنمية مجتمع المعرفة.') }}
            </p>
        </div>

        <!-- الرسالة -->
        <div class="pillar-card">
            <div class="pillar-icon">
                <i class="bi bi-rocket-takeoff-fill"></i>
            </div>
            <h2>رسالتنا</h2>
            <p>
                {{ \App\Models\Setting::get('about_mission', 'توفير بيئة تعليمية ذكية، ورعاية طلابية شاملة تعزز التفكير النقدي والابتكار وتصقل المواهب، من خلال كادر أكاديمي محترف وشراكة مجتمعية فاعلة ومناهج مواكبة.') }}
            </p>
        </div>

        <!-- القيم -->
        <div class="pillar-card">
            <div class="pillar-icon">
                <i class="bi bi-heart-pulse-fill"></i>
            </div>
            <h2>قيمنا الجوهرية</h2>
            <p>
                {{ \App\Models\Setting::get('about_values', 'القيم هي أساس هويتنا، ونلتزم بغرسها وتنميتها في نفوس طلابنا: التميز الأكاديمي، النزاهة والأمانة، الابتكار المستمر، الاحترام المتبادل، والمسؤولية المجتمعية.') }}
            </p>
        </div>
    </div>
</div>

<!-- History and Overview -->
<div class="history-section">
    <div class="history-container">
        <!-- Text -->
        <div class="history-content">
            <h2>شعارنا.. تعليمنا قيم</h2>
            <p style="white-space: pre-line; line-height: 1.9;">{{ \App\Models\Setting::get('about_history', "تأسست مدارس القيم الأهلية لتقدم نموذجاً تعليمياً متكاملاً يتجاوز مجرد تلقين المعلومات إلى بناء الإنسان. نؤمن بأن المعرفة الحقيقية هي تلك التي ترافقها قيم نبيلة تحكم السلوك وتوجه القدرات نحو البناء والعطاء.\n\nنعتمد في مدارسنا على أحدث الوسائل التكنولوجية والمنظومات الرقمية في التعليم وإدارة التقييمات، كما نسعى باستمرار لتطوير مهارات القرن الحادي والعشرين لدى طلابنا عبر المناهج الأكاديمية والأنشطة اللامنهجية الإبداعية.") }}</p>
            
            <!-- إحصاءات -->
            <div class="stats-card-row">
                <div class="about-stat-item">
                    <span class="about-stat-num">{{ \App\Models\Setting::get('about_stat_years', '+15') }}</span>
                    <span class="about-stat-label">عاماً من التميز</span>
                </div>
                <div class="about-stat-item">
                    <span class="about-stat-num">{{ \App\Models\Setting::get('about_stat_graduates', '+2,500') }}</span>
                    <span class="about-stat-label">خريج وخريجة</span>
                </div>
                <div class="about-stat-item">
                    <span class="about-stat-num">{{ \App\Models\Setting::get('about_stat_teachers', '+120') }}</span>
                    <span class="about-stat-label">معلم وإداري متميز</span>
                </div>
            </div>
        </div>

        <!-- Image and visual -->
        <div class="history-image-area">
            <div class="about-emblem-showcase">
                <img src="{{ \App\Models\Setting::get('school_logo') ? asset('storage/' . \App\Models\Setting::get('school_logo')) : asset('images/school_icon.png') }}" alt="شعار مدارس القيم الأهلية">
            </div>
        </div>
    </div>
</div>

<!-- Why Choose Us -->
<div class="features-section">
    <div class="section-title-center">
        <h2>لماذا تختار {{ \App\Models\Setting::get('school_name', 'مدارس القيم الأهلية') }}؟</h2>
        <p>{{ \App\Models\Setting::get('about_features_intro', 'نقدم بيئة ومقومات تعليمية استثنائية تجعلنا الخيار الأمثل لمستقبل أبنائكم الأكاديمي والمهني') }}</p>
    </div>

    <div class="features-grid">
        <!-- Feature 1 -->
        <div class="feature-item-box">
            <div class="feature-icon-wrapper">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="feature-desc-area">
                <h3>{{ \App\Models\Setting::get('about_feature1_title', 'كادر أكاديمي نخبوي') }}</h3>
                <p>{{ \App\Models\Setting::get('about_feature1_desc', 'نخبة من المعلمين والتربويين ذوي الخبرة العالية والمؤهلين لتوجيه الطلاب وتنمية مهارات التفكير العليا والابتكار.') }}</p>
            </div>
        </div>

        <!-- Feature 2 -->
        <div class="feature-item-box">
            <div class="feature-icon-wrapper">
                <i class="bi bi-laptop-fill"></i>
            </div>
            <div class="feature-desc-area">
                <h3>{{ \App\Models\Setting::get('about_feature2_title', 'بيئة تعليمية ذكية') }}</h3>
                <p>{{ \App\Models\Setting::get('about_feature2_desc', 'فصول مجهزة بأحدث التقنيات التفاعلية، ومختبرات علمية وحاسوبية متكاملة، ونظام إلكتروني شامل لإدارة القبول والامتحانات.') }}</p>
            </div>
        </div>

        <!-- Feature 3 -->
        <div class="feature-item-box">
            <div class="feature-icon-wrapper">
                <i class="bi bi-journal-bookmark-fill"></i>
            </div>
            <div class="feature-desc-area">
                <h3>{{ \App\Models\Setting::get('about_feature3_title', 'مناهج ريادية تفاعلية') }}</h3>
                <p>{{ \App\Models\Setting::get('about_feature3_desc', 'نقدم خططاً دراسية متميزة ومساندة تعزز الفهم والتجربة والبحث العملي، وتركز على بناء القدرات العلمية والتحليلية.') }}</p>
            </div>
        </div>

        <!-- Feature 4 -->
        <div class="feature-item-box">
            <div class="feature-icon-wrapper">
                <i class="bi bi-award-fill"></i>
            </div>
            <div class="feature-desc-area">
                <h3>{{ \App\Models\Setting::get('about_feature4_title', 'رعاية ومتابعة شاملة') }}</h3>
                <p>{{ \App\Models\Setting::get('about_feature4_desc', 'رعاية سلوكية وصحية متكاملة، وقنوات اتصال مباشرة ومستمرة مع أولياء الأمور لمتابعة مستويات أداء الطلاب ودعم مسيرتهم.') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Call To Action -->
<div class="about-cta-section">
    <div class="about-cta-card">
        <h2>هل أنت مستعد للانضمام لمدارسنا؟</h2>
        <p>نظام التقديم الإلكتروني وامتحانات القبول مفتوح ونشط حالياً. حدد صفك الدراسي وسجل بياناتك لتبدأ رحلتك الأكاديمية والتربوية المميزة معنا.</p>
        <a href="{{ route('home') }}#grades" class="cta-btn-main">
            <i class="bi bi-rocket-takeoff-fill"></i> استعرض اختبارات القبول المتاحة
        </a>
    </div>
</div>

@endsection
