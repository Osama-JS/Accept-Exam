@extends('layouts.admin')
@section('title', 'بنك الأسئلة')
@section('page-title', 'بنك الأسئلة')

@section('content')
<!-- Stats Section -->
<div class="stats-grid mb-4">
    <div class="stat-card purple">
        <div class="stat-icon"><i class="bi bi-patch-question"></i></div>
        <div>
            <div class="stat-value">{{ $totalCount }}</div>
            <div class="stat-label">إجمالي الأسئلة</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="d-flex gap-3 align-center" style="flex-wrap:wrap">
            <div style="flex: 1; min-width: 250px; position: relative;">
                <i class="bi bi-search" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" name="search" class="form-control" placeholder="بحث في نص السؤال..." value="{{ request('search') }}" style="padding-right: 35px;">
            </div>
            
            <select name="grade_id" class="form-control" style="max-width:200px" onchange="this.form.submit()">
                <option value="">-- كل الصفوف --</option>
                @foreach($grades as $g)
                    <option value="{{ $g->id }}" {{ request('grade_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                @endforeach
            </select>

            <select name="subject_id" class="form-control" style="max-width:200px" onchange="this.form.submit()">
                <option value="">-- كل المواد --</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary">تصفية</button>
            
            @if(request()->hasAny(['grade_id','subject_id', 'search']))
                <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">إعادة تعيين</a>
            @endif
            
            <a href="{{ route('admin.questions.create') }}" class="btn btn-success ms-auto">
                <i class="bi bi-plus-lg"></i> إضافة سؤال
            </a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-patch-question text-primary"></i> الأسئلة ({{ $questions->total() }})</div>
    </div>
    <div class="table-wrapper">
        @if($questions->isEmpty())
            <div class="empty-state">
                <i class="bi bi-patch-question"></i>
                <h3>لا توجد أسئلة</h3>
                <p>ابدأ بإضافة الأسئلة لبنك الأسئلة</p>
            </div>
        @else
        <table>
            <thead>
                <tr><th>#</th><th>نص السؤال</th><th>المادة</th><th>الصف</th><th>الخيارات</th><th>الإجراءات</th></tr>
            </thead>
            <tbody>
                @foreach($questions as $q)
                <tr>
                    <td class="text-muted">{{ $q->id }}</td>
                    <td style="max-width:300px">{{ Str::limit($q->text, 80) }}</td>
                    <td><span class="badge badge-info">{{ $q->subject->name }}</span></td>
                    <td class="text-muted" style="font-size:12px">{{ $q->subject->grade->name }}</td>
                    <td><span class="badge badge-gray">{{ $q->choices->count() }} خيار</span></td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.questions.edit', $q) }}" class="btn btn-warning btn-sm btn-icon"><i class="bi bi-pencil"></i></a>
                            <form id="del-q-{{ $q->id }}" method="POST" action="{{ route('admin.questions.destroy', $q) }}">
                                @csrf @method('DELETE')
                            </form>
                            <button onclick="confirmDelete('del-q-{{ $q->id }}')" class="btn btn-danger btn-sm btn-icon"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @if($questions->hasPages())
    <div class="card-footer">
        {{ $questions->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
