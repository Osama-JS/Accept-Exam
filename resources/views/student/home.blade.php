@extends('layouts.student')
@section('title', 'نظام امتحانات القبول - الرئيسية')

@push('styles')
<style>
.hero {
    background: linear-gradient(135deg, #0c1222 0%, #1a1040 40%, #0e1528 100%);
    padding: 100px 40px 90px;
    text-align: center;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.hero::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse 50% 70% at 50% 40%, rgba(79,70,229,.2) 0%, transparent 65%),
                radial-gradient(ellipse 40% 50% at 80% 20%, rgba(124,58,237,.15) 0%, transparent 60%);
}
.hero::after {
    content: '';
    position: absolute; bottom: 0; left: 0; right: 0;
    height: 120px;
    background: linear-gradient(to top, var(--body-bg), transparent);
}
.hero-content { position: relative; z-index: 1; max-width: 720px; margin: 0 auto; animation: heroIn .8s ease-out; }
@keyframes heroIn { from { opacity:0; transform: translateY(24px); } to { opacity:1; transform: translateY(0); } }
.hero h1 { font-size: 44px; font-weight: 800; margin-bottom: 18px; line-height: 1.2; letter-spacing: -0.02em; }
.hero h1 span { background: linear-gradient(135deg, #818cf8, #c084fc, #f0abfc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.hero p { font-size: 18px; color: #94a3b8; margin-bottom: 36px; line-height: 1.7; }
.hero-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); border-radius: 30px; padding: 8px 20px; font-size: 13px; color: #a5b4fc; margin-bottom: 24px; backdrop-filter: blur(8px); animation: badgeIn .6s ease .2s both; }
@keyframes badgeIn { from { opacity:0; transform: translateY(10px); } }
/* Floating particles */
.particle { position: absolute; border-radius: 50%; background: rgba(99,102,241,.15); animation: float 20s infinite linear; pointer-events: none; }
.particle:nth-child(1) { width:6px; height:6px; top:20%; left:15%; animation-duration:22s; }
.particle:nth-child(2) { width:4px; height:4px; top:60%; left:75%; animation-duration:18s; animation-delay:3s; }
.particle:nth-child(3) { width:8px; height:8px; top:40%; left:45%; animation-duration:25s; animation-delay:7s; background:rgba(167,139,250,.1); }
.particle:nth-child(4) { width:5px; height:5px; top:80%; left:25%; animation-duration:20s; animation-delay:5s; }
.particle:nth-child(5) { width:3px; height:3px; top:30%; left:85%; animation-duration:16s; animation-delay:2s; }
@keyframes float { 0%{transform:translateY(0) translateX(0)} 25%{transform:translateY(-40px) translateX(20px)} 50%{transform:translateY(-20px) translateX(-15px)} 75%{transform:translateY(-50px) translateX(10px)} 100%{transform:translateY(0) translateX(0)} }

.grades-section { max-width: 940px; margin: -40px auto 60px; padding: 0 24px; position: relative; z-index: 2; }
.grades-section h2 { font-size: 26px; font-weight: 800; margin-bottom: 8px; text-align: center; letter-spacing: -0.01em; }
.grades-section .subtitle { text-align: center; color: var(--text-muted); margin-bottom: 40px; font-size: 15px; }

.grades-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: 20px; }
.grade-card {
    background: var(--card-bg);
    border: 1.5px solid var(--border);
    border-radius: 18px;
    padding: 28px;
    text-decoration: none;
    color: var(--text-main);
    display: flex; flex-direction: column; gap: 14px;
    transition: all .3s cubic-bezier(.4,0,.2,1);
    position: relative; overflow: hidden;
    animation: cardIn .5s ease-out both;
}
.grade-card:nth-child(1) { animation-delay: .1s; }
.grade-card:nth-child(2) { animation-delay: .2s; }
.grade-card:nth-child(3) { animation-delay: .3s; }
.grade-card:nth-child(4) { animation-delay: .4s; }
@keyframes cardIn { from { opacity:0; transform: translateY(20px); } }
.grade-card::before {
    content: '';
    position: absolute; top: 0; right: 0;
    width: 100%; height: 4px;
    background: linear-gradient(90deg, #4f46e5, #7c3aed, #a855f7);
    transform: scaleX(0); transform-origin: right;
    transition: transform .3s cubic-bezier(.4,0,.2,1);
}
.grade-card:hover { transform: translateY(-6px); box-shadow: 0 16px 48px rgba(79,70,229,.15); border-color: #a5b4fc; }
.grade-card:hover::before { transform: scaleX(1); }
.grade-icon { width: 54px; height: 54px; background: linear-gradient(135deg, rgba(79,70,229,.08), rgba(124,58,237,.08)); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 26px; transition: transform .3s; }
.grade-card:hover .grade-icon { transform: scale(1.1) rotate(-4deg); }
.grade-name { font-size: 17px; font-weight: 700; }
.grade-meta { font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
.grade-arrow { margin-right: auto; width: 34px; height: 34px; background: var(--body-bg); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary); transition: all .3s; }
.grade-card:hover .grade-arrow { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; transform: translateX(-4px); }

.steps-section { background: #fff; padding: 72px 24px; position: relative; }
.steps-section h2 { text-align: center; font-size: 24px; font-weight: 800; margin-bottom: 44px; }
.steps { display: flex; gap: 0; max-width: 860px; margin: 0 auto; position: relative; }
.steps::before { content: ''; position: absolute; top: 34px; right: 80px; left: 80px; height: 3px; background: linear-gradient(90deg, #4f46e5, #7c3aed, #a855f7); border-radius: 2px; opacity: .2; }
.step { flex: 1; text-align: center; padding: 0 16px; position: relative; }
.step-num { width: 68px; height: 68px; background: linear-gradient(135deg, #4f46e5, #7c3aed); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px; color: #fff; margin: 0 auto 18px; box-shadow: 0 6px 24px rgba(79,70,229,.3); position: relative; z-index: 1; transition: transform .3s, box-shadow .3s; }
.step:hover .step-num { transform: scale(1.1); box-shadow: 0 8px 32px rgba(79,70,229,.4); }
.step h3 { font-size: 15px; font-weight: 700; margin-bottom: 6px; }
.step p { font-size: 13px; color: var(--text-muted); line-height: 1.6; }
@media(max-width:640px){ .steps { flex-direction: column; gap: 24px; } .steps::before { display:none; } }
</style>
@endpush

@section('content')
<!-- Hero -->
<div class="hero">
    <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
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
