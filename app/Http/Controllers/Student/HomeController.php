<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Grade;

class HomeController extends Controller
{
    public function index()
    {
        $grades = Grade::ordered()
            ->whereHas('exams', fn($q) => $q->where('is_active', true))
            ->withCount(['exams' => fn($q) => $q->where('is_active', true)])
            ->get();

        return view('student.home', compact('grades'));
    }

    public function about()
    {
        return view('student.about');
    }

    public function exams(Grade $grade)
    {
        $exams = Exam::where('grade_id', $grade->id)
            ->where('is_active', true)
            ->with('academicYear')
            ->get();

        return view('student.exams', compact('grade', 'exams'));
    }
}
