<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Question;
use App\Models\Student;
use App\Models\StudentExam;
use App\Models\Subject;

class DashboardController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $years = \App\Models\AcademicYear::orderBy('name', 'desc')->get();
        $currentYear = \App\Models\AcademicYear::where('is_current', true)->first();
        
        $selectedYearId = $request->get('academic_year_id', $currentYear?->id);
        $selectedYear = $years->find($selectedYearId);

        // Stats filtered by selected academic year
        $stats = [
            'grades'          => Grade::count(), // Grades are usually global
            'subjects'        => Subject::count(), // Subjects are usually global
            'questions'       => Question::count(), // Questions are global
            'exams'           => Exam::where('academic_year_id', $selectedYearId)->count(),
            'students'        => Student::whereHas('exams', function($q) use ($selectedYearId) {
                $q->where('academic_year_id', $selectedYearId);
            })->count(),
            'total_results'   => StudentExam::whereHas('exam', function($q) use ($selectedYearId) {
                $q->where('academic_year_id', $selectedYearId);
            })->count(),
            'passed'          => StudentExam::where('status', 'pass')
                ->whereHas('exam', function($q) use ($selectedYearId) {
                    $q->where('academic_year_id', $selectedYearId);
                })->count(),
            'failed'          => StudentExam::where('status', 'fail')
                ->whereHas('exam', function($q) use ($selectedYearId) {
                    $q->where('academic_year_id', $selectedYearId);
                })->count(),
        ];

        $recentResults = StudentExam::with(['student', 'exam.grade'])
            ->whereHas('exam', function($q) use ($selectedYearId) {
                $q->where('academic_year_id', $selectedYearId);
            })
            ->latest()
            ->take(8)
            ->get();

        // Data for Charts
        $resultsByGrade = Grade::all()->map(function($grade) use ($selectedYearId) {
            return [
                'name' => $grade->name,
                'passed' => StudentExam::where('status', 'pass')
                    ->whereHas('exam', function($q) use ($grade, $selectedYearId) {
                        $q->where('grade_id', $grade->id)->where('academic_year_id', $selectedYearId);
                    })->count(),
                'failed' => StudentExam::where('status', 'fail')
                    ->whereHas('exam', function($q) use ($grade, $selectedYearId) {
                        $q->where('grade_id', $grade->id)->where('academic_year_id', $selectedYearId);
                    })->count(),
            ];
        });

        return view('admin.dashboard', compact(
            'stats', 'recentResults', 'years', 'currentYear', 
            'selectedYearId', 'selectedYear', 'resultsByGrade'
        ));
    }
}
