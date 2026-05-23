<?php

namespace App\Exports;

use App\Models\StudentExam;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ResultsExport implements FromArray, WithStyles, ShouldAutoSize, WithDrawings
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function array(): array
    {
        $query = StudentExam::with(['student', 'exam.grade', 'exam.academicYear'])
            ->when(isset($this->filters['grade_id']) && $this->filters['grade_id'],
                fn($q) => $q->whereHas('exam', fn($e) => $e->where('grade_id', $this->filters['grade_id'])))
            ->when(isset($this->filters['status']) && $this->filters['status'],
                fn($q) => $q->where('status', $this->filters['status']))
            ->when(isset($this->filters['search']) && $this->filters['search'],
                fn($q) => $q->whereHas('student', fn($s) => $s->where('name', 'like', "%{$this->filters['search']}%")))
            ->latest()
            ->get();

        $totalApplicants = $query->count();
        $totalPass = $query->where('status', 'pass')->count();
        $totalFail = $totalApplicants - $totalPass;
        $successRate = $totalApplicants > 0 ? round(($totalPass / $totalApplicants) * 100) : 0;

        $date = now()->locale('ar-SA')->translatedFormat('j F Y');
        $time = now()->locale('ar-SA')->translatedFormat('h:i A');

        $rows = [
            // Row 1: Brand & Date
            ['', 'مدارس القيم الأهلية', '', '', '', '', '', '', '', '', '', '', '', '', '', '📋 نوع الملف: نتائج المتقدمين للاختبارات'],
            // Row 2: Subtitle & Time
            ['', 'نظام إدارة الامتحانات والمراحل التعليمية', '', '', '', '', '', '', '', '', '', '', '', '', '', '📅 تاريخ التصدير: ' . $date . ' | ' . $time],
            // Row 3: KPI Metrics Cards
            ['', '👥 إجمالي المتقدمين: ' . $totalApplicants, '🟢 الناجحين: ' . $totalPass, '🔴 الراسبين: ' . $totalFail, '📈 نسبة النجاح: ' . $successRate . '%', '', '', '', '', '', '', '', '', '', '', '🟢 حالة التقرير: معتمد'],
            // Row 4: Empty space separator
            ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
            // Row 5: Clean Dark Headers
            ['الترتيب', 'اسم الطالب', 'الصف المتقدم إليه', 'المدرسة السابقة', 'المعدل السابق', 'اسم ولي الأمر', 'هاتف ولي الأمر', 'اسم الاختبار', 'السنة الدراسية', 'الدرجة', 'الدرجة الكلية', 'درجة النجاح', 'الأسئلة الصحيحة', 'إجمالي الأسئلة', 'النتيجة', 'تاريخ الاختبار']
        ];

        $index = 1;
        foreach ($query as $row) {
            $rows[] = [
                '#' . $index++,
                optional($row->student)->name ?? 'غير محدد',
                optional(optional($row->exam)->grade)->name ?? '—',
                optional($row->student)->previous_school ?? 'غير محدد',
                optional($row->student)->last_grade_average ? optional($row->student)->last_grade_average . '%' : 'غير محدد',
                optional($row->student)->guardian_name ?? 'غير محدد',
                optional($row->student)->guardian_phone ?? 'غير محدد',
                optional($row->exam)->title ?? '—',
                optional(optional($row->exam)->academicYear)->name ?? '—',
                $row->score,
                $row->total_marks,
                $row->pass_marks,
                $row->correct_answers,
                $row->total_questions,
                $row->status === 'pass' ? 'ناجح ✓' : 'راسب ✗',
                $row->submitted_at?->locale('ar-SA')->translatedFormat('Y-m-d h:i A') ?? 'غير محدد',
            ];
        }

        // Add empty spacer row
        $rows[] = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        
        // Add footer row
        $rows[] = [
            'تم إنشاء هذا المستند تلقائياً عبر نظام إدارة مدارس القيم الذكية © ' . date('Y'),
            '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        // ── 1. إعدادات تخطيط الصفحة وتفعيل التصميم الحديث ──
        $sheet->setRightToLeft(true);
        $sheet->setShowGridLines(false); // إخفاء خطوط الشبكة التقليدية لمنح الصفحة مظهر الواجهة الاحترافية (Pure UI Layout)
        
        // 1. إعدادات الورقة الأساسية (A4 أفقي)
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        // 2. تفعيل الملاءمة الذكية (السر هنا لتصغير الجدول ليناسب الورقة)
        $sheet->getPageSetup()->setFitToPage(true); // تفعيل خاصية الملاءمة
        $sheet->getPageSetup()->setFitToWidth(1);   // إجبار الأعمدة على البقاء في عرض صفحة واحدة
        $sheet->getPageSetup()->setFitToHeight(0);  // السماح بتمدد الصفوف عمودياً لعدة صفحات إذا زادت البيانات

        // 3. تقليل الهوامش لتوفير أقصى مساحة ممكنة للجدول
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setBottom(0.5);

        // 4. توسيط الجدول في منتصف الورقة أفقياً (لمنظر جمالي عند الطباعة)
        $sheet->getPageSetup()->setHorizontalCentered(true);

        // 5. تصغير حجم الخط الافتراضي لكامل الشيت (يساعد الإكسيل على تجنب التصغير المفرط للورقة)
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10); 

        // ── بقية الكود الخاص بك ──
        $highestRow = $sheet->getHighestRow();
        $dataRowsCount = $highestRow - 7; // إجمالي الصفوف مستثنياً الهيدر والتذييل

        // دمج نصوص الترويسة الرئيسية
        $sheet->mergeCells('B1:J1');
        $sheet->mergeCells('B2:J2');
        // ── 2. تنسيق الهوية البصرية والعناوين (Brand Header) ──
        $sheet->getStyle('B1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '629716'], // Brand Dark Green
                'name' => 'Segoe UI'
            ]
        ]);
        
        $sheet->getStyle('B2')->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['rgb' => '64748B'], // Slate 500
                'name' => 'Segoe UI'
            ]
        ]);

        // تنسيق معلومات المستند الجانبية (Meta Info)
        $sheet->getStyle('P1:P2')->applyFromArray([
            'font' => [
                'size' => 9.5,
                'color' => ['rgb' => '64748B'],
                'name' => 'Segoe UI'
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ]);

        // ── 3. تنسيق بطاقات مؤشرات الأداء (KPI Cards Row 3) ──
        $sheet->getRowDimension(3)->setRowHeight(32);
        
        // بطاقة إجمالي المتقدمين - لون الهوية البصرية (أخضر هادئ)
        $sheet->getStyle('B3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => '629716'], 'name' => 'Segoe UI'], // Brand Dark Green
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5FBEA']], // Brand Light Green
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => [
                'outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'E0F2C2']]
            ]
        ]);

        // بطاقة إجمالي الناجحين - لون أخضر نجاح
        $sheet->getStyle('C3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => '166534'], 'name' => 'Segoe UI'], // Dark Mint Green
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']], // Light Mint Green
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => [
                'outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DCFCE7']]
            ]
        ]);

        // بطاقة إجمالي الراسبين - لون أحمر رسوب
        $sheet->getStyle('D3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => '991B1B'], 'name' => 'Segoe UI'], // Dark Red
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF2F2']], // Light Red
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => [
                'outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'FEE2E2']]
            ]
        ]);

        // بطاقة نسبة النجاح - لون أزرق إحصائي
        $sheet->getStyle('E3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => '0F766E'], 'name' => 'Segoe UI'], // Dark Teal
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDFA']], // Light Teal
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => [
                'outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CCFBF1']]
            ]
        ]);

        // حالة التقرير الجانبية - لون الهوية البصرية الرئيسي
        $sheet->getStyle('P3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => '76B51B'], 'name' => 'Segoe UI'], // Brand Green
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ]);

// ── 1. تقليص ارتفاعات صفوف الترويسة الرئيسية ──
       // ── 1. ضغط ارتفاعات صفوف الترويسة العلوية ──
// ── 1. ضغط ارتفاعات صفوف الترويسة العلوية ──
        $sheet->getRowDimension(1)->setRowHeight(18); // مضغوط جداً
        $sheet->getRowDimension(2)->setRowHeight(15); 
        $sheet->getRowDimension(4)->setRowHeight(8);  // مسافة تنفس صغيرة جداً

        // ── 2. ترويسة الجدول (Header) - تركيز عالي ──
        $sheet->getStyle('A5:P5')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 9, // أصغر مقاس مريح للترويسة
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Segoe UI'
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '629716'] 
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ]);
        $sheet->getRowDimension(5)->setRowHeight(24); // ارتفاع يبرز الترويسة قليلاً عن البيانات

        // ── 3. صفوف البيانات (Data Rows) - أقصى كثافة بيانية ──
        $dataStart = 6;
        $dataEnd = 5 + $dataRowsCount;

        if ($dataRowsCount > 0) {
            for ($row = $dataStart; $row <= $dataEnd; $row++) {
                // ارتفاع 20 هو المعيار الذهبي للبيانات المكتظة في UI/UX الإكسيل
                $sheet->getRowDimension($row)->setRowHeight(20); 
                
                $bgColor = ($row % 2 === 0) ? 'FFFFFF' : 'F9FCF5'; 
                
                $sheet->getStyle("A{$row}:P{$row}")->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'size' => 8, // الخط القياسي للبيانات الكثيفة
                        'color' => ['rgb' => '334155']
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $bgColor]
                    ],
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'F1F5F9'], // خطوط خفيفة جداً لمنع إرهاق العين
                        ]
                    ]
                ]);

                // محاذاة الأعمدة
                foreach (['A', 'E', 'G', 'J', 'K', 'L', 'M', 'N', 'O', 'P'] as $col) {
                    $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
                foreach (['B', 'C', 'D', 'F', 'H', 'I'] as $col) {
                    $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                }

                $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('76B51B')); 
                $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E3007')); 
                
                $sheet->getStyle("D{$row}")->getAlignment()->setWrapText(true);
                $sheet->getStyle("H{$row}")->getAlignment()->setWrapText(true);

                // ── شارات البيانات (Badges) - بحجم المايكرو (Micro UI) ──
                $sheet->getStyle("E{$row}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '629716'], 
                        'size' => 7.5 // تصغير الشارات لتتناسب مع ارتفاع الصف 20
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5FBEA'] 
                    ]
                ]);

                $statusVal = $sheet->getCell("O{$row}")->getValue();
                if (str_contains($statusVal, 'ناجح')) {
                    $sheet->getStyle("O{$row}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => '166534'], 
                            'size' => 7.5
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F0FDF4'] 
                        ]
                    ]);
                } else {
                    $sheet->getStyle("O{$row}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => '991B1B'], 
                            'size' => 7.5
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FEF2F2'] 
                        ]
                    ]);
                }
            }
        }

        // ── 4. التذييل (Footer) - مضغوط وغير مشتت ──
        $footerRow = $highestRow;
        $sheet->mergeCells("A{$footerRow}:P{$footerRow}");
        $sheet->getRowDimension($footerRow)->setRowHeight(18); // ارتفاع التذييل
        $sheet->getStyle("A{$footerRow}")->applyFromArray([
            'font' => [
                'name' => 'Segoe UI',
                'size' => 7, // خط صغير جداً لحقوق النسخ
                'color' => ['rgb' => '94A3B8'] // لون رمادي باهت لعدم سرقة الانتباه من البيانات
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8FAFC'] 
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ]);
       // ── تحديد قياسات مضغوطة ومدروسة للأعمدة (Compact UI) لملاءمة طباعة A4 ──
        // تم تقليص العرض الإجمالي بنسبة 30% لمنع تصغير الخطوط بشكل مفرط عند الطباعة
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(8);  // الترتيب/المعرف (كان 12)
        $sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(20); // الاسم الأساسي (كان 28)
        $sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(18); // نصوص فرعية (كان 25)
        $sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(18); // نصوص فرعية (كان 25)
        $sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(10); // شارات/أرقام (كان 14)
        $sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(18); // نصوص (كان 25)
        $sheet->getColumnDimension('G')->setAutoSize(false)->setWidth(12); // تواريخ/حالة (كان 18)
        $sheet->getColumnDimension('H')->setAutoSize(false)->setWidth(18); // نصوص (كان 25)
        $sheet->getColumnDimension('I')->setAutoSize(false)->setWidth(12); // تواريخ/حالة (كان 18)
        
        // أعمدة الأرقام والبيانات القصيرة جداً
        $sheet->getColumnDimension('J')->setAutoSize(false)->setWidth(8);  // (كان 12)
        $sheet->getColumnDimension('K')->setAutoSize(false)->setWidth(8);  // (كان 12)
        $sheet->getColumnDimension('L')->setAutoSize(false)->setWidth(8);  // (كان 12)
        $sheet->getColumnDimension('M')->setAutoSize(false)->setWidth(10); // (كان 16)
        $sheet->getColumnDimension('N')->setAutoSize(false)->setWidth(10); // (كان 16)
        
        $sheet->getColumnDimension('O')->setAutoSize(false)->setWidth(12); // النتيجة/الحالة (كان 18)
        $sheet->getColumnDimension('P')->setAutoSize(false)->setWidth(15); //
        return [];
    }

    public function drawings()
    {
        $logoPath = $this->getLogoPath();
        if (!$logoPath || !file_exists($logoPath)) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('School Logo');
        $drawing->setPath($logoPath);
        $drawing->setHeight(65);
        $drawing->setCoordinates('A1');
        
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(10);

        return [$drawing];
    }

    private function getLogoPath(): ?string
    {
        $logo = \App\Models\Setting::get('school_logo');
        if ($logo) {
            $path = storage_path('app/public/' . $logo);
            if (file_exists($path)) {
                return $path;
            }
        }
        
        $fallbackPath = public_path('assets/images/logo.png');
        if (file_exists($fallbackPath)) {
            return $fallbackPath;
        }
        
        $fallbackPath2 = public_path('images/school_icon.png');
        if (file_exists($fallbackPath2)) {
            return $fallbackPath2;
        }
        
        return null;
    }
}
