@extends('layouts.admin')
@section('title', 'الاختبارات')
@section('page-title', 'الاختبارات')

@section('content')
<!-- Stats Section -->
<div class="stats-grid mb-4">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="bi bi-journal-check"></i></div>
        <div>
            <div class="stat-value">{{ $totalCount }}</div>
            <div class="stat-label">إجمالي الاختبارات</div>
        </div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
        <div>
            <div class="stat-value">{{ $activeCount }}</div>
            <div class="stat-label">اختبارات مفعّلة</div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.exams.index') }}" class="d-flex gap-3 align-center" style="flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px; position: relative;">
                <i class="bi bi-search" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" name="search" class="form-control" placeholder="بحث باسم الاختبار..." value="{{ request('search') }}" style="padding-right: 35px;">
            </div>
            
            <select name="academic_year_id" class="form-control" style="width: 180px;" onchange="this.form.submit()">
                <option value="">كل السنوات</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                @endforeach
            </select>

            <select name="grade_id" class="form-control" style="width: 180px;" onchange="this.form.submit()">
                <option value="">كل الصفوف</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade->id }}" {{ request('grade_id') == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary">تصفية</button>
            @if(request()->hasAny(['search', 'academic_year_id', 'grade_id']))
                <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">إعادة تعيين</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-journal-check text-primary"></i> قائمة الاختبارات</div>
        <a href="{{ route('admin.exams.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> إنشاء اختبار</a>
    </div>
    <div class="table-wrapper">
        @if($exams->isEmpty())
            <div class="empty-state"><i class="bi bi-journal-check"></i><h3>لا توجد اختبارات بعد</h3><p>ابدأ بإنشاء اختبار جديد</p></div>
        @else
        <table>
            <thead>
                <tr><th>الاختبار</th><th>الصف المستهدف</th><th>السنة الدراسية</th><th>الدرجات</th><th>المتقدمون</th><th>الحالة</th><th>الإجراءات</th></tr>
            </thead>
            <tbody>
                @foreach($exams as $exam)
                <tr>
                    <td class="fw-bold">{{ $exam->title }}</td>
                    <td><span class="badge badge-primary">{{ $exam->grade->name }}</span></td>
                    <td class="text-muted">{{ $exam->academicYear->name }}</td>
                    <td>{{ $exam->pass_marks }} / {{ $exam->total_marks }}</td>
                    <td><span class="badge badge-info">{{ $exam->student_exams_count }} طالب</span></td>
                    <td>
                        @if($exam->is_active)
                            <span class="badge badge-success"><i class="bi bi-check-circle"></i> مفعّل</span>
                        @else
                            <span class="badge badge-danger"><i class="bi bi-x-circle"></i> موقوف</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-secondary btn-sm btn-icon" title="تفاصيل"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.exams.toggle', $exam) }}" class="btn btn-warning btn-sm btn-icon" title="تبديل الحالة"><i class="bi bi-toggle-{{ $exam->is_active ? 'on' : 'off' }}"></i></a>
                            <form id="del-ex-{{ $exam->id }}" method="POST" action="{{ route('admin.exams.destroy', $exam) }}">@csrf @method('DELETE')</form>
                            <button onclick="confirmDelete('del-ex-{{ $exam->id }}', 'حذف الاختبار سيمسح جميع نتائجه!')" class="btn btn-danger btn-sm btn-icon"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @if($exams->hasPages())
    <div class="card-footer">
        {{ $exams->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
