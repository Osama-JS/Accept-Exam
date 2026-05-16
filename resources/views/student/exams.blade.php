@extends('layouts.student')
@section('title', 'الاختبارات المتاحة - ' . $grade->name)

@push('styles')
<style>
.page-hero { background: linear-gradient(135deg, #0c1222 0%, #1a1040 50%, #0e1528 100%); color: #fff; padding: 48px 40px; text-align: center; position: relative; overflow: hidden; }
.page-hero::before { content:''; position:absolute; inset:0; background: radial-gradient(ellipse 50% 60% at 50% 50%, rgba(79,70,229,.15) 0%, transparent 65%); }
.page-hero h1 { font-size: 28px; font-weight: 800; margin-bottom: 8px; position: relative; }
.page-hero p { color: rgba(255,255,255,.65); font-size: 15px; position: relative; }
.exams-section { max-width: 780px; margin: 40px auto; padding: 0 24px; }
.exam-card {
    background: #fff; border: 1.5px solid var(--border); border-radius: 18px; padding: 28px;
    display: flex; justify-content: space-between; align-items: center; gap: 20px;
    margin-bottom: 16px; transition: all .3s cubic-bezier(.4,0,.2,1); text-decoration: none; color: inherit;
}
.exam-card:hover { border-color: #a5b4fc; box-shadow: 0 12px 40px rgba(79,70,229,.12); transform: translateY(-3px); }
.exam-icon { width: 58px; height: 58px; background: linear-gradient(135deg, #4f46e5, #7c3aed); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #fff; flex-shrink: 0; box-shadow: 0 4px 16px rgba(79,70,229,.3); transition: transform .3s; }
.exam-card:hover .exam-icon { transform: scale(1.08) rotate(-4deg); }
.exam-info h3 { font-size: 16px; font-weight: 700; margin-bottom: 6px; }
.exam-meta { display: flex; flex-wrap: wrap; gap: 12px; }
.meta-item { display: flex; align-items: center; gap: 4px; font-size: 13px; color: var(--text-muted); }
.btn-start { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; padding: 10px 26px; border-radius: 12px; font-weight: 700; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 8px; white-space: nowrap; transition: all .3s cubic-bezier(.4,0,.2,1); flex-shrink: 0; }
.btn-start:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(79,70,229,.4); }
</style>
@endpush

@section('content')
<div class="page-hero">
    <h1>{{ $grade->name }}</h1>
    <p>اختر الاختبار المناسب للبدء</p>
</div>

<div class="exams-section">
    <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--text-muted);text-decoration:none;font-size:13px;margin-bottom:24px">
        <i class="bi bi-arrow-right"></i> العودة لاختيار الصف
    </a>

    @if($exams->isEmpty())
        <div style="text-align:center;padding:60px;background:#fff;border-radius:16px;border:1px solid var(--border)">
            <div style="font-size:48px;margin-bottom:16px">📋</div>
            <h3 style="color:var(--text-muted)">لا توجد اختبارات متاحة لهذا الصف حالياً</h3>
        </div>
    @else
        @foreach($exams as $exam)
        <div class="exam-card">
            <div class="exam-icon">📝</div>
            <div class="exam-info" style="flex:1">
                <h3>{{ $exam->title }}</h3>
                <div class="exam-meta">
                    <div class="meta-item"><i class="bi bi-calendar3"></i> {{ $exam->academicYear->name }}</div>
                    <div class="meta-item"><i class="bi bi-award"></i> الدرجة الكلية: <strong>{{ $exam->total_marks }}</strong></div>
                    <div class="meta-item"><i class="bi bi-check-circle"></i> درجة النجاح: <strong>{{ $exam->pass_marks }}</strong></div>
                </div>
            </div>
            <a href="{{ route('exam.register', $exam) }}" class="btn-start">
                <i class="bi bi-play-circle"></i> ابدأ الاختبار
            </a>
        </div>
        @endforeach
    @endif
</div>
@endsection
