@extends('layouts.admin')
@section('title', 'تفاصيل الاختبار')
@section('page-title', 'تفاصيل الاختبار')

@section('content')
<div class="d-flex gap-3 align-center" style="margin-bottom:20px;flex-wrap:wrap">
    <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-right"></i> رجوع</a>
    <a href="{{ route('admin.exams.toggle', $exam) }}" class="btn {{ $exam->is_active ? 'btn-warning' : 'btn-success' }} btn-sm">
        <i class="bi bi-toggle-{{ $exam->is_active ? 'on' : 'off' }}"></i>
        {{ $exam->is_active ? 'إيقاف الاختبار' : 'تفعيل الاختبار' }}
    </a>
</div>

<div class="grid-2" style="margin-bottom:20px">
    <div class="card">
        <div class="card-header"><div class="card-title"><i class="bi bi-info-circle text-primary"></i> معلومات الاختبار</div></div>
        <div class="card-body">
            <table style="font-size:14px;width:100%">
                <tr><td style="color:var(--text-muted);padding:6px 0;width:40%">العنوان</td><td class="fw-bold">{{ $exam->title }}</td></tr>
                <tr><td style="color:var(--text-muted);padding:6px 0">الصف المستهدف</td><td><span class="badge badge-primary">{{ $exam->grade->name }}</span></td></tr>
                <tr><td style="color:var(--text-muted);padding:6px 0">السنة الدراسية</td><td>{{ $exam->academicYear->name }}</td></tr>
                <tr><td style="color:var(--text-muted);padding:6px 0">الدرجة الكلية</td><td class="fw-bold">{{ $exam->total_marks }}</td></tr>
                <tr><td style="color:var(--text-muted);padding:6px 0">درجة النجاح</td><td class="fw-bold text-success">{{ $exam->pass_marks }}</td></tr>
                <tr><td style="color:var(--text-muted);padding:6px 0">إجمالي الأسئلة</td><td>{{ $exam->totalQuestionsCount() }} سؤال</td></tr>
                <tr><td style="color:var(--text-muted);padding:6px 0">درجة كل سؤال</td><td>{{ number_format($exam->markPerQuestion(), 2) }}</td></tr>
                <tr><td style="color:var(--text-muted);padding:6px 0">الحالة</td>
                    <td>@if($exam->is_active)<span class="badge badge-success">مفعّل</span>@else<span class="badge badge-danger">موقوف</span>@endif</td></tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title"><i class="bi bi-book text-primary"></i> مصادر الأسئلة</div></div>
        <div class="card-body">
            @foreach($exam->subjectConfigs as $config)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)">
                <div>
                    <div class="fw-bold">{{ $config->subject->icon }} {{ $config->subject->name }}</div>
                    <div style="font-size:12px;color:var(--text-muted)">{{ $config->subject->grade->name }}</div>
                </div>
                <span class="badge badge-info">{{ $config->question_count }} سؤال</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-people text-primary"></i> الطلاب المتقدمون ({{ $exam->studentExams->count() }})</div>
        <a href="{{ route('admin.results.index') }}" class="btn btn-secondary btn-sm">عرض كل النتائج</a>
    </div>
    <div class="table-wrapper">
        @if($exam->studentExams->isEmpty())
            <div class="empty-state"><i class="bi bi-person-x"></i><h3>لم يتقدم أي طالب بعد</h3></div>
        @else
        <table>
            <thead><tr><th>الطالب</th><th>الدرجة</th><th>النتيجة</th><th>التاريخ</th><th></th></tr></thead>
            <tbody>
                @foreach($exam->studentExams as $se)
                <tr>
                    <td class="fw-bold">{{ $se->student->name }}</td>
                    <td>{{ $se->score }} / {{ $se->total_marks }}</td>
                    <td>@if($se->isPassed())<span class="badge badge-success">ناجح</span>@else<span class="badge badge-danger">راسب</span>@endif</td>
                    <td class="text-muted">{{ $se->submitted_at?->format('Y-m-d H:i') }}</td>
                    <td><a href="{{ route('admin.results.show', $se) }}" class="btn btn-secondary btn-sm btn-icon"><i class="bi bi-eye"></i></a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
