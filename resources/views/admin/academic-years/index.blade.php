@extends('layouts.admin')
@section('title', 'السنوات الدراسية')
@section('page-title', 'السنوات الدراسية')

@section('content')
<!-- Stats Section -->
<div class="stats-grid mb-4">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="bi bi-calendar3"></i></div>
        <div>
            <div class="stat-value">{{ $totalCount }}</div>
            <div class="stat-label">إجمالي السنوات</div>
        </div>
    </div>
    @if($currentYear)
    <div class="stat-card green">
        <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
        <div>
            <div class="stat-value" style="font-size: 20px;">{{ $currentYear->name }}</div>
            <div class="stat-label">السنة الحالية</div>
        </div>
    </div>
    @endif
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.academic-years.index') }}" class="d-flex gap-3 align-center">
            <div style="flex: 1; position: relative;">
                <i class="bi bi-search" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" name="search" class="form-control" placeholder="بحث في السنوات..." value="{{ request('search') }}" style="padding-right: 35px;">
            </div>
            <button type="submit" class="btn btn-primary">تصفية</button>
            @if(request('search'))
                <a href="{{ route('admin.academic-years.index') }}" class="btn btn-secondary">إعادة تعيين</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-calendar3 text-primary"></i> السنوات الدراسية</div>
        <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> إضافة سنة</a>
    </div>
    <div class="table-wrapper">
        @if($years->isEmpty())
            <div class="empty-state"><i class="bi bi-calendar3"></i><h3>لا توجد سنوات دراسية</h3></div>
        @else
        <table>
            <thead><tr><th>السنة الدراسية</th><th>الحالة</th><th>عدد الاختبارات</th><th>الإجراءات</th></tr></thead>
            <tbody>
                @foreach($years as $year)
                <tr>
                    <td class="fw-bold">{{ $year->name }}</td>
                    <td>
                        @if($year->is_current)
                            <span class="badge badge-success"><i class="bi bi-check-circle"></i> السنة الحالية</span>
                        @else
                            <span class="badge badge-gray">غير محددة</span>
                        @endif
                    </td>
                    <td><span class="badge badge-primary">{{ $year->exams_count }} اختبار</span></td>
                    <td>
                        <div class="d-flex gap-2">
                            @unless($year->is_current)
                            <form method="POST" action="{{ route('admin.academic-years.set-current', $year) }}">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-star"></i> تعيين كحالية</button>
                            </form>
                            @endunless
                            <a href="{{ route('admin.academic-years.edit', $year) }}" class="btn btn-warning btn-sm btn-icon"><i class="bi bi-pencil"></i></a>
                            <form id="del-y-{{ $year->id }}" method="POST" action="{{ route('admin.academic-years.destroy', $year) }}">@csrf @method('DELETE')</form>
                            <button onclick="confirmDelete('del-y-{{ $year->id }}')" class="btn btn-danger btn-sm btn-icon"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @if($years->hasPages())
    <div class="card-footer">
        {{ $years->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
