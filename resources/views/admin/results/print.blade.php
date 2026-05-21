<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>نتيجة الطالب - {{ $studentExam->student->name }}</title>

<!-- Favicon and App Icons -->
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">

<style>
    body { font-family: 'sans-serif'; margin: 0; padding: 20px; color: #1e293b; font-size: 13px; direction: rtl; }
    .header { text-align: center; border-bottom: 3px solid #1e3a5f; padding-bottom: 16px; margin-bottom: 20px; }
    .header h1 { font-size: 20px; margin: 0 0 4px; color: #1e3a5f; }
    .header p { color: #64748b; margin: 0; font-size: 13px; }
    
    .result-badge { display: inline-block; padding: 8px 28px; border-radius: 30px; font-size: 16px; font-weight: bold; margin: 12px 0; }
    .pass { background: #d1fae5; color: #065f46; border: 2px solid #10b981; }
    .fail { background: #fee2e2; color: #7f1d1d; border: 2px solid #ef4444; }
    
    .score-box { font-size: 34px; font-weight: bold; color: #1e3a5f; text-align: center; }
    
    /* mPDF Compatible Grid layout using Tables */
    table.layout-table { width: 100%; border-collapse: separate; border-spacing: 16px 0; margin-bottom: 24px; margin-right: -8px; margin-left: -8px; }
    table.layout-table td.col { width: 50%; vertical-align: top; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 16px; background: #fafafa; }
    
    .info-block h3 { font-size: 14px; color: #1e3a5f; margin: 0 0 12px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; font-weight: bold; }
    
    table.info { width: 100%; border-collapse: collapse; }
    table.info td { padding: 6px 0; font-size: 13px; border-bottom: 1px dashed #e2e8f0; }
    table.info td:first-child { color: #64748b; width: 45%; font-weight: bold; }
    table.info tr:last-child td { border-bottom: none; }
    
    table.answers { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 10px; }
    table.answers th { background: #f8fafc; padding: 10px; text-align: right; font-weight: bold; color: #475569; border: 1.5px solid #e2e8f0; }
    table.answers td { padding: 8px 10px; border: 1px solid #e2e8f0; }
    
    .correct { color: #10b981; font-weight: bold; }
    .wrong   { color: #ef4444; font-weight: bold; }
    
    /* mPDF Compatible Progress Bar using Table */
    table.progress-table { width: 300px; margin: 12px auto 6px; border-collapse: collapse; height: 10px; }
    table.progress-table td { height: 10px; padding: 0; }
    
    .footer { text-align: center; margin-top: 30px; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 12px; }
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

<div style="text-align:center;margin-bottom:24px">
    <div class="score-box" dir="rtl">{{ $studentExam->score }} من {{ $studentExam->total_marks }}</div>
    <div class="result-badge {{ $studentExam->isPassed() ? 'pass' : 'fail' }}">
        {{ $studentExam->isPassed() ? '✓ ناجح' : '✗ راسب' }}
    </div>
    <div>
        <table class="progress-table">
            <tr>
                <td style="width:{{ $studentExam->percentage() }}%; background:{{ $studentExam->isPassed() ? '#10b981' : '#ef4444' }};"></td>
                <td style="width:{{ 100 - $studentExam->percentage() }}%; background:#e2e8f0;"></td>
            </tr>
        </table>
        <small style="color:#64748b">النسبة: %{{ $studentExam->percentage() }} | درجة النجاح: {{ $studentExam->pass_marks }}</small>
    </div>
</div>

<table class="layout-table">
    <tr>
        <td class="col">
            <div class="info-block">
                <h3>بيانات الطالب</h3>
                <table class="info">
                    <tbody>
                        <tr><td>الاسم</td><td><strong>{{ $studentExam->student->name }}</strong></td></tr>
                        <tr><td>الصف المتقدم إليه</td><td>{{ $studentExam->student->applyingGrade->name }}</td></tr>
                        <tr><td>المدرسة السابقة</td><td>{{ $studentExam->student->previous_school }}</td></tr>
                        <tr><td>المعدل السابق</td><td dir="ltr" style="text-align: right;">%{{ $studentExam->student->last_grade_average }}</td></tr>
                        <tr><td>ولي الأمر</td><td>{{ $studentExam->student->guardian_name }}</td></tr>
                        <tr><td>هاتف ولي الأمر</td><td dir="ltr" style="text-align: right;">{{ $studentExam->student->guardian_phone }}</td></tr>
                    </tbody>
                </table>
            </div>
        </td>
        <td class="col">
            <div class="info-block">
                <h3>تفاصيل الاختبار</h3>
                <table class="info">
                    <tbody>
                        <tr><td>الاختبار</td><td><strong>{{ $studentExam->exam->title }}</strong></td></tr>
                        <tr><td>الصف</td><td>{{ $studentExam->exam->grade->name }}</td></tr>
                        <tr><td>السنة الدراسية</td><td>{{ $studentExam->exam->academicYear->name }}</td></tr>
                        <tr><td>الدرجة</td><td dir="rtl">{{ $studentExam->score }} من {{ $studentExam->total_marks }}</td></tr>
                        <tr><td>إجابات صحيحة</td><td dir="rtl">{{ $studentExam->correct_answers }} من {{ $studentExam->total_questions }}</td></tr>
                        <tr><td>تاريخ الاختبار</td><td dir="ltr" style="text-align: right;">{{ $studentExam->submitted_at?->format('Y-m-d H:i') }}</td></tr>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
</table>

<!-- تفاصيل الإجابات -->
<h3 style="font-size:15px;color:#1e3a5f;margin-bottom:8px;border-bottom:2px solid #e2e8f0;padding-bottom:6px">سجل الإجابات التفصيلي</h3>
<table class="answers">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 40%;">السؤال</th>
            <th style="width: 25%;">إجابة الطالب</th>
            <th style="width: 20%;">الصحيحة</th>
            <th style="width: 10%; text-align: center;">النتيجة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($studentExam->answers as $i => $answer)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $answer->question->text }}</td>
            <td class="{{ $answer->is_correct ? 'correct' : 'wrong' }}">{{ $answer->chosenChoice?->text ?? 'لم يجب' }}</td>
            <td class="correct">{{ $answer->question->correctChoice()?->text }}</td>
            <td class="{{ $answer->is_correct ? 'correct' : 'wrong' }}" style="text-align: center; font-size: 14px;">{{ $answer->is_correct ? '✓' : '✗' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <p>تم الإصدار بتاريخ <span dir="ltr">{{ now()->format('Y-m-d H:i') }}</span> | نظام امتحانات القبول للمدارس</p>
</div>
</body>
</html>
