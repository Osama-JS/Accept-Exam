<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>نتيجة الطالب - {{ $studentExam->student->name }}</title>
<style>
    body { font-family: 'Arial', sans-serif; margin: 0; padding: 20px; color: #1e293b; font-size: 13px; direction: rtl; }
    .header { text-align: center; border-bottom: 3px solid #1e3a5f; padding-bottom: 16px; margin-bottom: 20px; }
    .header h1 { font-size: 20px; margin: 0 0 4px; color: #1e3a5f; }
    .header p { color: #64748b; margin: 0; font-size: 13px; }
    .result-badge { display: inline-block; padding: 8px 28px; border-radius: 30px; font-size: 16px; font-weight: bold; margin: 12px 0; }
    .pass { background: #d1fae5; color: #065f46; border: 2px solid #10b981; }
    .fail { background: #fee2e2; color: #7f1d1d; border: 2px solid #ef4444; }
    .score-box { font-size: 36px; font-weight: 900; color: #1e3a5f; text-align: center; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
    .info-block { border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; }
    .info-block h3 { font-size: 13px; color: #64748b; margin: 0 0 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; }
    table.info td { padding: 5px 0; font-size: 13px; }
    table.info td:first-child { color: #64748b; width: 45%; }
    table.answers { width: 100%; border-collapse: collapse; font-size: 12px; }
    table.answers th { background: #f1f5f9; padding: 8px 10px; text-align: right; font-weight: 600; color: #64748b; border: 1px solid #e2e8f0; }
    table.answers td { padding: 7px 10px; border: 1px solid #e2e8f0; }
    .correct { color: #10b981; font-weight: bold; }
    .wrong   { color: #ef4444; }
    .progress { height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden; margin: 4px 0; }
    .progress-fill { height: 100%; border-radius: 4px; }
    .footer { text-align: center; margin-top: 20px; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    @media print { body { margin: 0; } .no-print { display: none; } }
</style>
</head>
<body>

<div class="no-print" style="text-align:center;margin-bottom:16px">
    <button onclick="window.print()" style="padding:8px 20px;background:#1e3a5f;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:14px">🖨️ طباعة</button>
    <button onclick="window.close()" style="padding:8px 20px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:6px;cursor:pointer;font-size:14px;margin-right:8px">إغلاق</button>
</div>

<div class="header">
    <h1>🎓 نظام امتحانات القبول للمدارس</h1>
    <p>نتيجة اختبار القبول | {{ $studentExam->exam->academicYear->name }}</p>
</div>

<div style="text-align:center;margin-bottom:20px">
    <div class="score-box">{{ $studentExam->score }} / {{ $studentExam->total_marks }}</div>
    <div class="result-badge {{ $studentExam->isPassed() ? 'pass' : 'fail' }}">
        {{ $studentExam->isPassed() ? '✓ ناجح' : '✗ راسب' }}
    </div>
    <div>
        <div class="progress" style="max-width:300px;margin:8px auto">
            <div class="progress-fill" style="width:{{ $studentExam->percentage() }}%;background:{{ $studentExam->isPassed() ? '#10b981' : '#ef4444' }}"></div>
        </div>
        <small style="color:#64748b">النسبة: {{ $studentExam->percentage() }}% | درجة النجاح: {{ $studentExam->pass_marks }}</small>
    </div>
</div>

<div class="grid">
    <div class="info-block">
        <h3>بيانات الطالب</h3>
        <table class="info"><tbody>
            <tr><td>الاسم</td><td><strong>{{ $studentExam->student->name }}</strong></td></tr>
            <tr><td>الصف المتقدم إليه</td><td>{{ $studentExam->student->applyingGrade->name }}</td></tr>
            <tr><td>المدرسة السابقة</td><td>{{ $studentExam->student->previous_school }}</td></tr>
            <tr><td>المعدل السابق</td><td>{{ $studentExam->student->last_grade_average }}%</td></tr>
            <tr><td>ولي الأمر</td><td>{{ $studentExam->student->guardian_name }}</td></tr>
            <tr><td>هاتف ولي الأمر</td><td dir="ltr">{{ $studentExam->student->guardian_phone }}</td></tr>
        </tbody></table>
    </div>
    <div class="info-block">
        <h3>تفاصيل الاختبار</h3>
        <table class="info"><tbody>
            <tr><td>الاختبار</td><td><strong>{{ $studentExam->exam->title }}</strong></td></tr>
            <tr><td>الصف</td><td>{{ $studentExam->exam->grade->name }}</td></tr>
            <tr><td>السنة الدراسية</td><td>{{ $studentExam->exam->academicYear->name }}</td></tr>
            <tr><td>الدرجة</td><td>{{ $studentExam->score }} / {{ $studentExam->total_marks }}</td></tr>
            <tr><td>إجابات صحيحة</td><td>{{ $studentExam->correct_answers }} / {{ $studentExam->total_questions }}</td></tr>
            <tr><td>تاريخ الاختبار</td><td>{{ $studentExam->submitted_at?->format('Y-m-d H:i') }}</td></tr>
        </tbody></table>
    </div>
</div>

<!-- تفاصيل الإجابات -->
<h3 style="font-size:14px;color:#1e3a5f;margin-bottom:8px;border-bottom:2px solid #e2e8f0;padding-bottom:6px">سجل الإجابات التفصيلي</h3>
<table class="answers">
    <thead><tr><th>#</th><th>السؤال</th><th>إجابة الطالب</th><th>الصحيحة</th><th>النتيجة</th></tr></thead>
    <tbody>
        @foreach($studentExam->answers as $i => $answer)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $answer->question->text }}</td>
            <td class="{{ $answer->is_correct ? 'correct' : 'wrong' }}">{{ $answer->chosenChoice?->text ?? 'لم يجب' }}</td>
            <td class="correct">{{ $answer->question->correctChoice()?->text }}</td>
            <td class="{{ $answer->is_correct ? 'correct' : 'wrong' }}">{{ $answer->is_correct ? '✓' : '✗' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <p>تم الإصدار بتاريخ {{ now()->format('Y-m-d H:i') }} | نظام امتحانات القبول للمدارس</p>
</div>
</body>
</html>
