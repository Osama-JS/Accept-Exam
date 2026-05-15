<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $query = Grade::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $grades = $query->withCount(['subjects', 'exams'])->ordered()->paginate(10);
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
}
