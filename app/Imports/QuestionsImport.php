<?php

namespace App\Imports;

use App\Models\Choice;
use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class QuestionsImport implements ToCollection, WithStartRow, SkipsEmptyRows
{
    protected int $gradeId;
    protected int $subjectId;

    public int $importedCount = 0;
    public array $errors = [];

    /**
     * خريطة تحويل رقم النوع إلى النص المقابل
     * 1 = اختيار من متعدد (mcq)
     * 2 = صح أو خطأ (tf)
     * 3 = مقالي (essay)
     * 4 = توصيل (matching)
     */
    private array $typeMap = [
        '1' => 'mcq',
        '2' => 'tf',
        '3' => 'essay',
        '4' => 'matching',
    ];

    /**
     * خريطة تحويل رقم الصعوبة إلى النص المقابل
     * 1 = سهل
     * 2 = متوسط
     * 3 = صعب
     */
    private array $difficultyMap = [
        '1' => 'easy',
        '2' => 'medium',
        '3' => 'hard',
    ];

    public function __construct(int $gradeId, int $subjectId)
    {
        $this->gradeId   = $gradeId;
        $this->subjectId = $subjectId;
    }

    /**
     * ابدأ من الصف 3 (الصف 1 و2 عناوين توضيحية)
     */
    public function startRow(): int
    {
        return 3;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $rowIndex => $row) {
            $realRow = $rowIndex + 3; // رقم الصف الحقيقي في الملف

            // تجاهل الصفوف الفارغة
            $questionText = trim((string) ($row[0] ?? ''));
            if (empty($questionText)) {
                continue;
            }

            // ── التحقق من نوع السؤال ──
            $typeRaw = trim((string) ($row[1] ?? ''));
            if (!array_key_exists($typeRaw, $this->typeMap)) {
                $this->errors[] = "الصف {$realRow}: نوع السؤال '{$typeRaw}' غير صحيح. استخدم: 1=اختيار متعدد، 2=صح/خطأ، 3=مقالي، 4=توصيل.";
                continue;
            }
            $type = $this->typeMap[$typeRaw];

            // ── التحقق من مستوى الصعوبة ──
            $diffRaw = trim((string) ($row[2] ?? ''));
            if (!array_key_exists($diffRaw, $this->difficultyMap)) {
                $this->errors[] = "الصف {$realRow}: مستوى الصعوبة '{$diffRaw}' غير صحيح. استخدم: 1=سهل، 2=متوسط، 3=صعب.";
                continue;
            }
            $difficulty = $this->difficultyMap[$diffRaw];

            // ── جمع الخيارات (الأعمدة D → H = index 3 → 7) ──
            $choices = [];
            for ($i = 3; $i <= 7; $i++) {
                $val = trim((string) ($row[$i] ?? ''));
                if ($val !== '') {
                    $choices[] = $val;
                }
            }

            // ── رقم الإجابة الصحيحة (العمود I = index 8) ──
            $correctRaw = trim((string) ($row[8] ?? ''));

            // ── التحقق حسب النوع ──
            if ($type === 'essay') {
                // المقالي: يجب وجود جواب نموذجي واحد على الأقل
                if (empty($choices)) {
                    $this->errors[] = "الصف {$realRow}: السؤال المقالي يجب أن يحتوي على جواب نموذجي في خانة الخيار أ (العمود D).";
                    continue;
                }
            } elseif ($type === 'matching') {
                // التوصيل: جميع الخيارات صحيحة، يحتاج خيارين على الأقل
                if (count($choices) < 2) {
                    $this->errors[] = "الصف {$realRow}: سؤال التوصيل يجب أن يحتوي على خيارين على الأقل.";
                    continue;
                }
            } else {
                // mcq و tf: يجب وجود خيارين على الأقل
                if (count($choices) < 2) {
                    $this->errors[] = "الصف {$realRow}: يجب إضافة خيارين على الأقل.";
                    continue;
                }

                // يجب تحديد الإجابة الصحيحة
                if (!is_numeric($correctRaw) || (int)$correctRaw < 1 || (int)$correctRaw > count($choices)) {
                    $this->errors[] = "الصف {$realRow}: رقم الإجابة الصحيحة '{$correctRaw}' غير صحيح. يجب أن يكون بين 1 و" . count($choices) . ".";
                    continue;
                }
            }

            // ── إنشاء السؤال ──
            try {
                $question = Question::create([
                    'grade_id'   => $this->gradeId,
                    'subject_id' => $this->subjectId,
                    'text'       => $questionText,
                    'type'       => $type,
                    'difficulty' => $difficulty,
                ]);

                // ── إنشاء الخيارات ──
                foreach ($choices as $index => $choiceText) {
                    if ($type === 'essay' || $type === 'matching') {
                        $isCorrect = true;
                    } else {
                        $isCorrect = ($index + 1) == (int)$correctRaw;
                    }

                    $question->choices()->create([
                        'text'       => $choiceText,
                        'is_correct' => $isCorrect,
                        'order'      => $index,
                    ]);
                }

                $this->importedCount++;

            } catch (\Exception $e) {
                $this->errors[] = "الصف {$realRow}: خطأ غير متوقع — " . $e->getMessage();
            }
        }
    }
}
