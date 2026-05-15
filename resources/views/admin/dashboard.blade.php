@extends('layouts.admin')
@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')

@section('content')
<!-- Welcome Section & Filter -->
<div class="welcome-banner mb-4">
    <div class="welcome-content">
        <h1>مرحباً بك، {{ auth()->guard('admin')->user()->name }} 👋</h1>
        <p>إحصاءات النظام حسب السنة الدراسية المختارة.</p>
    </div>
    
    <div class="dashboard-filters">
        <form method="GET" action="{{ route('admin.dashboard') }}" id="filter-form" class="d-flex align-center gap-3">
            @if($currentYear)
            <div class="current-year-info me-3">
                <i class="bi bi-calendar-check text-success"></i>
                <span>السنة المفعّلة: <strong>{{ $currentYear->name }}</strong></span>
            </div>
            @endif
            
            <div class="filter-group">
                <label for="academic_year_id" class="mb-1 d-block text-muted" style="font-size: 11px;">تصفية بالسنة الدراسية:</label>
                <select name="academic_year_id" id="academic_year_id" class="form-control form-control-sm" style="min-width: 180px;" onchange="this.form.submit()">
                    @foreach($years as $year)
                        <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                            السنة الدراسية: {{ $year->name }} {{ $year->is_current ? '(الحالية)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<div class="filter-summary mb-4">
    <div class="badge badge-info">
        <i class="bi bi-funnel"></i> عرض بيانات: {{ $selectedYear ? $selectedYear->name : 'غير محدد' }}
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="bi bi-layers"></i></div>
        <div>
            <div class="stat-value">{{ $stats['grades'] }}</div>
            <div class="stat-label">الصفوف الدراسية</div>
        </div>
    </div>
    <div class="stat-card cyan">
        <div class="stat-icon"><i class="bi bi-book"></i></div>
        <div>
            <div class="stat-value">{{ $stats['subjects'] }}</div>
            <div class="stat-label">المواد الدراسية</div>
        </div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon"><i class="bi bi-patch-question"></i></div>
        <div>
            <div class="stat-value">{{ $stats['questions'] }}</div>
            <div class="stat-label">الأسئلة في البنك</div>
        </div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon"><i class="bi bi-journal-check"></i></div>
        <div>
            <div class="stat-value">{{ $stats['exams'] }}</div>
            <div class="stat-label">الاختبارات</div>
        </div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon"><i class="bi bi-people"></i></div>
        <div>
            <div class="stat-value">{{ $stats['students'] }}</div>
            <div class="stat-label">الطلاب المتقدمين</div>
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
    @if($stats['total_results'] > 0)
    <div class="stat-card green">
        <div class="stat-icon"><i class="bi bi-percent"></i></div>
        <div>
            <div class="stat-value">{{ round(($stats['passed']/$stats['total_results'])*100) }}%</div>
            <div class="stat-label">نسبة النجاح</div>
        </div>
    </div>
    @endif
</div>

<!-- Charts Section -->
<style>
    .charts-container { display: grid; grid-template-columns: 1fr 2fr; gap: 24px; margin-bottom: 28px; }
    @media (max-width: 1024px) { .charts-container { grid-template-columns: 1fr; } }
</style>

<div class="charts-container">
    <!-- Results Distribution (Small) -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="bi bi-pie-chart text-primary"></i> إجمالي النتائج</div>
        </div>
        <div class="card-body" style="height: 320px; display: flex; align-items: center; justify-content: center; padding: 20px;">
            @if($stats['total_results'] > 0)
                <canvas id="resultsChart"></canvas>
            @else
                <div class="empty-state" style="padding: 0;">
                    <i class="bi bi-bar-chart" style="font-size: 40px;"></i>
                    <p>لا توجد نتائج</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Students Performance by Grade (Wide) -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="bi bi-people text-primary"></i> أداء الطلاب حسب الصف</div>
        </div>
        <div class="card-body" style="height: 320px; display: flex; align-items: center; justify-content: center; padding: 20px;">
            @if($resultsByGrade->sum('passed') + $resultsByGrade->sum('failed') > 0)
                <canvas id="gradesChart"></canvas>
            @else
                <div class="empty-state" style="padding: 0;">
                    <i class="bi bi-calendar-x" style="font-size: 40px;"></i>
                    <p>لا توجد بيانات لهذا العام</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Recent Results -->
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-clock-history text-primary"></i> آخر النتائج</div>
        <a href="{{ route('admin.results.index') }}" class="btn btn-secondary btn-sm">عرض الكل</a>
    </div>
    <div class="table-wrapper">
        @if($recentResults->isEmpty())
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h3>لا توجد نتائج بعد</h3>
                <p>ستظهر هنا نتائج الطلاب بعد أداء الاختبارات</p>
            </div>
        @else
        <table>
            <thead>
                <tr>
                    <th>اسم الطالب</th>
                    <th>الاختبار</th>
                    <th>الصف</th>
                    <th>الدرجة</th>
                    <th>النتيجة</th>
                    <th>التاريخ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentResults as $r)
                <tr>
                    <td class="fw-bold">{{ $r->student->name }}</td>
                    <td>{{ $r->exam->title }}</td>
                    <td><span class="badge badge-primary">{{ $r->exam->grade->name }}</span></td>
                    <td>{{ $r->score }} / {{ $r->total_marks }}</td>
                    <td>
                        @if($r->isPassed())
                            <span class="badge badge-success"><i class="bi bi-check"></i> ناجح</span>
                        @else
                            <span class="badge badge-danger"><i class="bi bi-x"></i> راسب</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $r->submitted_at?->diffForHumans() }}</td>
                    <td>
                        <a href="{{ route('admin.results.show', $r) }}" class="btn btn-secondary btn-sm btn-icon">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@push('scripts')
<script>
// Register Chart.js DataLabels plugin globally
Chart.register(ChartDataLabels);

document.addEventListener('DOMContentLoaded', function() {
    // 1. Results Chart (Doughnut)
    const ctxResults = document.getElementById('resultsChart');
    if (ctxResults) {
        new Chart(ctxResults, {
            type: 'doughnut',
            data: {
                labels: ['ناجح', 'راسب'],
                datasets: [{
                    data: [{{ $stats['passed'] }}, {{ $stats['failed'] }}],
                    backgroundColor: ['#10b981', '#ef4444'],
                    hoverOffset: 10,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { family: 'Cairo', weight: 'bold' } } },
                    datalabels: {
                        color: '#fff',
                        font: { family: 'Cairo', weight: 'bold', size: 14 },
                        formatter: (value) => value > 0 ? value : ''
                    }
                }
            }
        });
    }

    // 2. Grades Performance Chart (Grouped Bar)
    const ctxGrades = document.getElementById('gradesChart');
    if (ctxGrades) {
        new Chart(ctxGrades, {
            type: 'bar',
            data: {
                labels: {!! json_encode($resultsByGrade->pluck('name')) !!},
                datasets: [
                    {
                        label: 'ناجح',
                        data: {!! json_encode($resultsByGrade->pluck('passed')) !!},
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                    },
                    {
                        label: 'راسب',
                        data: {!! json_encode($resultsByGrade->pluck('failed')) !!},
                        backgroundColor: '#ef4444',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0, font: { family: 'Cairo' } } },
                    x: { ticks: { font: { family: 'Cairo', weight: 'bold' } } }
                },
                plugins: {
                    legend: { position: 'top', labels: { font: { family: 'Cairo' } } },
                    tooltip: { titleFont: { family: 'Cairo' }, bodyFont: { family: 'Cairo' } },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#64748b',
                        font: { family: 'Cairo', size: 10 },
                        formatter: (value) => value > 0 ? value : ''
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
