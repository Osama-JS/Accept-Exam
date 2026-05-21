<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Student\HomeController;
use App\Http\Controllers\Student\ExamController as StudentExamController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth (public)
    Route::get('login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout',[AuthController::class, 'logout'])->name('logout');

    // Protected
    Route::middleware('admin.auth')->group(function () {

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Grades
        Route::post('grades/export', [GradeController::class, 'export'])->name('grades.export');
        Route::get('grades/{grade}/subjects', [GradeController::class, 'getSubjects'])->name('grades.subjects');
        Route::post('grades/{grade}/subjects', [GradeController::class, 'syncSubjects'])->name('grades.subjects.sync');
        Route::resource('grades', GradeController::class)->except(['show']);

        // Subjects
        Route::resource('subjects', SubjectController::class)->except(['show']);
        Route::get('subjects/by-grade/{grade}', [SubjectController::class, 'byGrade'])
            ->name('subjects.by-grade');
        Route::get('subjects/{subject}/question-stats/{grade}', [SubjectController::class, 'questionStats'])
            ->name('subjects.question-stats');

        // Questions
        Route::delete('questions/bulk-destroy', [QuestionController::class, 'bulkDestroy'])
            ->name('questions.bulk-destroy');
        Route::resource('questions', QuestionController::class)->except(['show']);
        Route::get('questions/by-subject/{subject}', [QuestionController::class, 'bySubject'])
            ->name('questions.by-subject');

        // Academic Years
        Route::resource('academic-years', AcademicYearController::class)->except(['show']);
        Route::post('academic-years/{academicYear}/set-current',
            [AcademicYearController::class, 'setCurrent'])->name('academic-years.set-current');

        // Exams
        Route::resource('exams', ExamController::class)->except(['edit', 'update']);
        Route::get('exams/{exam}/toggle', [ExamController::class, 'toggle'])->name('exams.toggle');

        // Results
        Route::get('results',                        [ResultController::class, 'index'])->name('results.index');
        Route::get('results/export',                 [ResultController::class, 'export'])->name('results.export');
        Route::get('results/{studentExam}',          [ResultController::class, 'show'])->name('results.show');
        Route::get('results/{studentExam}/print',    [ResultController::class, 'print'])->name('results.print');

        // Settings
        Route::get('settings',  [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/grade/{grade}/exams', [HomeController::class, 'exams'])->name('student.exams');

Route::prefix('exam')->name('exam.')->group(function () {
    Route::get('{exam}',          [StudentExamController::class, 'register'])->name('register');
    Route::post('{exam}/start',   [StudentExamController::class, 'start'])->name('start');
    Route::get('session/take',    [StudentExamController::class, 'take'])->name('take');
    Route::post('session/submit', [StudentExamController::class, 'submit'])->name('submit');
    Route::get('result/{token}',  [StudentExamController::class, 'result'])->name('result');
});
