@extends('layouts.admin')
@section('title', 'تفاصيل نتيجة الطالب')
@section('page-title', 'تفاصيل نتيجة الطالب')

@section('content')
<div class="d-flex gap-3 align-center" style="margin-bottom:20px;flex-wrap:wrap">
    <a href="{{ route('admin.results.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-right"></i> رجوع</a>
    <a href="{{ route('admin.results.print', $studentExam) }}" target="_blank" class="btn btn-primary btn-sm">
        <i class="bi bi-printer"></i> طباعة النتيجة
    </a>
</div>

<div class="grid-2" style="margin-bottom:20px">
    <!-- بيانات الطالب -->
    <div class="card">
        <div class="card-header"><div class="card-title"><i class="bi bi-person text-primary"></i> بيانات الطالب</div></div>
        <div class="card-body">
            <table style="width:100%;font-size:14px">
                <tr><td style="color:var(--text-muted);padding:7px 0;width:45%">الاسم</td><td class="fw-bold">{{ $studentExam->student->name }}</td></tr>
                <tr><td style="color:var(--text-muted);padding:7px 0">الصف المتقدم إليه</td><td>{{ $studentExam->student->applyingGrade->name }}</td></tr>
                <tr><td style="color:var(--text-muted);padding:7px 0">المدرسة السابقة</td><td>{{ $studentExam->student->previous_school }}</td></tr>
                <tr><td style="color:var(--text-muted);padding:7px 0">المعدل السابق</td><td>{{ $studentExam->student->last_grade_average }}%</td></tr>
                <tr><td style="color:var(--text-muted);padding:7px 0">اسم ولي الأمر</td><td>{{ $studentExam->student->guardian_name }}</td></tr>
                <tr><td style="color:var(--text-muted);padding:7px 0">هاتف ولي الأمر</td><td dir="ltr">{{ $studentExam->student->guardian_phone }}</td></tr>
            </table>
        </div>
    </div>
    <!-- نتيجة الاختبار -->
    <div class="card">
        <div class="card-header"><div class="card-title"><i class="bi bi-journal-check text-primary"></i> نتيجة الاختبار</div></div>
        <div class="card-body">
            <div style="text-align:center;margin-bottom:20px">
                <div style="font-size:48px;font-weight:800;color:{{ $studentExam->isPassed() ? 'var(--success)' : 'var(--danger)' }}">
                    {{ $studentExam->score }}<span style="font-size:24px;color:var(--text-muted)">/{{ $studentExam->total_marks }}</span>
                </div>
                <div style="margin-top:8px">
                    @if($studentExam->isPassed())
                        <span class="badge badge-success" style="font-size:16px;padding:8px 20px">✓ ناجح</span>
                    @else
                        <span class="badge badge-danger" style="font-size:16px;padding:8px 20px">✗ راسب</span>
                    @endif
                </div>
            </div>
            <!-- Progress bar -->
            <div style="margin-bottom:16px">
                <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;color:var(--text-muted)">
                    <span>النسبة المئوية</span><span>{{ $studentExam->percentage() }}%</span>
                </div>
                <div style="height:10px;background:#e2e8f0;border-radius:5px;overflow:hidden">
                    <div style="height:100%;width:{{ $studentExam->percentage() }}%;background:{{ $studentExam->isPassed() ? 'var(--success)' : 'var(--danger)' }};border-radius:5px;transition:width .5s"></div>
                </div>
            </div>
            <table style="width:100%;font-size:14px">
                <tr><td style="color:var(--text-muted);padding:6px 0">درجة النجاح</td><td class="fw-bold">{{ $studentExam->pass_marks }}</td></tr>
                <tr><td style="color:var(--text-muted);padding:6px 0">إجابات صحيحة</td><td class="text-success fw-bold">{{ $studentExam->correct_answers }} / {{ $studentExam->total_questions }}</td></tr>
                <tr><td style="color:var(--text-muted);padding:6px 0">وقت التسليم</td><td>{{ $studentExam->submitted_at?->format('Y-m-d H:i') }}</td></tr>
            </table>
        </div>
    </div>
</div>

<!-- سجل الإجابات -->
<div class="card">
    <div class="card-header"><div class="card-title"><i class="bi bi-list-check text-primary"></i> سجل الإجابات التفصيلي</div></div>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>#</th><th>السؤال</th><th>إجابة الطالب</th><th>الإجابة الصحيحة</th><th>النتيجة</th></tr></thead>
            <tbody>
                @foreach($studentExam->answers as $i => $answer)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td style="max-width:280px">{{ Str::limit($answer->question->text, 70) }}</td>
                    <td>{{ $answer->chosenChoice?->text ?? '<span class="text-muted">لم يجب</span>' }}</td>
                    <td class="text-success">{{ $answer->question->correctChoice()?->text }}</td>
                    <td>
                        @if($answer->is_correct)
                            <span class="badge badge-success"><i class="bi bi-check"></i> صحيح</span>
                        @else
                            <span class="badge badge-danger"><i class="bi bi-x"></i> خطأ</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
