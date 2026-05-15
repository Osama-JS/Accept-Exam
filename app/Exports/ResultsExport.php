<?php

namespace App\Exports;

use App\Models\StudentExam;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResultsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}

    public function query()
    {
        return StudentExam::with(['student', 'exam.grade', 'exam.academicYear'])
            ->when(isset($this->filters['grade_id']) && $this->filters['grade_id'],
                fn($q) => $q->whereHas('exam', fn($e) => $e->where('grade_id', $this->filters['grade_id'])))
            ->when(isset($this->filters['status']) && $this->filters['status'],
                fn($q) => $q->where('status', $this->filters['status']))
            ->latest();
    }

    public function headings(): array
    {
        return ['#', 'اسم الطالب', 'الصف المتقدم إليه', 'المدرسة السابقة', 'المعدل',
            'اسم ولي الأمر', 'هاتف ولي الأمر', 'اسم الاختبار', 'السنة الدراسية',
            'الدرجة', 'الدرجة الكلية', 'درجة النجاح', 'عدد الأسئلة الصحيحة',
            'إجمالي الأسئلة', 'النتيجة', 'تاريخ الاختبار'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->student->name,
            $row->exam->grade->name,
            $row->student->previous_school,
            $row->student->last_grade_average,
            $row->student->guardian_name,
            $row->student->guardian_phone,
            $row->exam->title,
            $row->exam->academicYear->name,
            $row->score,
            $row->total_marks,
            $row->pass_marks,
            $row->correct_answers,
            $row->total_questions,
            $row->status === 'pass' ? 'ناجح ✓' : 'راسب ✗',
            $row->submitted_at?->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
