@extends('layouts.student')
@section('title', 'نتيجة الاختبار')

@push('styles')
<style>
.result-page { max-width: 680px; margin: 48px auto; padding: 0 24px; }

.result-hero {
    border-radius: 20px;
    padding: 48px 32px;
    text-align: center;
    color: #fff;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
}
.result-hero.pass { background: linear-gradient(135deg, #065f46, #10b981); }
.result-hero.fail { background: linear-gradient(135deg, #7f1d1d, #ef4444); }
.result-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse 70% 60% at 50% 30%, rgba(255,255,255,.15) 0%, transparent 70%);
}
.result-hero .content { position: relative; z-index: 1; }
.result-hero .icon { font-size: 64px; margin-bottom: 12px; display: block; animation: bounce 0.8s ease; }
.result-hero h2 { font-size: 28px; font-weight: 800; margin-bottom: 6px; }
.result-hero .sub { font-size: 16px; opacity: .85; }
.score-display { margin: 20px 0; }
.score-num { font-size: 72px; font-weight: 900; line-height: 1; }
.score-denom { font-size: 32px; opacity: .7; }
.score-pct { font-size: 18px; opacity: .85; }

/* Progress ring */
.progress-ring { position: relative; width: 120px; height: 120px; margin: 0 auto 16px; }
.progress-ring svg { transform: rotate(-90deg); }
.progress-ring .track { fill: none; stroke: rgba(255,255,255,.2); stroke-width: 8; }
.progress-ring .fill { fill: none; stroke: #fff; stroke-width: 8; stroke-linecap: round; transition: stroke-dashoffset 1.5s ease; }
.progress-ring .center-text { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; }

.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
.info-card { background: #fff; border: 1.5px solid var(--border); border-radius: 14px; padding: 20px; }
.info-card h4 { font-size: 13px; color: var(--text-muted); margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }
.info-card table { width: 100%; font-size: 13px; }
.info-card td { padding: 5px 0; }
.info-card td:first-child { color: var(--text-muted); width: 50%; }
.info-card td:last-child { font-weight: 600; }

.actions { display: flex; gap: 12px; flex-wrap: wrap; }
.action-btn { flex: 1; min-width: 180px; padding: 13px 20px; border-radius: 12px; font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; border: none; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; transition: all .2s; }
.action-btn.primary { background: var(--primary); color: #fff; }
.action-btn.primary:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 16px rgba(37,99,235,.4); }
.action-btn.secondary { background: #f1f5f9; color: var(--text-main); border: 1px solid var(--border); }
.action-btn.secondary:hover { background: var(--border); }

@keyframes bounce { 0%,100%{transform:scale(1)} 50%{transform:scale(1.15)} }
@keyframes countUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
.score-num { animation: countUp .6s ease .3s both; }
@media(max-width:580px){ .info-grid{grid-template-columns:1fr} }
</style>
@endpush

@section('content')
<div class="result-page">
    <!-- Hero Result -->
    <div class="result-hero {{ $studentExam->isPassed() ? 'pass' : 'fail' }}">
        <div class="content">
            @if($studentExam->isPassed())
                <span class="icon">🎉</span>
                <h2>مبروك! لقد نجحت</h2>
                <p class="sub">أحسنت! حققت درجة مميزة في اختبار القبول</p>
            @else
                <span class="icon">💪</span>
                <h2>للأسف لم تنجح</h2>
                <p class="sub">لا تستسلم، يمكنك المحاولة مرة أخرى</p>
            @endif

            <div class="score-display">
                <div class="score-num">
                    {{ $studentExam->score }}
                    <span class="score-denom">/ {{ $studentExam->total_marks }}</span>
                </div>
                <div class="score-pct">{{ $studentExam->percentage() }}%</div>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="info-grid">
        <div class="info-card">
            <h4><i class="bi bi-person"></i> بيانات الطالب</h4>
            <table>
                <tr><td>الاسم</td><td>{{ $studentExam->student->name }}</td></tr>
                <tr><td>الصف</td><td>{{ $studentExam->student->applyingGrade->name }}</td></tr>
                <tr><td>المدرسة</td><td>{{ $studentExam->student->previous_school }}</td></tr>
                <tr><td>المعدل السابق</td><td>{{ $studentExam->student->last_grade_average }}%</td></tr>
            </table>
        </div>
        <div class="info-card">
            <h4><i class="bi bi-journal-check"></i> تفاصيل الاختبار</h4>
            <table>
                <tr><td>الدرجة</td><td class="text-{{ $studentExam->isPassed() ? 'success' : 'danger' }} fw-bold">{{ $studentExam->score }} / {{ $studentExam->total_marks }}</td></tr>
                <tr><td>درجة النجاح</td><td>{{ $studentExam->pass_marks }}</td></tr>
                <tr><td>إجابات صحيحة</td><td class="text-success">{{ $studentExam->correct_answers }} / {{ $studentExam->total_questions }}</td></tr>
                <tr><td>إجابات خاطئة</td><td class="text-danger">{{ $studentExam->total_questions - $studentExam->correct_answers }}</td></tr>
            </table>
        </div>
    </div>

    <!-- Summary bar -->
    <div style="background:#fff;border:1.5px solid var(--border);border-radius:14px;padding:20px 24px;margin-bottom:20px">
        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--text-muted);margin-bottom:8px">
            <span>درجتك: {{ $studentExam->score }}</span>
            <span>النجاح من: {{ $studentExam->pass_marks }}</span>
            <span>الكلي: {{ $studentExam->total_marks }}</span>
        </div>
        <div style="height:14px;background:#f1f5f9;border-radius:7px;overflow:hidden;position:relative">
            <!-- Pass line -->
            <div style="position:absolute;right:0;height:100%;width:{{ ($studentExam->pass_marks/$studentExam->total_marks)*100 }}%;background:rgba(245,158,11,.2);border-left:2px dashed #f59e0b"></div>
            <!-- Score fill -->
            <div style="height:100%;width:{{ $studentExam->percentage() }}%;background:{{ $studentExam->isPassed() ? 'linear-gradient(90deg,#10b981,#34d399)' : 'linear-gradient(90deg,#ef4444,#f87171)' }};border-radius:7px;transition:width 1s ease"></div>
        </div>
        <div style="text-align:center;margin-top:8px;font-size:13px;font-weight:700;color:{{ $studentExam->isPassed() ? '#10b981' : '#ef4444' }}">
            {{ $studentExam->percentage() }}%
        </div>
    </div>

    <!-- Actions -->
    <div class="actions">
        <a href="{{ route('home') }}" class="action-btn secondary">
            <i class="bi bi-house"></i> العودة للرئيسية
        </a>
        <a href="#" onclick="window.print()" class="action-btn primary">
            <i class="bi bi-printer"></i> طباعة النتيجة
        </a>
    </div>
</div>
@endsection
