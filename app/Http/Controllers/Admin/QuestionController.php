<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $grades   = Grade::ordered()->get();
        
        $query = Question::with(['subject', 'grade', 'choices']);

        if ($request->filled('search')) {
            $query->where('text', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('grade_id')) {
            $query->where('grade_id', $request->grade_id);
            $grade = Grade::find($request->grade_id);
            if ($grade) {
                $subjects = $grade->subjects;
            } else {
                $subjects = collect();
            }
        } else {
            $subjects = Subject::orderBy('name')->get();
        }

        if ($request->filled('difficulty') && $request->difficulty !== 'all') {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $perPage = $request->integer('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $questions = $query->latest()->paginate($perPage)->withQueryString();
        $totalCount = Question::count();

        // جلب الفلاتر السريعة (جميع الصفوف والمواد المرتبطة وبها أسئلة)
        $quickFilters = [];
        $gradesWithSubjects = Grade::with('subjects')->ordered()->get();
        foreach ($gradesWithSubjects as $grade) {
            foreach ($grade->subjects as $subject) {
                $qCount = Question::where('grade_id', $grade->id)
                    ->where('subject_id', $subject->id)
                    ->count();
                
                if ($qCount > 0) {
                    $quickFilters[] = [
                        'grade_id' => $grade->id,
                        'grade_name' => $grade->name,
                        'subject_id' => $subject->id,
                        'subject_name' => $subject->name,
                        'count' => $qCount
                    ];
                }
            }
        }

        return view('admin.questions.index', compact('questions', 'grades', 'subjects', 'totalCount', 'quickFilters'));
    }

    public function create()
    {
        $grades = Grade::ordered()->get();
        return view('admin.questions.create', compact('grades'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'grade_id'                => 'required|exists:grades,id',
            'subject_id'              => 'required|exists:subjects,id',
            'text'                    => 'required|string',
            'type'                    => 'required|string|in:mcq,tf,essay,matching',
            'difficulty'              => 'required|string|in:easy,medium,hard',
            'choices'                 => 'required|array',
            'choices.*.text'          => 'required|string|max:500',
            'correct_choice'          => 'required_unless:type,matching|integer',
        ], [
            'grade_id.required'    => 'الصف الدراسي مطلوب.',
            'subject_id.required'  => 'المادة الدراسية مطلوبة.',
            'text.required'        => 'نص السؤال مطلوب.',
        ]);

        if ($request->type === 'essay' && count($request->choices) < 1) {
            return back()->withErrors(['choices' => 'يجب إدخال الجواب النموذجي للسؤال المقالي.'])->withInput();
        }
        if ($request->type !== 'essay' && count($request->choices) < 2) {
            return back()->withErrors(['choices' => 'يجب إضافة خيارين على الأقل.'])->withInput();
        }

        $question = Question::create([
            'grade_id'   => $data['grade_id'],
            'subject_id' => $data['subject_id'],
            'text'       => $data['text'],
            'type'       => $data['type'],
            'difficulty' => $data['difficulty'],
        ]);

        foreach ($request->choices as $index => $choice) {
            $isCorrect = ($request->type === 'matching' || $request->type === 'essay') 
                ? true 
                : ($index == $request->correct_choice);

            $question->choices()->create([
                'text'       => $choice['text'],
                'is_correct' => $isCorrect,
                'order'      => $index,
            ]);
        }

        return redirect()->route('admin.questions.index')->with('success', 'تم إضافة السؤال بنجاح.');
    }

    public function edit(Question $question)
    {
        $question->load('choices');
        $grades = Grade::ordered()->get();
        $subjects = $question->grade ? $question->grade->subjects : collect();
        return view('admin.questions.edit', compact('question', 'grades', 'subjects'));
    }

    public function update(Request $request, Question $question)
    {
        $data = $request->validate([
            'grade_id'       => 'required|exists:grades,id',
            'subject_id'     => 'required|exists:subjects,id',
            'text'           => 'required|string',
            'type'           => 'required|string|in:mcq,tf,essay,matching',
            'difficulty'     => 'required|string|in:easy,medium,hard',
            'choices'        => 'required|array',
            'choices.*.text' => 'required|string|max:500',
            'correct_choice' => 'required_unless:type,matching|integer',
        ]);

        if ($request->type === 'essay' && count($request->choices) < 1) {
            return back()->withErrors(['choices' => 'يجب إدخال الجواب النموذجي للسؤال المقالي.'])->withInput();
        }
        if ($request->type !== 'essay' && count($request->choices) < 2) {
            return back()->withErrors(['choices' => 'يجب إضافة خيارين على الأقل.'])->withInput();
        }

        $question->update([
            'grade_id'   => $data['grade_id'],
            'subject_id' => $data['subject_id'],
            'text'       => $data['text'],
            'type'       => $data['type'],
            'difficulty' => $data['difficulty'],
        ]);
        $question->choices()->delete();

        foreach ($request->choices as $index => $choice) {
            $isCorrect = ($request->type === 'matching' || $request->type === 'essay') 
                ? true 
                : ($index == $request->correct_choice);

            $question->choices()->create([
                'text'       => $choice['text'],
                'is_correct' => $isCorrect,
                'order'      => $index,
            ]);
        }

        return redirect()->route('admin.questions.index')->with('success', 'تم تحديث السؤال بنجاح.');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('admin.questions.index')->with('success', 'تم حذف السؤال بنجاح.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = explode(',', $request->input('ids', ''));
        $validIds = array_filter($ids, 'is_numeric');
        if (!empty($validIds)) {
            Question::whereIn('id', $validIds)->delete();
            return redirect()->route('admin.questions.index')->with('success', 'تم حذف الأسئلة المحددة بنجاح.');
        }
        return redirect()->route('admin.questions.index')->with('error', 'يرجى تحديد أسئلة للحذف.');
    }

    public function bySubject(Subject $subject)
    {
        return response()->json($subject->questions()->count());
    }
}
