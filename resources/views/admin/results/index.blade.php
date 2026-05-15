@extends('layouts.admin')
@section('title', 'نتائج الطلاب')
@section('page-title', 'نتائج الطلاب')

@section('content')
<!-- Stats Section -->
<div class="stats-grid mb-4">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="bi bi-people"></i></div>
        <div>
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">إجمالي المشاركين</div>
        </div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
        <div>
            <div class="stat-value">{{ $stats['passed'] }}</div>
            <div class="stat-label">الناجحون</div>
        </div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon"><i class="bi bi-x-circle"></i></div>
        <div>
            <div class="stat-value">{{ $stats['failed'] }}</div>
            <div class="stat-label">الراسبون</div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.results.index') }}" class="d-flex gap-3 align-center" style="flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px; position: relative;">
                <i class="bi bi-search" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" name="search" class="form-control" placeholder="بحث باسم الطالب..." value="{{ request('search') }}" style="padding-right: 35px;">
            </div>
            
            <select name="grade_id" class="form-control" style="width: 180px;" onchange="this.form.submit()">
                <option value="">كل الصفوف</option>
                @foreach($grades as $g)
                    <option value="{{ $g->id }}" {{ request('grade_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                @endforeach
            </select>

            <select name="status" class="form-control" style="width: 150px;" onchange="this.form.submit()">
                <option value="">كل الحالات</option>
                <option value="pass" {{ request('status') == 'pass' ? 'selected' : '' }}>ناجح</option>
                <option value="fail" {{ request('status') == 'fail' ? 'selected' : '' }}>راسب</option>
            </select>

            <button type="submit" class="btn btn-primary">تصفية</button>
            @if(request()->hasAny(['search', 'grade_id', 'status']))
                <a href="{{ route('admin.results.index') }}" class="btn btn-secondary">إعادة تعيين</a>
            @endif

            <a href="{{ route('admin.results.export') }}?{{ http_build_query(request()->all()) }}" class="btn btn-success ms-auto">
                <i class="bi bi-file-earmark-excel"></i> تصدير Excel
            </a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><div class="card-title"><i class="bi bi-bar-chart text-primary"></i> النتائج ({{ $results->total() }})</div></div>
    <div class="table-wrapper">
        @if($results->isEmpty())
            <div class="empty-state"><i class="bi bi-search"></i><h3>لا توجد نتائج</h3></div>
        @else
        <table>
            <thead>
                <tr><th>الطالب</th><th>الاختبار</th><th>الصف</th><th>الدرجة</th><th>النسبة</th><th>النتيجة</th><th>التاريخ</th><th>الإجراءات</th></tr>
            </thead>
            <tbody>
                @foreach($results as $r)
                <tr>
                    <td class="fw-bold">{{ $r->student->name }}</td>
                    <td>{{ Str::limit($r->exam->title, 30) }}</td>
                    <td><span class="badge badge-primary" style="font-size:11px">{{ $r->exam->grade->name }}</span></td>
                    <td>{{ $r->score }} / {{ $r->total_marks }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="flex:1;height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden">
                                <div style="height:100%;width:{{ $r->percentage() }}%;background:{{ $r->isPassed() ? '#10b981' : '#ef4444' }};border-radius:3px"></div>
                            </div>
                            <span style="font-size:12px;font-weight:700">{{ $r->percentage() }}%</span>
                        </div>
                    </td>
                    <td>@if($r->isPassed())<span class="badge badge-success">ناجح</span>@else<span class="badge badge-danger">راسب</span>@endif</td>
                    <td class="text-muted" style="font-size:12px">{{ $r->submitted_at?->format('Y-m-d') }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.results.show', $r) }}" class="btn btn-secondary btn-sm btn-icon"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.results.print', $r) }}" target="_blank" class="btn btn-primary btn-sm btn-icon"><i class="bi bi-printer"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @if($results->hasPages())
    <div class="card-footer">
        {{ $results->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
