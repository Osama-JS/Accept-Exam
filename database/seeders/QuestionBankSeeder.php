<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Subject;
use App\Models\Question;
use App\Models\Choice;
use Illuminate\Database\Seeder;

class QuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        // 1. تنظيف الأسئلة والخيارات القديمة لتجنب التكرار وتوفير بنك نظيف
        Question::query()->delete();

        // 2. الحصول على الصفوف الأساسية في النظام
        $grade1 = Grade::where('name', 'like', '%الأول الابتدائي%')->first();
        $grade7 = Grade::where('name', 'like', '%الأول المتوسط%')->first();
        $grade10 = Grade::where('name', 'like', '%الأول الثانوي%')->first();

        // في حال لم تكن موجودة، نأخذ أول صفوف كبديل
        $allGrades = Grade::ordered()->get();
        if (!$grade1 && isset($allGrades[0])) $grade1 = $allGrades[0];
        if (!$grade7 && isset($allGrades[6])) $grade7 = $allGrades[6];
        if (!$grade10 && isset($allGrades[9])) $grade10 = $allGrades[9];

        // 3. الحصول على المواد أو إنشائها وربطها
        $math = Subject::where('name', 'الرياضيات')->first();
        if (!$math) {
            $math = Subject::create(['name' => 'الرياضيات', 'icon' => '🔢']);
        }
        
        $arabic = Subject::where('name', 'اللغة العربية')->first();
        if (!$arabic) {
            $arabic = Subject::create(['name' => 'اللغة العربية', 'icon' => '📖']);
        }

        $science = Subject::where('name', 'العلوم')->first();
        if (!$science) {
            $science = Subject::create(['name' => 'العلوم', 'icon' => '🔬']);
        }

        $english = Subject::where('name', 'اللغة الإنجليزية')->first();
        if (!$english) {
            $english = Subject::create(['name' => 'اللغة الإنجليزية', 'icon' => '🇬🇧']);
        }

        $computer = Subject::where('name', 'الحاسب الآلي')->first();
        if (!$computer) {
            $computer = Subject::create(['name' => 'الحاسب الآلي', 'icon' => '💻']);
        }

        // 4. ربط المواد بالصفوف (عبر جدول التجسير Many-to-Many)
        if ($grade1) {
            $grade1->subjects()->syncWithoutDetaching([$math->id, $arabic->id]);
        }
        if ($grade7) {
            $grade7->subjects()->syncWithoutDetaching([$math->id, $science->id, $english->id]);
        }
        if ($grade10) {
            $grade10->subjects()->syncWithoutDetaching([$math->id, $english->id, $computer->id]);
        }

        // 5. إضافة الأسئلة الحقيقية

        // === أسئلة الصف الأول الابتدائي ===
        if ($grade1) {
            // أ. رياضيات
            $this->createQuestion($grade1->id, $math->id, 'ما هو ناتج جمع 3 + 2؟', 'easy', 'mcq', [
                ['text' => '5', 'is_correct' => true],
                ['text' => '4', 'is_correct' => false],
                ['text' => '6', 'is_correct' => false],
                ['text' => '3', 'is_correct' => false]
            ]);

            $this->createQuestion($grade1->id, $math->id, 'العدد الذي يأتي مباشرة بعد العدد 7 هو العدد 8.', 'easy', 'tf', [
                ['text' => 'صح', 'is_correct' => true],
                ['text' => 'خطأ', 'is_correct' => false]
            ]);

            // ب. لغة عربية
            $this->createQuestion($grade1->id, $arabic->id, 'ما هو الحرف الأول في كلمة "أرنب"؟', 'easy', 'mcq', [
                ['text' => 'أ', 'is_correct' => true],
                ['text' => 'ب', 'is_correct' => false],
                ['text' => 'ت', 'is_correct' => false],
                ['text' => 'ج', 'is_correct' => false]
            ]);

            $this->createQuestion($grade1->id, $arabic->id, 'حرف التاء هو آخر حروف الهجاء في اللغة العربية.', 'easy', 'tf', [
                ['text' => 'خطأ', 'is_correct' => true],
                ['text' => 'صح', 'is_correct' => false]
            ]);
        }

        // === أسئلة الصف الأول المتوسط ===
        if ($grade7) {
            // أ. علوم
            $this->createQuestion($grade7->id, $science->id, 'ما هو الكوكب الأكثر قرباً من الشمس في النظام الشمسي؟', 'medium', 'mcq', [
                ['text' => 'عطارد', 'is_correct' => true],
                ['text' => 'الزهرة', 'is_correct' => false],
                ['text' => 'المريخ', 'is_correct' => false],
                ['text' => 'الأرض', 'is_correct' => false]
            ]);

            $this->createQuestion($grade7->id, $science->id, 'تسمى عملية تحول الماء من الحالة السائلة إلى الحالة الغازية بـ:', 'medium', 'mcq', [
                ['text' => 'التبخر', 'is_correct' => true],
                ['text' => 'التكثف', 'is_correct' => false],
                ['text' => 'التجمد', 'is_correct' => false],
                ['text' => 'الانصهار', 'is_correct' => false]
            ]);

            $this->createQuestion($grade7->id, $science->id, 'الخلية هي وحدة البناء والوظيفة الأساسية في جسم جميع الكائنات الحية.', 'easy', 'tf', [
                ['text' => 'صح', 'is_correct' => true],
                ['text' => 'خطأ', 'is_correct' => false]
            ]);

            // ب. رياضيات
            $this->createQuestion($grade7->id, $math->id, 'ما هو حل المعادلة الجبرية التالية: 2س = 10؟', 'medium', 'mcq', [
                ['text' => 'س = 5', 'is_correct' => true],
                ['text' => 'س = 2', 'is_correct' => false],
                ['text' => 'س = 10', 'is_correct' => false],
                ['text' => 'س = 8', 'is_correct' => false]
            ]);

            $this->createQuestion($grade7->id, $math->id, 'مجموع قياسات زوايا المثلث الداخلية يساوي دائماً 180 درجة.', 'easy', 'tf', [
                ['text' => 'صح', 'is_correct' => true],
                ['text' => 'خطأ', 'is_correct' => false]
            ]);
        }

        // === أسئلة الصف الثالث الثانوي / الأول الثانوي ===
        if ($grade10) {
            // أ. رياضيات
            $this->createQuestion($grade10->id, $math->id, 'ما هي قيمة لوغاريتم العدد 100 للاساس 10 (log10 100)؟', 'hard', 'mcq', [
                ['text' => '2', 'is_correct' => true],
                ['text' => '10', 'is_correct' => false],
                ['text' => '1', 'is_correct' => false],
                ['text' => '3', 'is_correct' => false]
            ]);

            $this->createQuestion($grade10->id, $math->id, 'إذا كانت قيمة الجيب (sin) لزاوية ما تساوي 1، فإن هذه الزاوية بالدرجات هي:', 'hard', 'mcq', [
                ['text' => '90', 'is_correct' => true],
                ['text' => '45', 'is_correct' => false],
                ['text' => '60', 'is_correct' => false],
                ['text' => '0', 'is_correct' => false]
            ]);

            // ب. لغة إنجليزية
            $this->createQuestion($grade10->id, $english->id, 'Choose the correct option: "She ___ to the primary school yesterday."', 'medium', 'mcq', [
                ['text' => 'went', 'is_correct' => true],
                ['text' => 'goes', 'is_correct' => false],
                ['text' => 'go', 'is_correct' => false],
                ['text' => 'gone', 'is_correct' => false]
            ]);

            $this->createQuestion($grade10->id, $english->id, 'The English pronoun "They" can ONLY be used for plural animate subjects.', 'medium', 'tf', [
                ['text' => 'خطأ', 'is_correct' => true],
                ['text' => 'صح', 'is_correct' => false]
            ]);

            // ج. حاسب آلي
            $this->createQuestion($grade10->id, $computer->id, 'أي مما يلي يعتبر العقل المفكر والمسؤول عن معالجة البيانات في الحاسب؟', 'medium', 'mcq', [
                ['text' => 'وحدة المعالجة المركزية (CPU)', 'is_correct' => true],
                ['text' => 'ذاكرة الوصول العشوائي (RAM)', 'is_correct' => false],
                ['text' => 'القرص الصلب (Hard Disk)', 'is_correct' => false],
                ['text' => 'لوحة الأم (Motherboard)', 'is_correct' => false]
            ]);

            // أسئلة توصيل ومقالية إضافية
            $this->createQuestion($grade7->id, $science->id, 'صل كل مصطلح علمي بتعريفه الصحيح:', 'medium', 'matching', [
                ['text' => 'الخلية|وحدة البناء والوظيفة في الكائن الحي', 'is_correct' => true],
                ['text' => 'التبخر|تحول السائل إلى غاز بالحرارة', 'is_correct' => true],
                ['text' => 'التكثف|تحول الغاز إلى سائل بالبرودة', 'is_correct' => true],
                ['text' => 'النواة|مركز التحكم والنشاط في الخلية', 'is_correct' => true]
            ]);

            $this->createQuestion($grade7->id, $science->id, 'اكتب مقالاً مبسطاً تشرح فيه دور النباتات في الحفاظ على نسبة الأكسجين في الغلاف الجوي.', 'medium', 'essay', [
                ['text' => 'النباتات البناء الضوئي الأكسجين ثاني أكسيد الكربون', 'is_correct' => true]
            ]);

            $this->createQuestion($grade10->id, $computer->id, 'صل مكونات الحاسب بنوعها الوظيفي المناسب:', 'medium', 'matching', [
                ['text' => 'الشاشة|وحدة إخراج للبيانات والمعلومات', 'is_correct' => true],
                ['text' => 'لوحة المفاتيح|وحدة إدخال أساسية للحروف والأرقام', 'is_correct' => true],
                ['text' => 'المعالج CPU|وحدة المعالجة وتفسير العمليات', 'is_correct' => true],
                ['text' => 'القرص الصلب SSD|وحدة التخزين الدائم للبيانات', 'is_correct' => true]
            ]);
        }
    }

    private function createQuestion(int $gradeId, int $subjectId, string $text, string $difficulty, string $type, array $choices): void
    {
        $q = Question::create([
            'grade_id'   => $gradeId,
            'subject_id' => $subjectId,
            'text'       => $text,
            'difficulty' => $difficulty,
            'type'       => $type
        ]);

        foreach ($choices as $index => $c) {
            Choice::create([
                'question_id' => $q->id,
                'text'        => $c['text'],
                'is_correct'  => $c['is_correct'],
                'order'       => $index + 1
            ]);
        }
    }
}
