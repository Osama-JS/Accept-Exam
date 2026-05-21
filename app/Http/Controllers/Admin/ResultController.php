<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\StudentExam;
use App\Exports\ResultsExport;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $grades  = Grade::ordered()->get();
        $query = StudentExam::with(['student', 'exam.grade', 'exam.academicYear']);

        if ($request->filled('grade_id')) {
            $query->whereHas('exam', fn($e) => $e->where('grade_id', $request->grade_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('student', fn($s) => $s->where('name', 'like', "%{$request->search}%"));
        }

        $results = $query->latest()->paginate(20)->withQueryString();
        
        $stats = [
            'total'  => StudentExam::count(),
            'passed' => StudentExam::where('status', 'pass')->count(),
            'failed' => StudentExam::where('status', 'fail')->count(),
        ];

        return view('admin.results.index', compact('results', 'grades', 'stats'));
    }

    public function show(StudentExam $studentExam)
    {
        $studentExam->load([
            'student',
            'exam.grade',
            'exam.academicYear',
            'answers.question.choices',
            'answers.chosenChoice',
        ]);
        return view('admin.results.show', compact('studentExam'));
    }

    public function print(StudentExam $studentExam)
    {
        $studentExam->load([
            'student',
            'exam.grade',
            'exam.academicYear',
            'answers.question.choices',
            'answers.chosenChoice',
        ]);

        $pdf = Pdf::loadView('admin.results.print', compact('studentExam'));
        return $pdf->stream("result-{$studentExam->result_token}.pdf");
    }

    public function export(Request $request)
    {
        return Excel::download(new ResultsExport($request->all()), 'نتائج-الاختبارات.xlsx');
    }
}
