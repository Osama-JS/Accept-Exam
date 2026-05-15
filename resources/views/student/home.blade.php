@extends('layouts.student')
@section('title', 'نظام امتحانات القبول - الرئيسية')

@push('styles')
<style>
.hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
    padding: 80px 40px;
    text-align: center;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.hero::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse 60% 80% at 50% 50%, rgba(37,99,235,.2) 0%, transparent 70%);
}
.hero-content { position: relative; z-index: 1; max-width: 700px; margin: 0 auto; }
.hero h1 { font-size: 40px; font-weight: 800; margin-bottom: 16px; line-height: 1.2; }
.hero h1 span { background: linear-gradient(135deg, #60a5fa, #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.hero p { font-size: 18px; color: #94a3b8; margin-bottom: 32px; }
.hero-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12); border-radius: 30px; padding: 6px 16px; font-size: 13px; color: #94a3b8; margin-bottom: 20px; }

.grades-section { max-width: 900px; margin: 60px auto; padding: 0 24px; }
.grades-section h2 { font-size: 24px; font-weight: 700; margin-bottom: 8px; text-align: center; }
.grades-section .subtitle { text-align: center; color: var(--text-muted); margin-bottom: 36px; }

.grades-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; }
.grade-card {
    background: var(--card-bg);
    border: 1.5px solid var(--border);
    border-radius: 16px;
    padding: 24px;
    text-decoration: none;
    color: var(--text-main);
    display: flex; flex-direction: column; gap: 12px;
    transition: all .25s;
    position: relative; overflow: hidden;
}
.grade-card::before {
    content: '';
    position: absolute; top: 0; right: 0;
    width: 100%; height: 4px;
    background: linear-gradient(90deg, var(--primary), #8b5cf6);
    transform: scaleX(0); transform-origin: right;
    transition: transform .25s;
}
.grade-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,.12); border-color: var(--primary); }
.grade-card:hover::before { transform: scaleX(1); }
.grade-icon { width: 52px; height: 52px; background: linear-gradient(135deg, rgba(37,99,235,.1), rgba(139,92,246,.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
.grade-name { font-size: 16px; font-weight: 700; }
.grade-meta { font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
.grade-arrow { margin-right: auto; width: 32px; height: 32px; background: var(--body-bg); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary); transition: background .2s; }
.grade-card:hover .grade-arrow { background: var(--primary); color: #fff; }

.steps-section { background: #fff; padding: 60px 24px; }
.steps-section h2 { text-align: center; font-size: 22px; font-weight: 700; margin-bottom: 36px; }
.steps { display: flex; gap: 0; max-width: 800px; margin: 0 auto; position: relative; }
.steps::before { content: ''; position: absolute; top: 32px; right: 80px; left: 80px; height: 2px; background: var(--border); }
.step { flex: 1; text-align: center; padding: 0 16px; position: relative; }
.step-num { width: 64px; height: 64px; background: linear-gradient(135deg, var(--primary), #8b5cf6); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #fff; margin: 0 auto 16px; box-shadow: 0 4px 16px rgba(37,99,235,.3); position: relative; z-index: 1; }
.step h3 { font-size: 14px; font-weight: 700; margin-bottom: 6px; }
.step p { font-size: 13px; color: var(--text-muted); }
</style>
@endpush

@section('content')
<!-- Hero -->
<div class="hero">
    <div class="hero-content">
        <div class="hero-badge"><i class="bi bi-shield-check"></i> اختبار آمن وموثوق</div>
        <h1><span>{{ \App\Models\Setting::get('school_name', 'نظام امتحانات القبول') }}</span><br>الإلكتروني</h1>
        <p>{{ \App\Models\Setting::get('welcome_message', 'اختبر نفسك وتقدم للصف الذي تريد بطريقة سهلة وعادلة') }}</p>
    </div>
</div>

<!-- Steps -->
<div class="steps-section">
    <h2>كيف يعمل النظام؟</h2>
    <div class="steps">
        <div class="step">
            <div class="step-num">📚</div>
            <h3>اختر الصف</h3>
            <p>اختر الصف الذي تريد التقدم إليه</p>
        </div>
        <div class="step">
            <div class="step-num">📝</div>
            <h3>أدخل بياناتك</h3>
            <p>أدخل بياناتك الشخصية ومعلومات المدرسة</p>
        </div>
        <div class="step">
            <div class="step-num">⏱️</div>
            <h3>أجب على الأسئلة</h3>
            <p>أجب على أسئلة الاختبار بهدوء وتركيز</p>
        </div>
        <div class="step">
            <div class="step-num">🏆</div>
            <h3>احصل على نتيجتك</h3>
            <p>ستظهر نتيجتك فوراً بعد الانتهاء</p>
        </div>
    </div>
</div>

<!-- Grades -->
<div class="grades-section">
    <h2>اختر الصف الدراسي</h2>
    <p class="subtitle">اختر الصف الذي تريد التقدم إليه لعرض الاختبارات المتاحة</p>

    @if($grades->isEmpty())
        <div style="text-align:center;padding:60px;color:var(--text-muted)">
            <div style="font-size:48px;margin-bottom:16px">📋</div>
            <h3>لا توجد اختبارات متاحة حالياً</h3>
            <p>سيتم إضافة الاختبارات قريباً</p>
        </div>
    @else
        <div class="grades-grid">
            @foreach($grades as $grade)
            <a href="{{ route('student.exams', $grade) }}" class="grade-card">
                <div class="grade-icon">🎓</div>
                <div>
                    <div class="grade-name">{{ $grade->name }}</div>
                    <div class="grade-meta">
                        <i class="bi bi-journal-check"></i>
                        {{ $grade->exams_count }} {{ $grade->exams_count == 1 ? 'اختبار' : 'اختبارات' }} متاحة
                    </div>
                </div>
                <div class="d-flex align-center">
                    <span style="font-size:13px;color:var(--text-muted)">بدء التقديم</span>
                    <div class="grade-arrow"><i class="bi bi-arrow-left"></i></div>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
