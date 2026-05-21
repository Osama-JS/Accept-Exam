<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Exports\GradesExport;
use Maatwebsite\Excel\Facades\Excel;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $query = Grade::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Add subjects filter
        if ($request->filled('subjects_filter')) {
            if ($request->subjects_filter === 'has_subjects') {
                $query->has('subjects');
            } elseif ($request->subjects_filter === 'no_subjects') {
                $query->doesntHave('subjects');
            }
        }

        // Add exams filter
        if ($request->filled('exams_filter')) {
            if ($request->exams_filter === 'has_exams') {
                $query->has('exams');
            } elseif ($request->exams_filter === 'no_exams') {
                $query->doesntHave('exams');
            }
        }

        $query->withCount(['subjects', 'exams']);

        // Add sorting
        if ($request->filled('sort_by')) {
            if ($request->sort_by === 'subjects_count') {
                $query->orderBy('subjects_count', 'desc');
            } elseif ($request->sort_by === 'exams_count') {
                $query->orderBy('exams_count', 'desc');
            } else {
                $query->ordered();
            }
        } else {
            $query->ordered();
        }

        $grades = $query->paginate(12);
        $totalCount = Grade::count();

        return view('admin.grades.index', compact('grades', 'totalCount'));
    }

    public function create()
    {
        return view('admin.grades.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:grades,name',
            'order'       => 'required|integer|min:1',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required'  => 'اسم الصف مطلوب.',
            'name.unique'    => 'هذا الصف موجود بالفعل.',
            'order.required' => 'الترتيب مطلوب.',
        ]);

        Grade::create($data);

        return redirect()->route('admin.grades.index')
            ->with('success', 'تم إضافة الصف الدراسي بنجاح.');
    }

    public function edit(Grade $grade)
    {
        return view('admin.grades.edit', compact('grade'));
    }

    public function update(Request $request, Grade $grade)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:grades,name,' . $grade->id,
            'order'       => 'required|integer|min:1',
            'description' => 'nullable|string|max:500',
        ]);

        $grade->update($data);

        return redirect()->route('admin.grades.index')
            ->with('success', 'تم تحديث الصف الدراسي بنجاح.');
    }

    public function destroy(Grade $grade)
    {
        if ($grade->subjects()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الصف لأنه يحتوي على مواد دراسية.');
        }

        $grade->delete();
        return redirect()->route('admin.grades.index')
            ->with('success', 'تم حذف الصف الدراسي بنجاح.');
    }

    public function getSubjects(Grade $grade)
    {
        // Fetch all subjects ordered by name
        $subjects = Subject::orderBy('name')->get();
        
        // Pluck already associated subject ids
        $associatedSubjectIds = $grade->subjects->pluck('id')->toArray();
        
        // Map to include flag and accurate double-constraint question count
        $mappedSubjects = $subjects->map(function ($subject) use ($grade, $associatedSubjectIds) {
            $questionsCount = Question::where('grade_id', $grade->id)
                ->where('subject_id', $subject->id)
                ->count();
                
            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'is_associated' => in_array($subject->id, $associatedSubjectIds),
                'questions_count' => $questionsCount,
            ];
        });
        
        return response()->json([
            'grade' => [
                'id' => $grade->id,
                'name' => $grade->name,
            ],
            'subjects' => $mappedSubjects,
        ]);
    }

    public function syncSubjects(Request $request, Grade $grade)
    {
        $request->validate([
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);
        
        $subjectIds = $request->input('subject_ids', []);
        
        // Sync relationships via pivot table
        $grade->subjects()->sync($subjectIds);
        
        return response()->json([
            'success' => true,
            'message' => 'تم مزامنة وتحديث ربط المواد الدراسية بالصف الدراسي بنجاح.',
        ]);
    }

    public function export(Request $request)
    {
        $gradeIds = $request->input('ids', []);
        
        $fileName = 'تقرير_الصفوف_الدراسية_' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new GradesExport($gradeIds), $fileName);
    }
}
