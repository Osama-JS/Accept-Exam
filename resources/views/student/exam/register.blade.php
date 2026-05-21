@extends('layouts.student')
@section('title', 'تسجيل بيانات الطالب - ' . $exam->title)

@push('styles')
<style>
    /* ── واجهة التسجيل المتقدمة (Register Animations & Depth) ── */
    .register-section {
        max-width: 780px; margin: 48px auto 100px; padding: 0 24px;
        animation: fadeUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(24px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .glow-blob {
        position: absolute; width: 300px; height: 300px; border-radius: 50%;
        background: radial-gradient(circle, rgba(118, 181, 27, 0.06) 0%, rgba(255,255,255,0) 70%);
        filter: blur(40px); z-index: -1; pointer-events: none;
    }

    /* 🔙 كبسولة الترجيع للمسار */
    .breadcrumb-row {
        margin-bottom: 20px; display: flex; align-items: center; justify-content: flex-start;
    }
    .btn-back {
        display: inline-flex; align-items: center; gap: 8px; color: #64748b;
        text-decoration: none; font-size: 14px; font-weight: 800; transition: all 0.2s;
        background: #ffffff; border: 1.5px solid #e2e8f0; padding: 8px 18px; border-radius: 30px;
    }
    .btn-back:hover { color: var(--primary); border-color: var(--primary-light); transform: translateX(4px); }

    /* ── كارت ملخص الاختبار (Exam Summary Banner) ── */
    .exam-summary {
        background: linear-gradient(135deg, #475b27 0%, #76b51b 100%);
        border-radius: 24px; padding: 32px; color: #fff; margin-bottom: 32px;
        display: flex; align-items: center; gap: 24px;
        position: relative; overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.1);
    }
    .exam-summary::before {
        content: ''; position: absolute; inset: 0;
        background: radial-gradient(circle at 80% 20%, rgba(118, 181, 27, 0.15), transparent 50%);
        pointer-events: none;
    }
    
    .exam-summary .icon {
        width: 56px; height: 56px; background: rgba(255, 255, 255, 0.1);
        border: 1.5px solid rgba(255, 255, 255, 0.15); border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 26px; flex-shrink: 0;
    }
    
    .exam-summary h2 { font-size: 19px; font-weight: 900; margin-bottom: 12px; }
    
    .exam-meta-list { display: flex; gap: 10px; flex-wrap: wrap; }
    .exam-meta-list span {
        background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px); border-radius: 30px;
        padding: 5px 14px; font-size: 12.5px; font-weight: 700;
        border: 1px solid rgba(255, 255, 255, 0.05);
        display: inline-flex; align-items: center; gap: 6px;
    }

    /* ── كارت إدخال البيانات الفخم (Form Card) ── */
    .register-card {
        background: #ffffff; border-radius: 24px; border: 1.5px solid #e2e8f0;
        overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.03);
    }
    
    .register-card .card-head {
        background: #fafbfd; border-bottom: 1.5px solid #e2e8f0;
        padding: 24px 32px; display: flex; align-items: center; gap: 12px;
    }
    .register-card .card-head i { color: var(--primary); font-size: 22px; }
    .register-card .card-head h3 { font-size: 17px; font-weight: 900; color: #0f172a; }
    
    .register-card .card-body { padding: 32px; }
    
    /* حقول الإدخال والغروب */
    .form-group {
        margin-bottom: 24px; display: flex; flex-direction: column; gap: 8px;
    }
    .form-group label {
        font-size: 14px; font-weight: 800; color: #334155;
        display: flex; align-items: center; gap: 6px;
    }
    .form-group label span { color: var(--danger); }
    
    .form-control {
        width: 100%; padding: 14px 18px; border: 2px solid #e2e8f0;
        border-radius: 14px; font-size: 15px; font-weight: 700;
        color: #0f172a; background: #ffffff;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: inherit;
    }
    .form-control::placeholder { color: #94a3b8; font-weight: 600; }
    .form-control:focus {
        outline: none; border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(118, 181, 27, 0.12);
        background: #ffffff;
    }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    
    /* كبسولة التنبيه والتعليمات */
    .info-alert-box {
        background: rgba(3, 105, 161, 0.05); border: 1.5px solid rgba(3, 105, 161, 0.12);
        border-radius: 16px; padding: 16px 20px; margin-bottom: 28px;
        font-size: 13.5px; color: #0369a1; line-height: 1.6;
        display: flex; gap: 12px; align-items: flex-start;
    }
    .info-alert-box i { font-size: 18px; margin-top: 2px; }

    /* زر البدء النهائي الفخم */
    .submit-btn {
        width: 100%; padding: 16px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #fff; border: none; border-radius: 14px;
        font-family: 'Cairo', sans-serif; font-size: 16px; font-weight: 850;
        cursor: pointer; transition: all 0.3s ease;
        display: flex; align-items: center; justify-content: center; gap: 10px;
        box-shadow: var(--shadow-primary);
    }
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(118, 181, 27, 0.35);
    }

    @media (max-width: 600px) {
        .form-row { grid-template-columns: 1fr; gap: 0; }
        .register-card .card-body { padding: 24px; }
    }
</style>
@endpush

@section('content')
<div class="glow-blob" style="top: 20%; right: 10%;"></div>
<div class="glow-blob" style="top: 50%; left: 10%;"></div>

<div class="register-section">
    <!-- كبسولة الترجيع للمسار -->
    <div class="breadcrumb-row">
        <a href="{{ route('student.exams', $exam->grade) }}" class="btn-back">
            <i class="bi bi-arrow-right"></i> العودة لقائمة الامتحانات
        </a>
    </div>

    <!-- كارت ملخص الاختبار البانورامي -->
    <div class="exam-summary">
        <div class="icon"><i class="bi bi-file-earmark-text-fill"></i></div>
        <div>
            <h2>{{ $exam->title }}</h2>
            <div class="exam-meta-list">
                <span><i class="bi bi-mortarboard-fill"></i> {{ $exam->grade->name }}</span>
                <span><i class="bi bi-award-fill"></i> الدرجة الكلية: {{ $exam->total_marks }}</span>
                <span><i class="bi bi-shield-check"></i> حد القبول: {{ $exam->pass_marks }}</span>
                <span><i class="bi bi-question-circle-fill"></i> {{ $exam->totalQuestionsCount() }} سؤالاً</span>
            </div>
        </div>
    </div>

    <!-- كارت إدخال البيانات المتقدم -->
    <div class="register-card">
        <div class="card-head">
            <i class="bi bi-person-lines-fill"></i>
            <h3>بوابة تسجيل بيانات الطالب المتقدم</h3>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div style="background: rgba(239, 68, 68, 0.06); border: 1.5px solid rgba(239, 68, 68, 0.15); border-radius: 14px; padding: 16px 20px; margin-bottom: 24px; color: #b91c1c; font-size: 13.5px;">
                    <strong style="display: block; margin-bottom: 6px;"><i class="bi bi-exclamation-octagon-fill"></i> تنبيه: يرجى تصحيح الأخطاء التالية:</strong>
                    <ul style="padding-right: 18px; margin: 0; line-height: 1.6;">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('exam.start', $exam) }}">
                @csrf
                
                <!-- الاسم بالكامل -->
                <div class="form-group">
                    <label for="name"><i class="bi bi-person-fill" style="color: var(--primary);"></i> اسم الطالب رباعياً <span>*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="أدخل اسم الطالب رباعياً" required>
                </div>

                <!-- المدرسة السابقة والمعدل -->
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="bi bi-building-fill" style="color: var(--primary);"></i> المدرسة السابقة <span>*</span></label>
                        <input type="text" name="previous_school" class="form-control" value="{{ old('previous_school') }}" placeholder="المدرسة السابقة للطالب" required>
                    </div>
                    <div class="form-group">
                        <label><i class="bi bi-star-fill" style="color: var(--primary);"></i> معدل الصف السابق (%) <span>*</span></label>
                        <input type="number" name="last_grade_average" class="form-control" value="{{ old('last_grade_average') }}" min="0" max="100" step="0.1" placeholder="مثال: 92.5" required>
                    </div>
                </div>

                <!-- اسم ولي الأمر والاتصال -->
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="bi bi-person-hearts" style="color: var(--primary);"></i> اسم ولي الأمر رباعياً <span>*</span></label>
                        <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name') }}" placeholder="اسم ولي أمر الطالب" required>
                    </div>
                    <div class="form-group">
                        <label><i class="bi bi-telephone-fill" style="color: var(--primary);"></i> رقم هاتف ولي الأمر <span>*</span></label>
                        <input type="tel" name="guardian_phone" class="form-control" value="{{ old('guardian_phone') }}" placeholder="05xxxxxxxx" required>
                    </div>
                </div>

                <!-- تنبيه وتوجيه آمن -->
                <div class="info-alert-box">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>
                        <strong>ملاحظة هامة للجلسة:</strong> بمجرد نقر زر المتابعة بالأسفل، سيقوم النظام بتأمين اختبارك وحساب العداد الزمني. يرجى عدم تحديث الصفحة أو إغلاقها لضمان حفظ إجاباتك.
                    </div>
                </div>

                <!-- زر البدء الملون -->
                <button type="submit" class="submit-btn">
                    <i class="bi bi-play-circle-fill" style="font-size: 18px;"></i>
                    بدء جلسة الاختبار الآمنة
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
