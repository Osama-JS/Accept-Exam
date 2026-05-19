@extends('layouts.admin')

@section('title', 'تفاصيل نتيجة الطالب')
@section('page-title', 'تفاصيل نتيجة الطالب')

@section('breadcrumb')
    <a href="{{ route('admin.exams.index') }}">إدارة الاختبارات</a>
    <span>/</span>
    <a href="{{ route('admin.results.index') }}">لوحة النتائج</a>
    <span>/</span>
    <span style="color: var(--text-main); font-weight: 700;">تفاصيل النتيجة</span>
@endsection

@push('styles')
<style>
    /* ── بنر نتيجة الطالب (Student Hero) ── */
    .result-hero {
        background: {{ $studentExam->isPassed() ? 'linear-gradient(135deg, #1b3820 0%, #76b51b 100%)' : 'linear-gradient(135deg, #5c1818 0%, #ef4444 100%)' }};
        border-radius: 24px; padding: 36px; color: #fff; margin-bottom: 28px;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 24px;
        position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
    }
    .result-hero::after {
        content: '{{ $studentExam->isPassed() ? "\F2E6" : "\F622" }}'; font-family: 'bootstrap-icons'; position: absolute;
        left: -20px; bottom: -40px; font-size: 190px; opacity: 0.08; line-height: 1; pointer-events: none;
    }
    .hero-title { font-size: 26px; font-weight: 850; margin: 0 0 12px 0; }
    .hero-badges { display: flex; gap: 10px; flex-wrap: wrap; }
    .hero-badge {
        background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.18);
        padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 700;
        display: inline-flex; align-items: center; gap: 6px; backdrop-filter: blur(6px);
    }

    /* ── تخطيط مساحة العمل ── */
    .show-grid {
        display: grid; grid-template-columns: 1.25fr 1fr; gap: 24px;
        align-items: start; margin-bottom: 28px;
    }
    @media (max-width: 992px) {
        .show-grid { grid-template-columns: 1fr; }
    }

    /* ── البطاقات العامة ── */
    .ws-card {
        background: #fff; border-radius: 20px; border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.02), 0 8px 10px -6px rgba(0,0,0,0.02); overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .ws-card:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.04); }
    .ws-card-header {
        padding: 22px 28px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;
    }
    .ws-card-title { font-size: 16px; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px; }
    .ws-card-body { padding: 28px; }

    /* ── جدول البيانات الشخصية الفاخر ── */
    .info-table { width: 100%; border-collapse: collapse; }
    .info-table tr { border-bottom: 1px solid #f8fafc; }
    .info-table tr:last-child { border: none; }
    .info-table td { padding: 16px 0; font-size: 14px; }
    .info-table td.lbl { color: #64748b; font-weight: 700; width: 45%; display: flex; align-items: center; gap: 10px; }
    .info-table td.lbl i { color: #94a3b8; font-size: 18px; }
    .info-table td.val { color: #1e293b; font-weight: 800; text-align: left; }

    /* ── الدائرة البيانية لمؤشر النسبة (Circular SVG Progress) ── */
    .circular-progress-wrapper {
        position: relative; width: 130px; height: 130px; margin: 0 auto 24px;
    }
    .circular-svg { transform: rotate(-90deg); }
    .circle-bg { fill: none; stroke: #f1f5f9; stroke-width: 10; }
    .circle-progress {
        fill: none;
        stroke: {{ $studentExam->isPassed() ? '#16a34a' : '#dc2626' }};
        stroke-width: 10;
        stroke-linecap: round;
        transition: stroke-dashoffset 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .circular-percentage {
        position: absolute; inset: 0; display: flex; flex-direction: column;
        align-items: center; justify-content: center; line-height: 1.2;
    }
    .circular-percentage .pct { font-size: 26px; font-weight: 900; color: #1e293b; }
    .circular-percentage .score { font-size: 12px; font-weight: 700; color: #94a3b8; margin-top: 2px; }

    /* وسوم النتائج */
    .badge-pass { background: #dcfce7; color: #166534; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
    .badge-fail { background: #fee2e2; color: #991b1b; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }

    /* سجل الإجابات التفصيلي */
    .td-correct { color: #166534; font-weight: 700; background: #e8f5e9; border-radius: 8px; padding: 4px 10px; display: inline-block; font-size: 12.5px; }
    
    /* ── التبويبات الفاخرة للفرز اللحظي للإجابات ── */
    .tab-btn {
        background: transparent; border: none; font-size: 13px; font-weight: 700; color: #64748b;
        padding: 8px 16px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 6px;
        transition: all 0.2s; outline: none;
    }
    .tab-btn:hover { background: #f8fafc; color: var(--primary); }
    .tab-btn.active { background: var(--primary-light); color: var(--primary-dark); }
    .tab-badge { background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 20px; font-size: 11px; }
    .tab-btn.active .tab-badge { background: #fff; color: var(--primary-dark); }
</style>
@endpush

@section('content')

<!-- بنر نتيجة الطالب (Student Hero) -->
<div class="result-hero">
    <div>
        <h2 class="hero-title">ورقة إجابات: {{ $studentExam->student->name }}</h2>
        <div class="hero-badges">
            <span class="hero-badge"><i class="bi bi-layers"></i> الصف المتقدم إليه: {{ $studentExam->student->applyingGrade->name }}</span>
            <span class="hero-badge"><i class="bi bi-building"></i> المدرسة السابقة: {{ $studentExam->student->previous_school }}</span>
            <span class="hero-badge">
                @if($studentExam->isPassed())
                    <i class="bi bi-check-circle-fill"></i> ناجح في القبول
                @else
                    <i class="bi bi-x-circle-fill"></i> لم يتجاوز الاختبار
                @endif
            </span>
        </div>
    </div>
    
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="{{ route('admin.results.index') }}" class="btn" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); color: #fff; border-radius: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px;">
            <i class="bi bi-arrow-right"></i> رجوع للنتائج
        </a>
        
        <a href="{{ route('admin.results.print', $studentExam) }}" target="_blank" class="btn" style="background: #fff; border: none; color: #1e293b; border-radius: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; box-shadow: 0 6px 20px rgba(0,0,0,0.1);">
            <i class="bi bi-printer"></i> طباعة النتيجة
        </a>
    </div>
</div>

<!-- تخطيط مساحة العمل -->
<div class="show-grid">
    
    <!-- العمود الأيمن: بيانات الطالب الشخصية والتعليمية -->
    <div class="ws-card">
        <div class="ws-card-header">
            <h3 class="ws-card-title"><i class="bi bi-person-badge text-primary" style="font-size: 20px;"></i> الملف التعريفي للطالب</h3>
        </div>
        <div class="ws-card-body">
            <table class="info-table">
                <tr>
                    <td class="lbl"><i class="bi bi-person"></i> اسم الطالب الكامل</td>
                    <td class="val">{{ $studentExam->student->name }}</td>
                </tr>
                <tr>
                    <td class="lbl"><i class="bi bi-layers"></i> الصف المتقدم إليه</td>
                    <td class="val">{{ $studentExam->student->applyingGrade->name }}</td>
                </tr>
                <tr>
                    <td class="lbl"><i class="bi bi-building"></i> المدرسة السابقة</td>
                    <td class="val">{{ $studentExam->student->previous_school }}</td>
                </tr>
                <tr>
                    <td class="lbl"><i class="bi bi-award"></i> معدل الصف السابق</td>
                    <td class="val" style="color: var(--primary);">{{ $studentExam->student->last_grade_average }}%</td>
                </tr>
                <tr>
                    <td class="lbl"><i class="bi bi-people"></i> اسم ولي الأمر</td>
                    <td class="val">{{ $studentExam->student->guardian_name }}</td>
                </tr>
                <tr>
                    <td class="lbl"><i class="bi bi-telephone"></i> هاتف ولي الأمر</td>
                    <td class="val" dir="ltr">{{ $studentExam->student->guardian_phone }}</td>
                </tr>
            </table>
        </div>
    </div>
    
    <!-- العمود الأيسر: تفاصيل أداء الاختبار والدائرة البيانية الفاخرة -->
    <div class="ws-card">
        <div class="ws-card-header">
            <h3 class="ws-card-title"><i class="bi bi-journal-check text-success" style="font-size: 20px;"></i> مؤشرات أداء الاختبار</h3>
        </div>
        <div class="ws-card-body">
            <!-- المؤشر الدائري الفاخر SVG -->
            <div class="circular-progress-wrapper">
                <svg width="130" height="130" viewBox="0 0 120 120" class="circular-svg">
                    <circle cx="60" cy="60" r="50" class="circle-bg" />
                    <circle cx="60" cy="60" r="50" class="circle-progress" style="stroke-dasharray: 314.16; stroke-dashoffset: {{ 314.16 - ($studentExam->percentage() / 100) * 314.16 }};" />
                </svg>
                <div class="circular-percentage">
                    <span class="pct">{{ $studentExam->percentage() }}%</span>
                    <span class="score">{{ $studentExam->score }} / {{ $studentExam->total_marks }}</span>
                </div>
            </div>

            <div style="text-align: center; margin-bottom: 24px;">
                @if($studentExam->isPassed())
                    <span class="badge-pass"><i class="bi bi-check-circle-fill"></i> ✓ اجتاز بنجاح</span>
                @else
                    <span class="badge-fail"><i class="bi bi-x-circle-fill"></i> ✗ لم يتجاوز الاختبار</span>
                @endif
            </div>
            
            <table class="info-table" style="font-size: 13.5px;">
                <tr>
                    <td class="lbl"><i class="bi bi-flag"></i> درجة نجاح الاختبار</td>
                    <td class="val">{{ $studentExam->pass_marks }} درجة</td>
                </tr>
                <tr>
                    <td class="lbl"><i class="bi bi-check2-square"></i> عدد الإجابات الصحيحة</td>
                    <td class="val text-success">{{ $studentExam->correct_answers }} / {{ $studentExam->total_questions }} سؤال</td>
                </tr>
                <tr>
                    <td class="lbl"><i class="bi bi-clock-history"></i> تاريخ ووقت التسليم</td>
                    <td class="val">{{ $studentExam->submitted_at?->format('Y-m-d H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>

</div>

<!-- سجل الإجابات التفصيلي للمصحح والمدير مع فرز لحظي مدمج -->
<div class="ws-card" style="margin-bottom: 40px;">
    <div class="ws-card-header" style="flex-wrap: wrap; gap: 16px;">
        <h3 class="ws-card-title"><i class="bi bi-list-check text-primary" style="font-size: 20px;"></i> سجل الإجابات التفصيلي والتقييم</h3>
        
        <!-- نظام التبويبات الفاخر للفرز السريع -->
        <div style="display: flex; gap: 8px;">
            <button type="button" class="tab-btn active" onclick="filterLog(event, 'all')">
                الكل <span class="tab-badge">{{ $studentExam->answers->count() }}</span>
            </button>
            <button type="button" class="tab-btn" onclick="filterLog(event, 'correct')">
                صحيح <span class="tab-badge" style="background: #dcfce7; color: #166534;">{{ $studentExam->correct_answers }}</span>
            </button>
            <button type="button" class="tab-btn" onclick="filterLog(event, 'incorrect')">
                أخطاء <span class="tab-badge" style="background: #fee2e2; color: #991b1b;">{{ $studentExam->total_questions - $studentExam->correct_answers }}</span>
            </button>
        </div>
    </div>
    
    <div class="table-wrapper">
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>نص السؤال</th>
                    <th>إجابة الطالب المختارة</th>
                    <th>الإجابة الصحيحة المحددة</th>
                    <th>النقاط</th>
                    <th>التقييم</th>
                </tr>
            </thead>
            <tbody id="answer-log-tbody">
                @php
                    $markPerQ = $studentExam->exam->markPerQuestion();
                @endphp
                @foreach($studentExam->answers as $i => $answer)
                @php
                    $points = $answer->is_correct ? number_format($markPerQ, 2) : '0.00';
                @endphp
                <tr class="answer-log-row" data-correct="{{ $answer->is_correct ? '1' : '0' }}">
                    <td class="text-muted" style="font-weight: 700;">{{ $i + 1 }}</td>
                    <td style="max-width: 300px; font-weight: 700; color: #1e293b;">{{ Str::limit($answer->question->text, 100) }}</td>
                    <td>
                        @if($answer->chosenChoice)
                            <span style="font-weight: 600; color: #475569;">{{ $answer->chosenChoice->text }}</span>
                        @else
                            <span class="text-muted" style="font-style: italic;">لم يجب / تخطى السؤال</span>
                        @endif
                    </td>
                    <td>
                        <span class="td-correct">{{ $answer->question->correctChoice()?->text ?? 'لا يوجد' }}</span>
                    </td>
                    <td>
                        @if($answer->is_correct)
                            <span class="badge-pass" style="font-size: 11px; padding: 4px 8px;">+{{ $points }} د</span>
                        @else
                            <span class="badge-fail" style="font-size: 11px; padding: 4px 8px;">{{ $points }} د</span>
                        @endif
                    </td>
                    <td>
                        @if($answer->is_correct)
                            <span class="badge-pass" style="padding: 4px 10px; border-radius: 6px;"><i class="bi bi-check"></i> صحيح</span>
                        @else
                            <span class="badge-fail" style="padding: 4px 10px; border-radius: 6px;"><i class="bi bi-x"></i> خطأ</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── دالة فرز وتصفية الإجابات لحظياً بالـ JS ──
    window.filterLog = function(event, type) {
        // تحديث التبويب النشط
        const buttons = document.querySelectorAll('.tab-btn');
        buttons.forEach(btn => btn.classList.remove('active'));
        event.currentTarget.classList.add('active');
        
        // تصفية أسطر الجدول بناءً على الميزة المطلوبة
        const rows = document.querySelectorAll('.answer-log-row');
        rows.forEach(row => {
            const isCorrect = row.getAttribute('data-correct') === '1';
            
            if (type === 'all') {
                row.style.display = '';
            } else if (type === 'correct') {
                row.style.display = isCorrect ? '' : 'none';
            } else if (type === 'incorrect') {
                row.style.display = !isCorrect ? '' : 'none';
            }
        });
    }
</script>
@endpush
