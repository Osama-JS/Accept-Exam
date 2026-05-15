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
        $query = Subject::query()->with('grade')->withCount('questions');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('grade_id')) {
            $query->where('grade_id', $request->grade_id);
        }

        $subjects = $query->latest()->paginate(10);
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
            'grade_id' => 'required|exists:grades,id',
            'name'     => 'required|string|max:100',
            'icon'     => 'nullable|string|max:10',
        ], [
            'grade_id.required' => 'الصف الدراسي مطلوب.',
            'grade_id.exists'   => 'الصف المحدد غير موجود.',
            'name.required'     => 'اسم المادة مطلوب.',
        ]);

        Subject::create($data);
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
            'grade_id' => 'required|exists:grades,id',
            'name'     => 'required|string|max:100',
            'icon'     => 'nullable|string|max:10',
        ]);

        $subject->update($data);
        return redirect()->route('admin.subjects.index')->with('success', 'تم تحديث المادة بنجاح.');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->questions()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف المادة لأنها تحتوي على أسئلة.');
        }
        $subject->delete();
        return redirect()->route('admin.subjects.index')->with('success', 'تم حذف المادة بنجاح.');
    }

    // AJAX: جلب المواد حسب الصف
    public function byGrade(Grade $grade)
    {
        return response()->json($grade->subjects()->get(['id', 'name', 'icon']));
    }
}
