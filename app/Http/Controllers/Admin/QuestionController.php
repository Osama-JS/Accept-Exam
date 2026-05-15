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
        $subjects = collect();
        
        $query = Question::with(['subject.grade', 'choices']);

        if ($request->filled('search')) {
            $query->where('text', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('grade_id')) {
            $query->whereHas('subject', fn($s) => $s->where('grade_id', $request->grade_id));
            $subjects = Subject::where('grade_id', $request->grade_id)->get();
        }

        $questions = $query->latest()->paginate(15)->withQueryString();
        $totalCount = Question::count();

        return view('admin.questions.index', compact('questions', 'grades', 'subjects', 'totalCount'));
    }

    public function create()
    {
        $grades = Grade::ordered()->get();
        return view('admin.questions.create', compact('grades'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id'              => 'required|exists:subjects,id',
            'text'                    => 'required|string',
            'choices'                 => 'required|array|min:2',
            'choices.*.text'          => 'required|string|max:500',
            'choices.*.is_correct'    => 'nullable|boolean',
            'correct_choice'          => 'required|integer',
        ], [
            'subject_id.required'  => 'المادة الدراسية مطلوبة.',
            'text.required'        => 'نص السؤال مطلوب.',
            'choices.min'          => 'يجب إضافة خيارين على الأقل.',
            'correct_choice.required' => 'يجب تحديد الإجابة الصحيحة.',
        ]);

        $question = Question::create([
            'subject_id' => $data['subject_id'],
            'text'       => $data['text'],
        ]);

        foreach ($request->choices as $index => $choice) {
            $question->choices()->create([
                'text'       => $choice['text'],
                'is_correct' => ($index == $request->correct_choice),
                'order'      => $index,
            ]);
        }

        return redirect()->route('admin.questions.index')->with('success', 'تم إضافة السؤال بنجاح.');
    }

    public function edit(Question $question)
    {
        $question->load('choices');
        $grades = Grade::ordered()->get();
        $subjects = Subject::where('grade_id', $question->subject->grade_id)->get();
        return view('admin.questions.edit', compact('question', 'grades', 'subjects'));
    }

    public function update(Request $request, Question $question)
    {
        $data = $request->validate([
            'subject_id'     => 'required|exists:subjects,id',
            'text'           => 'required|string',
            'choices'        => 'required|array|min:2',
            'choices.*.text' => 'required|string|max:500',
            'correct_choice' => 'required|integer',
        ]);

        $question->update(['subject_id' => $data['subject_id'], 'text' => $data['text']]);
        $question->choices()->delete();

        foreach ($request->choices as $index => $choice) {
            $question->choices()->create([
                'text'       => $choice['text'],
                'is_correct' => ($index == $request->correct_choice),
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

    public function bySubject(Subject $subject)
    {
        return response()->json($subject->questions()->count());
    }
}
