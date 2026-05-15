@extends('layouts.student')
@section('title', 'الاختبارات المتاحة - ' . $grade->name)

@push('styles')
<style>
.page-hero { background: linear-gradient(135deg, #1e3a5f, #2d6a9f); color: #fff; padding: 40px; text-align: center; }
.page-hero h1 { font-size: 26px; font-weight: 800; margin-bottom: 8px; }
.page-hero p { color: rgba(255,255,255,.75); font-size: 15px; }
.exams-section { max-width: 760px; margin: 40px auto; padding: 0 24px; }
.exam-card {
    background: #fff; border: 1.5px solid var(--border); border-radius: 16px; padding: 24px;
    display: flex; justify-content: space-between; align-items: center; gap: 20px;
    margin-bottom: 16px; transition: all .25s; text-decoration: none; color: inherit;
}
.exam-card:hover { border-color: var(--primary); box-shadow: 0 8px 32px rgba(37,99,235,.1); transform: translateY(-2px); }
.exam-icon { width: 56px; height: 56px; background: linear-gradient(135deg, var(--primary), #8b5cf6); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 26px; color: #fff; flex-shrink: 0; }
.exam-info h3 { font-size: 16px; font-weight: 700; margin-bottom: 6px; }
.exam-meta { display: flex; flex-wrap: wrap; gap: 12px; }
.meta-item { display: flex; align-items: center; gap: 4px; font-size: 13px; color: var(--text-muted); }
.btn-start { background: var(--primary); color: #fff; padding: 10px 24px; border-radius: 10px; font-weight: 700; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 8px; white-space: nowrap; transition: all .2s; flex-shrink: 0; }
.btn-start:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 16px rgba(37,99,235,.4); }
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
