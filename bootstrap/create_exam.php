<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Exam;
use App\Models\ExamSubjectConfig;
use App\Models\Grade;
use App\Models\AcademicYear;

// السنة الحالية
$year = AcademicYear::where('is_current', true)->first();

// الصف المستهدف: الصف الثاني (الطلاب يتقدمون إليه)
$targetGrade = Grade::where('order', 2)->first();

// مصدر الأسئلة: الصف الأول (يحتوي على أسئلة)
$sourceGrade = Grade::where('order', 1)->first();

if (!$year || !$targetGrade || !$sourceGrade) {
    echo "خطأ: تأكد من وجود سنة دراسية وصفوف في قاعدة البيانات.\n";
    exit(1);
}

// إنشاء الاختبار
$exam = Exam::create([
    'academic_year_id' => $year->id,
    'grade_id'         => $targetGrade->id,
    'title'            => 'اختبار القبول للصف الثاني الابتدائي 2024-2025',
    'total_marks'      => 100,
    'pass_marks'       => 60,
    'is_active'        => true,
]);

// إضافة مصادر الأسئلة من الصف الأول
$count = 0;
foreach ($sourceGrade->subjects as $subject) {
    $available = $subject->questions()->count();
    if ($available > 0) {
        ExamSubjectConfig::create([
            'exam_id'        => $exam->id,
            'subject_id'     => $subject->id,
            'question_count' => min(5, $available),
        ]);
        $count++;
    }
}

echo "✓ تم إنشاء الاختبار: {$exam->title}\n";
echo "  الصف المستهدف: {$targetGrade->name}\n";
echo "  مصادر الأسئلة: {$count} مادة\n";
echo "  إجمالي الأسئلة: " . $exam->totalQuestionsCount() . " سؤال\n";
