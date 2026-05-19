@extends('layouts.student')
@section('title', 'الامتحانات المتاحة - ' . $grade->name)

@push('styles')
<style>
    /* ── واجهة الترحيب الفاخرة للصف (Grade Neon Hero) ── */
    .page-hero {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #fff; padding: 56px 40px; text-align: center; position: relative; overflow: hidden;
        border-radius: 24px; margin: 32px 0; border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .page-hero::before {
        content: ''; position: absolute; inset: 0;
        background: radial-gradient(ellipse 60% 80% at 50% 50%, rgba(118, 181, 27, 0.15) 0%, transparent 65%);
    }
    .page-hero::after {
        content: '\F644'; font-family: 'bootstrap-icons'; position: absolute;
        left: -10px; bottom: -30px; font-size: 160px; opacity: 0.05; line-height: 1; pointer-events: none;
    }
    .page-hero h1 { font-size: 36px; font-weight: 950; margin-bottom: 12px; position: relative; letter-spacing: -0.5px; }
    .page-hero p { color: #94a3b8; font-size: 16px; position: relative; font-weight: 600; }
    
    /* ── كبسولة الترجيع للمسار ── */
    .breadcrumb-row {
        max-width: 860px; margin: 0 auto 16px; padding: 0 12px;
        display: flex; align-items: center; justify-content: flex-start;
    }
    .btn-back {
        display: inline-flex; align-items: center; gap: 8px; color: #64748b;
        text-decoration: none; font-size: 14px; font-weight: 800; transition: all 0.2s;
        background: #ffffff; border: 1.5px solid #e2e8f0; padding: 8px 18px; border-radius: 30px;
    }
    .btn-back:hover { color: var(--primary); border-color: var(--primary-light); transform: translateX(4px); }

    /* ── منطقة الامتحانات ── */
    .exams-section { max-width: 860px; margin: 0 auto 80px; padding: 0 12px; }
    
    /* كرت الامتحان المطور كـ "كبسولة زمنية" */
    .exam-card {
        background: #fff; border: 1.5px solid #e2e8f0; border-radius: 24px; padding: 32px;
        display: flex; justify-content: space-between; align-items: center; gap: 24px;
        margin-bottom: 20px; transition: all .3s cubic-bezier(.4,0,.2,1);
        text-decoration: none; color: inherit; position: relative; overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);
    }
    .exam-card:hover {
        border-color: rgba(118, 181, 27, 0.4);
        box-shadow: 0 20px 25px -5px rgba(118, 181, 27, 0.08);
        transform: translateY(-5px);
    }
    
    .exam-icon {
        width: 60px; height: 60px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: 16px; display: flex; align-items: center; justify-content: center;
        font-size: 26px; color: #fff; flex-shrink: 0;
        box-shadow: 0 8px 16px rgba(118, 181, 27, 0.2);
        transition: transform .3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .exam-card:hover .exam-icon { transform: scale(1.1) rotate(-6deg); }
    
    .exam-info { flex: 1; }
    .exam-info h3 { font-size: 18px; font-weight: 900; margin-bottom: 12px; color: #1e293b; }
    
    /* بيانات الميتا كبسولات دائرية */
    .exam-meta { display: flex; flex-wrap: wrap; gap: 10px; }
    
    .meta-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px; border-radius: 30px; font-size: 12px; font-weight: 800;
        border: 1.5px solid;
    }
    
    /* 🔘 السنة الأكاديمية */
    .mb-year { background: #f8fafc; color: #64748b; border-color: #e2e8f0; font-family: 'Inter', sans-serif !important; }
    
    /* 🌟 الدرجة الكلية */
    .mb-total { background: rgba(245, 158, 11, 0.05); color: #d97706; border-color: rgba(245, 158, 11, 0.15); }
    
    /* 🛡️ درجة النجاح */
    .mb-pass { background: rgba(16, 185, 129, 0.05); color: #10b981; border-color: rgba(16, 185, 129, 0.15); }

    /* 📊 عدد الأسئلة */
    .mb-questions { background: rgba(6, 182, 212, 0.05); color: #0891b2; border-color: rgba(6, 182, 212, 0.15); }

    /* زر بدء التقديم الفخم */
    .btn-start {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #fff; padding: 12px 28px; border-radius: 14px; font-weight: 850; font-size: 13.5px;
        text-decoration: none; display: flex; align-items: center; gap: 8px; white-space: nowrap;
        transition: all .3s; flex-shrink: 0; box-shadow: var(--shadow-primary);
    }
    .btn-start:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(118, 181, 27, 0.35);
    }
    .btn-start i { font-size: 16px; transition: transform 0.3s; }
    .exam-card:hover .btn-start i { transform: translateX(-4px); }

    @media (max-width: 768px) {
        .exam-card { flex-direction: column; text-align: center; align-items: center; padding: 24px; gap: 16px; }
        .exam-meta { justify-content: center; }
        .btn-start { width: 100%; justify-content: center; }
    }
</style>
@endpush

@section('content')
<div class="exams-section">
    
    <!-- كبسولة الترجيع للمسار -->
    <div class="breadcrumb-row">
        <a href="{{ route('home') }}" class="btn-back">
            <i class="bi bi-arrow-right"></i> العودة لقائمة الصفوف
        </a>
    </div>

    <!-- ترويسة الصف الفاخرة -->
    <div class="page-hero">
        <h1>الامتحانات المتاحة لـ {{ $grade->name }}</h1>
        <p>يرجى اختيار أحد الامتحانات المتاحة بالأسفل لبدء تسجيل بياناتك والتقديم الفوري</p>
    </div>

    @if($exams->isEmpty())
        <div style="text-align:center; padding:80px 20px; background:#fff; border-radius:24px; border:1.5px dashed #cbd5e0; color:var(--text-muted)">
            <i class="bi bi-journal-x" style="font-size:52px; color:#cbd5e0; display:block; margin-bottom:16px"></i>
            <h3 style="font-weight: 850; color: #1e293b;">لا توجد امتحانات متاحة حالياً لهذا الصف</h3>
            <p style="font-size:14px; margin-top:6px;">يرجى العودة في وقت لاحق أو مراجعة لجنة القبول والامتحانات بالمدارس.</p>
        </div>
    @else
        @foreach($exams as $exam)
        <div class="exam-card">
            <div class="exam-icon"><i class="bi bi-file-earmark-ruled"></i></div>
            <div class="exam-info">
                <h3>{{ $exam->title }}</h3>
                <div class="exam-meta">
                    <span class="meta-badge mb-year" title="السنة الدراسية الفعالة">
                        <i class="bi bi-calendar2-event-fill"></i>
                        {{ $exam->academicYear->name }}
                    </span>
                    <span class="meta-badge mb-total" title="الدرجة الكلية القصوى">
                        <i class="bi bi-award-fill"></i>
                        الدرجة الكلية: {{ $exam->total_marks }}
                    </span>
                    <span class="meta-badge mb-pass" title="الحد الأدنى لدرجة النجاح">
                        <i class="bi bi-shield-check"></i>
                        درجة النجاح: {{ $exam->pass_marks }}
                    </span>
                    <span class="meta-badge mb-questions" title="إجمالي عدد الأسئلة المدرجة">
                        <i class="bi bi-list-ol"></i>
                        الأسئلة: {{ $exam->totalQuestionsCount() }} سؤالاً
                    </span>
                </div>
            </div>
            
            <a href="{{ route('exam.register', $exam) }}" class="btn-start">
                <span>ابدأ الامتحان الآن</span>
                <i class="bi bi-arrow-left-circle-fill"></i>
            </a>
        </div>
        @endforeach
    @endif
</div>
@endsection
