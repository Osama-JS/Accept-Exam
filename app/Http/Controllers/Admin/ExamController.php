<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Setting;
use App\Models\Subject;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with(['grade' => function($q) {
            $q->withCount('students');
        }, 'academicYear'])->withCount('studentExams');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('grade_id')) {
            $query->where('grade_id', $request->grade_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'locked') {
                $query->where('is_active', false);
            }
        }

        $exams = $query->latest()->paginate(10);
        
        $grades = Grade::ordered()->get();
        $academicYears = AcademicYear::latest()->get();
        $totalCount = Exam::count();
        $activeCount = Exam::where('is_active', true)->count();

        return view('admin.exams.index', compact('exams', 'grades', 'academicYears', 'totalCount', 'activeCount'));
    }

    public function create()
    {
        $grades        = Grade::ordered()->get();
        $academicYears = AcademicYear::latest()->get();
        $currentYearId = Setting::getCurrentAcademicYearId();
        return view('admin.exams.create', compact('grades', 'academicYears', 'currentYearId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'academic_year_id'                  => 'required|exists:academic_years,id',
            'grade_id'                          => 'required|exists:grades,id',
            'title'                             => 'required|string|max:200',
            'total_marks'                       => 'required|integer|min:1',
            'pass_marks'                        => 'required|integer|min:1|lte:total_marks',
            'configs'                           => 'required|array|min:1',
            'configs.*.subject_id'              => 'required|exists:subjects,id',
            'configs.*.question_count'          => 'required|integer|min:1',
        ], [
            'configs.required'                  => 'يجب تحديد مادة واحدة على الأقل.',
            'pass_marks.lte'                    => 'درجة النجاح لا يمكن أن تتجاوز الدرجة الكلية.',
        ]);

        // التحقق: لا يمكن سحب أسئلة من نفس الصف المستهدف
        foreach ($request->configs as $config) {
            $subject = Subject::findOrFail($config['subject_id']);
            if ($subject->grade_id == $request->grade_id) {
                return back()->withErrors(['configs' => 'لا يمكن سحب أسئلة من نفس الصف المستهدف للاختبار.'])
                    ->withInput();
            }
            // التحقق من كفاية الأسئلة
            $available = $subject->questions()->count();
            if ($available < $config['question_count']) {
                return back()->withErrors(['configs' => "عدد الأسئلة المطلوبة من مادة \"{$subject->name}\" ({$config['question_count']}) يتجاوز المتاح ({$available})."])
                    ->withInput();
            }
        }

        $exam = Exam::create([
            'academic_year_id' => $data['academic_year_id'],
            'grade_id'         => $data['grade_id'],
            'title'            => $data['title'],
            'total_marks'      => $data['total_marks'],
            'pass_marks'       => $data['pass_marks'],
            'is_active'        => true,
        ]);

        foreach ($request->configs as $config) {
            $exam->subjectConfigs()->create([
                'subject_id'     => $config['subject_id'],
                'question_count' => $config['question_count'],
            ]);
        }

        return redirect()->route('admin.exams.index')->with('success', 'تم إنشاء الاختبار بنجاح.');
    }

    public function show(Exam $exam)
    {
        $exam->load(['grade', 'academicYear', 'subjectConfigs.subject.grade', 'studentExams.student']);
        return view('admin.exams.show', compact('exam'));
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('admin.exams.index')->with('success', 'تم حذف الاختبار بنجاح.');
    }

    public function toggle(Exam $exam)
    {
        $exam->update(['is_active' => !$exam->is_active]);
        $status = $exam->is_active ? 'مفعّل' : 'موقوف';
        return back()->with('success', "تم تغيير حالة الاختبار إلى: {$status}.");
    }
}
