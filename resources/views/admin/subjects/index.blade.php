@extends('layouts.admin')
@section('title', 'المواد الدراسية')
@section('page-title', 'المواد الدراسية')

@section('content')
<!-- Stats Section -->
<div class="stats-grid mb-4">
    <div class="stat-card cyan">
        <div class="stat-icon"><i class="bi bi-book"></i></div>
        <div>
            <div class="stat-value">{{ $totalCount }}</div>
            <div class="stat-label">إجمالي المواد</div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.subjects.index') }}" class="d-flex gap-3 align-center" style="flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px; position: relative;">
                <i class="bi bi-search" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" name="search" class="form-control" placeholder="بحث باسم المادة..." value="{{ request('search') }}" style="padding-right: 35px;">
            </div>
            <div style="width: 200px;">
                <select name="grade_id" class="form-control" onchange="this.form.submit()">
                    <option value="">كل الصفوف</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade->id }}" {{ request('grade_id') == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">تصفية</button>
            @if(request('search') || request('grade_id'))
                <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">إعادة تعيين</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-book text-primary"></i> قائمة المواد الدراسية</div>
        <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> إضافة مادة
        </a>
    </div>
    <div class="table-wrapper">
        @if($subjects->isEmpty())
            <div class="empty-state">
                <i class="bi bi-book"></i>
                <h3>لا توجد مواد دراسية</h3>
                <p>ابدأ بإضافة المواد وربطها بالصفوف الدراسية</p>
            </div>
        @else
        <table>
            <thead>
                <tr><th>المادة</th><th>الصف الدراسي</th><th>عدد الأسئلة</th><th>الإجراءات</th></tr>
            </thead>
            <tbody>
                @foreach($subjects as $subject)
                <tr>
                    <td class="fw-bold">{{ $subject->icon }} {{ $subject->name }}</td>
                    <td><span class="badge badge-primary">{{ $subject->grade->name }}</span></td>
                    <td><span class="badge badge-info">{{ $subject->questions_count }} سؤال</span></td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.questions.index', ['subject_id' => $subject->id]) }}" class="btn btn-secondary btn-sm" title="عرض الأسئلة">
                                <i class="bi bi-patch-question"></i> الأسئلة
                            </a>
                            <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-warning btn-sm btn-icon">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form id="del-sub-{{ $subject->id }}" method="POST" action="{{ route('admin.subjects.destroy', $subject) }}">
                                @csrf @method('DELETE')
                            </form>
                            <button onclick="confirmDelete('del-sub-{{ $subject->id }}', 'هل تريد حذف هذه المادة؟')"
                                class="btn btn-danger btn-sm btn-icon"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @if($subjects->hasPages())
    <div class="card-footer">
        {{ $subjects->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
