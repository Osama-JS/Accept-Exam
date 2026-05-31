<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class QuestionsTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'نموذج الأسئلة'   => new QuestionsTemplateSheet(),
            'دليل الاستخدام'  => new QuestionsTemplateGuideSheet(),
        ];
    }
}

// ─────────────────────────────────────────────────────────────────
// الورقة الأولى: نموذج الأسئلة
// ─────────────────────────────────────────────────────────────────
class QuestionsTemplateSheet implements FromArray, WithStyles
{
    public function array(): array
    {
        return [
            // الصف 1: ترويسة المدرسة والنظام
            [
                '',
                'نظام امتحانات القبول',
                '', '', '', '', '', '', '',
                '📋 نموذج استيراد الأسئلة',
            ],
            // الصف 2: أسماء الأعمدة (الرأس الفعلي للبيانات)
            [
                'نص السؤال (مطلوب)',
                'نوع السؤال',
                'مستوى الصعوبة',
                'الخيار أ',
                'الخيار ب',
                'الخيار ج',
                'الخيار د',
                'الخيار هـ',
                'رقم الإجابة الصحيحة',
            ],
            // ─── أمثلة: سؤال اختيار من متعدد (mcq) ───
            [
                'ما هي عاصمة المملكة العربية السعودية؟',
                '1',
                '1',
                'الرياض',
                'جدة',
                'مكة المكرمة',
                'الدمام',
                '',
                '1',
            ],
            [
                'كم عدد أيام الأسبوع؟',
                '1',
                '1',
                '5',
                '6',
                '7',
                '8',
                '',
                '3',
            ],
            // ─── مثال: سؤال صح أو خطأ (tf) ───
            [
                'الشمس تشرق من الغرب',
                '2',
                '1',
                'صح',
                'خطأ',
                '',
                '',
                '',
                '2',
            ],
            [
                'الماء يتجمد عند صفر درجة مئوية',
                '2',
                '2',
                'صح',
                'خطأ',
                '',
                '',
                '',
                '1',
            ],
            // ─── مثال: سؤال مقالي (essay) ───
            [
                'اشرح أهمية التعليم في بناء المجتمع',
                '3',
                '3',
                'التعليم هو أساس بناء الأجيال وتطوير المجتمعات وتحقيق التنمية المستدامة',
                '',
                '',
                '',
                '',
                '',
            ],
            // ─── مثال: سؤال توصيل (matching) ───
            [
                'صل بين الدولة وعاصمتها: (أ) السعودية (ب) مصر (ج) الإمارات',
                '4',
                '2',
                'الرياض',
                'القاهرة',
                'أبوظبي',
                '',
                '',
                '',
            ],
            // ─── سؤال متوسط الصعوبة للتوضيح ───
            [
                'ما هو الجذر التربيعي للعدد 144؟',
                '1',
                '2',
                '10',
                '11',
                '12',
                '13',
                '',
                '3',
            ],
            // ─── سؤال صعب ───
            [
                'أي من المعادلات التالية تمثل قانون نيوتن الثاني؟',
                '1',
                '3',
                'F = ma',
                'E = mc²',
                'PV = nRT',
                'F = kx',
                '',
                '1',
            ],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // ── إعداد عام للورقة ──
        $sheet->setRightToLeft(true);
        $sheet->setShowGridLines(false);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        // ── الصف 1: ترويسة المشروع ──
        $sheet->mergeCells('B1:I1');
        $sheet->getRowDimension(1)->setRowHeight(32);
        $sheet->getStyle('B1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 15,
                'color' => ['rgb' => '629716'],
                'name'  => 'Segoe UI',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getStyle('J1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 9,
                'color' => ['rgb' => '64748B'],
                'name'  => 'Segoe UI',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ── الصف 2: رأس الجدول (أعمدة البيانات) ──
        $sheet->getRowDimension(2)->setRowHeight(38);
        $sheet->getStyle('A2:I2')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 10,
                'color' => ['rgb' => 'FFFFFF'],
                'name'  => 'Segoe UI',
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '629716'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '4A7511'],
                ],
            ],
        ]);

        // ── صفوف البيانات (الأمثلة) ──
        $dataColors = [
            3  => ['bg' => 'F0FDF4', 'type_bg' => 'DCFCE7', 'type_color' => '166534'], // mcq - أخضر فاتح
            4  => ['bg' => 'F0FDF4', 'type_bg' => 'DCFCE7', 'type_color' => '166534'],
            5  => ['bg' => 'EFF6FF', 'type_bg' => 'DBEAFE', 'type_color' => '1D4ED8'], // tf  - أزرق فاتح
            6  => ['bg' => 'EFF6FF', 'type_bg' => 'DBEAFE', 'type_color' => '1D4ED8'],
            7  => ['bg' => 'FEF9C3', 'type_bg' => 'FEF08A', 'type_color' => '854D0E'], // essay - أصفر
            8  => ['bg' => 'FDF4FF', 'type_bg' => 'F3E8FF', 'type_color' => '7E22CE'], // matching - بنفسجي
            9  => ['bg' => 'F0FDF4', 'type_bg' => 'DCFCE7', 'type_color' => '166534'],
            10 => ['bg' => 'FEF2F2', 'type_bg' => 'FEE2E2', 'type_color' => '991B1B'], // hard - أحمر فاتح
        ];

        foreach ($dataColors as $row => $colors) {
            $sheet->getRowDimension($row)->setRowHeight(32);

            // خلفية الصف
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'font' => [
                    'size' => 10,
                    'name' => 'Segoe UI',
                    'color' => ['rgb' => '1E293B'],
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $colors['bg']],
                ],
                'alignment' => [
                    'vertical'  => Alignment::VERTICAL_CENTER,
                    'wrapText'  => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_HAIR,
                        'color'       => ['rgb' => 'E2E8F0'],
                    ],
                ],
            ]);

            // تمييز خلية النوع بلون مختلف
            $sheet->getStyle("B{$row}")->applyFromArray([
                'font' => [
                    'bold'  => true,
                    'size'  => 10,
                    'color' => ['rgb' => $colors['type_color']],
                    'name'  => 'Segoe UI',
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $colors['type_bg']],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // تمييز خلية الإجابة الصحيحة
            $sheet->getStyle("I{$row}")->applyFromArray([
                'font' => [
                    'bold'  => true,
                    'size'  => 11,
                    'color' => ['rgb' => '166534'],
                    'name'  => 'Segoe UI',
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DCFCE7'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // تمييز خلية الصعوبة
            $sheet->getStyle("C{$row}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 10,
                    'name' => 'Segoe UI',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // محاذاة نص السؤال
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 10,
                    'name' => 'Segoe UI',
                    'color' => ['rgb' => '1E293B'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
            ]);
        }

        // ── أعراض الأعمدة ──
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(45); // نص السؤال
        $sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(14); // نوع السؤال
        $sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(14); // الصعوبة
        $sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(22); // خيار أ
        $sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(22); // خيار ب
        $sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(22); // خيار ج
        $sheet->getColumnDimension('G')->setAutoSize(false)->setWidth(22); // خيار د
        $sheet->getColumnDimension('H')->setAutoSize(false)->setWidth(22); // خيار هـ
        $sheet->getColumnDimension('I')->setAutoSize(false)->setWidth(18); // الإجابة الصحيحة

        return [];
    }
}

// ─────────────────────────────────────────────────────────────────
// الورقة الثانية: دليل الاستخدام
// ─────────────────────────────────────────────────────────────────
class QuestionsTemplateGuideSheet implements FromArray, WithStyles
{
    public function array(): array
    {
        return [
            ['📘 دليل استخدام نموذج الأسئلة'],
            [''],
            ['── رموز نوع السؤال (العمود ب) ──'],
            ['الرقم', 'النوع', 'الوصف'],
            ['1', 'اختيار من متعدد (MCQ)', 'أسئلة ذات خيارات متعددة مع إجابة صحيحة واحدة'],
            ['2', 'صح أو خطأ (T/F)', 'اكتب "صح" في الخيار أ و"خطأ" في الخيار ب، ثم حدد الإجابة (1 أو 2)'],
            ['3', 'مقالي (Essay)', 'اكتب الجواب النموذجي في خانة الخيار أ فقط، ولا داعي لتحديد رقم الإجابة'],
            ['4', 'توصيل (Matching)', 'ضع العناصر في الخيارات، وجميعها ستُعتبر صحيحة تلقائياً'],
            [''],
            ['── رموز مستوى الصعوبة (العمود ج) ──'],
            ['الرقم', 'المستوى', 'الوصف'],
            ['1', 'سهل', 'أسئلة للمستوى الأساسي'],
            ['2', 'متوسط', 'أسئلة للمستوى المتوسط'],
            ['3', 'صعب', 'أسئلة للمستوى المتقدم'],
            [''],
            ['── ملاحظات مهمة ──'],
            ['✅ لا تعدّل الصفين الأول والثاني، ابدأ إدخال بياناتك من الصف الثالث فصاعداً'],
            ['✅ يمكنك إضافة ما تشاء من الأسئلة دون حد أقصى'],
            ['✅ تأكد من تطابق رقم الإجابة الصحيحة مع عدد الخيارات المدخلة'],
            ['✅ للأسئلة المقالية وأسئلة التوصيل، رقم الإجابة الصحيحة غير مطلوب'],
            ['✅ إذا فشل استيراد سؤال معين، سيتم تجاوزه وعرض سبب الفشل'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->setRightToLeft(true);
        $sheet->setShowGridLines(false);

        // عنوان رئيسي
        $sheet->mergeCells('A1:D1');
        $sheet->getRowDimension(1)->setRowHeight(38);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 14,
                'color' => ['rgb' => '629716'],
                'name'  => 'Segoe UI',
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5FBEA'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // عناوين الأقسام
        foreach ([3, 10] as $sectionRow) {
            $sheet->mergeCells("A{$sectionRow}:D{$sectionRow}");
            $sheet->getRowDimension($sectionRow)->setRowHeight(28);
            $sheet->getStyle("A{$sectionRow}")->applyFromArray([
                'font' => [
                    'bold'  => true,
                    'size'  => 10,
                    'color' => ['rgb' => 'FFFFFF'],
                    'name'  => 'Segoe UI',
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '629716'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // رأس جداول التفسير
        foreach ([4, 11] as $headerRow) {
            $sheet->getRowDimension($headerRow)->setRowHeight(30);
            $sheet->getStyle("A{$headerRow}:D{$headerRow}")->applyFromArray([
                'font' => [
                    'bold'  => true,
                    'size'  => 10,
                    'color' => ['rgb' => '1E293B'],
                    'name'  => 'Segoe UI',
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F1F5F9'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // عنوان الملاحظات
        $sheet->mergeCells('A16:D16');
        $sheet->getRowDimension(16)->setRowHeight(28);
        $sheet->getStyle('A16')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 10,
                'color' => ['rgb' => 'FFFFFF'],
                'name'  => 'Segoe UI',
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // صفوف الملاحظات
        for ($r = 17; $r <= 21; $r++) {
            $sheet->mergeCells("A{$r}:D{$r}");
            $sheet->getRowDimension($r)->setRowHeight(26);
            $sheet->getStyle("A{$r}")->applyFromArray([
                'font' => [
                    'size'  => 10,
                    'color' => ['rgb' => '1E293B'],
                    'name'  => 'Segoe UI',
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EFF6FF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(12);
        $sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(30);
        $sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(60);
        $sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(20);

        return [];
    }
}
