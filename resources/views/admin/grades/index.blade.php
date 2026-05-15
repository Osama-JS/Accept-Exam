@extends('layouts.admin')
@section('title', 'الصفوف الدراسية')
@section('page-title', 'الصفوف الدراسية')

@section('content')
<!-- Stats Section -->
<div class="stats-grid mb-4">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="bi bi-layers"></i></div>
        <div>
            <div class="stat-value">{{ $totalCount }}</div>
            <div class="stat-label">إجمالي الصفوف</div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.grades.index') }}" class="d-flex gap-3 align-center">
            <div style="flex: 1; position: relative;">
                <i class="bi bi-search" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" name="search" class="form-control" placeholder="بحث باسم الصف أو الوصف..." value="{{ request('search') }}" style="padding-right: 35px;">
            </div>
            <button type="submit" class="btn btn-primary">تصفية</button>
            @if(request('search'))
                <a href="{{ route('admin.grades.index') }}" class="btn btn-secondary">إعادة تعيين</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-layers text-primary"></i> قائمة الصفوف الدراسية</div>
        <a href="{{ route('admin.grades.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> إضافة صف جديد
        </a>
    </div>
    <div class="table-wrapper">
        @if($grades->isEmpty())
            <div class="empty-state">
                <i class="bi bi-layers"></i>
                <h3>لا توجد صفوف دراسية</h3>
                <p>ابدأ بإضافة الصفوف الدراسية للنظام</p>
            </div>
        @else
        <table>
            <thead>
                <tr>
                    <th>الترتيب</th>
                    <th>اسم الصف</th>
                    <th>الوصف</th>
                    <th>عدد المواد</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grades as $grade)
                <tr>
                    <td><span class="badge badge-gray"># {{ $grade->order }}</span></td>
                    <td class="fw-bold">{{ $grade->name }}</td>
                    <td class="text-muted">{{ $grade->description ?? '—' }}</td>
                    <td><span class="badge badge-primary">{{ $grade->subjects_count }} مادة</span></td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.grades.edit', $grade) }}" class="btn btn-warning btn-sm btn-icon" title="تعديل">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form id="del-grade-{{ $grade->id }}" method="POST" action="{{ route('admin.grades.destroy', $grade) }}">
                                @csrf @method('DELETE')
                            </form>
                            <button onclick="confirmDelete('del-grade-{{ $grade->id }}', 'هل تريد حذف هذا الصف؟')"
                                class="btn btn-danger btn-sm btn-icon" title="حذف">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @if($grades->hasPages())
    <div class="card-footer">
        {{ $grades->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
