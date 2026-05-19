<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Setting;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        $query = AcademicYear::withCount('exams');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $years = $query->latest()->paginate(10);
        $totalCount = AcademicYear::count();
        $currentYear = AcademicYear::where('is_current', true)->first();

        return view('admin.academic-years.index', compact('years', 'totalCount', 'currentYear'));
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:academic_years,name',
        ], ['name.unique' => 'هذه السنة الدراسية موجودة بالفعل.']);

        $academicYear = AcademicYear::create($data);
        
        if ($request->boolean('is_current')) {
            $academicYear->makeCurrent();
            Setting::set('current_academic_year_id', $academicYear->id);
        }
        
        return redirect()->route('admin.academic-years.index')->with('success', 'تم إضافة السنة الدراسية بنجاح.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:academic_years,name,' . $academicYear->id,
        ]);
        $academicYear->update($data);
        return redirect()->route('admin.academic-years.index')->with('success', 'تم تحديث السنة الدراسية بنجاح.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->exams()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف السنة لأنها مرتبطة باختبارات.');
        }
        $academicYear->delete();
        return redirect()->route('admin.academic-years.index')->with('success', 'تم الحذف بنجاح.');
    }

    public function setCurrent(AcademicYear $academicYear)
    {
        $academicYear->makeCurrent();
        Setting::set('current_academic_year_id', $academicYear->id);
        return back()->with('success', "تم تعيين \"{$academicYear->name}\" كالسنة الدراسية الحالية.");
    }
}
