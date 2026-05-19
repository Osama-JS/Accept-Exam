<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::query()->with('grades')->withCount('questions');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('grade_id')) {
            $query->whereHas('grades', function($q) use ($request) {
                $q->where('grade_id', $request->grade_id);
            });
        }

        $subjects = $query->latest()->paginate(10)->withQueryString();
        $grades   = Grade::ordered()->get();
        $totalCount = Subject::count();

        return view('admin.subjects.index', compact('subjects', 'grades', 'totalCount'));
    }

    public function create()
    {
        $grades = Grade::ordered()->get();
        return view('admin.subjects.create', compact('grades'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'grade_ids'   => 'required|array|min:1',
            'grade_ids.*' => 'exists:grades,id',
            'name'        => 'required|string|max:100',
            'icon'        => 'nullable|string|max:10',
        ], [
            'grade_ids.required' => 'الصف الدراسي مطلوب.',
            'grade_ids.array'    => 'يجب اختيار صف دراسي واحد على الأقل.',
            'name.required'      => 'اسم المادة مطلوب.',
        ]);

        $subject = Subject::create([
            'name' => $data['name'],
            'icon' => $data['icon'],
        ]);
        $subject->grades()->sync($data['grade_ids']);

        return redirect()->route('admin.subjects.index')->with('success', 'تم إضافة المادة بنجاح.');
    }

    public function edit(Subject $subject)
    {
        $grades = Grade::ordered()->get();
        return view('admin.subjects.edit', compact('subject', 'grades'));
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'grade_ids'   => 'required|array|min:1',
            'grade_ids.*' => 'exists:grades,id',
            'name'        => 'required|string|max:100',
            'icon'        => 'nullable|string|max:10',
        ], [
            'grade_ids.required' => 'الصف الدراسي مطلوب.',
            'grade_ids.array'    => 'يجب اختيار صف دراسي واحد على الأقل.',
            'name.required'      => 'اسم المادة مطلوب.',
        ]);

        $subject->update([
            'name' => $data['name'],
            'icon' => $data['icon'],
        ]);
        $subject->grades()->sync($data['grade_ids']);

        return redirect()->route('admin.subjects.index')->with('success', 'تم تحديث المادة بنجاح.');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->questions()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف المادة لأنها تحتوي على أسئلة.');
        }
        $subject->grades()->detach();
        $subject->delete();
        return redirect()->route('admin.subjects.index')->with('success', 'تم حذف المادة بنجاح.');
    }

    // AJAX: جلب المواد حسب الصف
    public function byGrade(Grade $grade)
    {
        return response()->json($grade->subjects()->get(['subjects.id', 'subjects.name', 'subjects.icon']));
    }

    // AJAX: جلب إحصائيات الأسئلة التابعة للمادة حسب الصف الدراسي
    public function questionStats(Subject $subject, Grade $grade)
    {
        $questions = $subject->questions()->where('grade_id', $grade->id)->get(['difficulty', 'type']);
        
        return response()->json([
            'total' => $questions->count(),
            'difficulties' => [
                'easy'   => $questions->where('difficulty', 'easy')->count(),
                'medium' => $questions->where('difficulty', 'medium')->count(),
                'hard'   => $questions->where('difficulty', 'hard')->count(),
            ],
            'types' => [
                'mcq'      => $questions->where('type', 'mcq')->count(),
                'tf'       => $questions->where('type', 'tf')->count(),
                'matching' => $questions->where('type', 'matching')->count(),
                'essay'    => $questions->where('type', 'essay')->count(),
            ]
        ]);
    }
}
