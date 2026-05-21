<?php

namespace App\Exports;

use App\Models\Grade;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class GradesExport implements FromArray, WithStyles, ShouldAutoSize, WithDrawings
{
    private array $gradeIds;

    public function __construct(array $gradeIds = [])
    {
        $this->gradeIds = $gradeIds;
    }

    public function array(): array
    {
        $grades = Grade::query()
            ->withCount(['subjects', 'exams'])
            ->when(!empty($this->gradeIds), fn($q) => $q->whereIn('id', $this->gradeIds))
            ->ordered()
            ->get();

        $totalGrades = $grades->count();
        $totalSubjects = $grades->sum('subjects_count');
        $totalExams = $grades->sum('exams_count');

        $date = now()->locale('ar-SA')->translatedFormat('j F Y');
        $time = now()->locale('ar-SA')->translatedFormat('h:i A');

        $rows = [
            // Row 1: Brand & Date
            ['', 'مدارس القيم الأهلية', '', '', '📋 نوع الملف: إحصائيات الصفوف والمراحل'],
            // Row 2: Subtitle & Time
            ['', 'نظام إدارة الامتحانات والمراحل التعليمية', '', '', '📅 تاريخ التصدير: ' . $date . ' | ' . $time],
            // Row 3: KPI Metrics Cards
            ['', '🗂️ إجمالي المراحل: ' . $totalGrades, '📚 إجمالي المواد: ' . $totalSubjects, '📝 إجمالي الاختبارات: ' . $totalExams, '🟢 حالة التقرير: معتمد'],
            // Row 4: Empty space separator
            ['', '', '', '', ''],
            // Row 5: Clean Dark Headers
            ['الترتيب', 'اسم الصف الدراسي', 'الوصف والتفاصيل', 'المواد الدراسية', 'الاختبارات المقررة']
        ];

        foreach ($grades as $grade) {
            $rows[] = [
                '#' . $grade->order,
                $grade->name,
                $grade->description ?? 'لا يوجد وصف مخصص لهذا الصف الدراسي حالياً.',
                $grade->subjects_count . ' مواد',
                $grade->exams_count . ' اختبار'
            ];
        }

        // Add empty spacer row
        $rows[] = ['', '', '', '', ''];
        
        // Add footer row
        $rows[] = [
            'تم إنشاء هذا المستند تلقائياً عبر نظام إدارة مدارس القيم الذكية © ' . date('Y'),
            '',
            '',
            '',
            ''
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        // ── 1. إعدادات تخطيط الصفحة وتفعيل التصميم الحديث ──
        $sheet->setRightToLeft(true);
        $sheet->setShowGridLines(false); // إخفاء خطوط الشبكة التقليدية لمنح الصفحة مظهر الواجهة الاحترافية (Pure UI Layout)
        
        // ── ضبط ملاءمة الصفحة للعرض والطباعة لتناسب عرض الصفحة أفقياً ──
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE); // تخطيط أفقي لملاءمة الأعمدة العريضة
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4); // ورق A4 القياسي
        $sheet->getPageSetup()->setFitToPage(true); // ملاءمة التخطيط
        $sheet->getPageSetup()->setFitToWidth(1);   // الملاءمة على عرض صفحة واحدة أفقياً (تمنع قص الأعمدة)
        $sheet->getPageSetup()->setFitToHeight(0);  // ترك الارتفاع يمتد بحرية حسب عدد الصفوف
        
        $highestRow = $sheet->getHighestRow();
        $dataRowsCount = $highestRow - 7; // إجمالي الصفوف مستثنياً الهيدر والتذييل

        // دمج نصوص الترويسة الرئيسية
        $sheet->mergeCells('B1:D1');
        $sheet->mergeCells('B2:D2');

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
        $sheet->getStyle('E1:E2')->applyFromArray([
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
        
        // بطاقة إجمالي المراحل - لون الهوية البصرية (أخضر هادئ)
        $sheet->getStyle('B3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => '629716'], 'name' => 'Segoe UI'], // Brand Dark Green
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5FBEA']], // Brand Light Green (8% opacity)
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => [
                'outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'E0F2C2']]
            ]
        ]);

        // بطاقة إجمالي المواد - لون الهوية البصرية الثاني (أخضر عشبي)
        $sheet->getStyle('C3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => '4A7511'], 'name' => 'Segoe UI'],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EBF5D5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => [
                'outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'D1EAA4']]
            ]
        ]);

        // بطاقة إجمالي الاختبارات - لون الهوية البصرية الثالث (أخضر زيتوني)
        $sheet->getStyle('D3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => '3A5B0D'], 'name' => 'Segoe UI'],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5F0CC']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => [
                'outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'C2E08F']]
            ]
        ]);

        // حالة التقرير الجانبية - لون الهوية البصرية الرئيسي
        $sheet->getStyle('E3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => '76B51B'], 'name' => 'Segoe UI'], // Brand Green
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ]);

        // ارتفاعات صفوف الترويسة
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(15); // مسافة فاصلة فارغة

        // ── 4. تنسيق ترويسة الجدول المتناسقة مع الشعار (Brand Header Row 5) ──
        $sheet->getStyle('A5:E5')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Segoe UI'
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '629716'] // Brand Dark Green
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ]);
        $sheet->getRowDimension(5)->setRowHeight(40);

        // ── 5. تنسيق صفوف البيانات وتأثيرات الـ UI/UX ──
        $dataStart = 6;
        $dataEnd = 5 + $dataRowsCount;

        if ($dataRowsCount > 0) {
            for ($row = $dataStart; $row <= $dataEnd; $row++) {
                $sheet->getRowDimension($row)->setRowHeight(34); // ارتفاع مريح للأسطر
                
                // تظليل متبادل خفيف جداً من وحي لون الشعار لمنع التشتت (Brand Zebra Striping)
                $bgColor = ($row % 2 === 0) ? 'FFFFFF' : 'F9FCF5'; // Soft brand green tint
                
                $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'size' => 10,
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
                        // استخدام خطوط أفقية ناعمة من درجات لون الشعار بدون خطوط عمودية
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'EBF3DC'], // Soft green-gray border
                        ]
                    ]
                ]);

                // محاذاة الأعمدة
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // تمييز نصوص الترتيب والأسماء بألوان متناسقة مع الهوية البصرية
                $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('76B51B')); // Brand Green
                $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E3007')); // Very dark green
                
                // التفاف تلقائي للنصوص الطويلة في عمود الوصف
                $sheet->getStyle("C{$row}")->getAlignment()->setWrapText(true);

                // ── شارة الويب (Badge) الخاصة بالمواد (Brand Light Green) ──
                $sheet->getStyle("D{$row}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '629716'], // Brand Dark Green
                        'size' => 9.5
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5FBEA'] // Brand Light Green
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'E0F2C2']
                        ]
                    ]
                ]);

                // ── شارة الويب (Badge) الخاصة بالاختبارات (Mint Green) ──
                $sheet->getStyle("E{$row}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '166534'], // Dark Mint Green
                        'size' => 9.5
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F0FDF4'] // Light Mint Green
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'DCFCE7']
                        ]
                    ]
                ]);
            }
        }

        // ── 6. تنسيق التذييل الإداري (Footer) ──
        $footerRow = $highestRow;
        $sheet->mergeCells("A{$footerRow}:E{$footerRow}");
        $sheet->getRowDimension($footerRow)->setRowHeight(35);
        $sheet->getStyle("A{$footerRow}")->applyFromArray([
            'font' => [
                'name' => 'Segoe UI',
                'size' => 8.5,
                'color' => ['rgb' => '629716'] // Brand Dark Green
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5FBEA'] // Brand Light Green
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ]);

        // تحديد قياسات ثابتة ومثالية للأعمدة لمنح مساحة عرض أكبر ومريحة للقرأءة
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(18);
        $sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(35);
        $sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(65);
        $sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(26);
        $sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(26);

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
