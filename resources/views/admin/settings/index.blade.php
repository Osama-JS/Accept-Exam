@extends('layouts.admin')

@section('title', 'إعدادات النظام')
@section('page-title', 'إعدادات النظام')

@section('breadcrumb')
    <span style="color: var(--text-main); font-weight: 700;">الإعدادات العامة للتحكم</span>
@endsection

@push('styles')
<style>
    /* ── تخطيط مساحة عمل الإعدادات (Settings Workspace) ── */
    .settings-workspace {
        display: grid; grid-template-columns: 280px 1fr; gap: 28px; align-items: start; margin-bottom: 50px;
    }
    @media (max-width: 992px) {
        .settings-workspace { grid-template-columns: 1fr; }
    }
    
    /* ── القائمة الجانبية للتبويبات (Settings Sidebar) ── */
    .settings-sidebar {
        background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 16px;
        display: flex; flex-direction: column; gap: 6px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);
    }
    .settings-tab-btn {
        display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-radius: 12px;
        font-size: 14px; font-weight: 750; color: #475569; background: transparent; border: none;
        cursor: pointer; text-align: right; width: 100%; transition: all 0.2s; outline: none;
    }
    .settings-tab-btn i { font-size: 20px; color: #94a3b8; transition: color 0.2s; }
    .settings-tab-btn:hover { background: #f8fafc; color: var(--primary); }
    .settings-tab-btn:hover i { color: var(--primary); }
    
    .settings-tab-btn.active { background: var(--primary-light); color: var(--primary-dark); }
    .settings-tab-btn.active i { color: var(--primary-dark); }
    
    /* ── كروت التبويبات (Settings Cards) ── */
    .settings-card {
        display: none; background: #fff; border-radius: 24px; border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.01), 0 8px 10px -6px rgba(0,0,0,0.01); overflow: hidden;
    }
    .settings-card.active { display: block; animation: fadeInTab 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    
    @keyframes fadeInTab {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .sc-header {
        padding: 24px 32px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 12px;
    }
    .sc-title { font-size: 17px; font-weight: 850; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px; }
    .sc-body { padding: 32px; }

    /* ── تخطيط داخلي ذو عمودين للمعاينة الحية ── */
    .sc-split-grid {
        display: grid; grid-template-columns: 1.3fr 1fr; gap: 32px; align-items: start;
    }
    @media (max-width: 1200px) {
        .sc-split-grid { grid-template-columns: 1fr; }
    }

    /* ── تصميم حقول الإدخال ذات الأيقونات ── */
    .input-icon-group {
        position: relative; margin-bottom: 24px;
    }
    .input-icon-group label {
        font-size: 13.5px; font-weight: 800; color: #475569; margin-bottom: 8px; display: block;
    }
    .input-icon-wrapper { position: relative; }
    .input-icon-wrapper i {
        position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
        color: #94a3b8; font-size: 18px; pointer-events: none; transition: color 0.2s; z-index: 10;
    }
    .input-icon-wrapper input, .input-icon-wrapper textarea {
        width: 100%; padding: 14px 18px 14px 48px; border-radius: 14px; border: 1.5px solid #cbd5e1;
        font-size: 14px; font-weight: 600; color: #1e293b; background: #fff; transition: all 0.2s;
        outline: none;
    }
    .input-icon-wrapper input:focus, .input-icon-wrapper textarea:focus {
        border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-light);
    }
    .input-icon-group:focus-within i { color: var(--primary); }

    /* ── بطاقات الخيارات التفاعلية للتحكم بالراديو (Toggle Cards) ── */
    .toggle-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;
    }
    @media (max-width: 576px) {
        .toggle-grid { grid-template-columns: 1fr; }
    }
    
    .toggle-card {
        border: 2px solid #e2e8f0; border-radius: 16px; padding: 22px;
        display: flex; gap: 14px; align-items: flex-start; cursor: pointer; transition: all 0.2s;
        position: relative;
    }
    .toggle-card input[type="radio"] {
        width: 20px; height: 20px; accent-color: var(--primary); margin-top: 3px; flex-shrink: 0;
    }
    .toggle-card-content { display: flex; flex-direction: column; gap: 4px; }
    .toggle-card-title { font-size: 14px; font-weight: 850; color: #1e293b; }
    .toggle-card-desc { font-size: 11.5px; font-weight: 600; color: #64748b; line-height: 1.4; }
    
    .toggle-card:hover { border-color: #cbd5e1; transform: translateY(-1px); }
    
    /* الحالات المحددة للراديو */
    .toggle-card.active-state { border-color: #10b981; background: #f0fdf4; }
    .toggle-card.danger-state { border-color: #ef4444; background: #fff5f5; }
    .toggle-card.primary-state { border-color: var(--primary); background: var(--primary-light); }

    /* ── لوحة المعاينة الحية لبوابة الطالب (Live Portal Preview) ── */
    .live-preview-panel {
        background: #0f172a; border-radius: 20px; padding: 28px; color: #fff;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.25); position: relative; overflow: hidden;
    }
    .live-preview-panel::after {
        content: '\F22A'; font-family: 'bootstrap-icons'; position: absolute;
        left: -20px; bottom: -30px; font-size: 120px; opacity: 0.04; pointer-events: none;
    }
    .preview-header {
        font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--primary);
        letter-spacing: 1px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;
    }
    .preview-pulse-dot {
        width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block;
        animation: pulseLive 1.5s infinite;
    }
    @keyframes pulseLive {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    
    .mock-login-card {
        background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
        border-radius: 18px; padding: 24px; text-align: center; backdrop-filter: blur(4px);
    }
</style>
@endpush

@section('content')

<div class="row">
    <div class="col-12">
        
        <!-- نموذج الإعدادات الرئيسي الشامل لجميع التبويبات -->
        <form action="{{ route('admin.settings.update') }}" method="POST" id="systemSettingsForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="settings-workspace">
                
                <!-- أولاً: القائمة الجانبية للتبويبات -->
                <div class="settings-sidebar">
                    <button type="button" class="settings-tab-btn active" onclick="switchSettingsTab(event, 'tab-general')">
                        <i class="bi bi-gear-fill"></i> الإعدادات العامة
                    </button>
                    <button type="button" class="settings-tab-btn" onclick="switchSettingsTab(event, 'tab-control')">
                        <i class="bi bi-shield-fill-check"></i> التحكم في النظام
                    </button>
                    <button type="button" class="settings-tab-btn" onclick="switchSettingsTab(event, 'tab-appearance')">
                        <i class="bi bi-palette-fill"></i> الهوية والمظهر
                    </button>
                    <button type="button" class="settings-tab-btn" onclick="switchSettingsTab(event, 'tab-about')">
                        <i class="bi bi-info-circle-fill"></i> صفحة من نحن
                    </button>
                    <button type="button" class="settings-tab-btn" onclick="switchSettingsTab(event, 'tab-steps')">
                        <i class="bi bi-list-ol"></i> خطوات رحلة القبول
                    </button>
                    <button type="button" class="settings-tab-btn" onclick="switchSettingsTab(event, 'tab-faq')">
                        <i class="bi bi-question-circle-fill"></i> الأسئلة الشائعة
                    </button>
                </div>

                <!-- ثانياً: مساحة كروت الإعدادات التفاعلية -->
                <div class="settings-content">
                    
                    <!-- 1. كرت الإعدادات العامة -->
                    <div class="settings-card active" id="tab-general">
                        <div class="sc-header">
                            <h3 class="sc-title"><i class="bi bi-gear-fill text-primary" style="font-size: 20px;"></i> الإعدادات العامة للمؤسسة</h3>
                        </div>
                        <div class="sc-body">
                            <div class="sc-split-grid">
                                
                                <!-- حقول الإدخال -->
                                <div>
                                    <div class="input-icon-group">
                                        <label for="school_name">اسم المدرسة / المؤسسة التعليمية</label>
                                        <div class="input-icon-wrapper">
                                            <i class="bi bi-building"></i>
                                            <input type="text" name="school_name" id="school_name" value="{{ $settings['school_name'] }}" required>
                                        </div>
                                    </div>

                                    <div class="input-icon-group">
                                        <label for="welcome_message">رسالة الترحيب للطلاب (تظهر في بوابة المتقدم)</label>
                                        <div class="input-icon-wrapper">
                                            <i class="bi bi-chat-left-quote" style="top: 24px; transform: none;"></i>
                                            <textarea name="welcome_message" id="welcome_message" rows="4" style="resize: none;">{{ $settings['welcome_message'] }}</textarea>
                                        </div>
                                    </div>

                                    <div class="input-icon-group">
                                        <label for="contact_email">بريد التواصل الإداري</label>
                                        <div class="input-icon-wrapper">
                                            <i class="bi bi-envelope"></i>
                                            <input type="email" name="contact_email" id="contact_email" value="{{ $settings['contact_email'] }}">
                                        </div>
                                    </div>
                                    <div class="input-icon-group" style="margin-bottom: 0;">
                                        <label for="contact_phone">رقم هاتف الدعم الفني</label>
                                        <div class="input-icon-wrapper">
                                            <i class="bi bi-telephone"></i>
                                            <input type="text" name="contact_phone" id="contact_phone" value="{{ $settings['contact_phone'] }}">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- لوحة المعاينة الحية لبوابة الطالب -->
                                <div class="live-preview-panel">
                                    <div class="preview-header">
                                        <span class="preview-pulse-dot"></span>
                                        معاينة حية لبوابة الطالب المتقدم
                                    </div>
                                    
                                    <div class="mock-login-card">
                                        <div style="width: 70px; height: 70px; border-radius: 16px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; overflow: hidden; padding: 4px;">
                                            <img id="preview-logo-g" src="{{ $settings['school_logo'] ? asset('storage/' . $settings['school_logo']) : asset('images/school_logo.png') }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                        </div>
                                        
                                        <h4 id="preview-name-g" style="font-size: 17px; font-weight: 900; color: #fff; margin: 0 0 10px 0;">{{ $settings['school_name'] }}</h4>
                                        <p id="preview-message-g" style="font-size: 11.5px; color: #94a3b8; line-height: 1.6; margin: 0 0 20px 0;">{{ $settings['welcome_message'] }}</p>
                                        
                                        <!-- تمثيل وهمي لحقول تسجيل الطلاب -->
                                        <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px;">
                                            <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; padding: 10px; font-size: 11px; color: #64748b; text-align: right;"><i class="bi bi-person"></i> اسم الطالب المتقدم</div>
                                            <div style="background: var(--primary); color: #fff; border-radius: 8px; padding: 10px; font-size: 12px; font-weight: 800; cursor: not-allowed; text-align: center;"><i class="bi bi-box-arrow-in-left"></i> دخول بوابة الامتحان</div>
                                        </div>
                                        
                                        <div style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 14px; display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; font-size: 10px; color: #64748b;">
                                            <span id="preview-email-g"><i class="bi bi-envelope"></i> {{ $settings['contact_email'] }}</span>
                                            <span id="preview-phone-g"><i class="bi bi-telephone"></i> {{ $settings['contact_phone'] }}</span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- أزرار الإجراءات للعامة -->
                            <div class="d-flex justify-content-between align-items-center mt-5" style="border-top: 1.5px solid #f1f5f9; padding-top: 24px; margin-top: 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px;">
                                <button type="submit" class="btn" style="background: var(--primary); color: #fff; border-radius: 12px; padding: 12px 36px; font-weight: 800; font-size: 14.5px; border: none; box-shadow: 0 4px 12px rgba(118,181,27,0.2); transition: all 0.2s; cursor: pointer;">
                                    <i class="bi bi-save"></i> حفظ التغييرات الإدارية
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn" style="background: #f1f5f9; color: #475569; border-radius: 12px; padding: 12px 30px; font-weight: 800; font-size: 14.5px; border: 1px solid #e2e8f0; text-decoration: none; text-align: center; transition: all 0.2s;">
                                    إلغاء
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 2. كرت التحكم في نظام الامتحانات -->
                    <div class="settings-card" id="tab-control">
                        <div class="sc-header">
                            <h3 class="sc-title"><i class="bi bi-shield-fill-check text-success" style="font-size: 20px;"></i> صلاحيات وحالة النظام</h3>
                        </div>
                        <div class="sc-body">
                            
                            <!-- إعداد حالة استقبال الامتحانات -->
                            <div style="margin-bottom: 32px;">
                                <label style="font-size: 14px; font-weight: 850; color: #475569; margin-bottom: 14px; display: block;">حالة النظام الاستقبالية (امتحانات القبول)</label>
                                <div class="toggle-grid">
                                    <label class="toggle-card {{ $settings['system_enabled'] == '1' ? 'active-state' : '' }}" onclick="updateCardState(this)">
                                        <input type="radio" name="system_enabled" value="1" {{ $settings['system_enabled'] == '1' ? 'checked' : '' }}>
                                        <div class="toggle-card-content">
                                            <span class="toggle-card-title text-success">مفعّل ونشط للطلاب</span>
                                            <span class="toggle-card-desc">يسمح هذا الوضع للطلاب بالولوج الكامل، تسجيل البيانات، وأداء امتحانات القبول المحددة.</span>
                                        </div>
                                    </label>
                                    <label class="toggle-card {{ $settings['system_enabled'] == '0' ? 'danger-state' : '' }}" onclick="updateCardState(this)">
                                        <input type="radio" name="system_enabled" value="0" {{ $settings['system_enabled'] == '0' ? 'checked' : '' }}>
                                        <div class="toggle-card-content">
                                            <span class="toggle-card-title text-danger">مغلق (وضع الصيانة)</span>
                                            <span class="toggle-card-desc">يتم حجب الطلاب تماماً من أداء أي اختبارات، ويظهر لهم تنبيه بأن النظام متوقف مؤقتاً.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div style="height: 1.5px; background: #f1f5f9; margin: 32px 0;"></div>

                            <!-- إعداد إظهار النتيجة الفورية للطلاب -->
                            <div>
                                <label style="font-size: 14px; font-weight: 850; color: #475569; margin-bottom: 14px; display: block;">إظهار نتيجة الاختبار للطلاب فور انتهائهم</label>
                                <div class="toggle-grid">
                                    <label class="toggle-card {{ $settings['show_results_instantly'] == '1' ? 'primary-state' : '' }}" onclick="updateCardState(this)">
                                        <input type="radio" name="show_results_instantly" value="1" {{ $settings['show_results_instantly'] == '1' ? 'checked' : '' }}>
                                        <div class="toggle-card-content">
                                            <span class="toggle-card-title text-primary">نعم، تظهر للطلاب فوراً</span>
                                            <span class="toggle-card-desc">بمجرد تسليم الطالب للاختبار، ستظهر له نسبته المئوية وتفاصيل نتيجته بنجاح/رسوب.</span>
                                        </div>
                                    </label>
                                    <label class="toggle-card {{ $settings['show_results_instantly'] == '0' ? '' : 'primary-state' }}" onclick="updateCardState(this)">
                                        <input type="radio" name="show_results_instantly" value="0" {{ $settings['show_results_instantly'] == '0' ? 'checked' : '' }}>
                                        <div class="toggle-card-content">
                                            <span class="toggle-card-title" style="color: #475569;">لا، إخفاء النتيجة للطلاب</span>
                                            <span class="toggle-card-desc">تُحجب النتيجة عن الطالب وتُحفظ كمسودة تظهر فقط في لوحة تحليلات الإدارة.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- أزرار الإجراءات للتحكم -->
                            <div class="d-flex justify-content-between align-items-center mt-5" style="border-top: 1.5px solid #f1f5f9; padding-top: 24px; margin-top: 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px;">
                                <button type="submit" class="btn" style="background: var(--primary); color: #fff; border-radius: 12px; padding: 12px 36px; font-weight: 800; font-size: 14.5px; border: none; box-shadow: 0 4px 12px rgba(118,181,27,0.2); transition: all 0.2s; cursor: pointer;">
                                    <i class="bi bi-save"></i> حفظ التغييرات الإدارية
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn" style="background: #f1f5f9; color: #475569; border-radius: 12px; padding: 12px 30px; font-weight: 800; font-size: 14.5px; border: 1px solid #e2e8f0; text-decoration: none; text-align: center; transition: all 0.2s;">
                                    إلغاء
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3. كرت الهوية والمظهر -->
                    <div class="settings-card" id="tab-appearance">
                        <div class="sc-header">
                            <h3 class="sc-title"><i class="bi bi-palette-fill text-warning" style="font-size: 20px;"></i> الهوية البصرية وشعار المدرسة</h3>
                        </div>
                        <div class="sc-body">
                            
                            <div class="sc-split-grid" style="margin-bottom: 32px;">
                                
                                <div>
                                    <label style="font-size: 14px; font-weight: 850; color: #475569; margin-bottom: 12px; display: block;">رفع وتعديل شعار المدرسة</label>
                                    
                                    <div style="display: flex; flex-direction: column; gap: 20px;">
                                        <!-- رفع صورة جديدة -->
                                        <input type="file" name="school_logo" id="school_logo" class="form-control" style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 14px; padding: 12px; font-size: 14px; width: 100%; cursor: pointer;">
                                        
                                        <p style="color: #64748b; font-size: 12px; font-weight: 600; margin: 0; line-height: 1.6;">
                                            <i class="bi bi-info-circle"></i> يفضل استخدام صور ذات امتداد شفّاف (PNG) ذات دقة مريحة وبحجم أقصى 2 ميجابايت للحفاظ على سرعة بوابة الطلاب.
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- لوحة معاينة الهوية والشعار -->
                                <div class="live-preview-panel">
                                    <div class="preview-header">
                                        <span class="preview-pulse-dot"></span>
                                        معاينة الشعار على الشاشة
                                    </div>
                                    
                                    <div class="mock-login-card" style="padding: 36px 24px;">
                                        <!-- معاينة الصورة الحالية أو المرفوعة -->
                                        <div style="width: 100px; height: 100px; border: 2.5px dashed rgba(118, 181, 27, 0.3); border-radius: 20px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: rgba(255,255,255,0.04); margin: 0 auto 16px; padding: 8px;">
                                            <img id="preview-logo-a" src="{{ $settings['school_logo'] ? asset('storage/' . $settings['school_logo']) : asset('images/school_logo.png') }}" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 10px;">
                                        </div>
                                        <span style="font-size: 11px; font-weight: 800; color: #94a3b8; display: block;">معاينة حجم الشعار الفعلي للطلاب</span>
                                    </div>
                                </div>

                            </div>

                            <div style="height: 1.5px; background: #e2e8f0; margin: 32px 0;"></div>

                            <div class="sc-split-grid">
                                
                                <div>
                                    <label style="font-size: 14px; font-weight: 850; color: #475569; margin-bottom: 12px; display: block;">صورة الواجهة الرئيسية للموقع (Hero Image)</label>
                                    
                                    <div style="display: flex; flex-direction: column; gap: 20px;">
                                        <!-- رفع صورة جديدة -->
                                        <input type="file" name="home_hero_image" id="home_hero_image" class="form-control" style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 14px; padding: 12px; font-size: 14px; width: 100%; cursor: pointer;">
                                        
                                        <p style="color: #64748b; font-size: 12px; font-weight: 600; margin: 0; line-height: 1.6;">
                                            <i class="bi bi-info-circle"></i> تظهر هذه الصورة في الصفحة الرئيسية للطلاب بجانب نص الترحيب. يفضل استخدام صور تعليمية ذات جودة عالية وبحجم أقصى 4 ميجابايت.
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- لوحة معاينة الصورة -->
                                <div class="live-preview-panel">
                                    <div class="preview-header">
                                        <span class="preview-pulse-dot"></span>
                                        معاينة صورة الواجهة الحالية
                                    </div>
                                    
                                    <div class="mock-login-card" style="padding: 16px; background: rgba(255,255,255,0.02);">
                                        <div style="width: 100%; height: 120px; border: 1.5px solid rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #fff;">
                                            <img id="preview-hero-a" src="{{ $settings['home_hero_image'] ? asset('storage/' . $settings['home_hero_image']) : asset('images/classroom_hero.png') }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <span style="font-size: 11px; font-weight: 800; color: #94a3b8; display: block; margin-top: 10px;">صورة الواجهة النشطة</span>
                                    </div>
                                </div>

                            </div>

                            <!-- أزرار الإجراءات للمظهر -->
                            <div class="d-flex justify-content-between align-items-center mt-5" style="border-top: 1.5px solid #f1f5f9; padding-top: 24px; margin-top: 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px;">
                                <button type="submit" class="btn" style="background: var(--primary); color: #fff; border-radius: 12px; padding: 12px 36px; font-weight: 800; font-size: 14.5px; border: none; box-shadow: 0 4px 12px rgba(118,181,27,0.2); transition: all 0.2s; cursor: pointer;">
                                    <i class="bi bi-save"></i> حفظ التغييرات الإدارية
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn" style="background: #f1f5f9; color: #475569; border-radius: 12px; padding: 12px 30px; font-weight: 800; font-size: 14.5px; border: 1px solid #e2e8f0; text-decoration: none; text-align: center; transition: all 0.2s;">
                                    إلغاء
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 4. كرت إعدادات صفحة من نحن -->
                    <div class="settings-card" id="tab-about">
                        <div class="sc-header">
                            <h3 class="sc-title"><i class="bi bi-info-circle-fill text-primary" style="font-size: 20px;"></i> محتوى صفحة من نحن</h3>
                        </div>
                        <div class="sc-body">
                            <div class="input-icon-group">
                                <label for="about_description">مقدمة صفحة من نحن (عن المدرسة)</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-chat-left-text" style="top: 24px; transform: none;"></i>
                                    <textarea name="about_description" id="about_description" rows="3" style="resize: none;">{{ $settings['about_description'] }}</textarea>
                                </div>
                            </div>

                            <div class="input-icon-group">
                                <label for="about_vision">رؤية المدرسة</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-eye-fill" style="top: 24px; transform: none;"></i>
                                    <textarea name="about_vision" id="about_vision" rows="3" style="resize: none;">{{ $settings['about_vision'] }}</textarea>
                                </div>
                            </div>

                            <div class="input-icon-group">
                                <label for="about_mission">رسالة المدرسة</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-rocket-takeoff-fill" style="top: 24px; transform: none;"></i>
                                    <textarea name="about_mission" id="about_mission" rows="3" style="resize: none;">{{ $settings['about_mission'] }}</textarea>
                                </div>
                            </div>

                            <div class="input-icon-group">
                                <label for="about_values">القيم الجوهرية</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-heart-pulse-fill" style="top: 24px; transform: none;"></i>
                                    <textarea name="about_values" id="about_values" rows="3" style="resize: none;">{{ $settings['about_values'] }}</textarea>
                                </div>
                            </div>

                            <div class="input-icon-group">
                                <label for="about_history">تفاصيل ونشأة المدرسة (من نحن)</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-journal-text" style="top: 24px; transform: none;"></i>
                                    <textarea name="about_history" id="about_history" rows="4" style="resize: none;">{{ $settings['about_history'] }}</textarea>
                                </div>
                            </div>

                            <div style="height: 1px; background: #e2e8f0; margin: 24px 0;"></div>
                            <h4 style="font-size: 15px; font-weight: 850; color: #1e293b; margin-bottom: 16px;">إحصائيات الإنجاز</h4>

                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px;">
                                <div class="input-icon-group" style="margin-bottom: 0;">
                                    <label for="about_stat_years">سنوات التميز</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-calendar3"></i>
                                        <input type="text" name="about_stat_years" id="about_stat_years" value="{{ $settings['about_stat_years'] }}" placeholder="مثال: 15">
                                    </div>
                                </div>
                                <div class="input-icon-group" style="margin-bottom: 0;">
                                    <label for="about_stat_graduates">عدد الخريجين</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-people"></i>
                                        <input type="text" name="about_stat_graduates" id="about_stat_graduates" value="{{ $settings['about_stat_graduates'] }}" placeholder="مثال: +2,500">
                                    </div>
                                </div>
                                <div class="input-icon-group" style="margin-bottom: 0;">
                                    <label for="about_stat_teachers">عدد المعلمين والإداريين</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-person-badge"></i>
                                        <input type="text" name="about_stat_teachers" id="about_stat_teachers" value="{{ $settings['about_stat_teachers'] }}" placeholder="مثال: 120">
                                    </div>
                                </div>
                            </div>

                            <div style="height: 1px; background: #e2e8f0; margin: 24px 0;"></div>
                            <h4 style="font-size: 15px; font-weight: 850; color: #1e293b; margin-bottom: 16px;">لماذا تختارنا؟ (ميزات المدارس)</h4>

                            <div class="input-icon-group">
                                <label for="about_features_intro">مقدمة ووصف قسم لماذا تختارنا</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-card-text"></i>
                                    <input type="text" name="about_features_intro" id="about_features_intro" value="{{ $settings['about_features_intro'] }}">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                                <!-- الميزة 1 -->
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 18px;">
                                    <h5 style="font-size: 13.5px; font-weight: 800; color: var(--primary-dark); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;"><i class="bi bi-people-fill"></i> الميزة الأولى</h5>
                                    <div class="input-icon-group" style="margin-bottom: 12px;">
                                        <label for="about_feature1_title" style="font-size: 12px;">العنوان</label>
                                        <input type="text" name="about_feature1_title" id="about_feature1_title" value="{{ $settings['about_feature1_title'] }}" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; outline: none; font-size: 13.5px; font-weight: 600;">
                                    </div>
                                    <div class="input-icon-group" style="margin-bottom: 0;">
                                        <label for="about_feature1_desc" style="font-size: 12px;">الوصف</label>
                                        <textarea name="about_feature1_desc" id="about_feature1_desc" rows="3" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; resize: none; outline: none; font-size: 13px; font-weight: 600;">{{ $settings['about_feature1_desc'] }}</textarea>
                                    </div>
                                </div>

                                <!-- الميزة 2 -->
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 18px;">
                                    <h5 style="font-size: 13.5px; font-weight: 800; color: var(--primary-dark); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;"><i class="bi bi-laptop-fill"></i> الميزة الثانية</h5>
                                    <div class="input-icon-group" style="margin-bottom: 12px;">
                                        <label for="about_feature2_title" style="font-size: 12px;">العنوان</label>
                                        <input type="text" name="about_feature2_title" id="about_feature2_title" value="{{ $settings['about_feature2_title'] }}" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; outline: none; font-size: 13.5px; font-weight: 600;">
                                    </div>
                                    <div class="input-icon-group" style="margin-bottom: 0;">
                                        <label for="about_feature2_desc" style="font-size: 12px;">الوصف</label>
                                        <textarea name="about_feature2_desc" id="about_feature2_desc" rows="3" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; resize: none; outline: none; font-size: 13px; font-weight: 600;">{{ $settings['about_feature2_desc'] }}</textarea>
                                    </div>
                                </div>

                                <!-- الميزة 3 -->
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 18px;">
                                    <h5 style="font-size: 13.5px; font-weight: 800; color: var(--primary-dark); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;"><i class="bi bi-journal-bookmark-fill"></i> الميزة الثالثة</h5>
                                    <div class="input-icon-group" style="margin-bottom: 12px;">
                                        <label for="about_feature3_title" style="font-size: 12px;">العنوان</label>
                                        <input type="text" name="about_feature3_title" id="about_feature3_title" value="{{ $settings['about_feature3_title'] }}" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; outline: none; font-size: 13.5px; font-weight: 600;">
                                    </div>
                                    <div class="input-icon-group" style="margin-bottom: 0;">
                                        <label for="about_feature3_desc" style="font-size: 12px;">الوصف</label>
                                        <textarea name="about_feature3_desc" id="about_feature3_desc" rows="3" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; resize: none; outline: none; font-size: 13px; font-weight: 600;">{{ $settings['about_feature3_desc'] }}</textarea>
                                    </div>
                                </div>

                                <!-- الميزة 4 -->
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 18px;">
                                    <h5 style="font-size: 13.5px; font-weight: 800; color: var(--primary-dark); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;"><i class="bi bi-award-fill"></i> الميزة الرابعة</h5>
                                    <div class="input-icon-group" style="margin-bottom: 12px;">
                                        <label for="about_feature4_title" style="font-size: 12px;">العنوان</label>
                                        <input type="text" name="about_feature4_title" id="about_feature4_title" value="{{ $settings['about_feature4_title'] }}" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; outline: none; font-size: 13.5px; font-weight: 600;">
                                    </div>
                                    <div class="input-icon-group" style="margin-bottom: 0;">
                                        <label for="about_feature4_desc" style="font-size: 12px;">الوصف</label>
                                        <textarea name="about_feature4_desc" id="about_feature4_desc" rows="3" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; resize: none; outline: none; font-size: 13px; font-weight: 600;">{{ $settings['about_feature4_desc'] }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- أزرار الإجراءات لصفحة من نحن -->
                            <div class="d-flex justify-content-between align-items-center mt-5" style="border-top: 1.5px solid #f1f5f9; padding-top: 24px; margin-top: 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px;">
                                <button type="submit" class="btn" style="background: var(--primary); color: #fff; border-radius: 12px; padding: 12px 36px; font-weight: 800; font-size: 14.5px; border: none; box-shadow: 0 4px 12px rgba(118,181,27,0.2); transition: all 0.2s; cursor: pointer;">
                                    <i class="bi bi-save"></i> حفظ التغييرات الإدارية
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn" style="background: #f1f5f9; color: #475569; border-radius: 12px; padding: 12px 30px; font-weight: 800; font-size: 14.5px; border: 1px solid #e2e8f0; text-decoration: none; text-align: center; transition: all 0.2s;">
                                    إلغاء
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 5. كرت خطوات رحلة القبول -->
                    <div class="settings-card" id="tab-steps">
                        <div class="sc-header">
                            <h3 class="sc-title"><i class="bi bi-list-ol text-info" style="font-size: 20px;"></i> خطوات رحلة القبول الإلكتروني</h3>
                        </div>
                        <div class="sc-body">
                            
                            <div class="input-icon-group">
                                <label for="steps_section_title">عنوان القسم الرئيسي</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-type-h1"></i>
                                    <input type="text" name="steps_section_title" id="steps_section_title" value="{{ $settings['steps_section_title'] }}">
                                </div>
                            </div>

                            <div class="input-icon-group">
                                <label for="steps_section_desc">الوصف الفرعي للقسم</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-card-text"></i>
                                    <input type="text" name="steps_section_desc" id="steps_section_desc" value="{{ $settings['steps_section_desc'] }}">
                                </div>
                            </div>

                            <div style="height: 1.5px; background: #e2e8f0; margin: 32px 0;"></div>

                            <!-- شبكة الخطوات الأربعة -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                                
                                <!-- الخطوة الأولى -->
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px;">
                                    <h5 style="font-size: 14px; font-weight: 850; color: #0284c7; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                                        <span style="background: rgba(2, 132, 199, 0.1); width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800;">1</span>
                                        الخطوة الأولى
                                    </h5>
                                    <div style="display: grid; grid-template-columns: 80px 1fr; gap: 12px; margin-bottom: 12px;">
                                        <div>
                                            <label for="step1_icon" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">رمز/إيموجي</label>
                                            <input type="text" name="step1_icon" id="step1_icon" value="{{ $settings['step1_icon'] }}" style="padding: 10px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; text-align: center; font-size: 16px;">
                                        </div>
                                        <div>
                                            <label for="step1_title" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">العنوان</label>
                                            <input type="text" name="step1_title" id="step1_title" value="{{ $settings['step1_title'] }}" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; font-size: 13.5px; font-weight: 600;">
                                        </div>
                                    </div>
                                    <div class="input-icon-group" style="margin-bottom: 0;">
                                        <label for="step1_desc" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">الوصف</label>
                                        <textarea name="step1_desc" id="step1_desc" rows="3" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; resize: none; font-size: 13px; font-weight: 600;">{{ $settings['step1_desc'] }}</textarea>
                                    </div>
                                </div>

                                <!-- الخطوة الثانية -->
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px;">
                                    <h5 style="font-size: 14px; font-weight: 850; color: #0284c7; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                                        <span style="background: rgba(2, 132, 199, 0.1); width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800;">2</span>
                                        الخطوة الثانية
                                    </h5>
                                    <div style="display: grid; grid-template-columns: 80px 1fr; gap: 12px; margin-bottom: 12px;">
                                        <div>
                                            <label for="step2_icon" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">رمز/إيموجي</label>
                                            <input type="text" name="step2_icon" id="step2_icon" value="{{ $settings['step2_icon'] }}" style="padding: 10px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; text-align: center; font-size: 16px;">
                                        </div>
                                        <div>
                                            <label for="step2_title" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">العنوان</label>
                                            <input type="text" name="step2_title" id="step2_title" value="{{ $settings['step2_title'] }}" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; font-size: 13.5px; font-weight: 600;">
                                        </div>
                                    </div>
                                    <div class="input-icon-group" style="margin-bottom: 0;">
                                        <label for="step2_desc" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">الوصف</label>
                                        <textarea name="step2_desc" id="step2_desc" rows="3" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; resize: none; font-size: 13px; font-weight: 600;">{{ $settings['step2_desc'] }}</textarea>
                                    </div>
                                </div>

                                <!-- الخطوة الثالثة -->
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px;">
                                    <h5 style="font-size: 14px; font-weight: 850; color: #0284c7; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                                        <span style="background: rgba(2, 132, 199, 0.1); width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800;">3</span>
                                        الخطوة الثالثة
                                    </h5>
                                    <div style="display: grid; grid-template-columns: 80px 1fr; gap: 12px; margin-bottom: 12px;">
                                        <div>
                                            <label for="step3_icon" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">رمز/إيموجي</label>
                                            <input type="text" name="step3_icon" id="step3_icon" value="{{ $settings['step3_icon'] }}" style="padding: 10px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; text-align: center; font-size: 16px;">
                                        </div>
                                        <div>
                                            <label for="step3_title" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">العنوان</label>
                                            <input type="text" name="step3_title" id="step3_title" value="{{ $settings['step3_title'] }}" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; font-size: 13.5px; font-weight: 600;">
                                        </div>
                                    </div>
                                    <div class="input-icon-group" style="margin-bottom: 0;">
                                        <label for="step3_desc" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">الوصف</label>
                                        <textarea name="step3_desc" id="step3_desc" rows="3" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; resize: none; font-size: 13px; font-weight: 600;">{{ $settings['step3_desc'] }}</textarea>
                                    </div>
                                </div>

                                <!-- الخطوة الرابعة -->
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px;">
                                    <h5 style="font-size: 14px; font-weight: 850; color: #0284c7; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                                        <span style="background: rgba(2, 132, 199, 0.1); width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800;">4</span>
                                        الخطوة الرابعة
                                    </h5>
                                    <div style="display: grid; grid-template-columns: 80px 1fr; gap: 12px; margin-bottom: 12px;">
                                        <div>
                                            <label for="step4_icon" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">رمز/إيموجي</label>
                                            <input type="text" name="step4_icon" id="step4_icon" value="{{ $settings['step4_icon'] }}" style="padding: 10px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; text-align: center; font-size: 16px;">
                                        </div>
                                        <div>
                                            <label for="step4_title" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">العنوان</label>
                                            <input type="text" name="step4_title" id="step4_title" value="{{ $settings['step4_title'] }}" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; font-size: 13.5px; font-weight: 600;">
                                        </div>
                                    </div>
                                    <div class="input-icon-group" style="margin-bottom: 0;">
                                        <label for="step4_desc" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">الوصف</label>
                                        <textarea name="step4_desc" id="step4_desc" rows="3" style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; resize: none; font-size: 13px; font-weight: 600;">{{ $settings['step4_desc'] }}</textarea>
                                    </div>
                                </div>

                            </div>

                            <!-- أزرار الإجراءات للخطوات -->
                            <div class="d-flex justify-content-between align-items-center mt-5" style="border-top: 1.5px solid #f1f5f9; padding-top: 24px; margin-top: 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px;">
                                <button type="submit" class="btn" style="background: var(--primary); color: #fff; border-radius: 12px; padding: 12px 36px; font-weight: 800; font-size: 14.5px; border: none; box-shadow: 0 4px 12px rgba(118,181,27,0.2); transition: all 0.2s; cursor: pointer;">
                                    <i class="bi bi-save"></i> حفظ التغييرات الإدارية
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn" style="background: #f1f5f9; color: #475569; border-radius: 12px; padding: 12px 30px; font-weight: 800; font-size: 14.5px; border: 1px solid #e2e8f0; text-decoration: none; text-align: center; transition: all 0.2s;">
                                    إلغاء
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- 6. كرت الأسئلة الشائعة -->
                    <div class="settings-card" id="tab-faq">
                        <div class="sc-header">
                            <h3 class="sc-title"><i class="bi bi-question-circle-fill text-warning" style="font-size: 20px;"></i> الأسئلة الشائعة حول القبول</h3>
                        </div>
                        <div class="sc-body">
                            
                            <div class="input-icon-group">
                                <label for="faq_section_title">عنوان القسم الرئيسي</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-type-h1"></i>
                                    <input type="text" name="faq_section_title" id="faq_section_title" value="{{ $settings['faq_section_title'] }}">
                                </div>
                            </div>

                            <div class="input-icon-group">
                                <label for="faq_section_desc">الوصف الفرعي للقسم</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-card-text"></i>
                                    <input type="text" name="faq_section_desc" id="faq_section_desc" value="{{ $settings['faq_section_desc'] }}">
                                </div>
                            </div>

                            <div style="height: 1.5px; background: #e2e8f0; margin: 32px 0;"></div>

                            <!-- قائمة الأسئلة الشائعة الديناميكية -->
                            <div id="faq-list-container" style="display: flex; flex-direction: column; gap: 24px; margin-bottom: 24px;">
                                @foreach($settings['faqs'] as $index => $faq)
                                <div class="faq-manage-item" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; position: relative; transition: all 0.3s ease;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                        <h5 class="faq-item-title" style="font-size: 14px; font-weight: 850; color: #d97706; margin: 0; display: flex; align-items: center; gap: 8px;">
                                            <i class="bi bi-patch-question-fill"></i> السؤال الشائع
                                        </h5>
                                        <div style="display: flex; gap: 8px;">
                                            <!-- أزرار ترتيب الأسئلة -->
                                            <button type="button" class="btn-faq-order btn-faq-up" onclick="moveFaqItem(this, 'up')" style="background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b; transition: all 0.2s;"><i class="bi bi-arrow-up"></i></button>
                                            <button type="button" class="btn-faq-order btn-faq-down" onclick="moveFaqItem(this, 'down')" style="background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b; transition: all 0.2s;"><i class="bi bi-arrow-down"></i></button>
                                            
                                            <button type="button" onclick="removeFaqItem(this)" style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 800; color: #dc2626; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: all 0.2s;">
                                                <i class="bi bi-trash3-fill"></i> حذف السؤال
                                            </button>
                                        </div>
                                    </div>
                                    <div class="input-icon-group" style="margin-bottom: 12px;">
                                        <label style="font-size: 12px; font-weight: 700; color: #475569;">نص السؤال</label>
                                        <input type="text" name="faqs[{{ $index }}][question]" value="{{ $faq['question'] }}" required style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; font-size: 13.5px; font-weight: 600;">
                                    </div>
                                    <div class="input-icon-group" style="margin-bottom: 0;">
                                        <label style="font-size: 12px; font-weight: 700; color: #475569;">الإجابة</label>
                                        <textarea name="faqs[{{ $index }}][answer]" rows="3" required style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; resize: none; font-size: 13px; font-weight: 600;">{{ $faq['answer'] }}</textarea>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- زر إضافة سؤال جديد -->
                            <div style="text-align: center; margin-bottom: 32px;">
                                <button type="button" id="btn-add-faq" style="background: #fff; border: 2px dashed var(--primary); color: var(--primary); border-radius: 14px; padding: 12px 24px; font-weight: 800; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(118, 181, 27, 0.05);">
                                    <i class="bi bi-plus-circle-fill"></i> إضافة سؤال شائع جديد
                                </button>
                            </div>

                            <!-- أزرار الإجراءات للأسئلة الشائعة -->
                            <div class="d-flex justify-content-between align-items-center mt-5" style="border-top: 1.5px solid #f1f5f9; padding-top: 24px; margin-top: 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px;">
                                <button type="submit" class="btn" style="background: var(--primary); color: #fff; border-radius: 12px; padding: 12px 36px; font-weight: 800; font-size: 14.5px; border: none; box-shadow: 0 4px 12px rgba(118,181,27,0.2); transition: all 0.2s; cursor: pointer;">
                                    <i class="bi bi-save"></i> حفظ التغييرات الإدارية
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn" style="background: #f1f5f9; color: #475569; border-radius: 12px; padding: 12px 30px; font-weight: 800; font-size: 14.5px; border: 1px solid #e2e8f0; text-decoration: none; text-align: center; transition: all 0.2s;">
                                    إلغاء
                                </a>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </form>

    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── دالة الانتقال بين تبويبات الإعدادات الجانبية ──
    window.switchSettingsTab = function(event, tabId) {
        // تحديث حالة الأزرار الجانبية
        const buttons = document.querySelectorAll('.settings-tab-btn');
        buttons.forEach(btn => btn.classList.remove('active'));
        event.currentTarget.classList.add('active');
        
        // إخفاء كافة كروت الإعدادات وإظهار الكرت المحدد
        const cards = document.querySelectorAll('.settings-card');
        cards.forEach(card => card.classList.remove('active'));
        
        const targetCard = document.getElementById(tabId);
        if (targetCard) {
            targetCard.classList.add('active');
        }
    }

    // ── تحديث نمط كروت الخيارات التفاعلية للتحكم (Toggle Cards) ──
    window.updateCardState = function(labelEl) {
        const grid = labelEl.closest('.toggle-grid');
        const cards = grid.querySelectorAll('.toggle-card');
        
        // إزالة الحالات النشطة من جميع الكروت في نفس الشبكة
        cards.forEach(card => {
            card.classList.remove('active-state', 'danger-state', 'primary-state');
        });
        
        // العثور على المدخل وراديو كرت الخيار الحالي
        const radio = labelEl.querySelector('input[type="radio"]');
        radio.checked = true;
        
        // تحديد الحالة اللفظية المناسبة وإلحاق الكلاس بناءً على قيمة الخيار
        const val = radio.value;
        const name = radio.name;
        
        if (name === 'system_enabled') {
            if (val === '1') {
                labelEl.classList.add('active-state');
            } else {
                labelEl.classList.add('danger-state');
            }
        } else if (name === 'show_results_instantly') {
            if (val === '1') {
                labelEl.classList.add('primary-state');
            } else {
                labelEl.classList.add('primary-state');
            }
        }
    }

    // ── محرك المزامنة الحية لإدخالات الإعدادات (Live Sync Engine) ──
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('school_name');
        const msgInput = document.getElementById('welcome_message');
        const emailInput = document.getElementById('contact_email');
        const phoneInput = document.getElementById('contact_phone');
        const logoInput = document.getElementById('school_logo');

        const previewNameG = document.getElementById('preview-name-g');
        const previewMsgG = document.getElementById('preview-message-g');
        const previewEmailG = document.getElementById('preview-email-g');
        const previewPhoneG = document.getElementById('preview-phone-g');
        
        const previewLogoG = document.getElementById('preview-logo-g');
        const previewLogoA = document.getElementById('preview-logo-a');

        if (nameInput) {
            nameInput.addEventListener('input', function() {
                previewNameG.innerText = nameInput.value || 'اسم المدرسة';
            });
        }

        if (msgInput) {
            msgInput.addEventListener('input', function() {
                previewMsgG.innerText = msgInput.value || 'رسالة الترحيب للطلاب المتقدمين...';
            });
        }

        if (emailInput) {
            emailInput.addEventListener('input', function() {
                previewEmailG.innerHTML = `<i class="bi bi-envelope"></i> ${emailInput.value}`;
            });
        }

        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                previewPhoneG.innerHTML = `<i class="bi bi-telephone"></i> ${phoneInput.value}`;
            });
        }

        if (logoInput) {
            logoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        if (previewLogoG) previewLogoG.src = evt.target.result;
                        if (previewLogoA) previewLogoA.src = evt.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        const heroInput = document.getElementById('home_hero_image');
        const previewHeroA = document.getElementById('preview-hero-a');

        if (heroInput) {
            heroInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        if (previewHeroA) previewHeroA.src = evt.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        // ── إدارة الأسئلة الشائعة الديناميكية ──
        const faqContainer = document.getElementById('faq-list-container');
        const btnAddFaq = document.getElementById('btn-add-faq');

        if (btnAddFaq && faqContainer) {
            btnAddFaq.addEventListener('click', function() {
                const index = faqContainer.children.length;
                const faqItemHtml = 
                '<div class="faq-manage-item" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; position: relative; opacity: 0; transform: translateY(10px); transition: all 0.3s ease;">' +
                    '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">' +
                        '<h5 class="faq-item-title" style="font-size: 14px; font-weight: 850; color: #d97706; margin: 0; display: flex; align-items: center; gap: 8px;">' +
                            '<i class="bi bi-patch-question-fill"></i> السؤال الشائع' +
                        '</h5>' +
                        '<div style="display: flex; gap: 8px;">' +
                            '<button type="button" class="btn-faq-order btn-faq-up" onclick="moveFaqItem(this, \'up\')" style="background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b; transition: all 0.2s;"><i class="bi bi-arrow-up"></i></button>' +
                            '<button type="button" class="btn-faq-order btn-faq-down" onclick="moveFaqItem(this, \'down\')" style="background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b; transition: all 0.2s;"><i class="bi bi-arrow-down"></i></button>' +
                            '<button type="button" onclick="removeFaqItem(this)" style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 800; color: #dc2626; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: all 0.2s;">' +
                                '<i class="bi bi-trash3-fill"></i> حذف السؤال' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                    '<div class="input-icon-group" style="margin-bottom: 12px;">' +
                        '<label style="font-size: 12px; font-weight: 700; color: #475569;">نص السؤال</label>' +
                        '<input type="text" name="faqs[' + index + '][question]" required style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; font-size: 13.5px; font-weight: 600;">' +
                    '</div>' +
                    '<div class="input-icon-group" style="margin-bottom: 0;">' +
                        '<label style="font-size: 12px; font-weight: 700; color: #475569;">الإجابة</label>' +
                        '<textarea name="faqs[' + index + '][answer]" rows="3" required style="padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%; resize: none; font-size: 13px; font-weight: 600;"></textarea>' +
                    '</div>' +
                '</div>';
                
                faqContainer.insertAdjacentHTML('beforeend', faqItemHtml);
                
                // Trigger animation
                const newItem = faqContainer.lastElementChild;
                setTimeout(() => {
                    newItem.style.opacity = '1';
                    newItem.style.transform = 'translateY(0)';
                }, 10);
                
                updateFaqIndexes();
            });

            window.removeFaqItem = function(button) {
                const item = button.closest('.faq-manage-item');
                if (item) {
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        item.remove();
                        updateFaqIndexes();
                    }, 300);
                }
            }

            window.moveFaqItem = function(button, direction) {
                const item = button.closest('.faq-manage-item');
                if (!item) return;
                
                if (direction === 'up') {
                    const prev = item.previousElementSibling;
                    if (prev) {
                        faqContainer.insertBefore(item, prev);
                    }
                } else if (direction === 'down') {
                    const next = item.nextElementSibling;
                    if (next) {
                        faqContainer.insertBefore(next, item);
                    }
                }
                
                updateFaqIndexes();
            }

            window.updateFaqIndexes = function() {
                const items = faqContainer.querySelectorAll('.faq-manage-item');
                items.forEach((item, index) => {
                    // Update input names
                    const questionInput = item.querySelector('input[type="text"]');
                    if (questionInput) {
                        questionInput.name = 'faqs[' + index + '][question]';
                    }
                    const answerTextarea = item.querySelector('textarea');
                    if (answerTextarea) {
                        answerTextarea.name = 'faqs[' + index + '][answer]';
                    }
                    
                    // Enable/disable up/down buttons based on position
                    const btnUp = item.querySelector('.btn-faq-up');
                    const btnDown = item.querySelector('.btn-faq-down');
                    
                    if (btnUp) {
                        btnUp.disabled = (index === 0);
                        btnUp.style.opacity = (index === 0) ? '0.4' : '1';
                        btnUp.style.pointerEvents = (index === 0) ? 'none' : 'auto';
                    }
                    if (btnDown) {
                        btnDown.disabled = (index === items.length - 1);
                        btnDown.style.opacity = (index === items.length - 1) ? '0.4' : '1';
                        btnDown.style.pointerEvents = (index === items.length - 1) ? 'none' : 'auto';
                    }
                });
            }

            // Call index update initially
            updateFaqIndexes();
        }
    });
</script>
@endpush
