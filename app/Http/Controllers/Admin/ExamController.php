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
            'duration_minutes'                  => 'required|integer|min:10',
            'pass_marks_percent'                => 'required|integer|min:1|max:100',
            'configs'                           => 'required|array|min:1',
            'configs.*.subject_id'              => 'required|exists:subjects,id',
            'configs.*.question_count'          => 'required|integer|min:1',
            'configs.*.marks_per_question'      => 'required|integer|min:1',
            'configs.*.easy_count'              => 'nullable|integer|min:0',
            'configs.*.easy_marks'              => 'nullable|integer|min:1',
            'configs.*.medium_count'            => 'nullable|integer|min:0',
            'configs.*.medium_marks'            => 'nullable|integer|min:1',
            'configs.*.hard_count'              => 'nullable|integer|min:0',
            'configs.*.hard_marks'              => 'nullable|integer|min:1',
            'configs.*.mcq_count'               => 'nullable|integer|min:0',
            'configs.*.tf_count'                => 'nullable|integer|min:0',
            'configs.*.matching_count'          => 'nullable|integer|min:0',
            'configs.*.essay_count'             => 'nullable|integer|min:0',
        ], [
            'configs.required'                  => 'يجب تحديد مادة واحدة على الأقل.',
        ]);

        $totalMarks = 0;

        foreach ($request->configs as $config) {
            $subject = Subject::findOrFail($config['subject_id']);
            if ($subject->grade_id == $request->grade_id) {
                return back()->withErrors(['configs' => 'لا يمكن سحب أسئلة من نفس الصف المستهدف للاختبار.'])->withInput();
            }
            $available = $subject->questions()->where('grade_id', $request->grade_id)->count();
            if ($available < $config['question_count']) {
                return back()->withErrors(['configs' => "عدد الأسئلة المطلوبة من مادة \"{$subject->name}\" ({$config['question_count']}) يتجاوز المتاح لهذه المادة في الصف المستهدف ({$available})."])->withInput();
            }

            $diffSum = (int)($config['easy_count'] ?? 0) + (int)($config['medium_count'] ?? 0) + (int)($config['hard_count'] ?? 0);
            if ($diffSum > 0 && $diffSum != $config['question_count']) {
                return back()->withErrors(['configs' => "في مادة {$subject->name}: مجموع مستويات الصعوبة ({$diffSum}) لا يساوي إجمالي الأسئلة المطلوبة ({$config['question_count']})."])->withInput();
            }

            $typeSum = (int)($config['mcq_count'] ?? 0) + (int)($config['tf_count'] ?? 0) + (int)($config['matching_count'] ?? 0) + (int)($config['essay_count'] ?? 0);
            if ($typeSum > 0 && $typeSum != $config['question_count']) {
                return back()->withErrors(['configs' => "في مادة {$subject->name}: مجموع أنواع الأسئلة ({$typeSum}) لا يساوي إجمالي الأسئلة المطلوبة ({$config['question_count']})."])->withInput();
            }

            if ($diffSum > 0) {
                $easyCount   = (int)($config['easy_count'] ?? 0);
                $easyMarks   = (int)($config['easy_marks'] ?? 1);
                $mediumCount = (int)($config['medium_count'] ?? 0);
                $mediumMarks = (int)($config['medium_marks'] ?? 1);
                $hardCount   = (int)($config['hard_count'] ?? 0);
                $hardMarks   = (int)($config['hard_marks'] ?? 1);
                
                $totalMarks += ($easyCount * $easyMarks) + ($mediumCount * $mediumMarks) + ($hardCount * $hardMarks);
            } else {
                $totalMarks += ($config['question_count'] * $config['marks_per_question']);
            }
        }

        $passMarks = (int)ceil($totalMarks * ($data['pass_marks_percent'] / 100));

        $exam = Exam::create([
            'academic_year_id' => $data['academic_year_id'],
            'grade_id'         => $data['grade_id'],
            'title'            => $data['title'],
            'total_marks'      => $totalMarks,
            'pass_marks'       => $passMarks,
            'duration_minutes' => $data['duration_minutes'],
            'is_active'        => true,
        ]);

        foreach ($request->configs as $config) {
            $exam->subjectConfigs()->create([
                'subject_id'         => $config['subject_id'],
                'question_count'     => $config['question_count'],
                'marks_per_question' => $config['marks_per_question'],
                'difficulties'       => [
                    'easy'   => [
                        'count' => (int)($config['easy_count'] ?? 0),
                        'marks' => (int)($config['easy_marks'] ?? 1),
                    ],
                    'medium' => [
                        'count' => (int)($config['medium_count'] ?? 0),
                        'marks' => (int)($config['medium_marks'] ?? 1),
                    ],
                    'hard'   => [
                        'count' => (int)($config['hard_count'] ?? 0),
                        'marks' => (int)($config['hard_marks'] ?? 1),
                    ],
                ],
                'types'              => [
                    'mcq'      => (int)($config['mcq_count'] ?? 0),
                    'tf'       => (int)($config['tf_count'] ?? 0),
                    'matching' => (int)($config['matching_count'] ?? 0),
                    'essay'    => (int)($config['essay_count'] ?? 0),
                ],
            ]);
        }

        return redirect()->route('admin.exams.index')->with('success', 'تم إنشاء الاختبار بنجاح. الدرجة الكلية هي: ' . $totalMarks);
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
