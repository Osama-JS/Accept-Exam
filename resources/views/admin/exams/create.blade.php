@extends('layouts.admin')

@section('title', 'إنشاء اختبار جديد')
@section('page-title', 'إنشاء اختبار جديد')

@section('breadcrumb')
    <a href="{{ route('admin.exams.index') }}">إدارة الاختبارات</a>
    <span>/</span>
    <span style="color: var(--text-main); font-weight: 700;">إنشاء اختبار</span>
@endsection

@push('styles')
<style>
    /* ── تخطيط مساحة العمل (Workspace Layout) ── */
    .workspace-grid {
        display: grid;
        grid-template-columns: 1.15fr 1fr;
        gap: 24px;
        align-items: start;
        margin-bottom: 40px;
    }
    @media (max-width: 992px) {
        .workspace-grid { grid-template-columns: 1fr; }
    }

    /* ── البطاقات العامة ── */
    .ws-card {
        background: #fff; border-radius: 16px; border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); overflow: hidden;
    }
    .ws-card-header {
        padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;
    }
    .ws-card-title { font-size: 16px; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px; }
    .ws-card-body { padding: 24px; }

    /* ── حقول الإدخال المحسنة ── */
    .form-group label {
        font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 8px; display: block;
    }
    .form-control {
        border-radius: 12px; border: 1.5px solid #cbd5e1; height: 46px; padding: 10px 16px; font-size: 13px;
        outline: none; transition: all 0.2s; width: 100%; background: #f8fafc;
    }
    .form-control:focus {
        border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-light); background: #fff;
    }
    
    /* ── مجموعة الحقول مع الأيقونات (Input with Icon Group) ── */
    .input-icon-group {
        position: relative;
        width: 100%;
    }
    .input-icon-group i {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 16px;
        pointer-events: none;
        transition: color 0.2s;
        z-index: 10;
    }
    .input-icon-group .form-control {
        padding-right: 46px !important;
    }
    .input-icon-group:focus-within i {
        color: var(--primary);
    }
    
    /* ── البطاقات المصغرة لإعدادات المواد ── */
    .source-card {
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px;
        padding: 20px; margin-bottom: 16px; display: flex; flex-direction: column; gap: 14px;
        position: relative; transition: all 0.2s;
    }
    .source-card:hover { border-color: #cbd5e0; background: #fff; }
    .source-card-header { display: flex; justify-content: space-between; align-items: center; }
    .source-card-title { font-size: 13px; font-weight: 800; color: #475569; }
    .btn-remove-source {
        background: #fef2f2; border: 1px solid #fee2e2; color: #ef4444; width: 32px; height: 32px;
        border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;
    }
    .btn-remove-source:hover { background: #ef4444; color: #fff; }

    .source-inputs {
        display: grid; grid-template-columns: 1fr 1.3fr 0.8fr 0.8fr; gap: 14px;
    }
    @media (max-width: 576px) {
        .source-inputs { grid-template-columns: 1fr; }
    }

    /* ── تنبيهات الحماية والتنبيهات المباشرة ── */
    .guard-alert {
        background: #fef2f2; border: 1.5px dashed #fecaca; border-radius: 12px; padding: 14px;
        color: #b91c1c; font-size: 13px; font-weight: 700; display: none; align-items: flex-start; gap: 8px; margin-top: 12px;
    }

    /* ── الأزرار المتقطعة لإضافة مادة ── */
    .btn-add-source {
        display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;
        padding: 14px; border: 2px dashed #cbd5e0; border-radius: 12px; background: transparent;
        color: var(--primary); font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s;
    }
    .btn-add-source:hover { border-color: var(--primary); background: rgba(118, 181, 27, 0.02); }

    .info-box {
        background: rgba(37,99,235,.06); border: 1px solid rgba(37,99,235,.15); border-radius: 12px;
        padding: 14px 18px; font-size: 13px; color: #1e3a8a; display: flex; align-items: flex-start; gap: 10px; margin-bottom: 20px;
    }

    /* ── التوزيع المتقدم (Premium Advanced Panel) ── */
    .advanced-config-panel {
        background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; margin-top: 16px; overflow: hidden;
        display: none;
    }
    .advanced-config-panel.active {
        display: block;
    }
    .advanced-config-header {
        background: rgba(226, 232, 240, 0.4); padding: 10px 16px; border-bottom: 1.5px solid #e2e8f0;
        display: flex; justify-content: space-between; align-items: center;
    }
    .advanced-config-header h4 { font-size: 13px; font-weight: 800; color: #334155; margin: 0; display: flex; align-items: center; gap: 8px; }
    .advanced-grid { display: grid; grid-template-columns: 1fr; gap: 16px; padding: 16px; }
    .config-section { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    .config-section-title { font-size: 13px; font-weight: 800; color: #475569; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
    .config-inputs-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(60px, 1fr)); gap: 12px; }
    .config-input-box { flex: 1; text-align: center; }
    .config-input-box label { font-size: 10.5px; font-weight: 800; color: #64748b; margin-bottom: 4px; display: block; }
    .config-input-box input { height: 34px; padding: 4px 8px; font-size: 13px; text-align: center; font-weight: 700; border-radius: 8px; }
    .config-input-box input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
</style>
@endpush

@section('content')

<form method="POST" action="{{ route('admin.exams.store') }}" id="exam-form">
    @csrf
    
    <div class="workspace-grid">
        
        <!-- العمود الأيمن: إعدادات الاختبار الأساسية -->
        <div class="ws-card">
            <div class="ws-card-header">
                <h3 class="ws-card-title"><i class="bi bi-file-earmark-plus text-primary" style="font-size: 20px;"></i> إعدادات الاختبار الأساسية</h3>
                <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary btn-sm" style="border-radius: 10px; font-weight: 700;"><i class="bi bi-arrow-right"></i> رجوع</a>
            </div>
            
            <div class="ws-card-body">
                <div class="info-box">
                    <i class="bi bi-info-circle-fill" style="font-size: 18px; color: #2563eb; flex-shrink: 0;"></i>
                    <span>ملاحظة: لا يمكن سحب الأسئلة من نفس الصف المستهدف للاختبار. تأكد من إعداد مصادر الأسئلة بشكل صحيح.</span>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>السنة الدراسية *</label>
                        <div class="input-icon-group">
                            <i class="bi bi-calendar3"></i>
                            <select name="academic_year_id" class="form-control" required>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $year->id == $currentYearId ? 'selected' : '' }}>
                                        {{ $year->name }} {{ $year->is_current ? '(الحالية)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>الصف المستهدف للاختبار *</label>
                        <div class="input-icon-group">
                            <i class="bi bi-layers"></i>
                            <select name="grade_id" id="target-grade" class="form-control" onchange="validateGrades(); updateAllStats();" required>
                                <option value="">-- اختر الصف --</option>
                                @foreach($grades as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label>عنوان الاختبار *</label>
                    <div class="input-icon-group">
                        <i class="bi bi-pencil-square"></i>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="مثال: اختبار القبول للصف الأول المتوسط 2026" required>
                    </div>
                </div>

                <div class="grid-2 mt-3">
                    <div class="form-group">
                        <label>مدة الاختبار (بالدقائق) *</label>
                        <div class="input-icon-group">
                            <i class="bi bi-stopwatch"></i>
                            <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', 60) }}" min="10" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>درجة النجاح المئوية (%) *</label>
                        <div class="input-icon-group">
                            <i class="bi bi-percent"></i>
                            <input type="number" name="pass_marks_percent" class="form-control" value="{{ old('pass_marks_percent', 50) }}" min="1" max="100" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- العمود الأيسر: مصادر الأسئلة والتحقق الذكي -->
        <div class="ws-card">
            <div class="ws-card-header">
                <h3 class="ws-card-title"><i class="bi bi-diagram-3 text-success" style="font-size: 20px;"></i> مصادر وطرق سحب الأسئلة</h3>
            </div>
            
            <div class="ws-card-body">
                <div id="configs-list">
                    
                    <!-- كرت مادة إدخال افتراضي -->
                    <div class="source-card">
                        <div class="source-card-header">
                            <span class="source-card-title"><i class="bi bi-book"></i> المادة المصدر #1</span>
                        </div>
                        
                        <div class="source-inputs">
                            <div>
                                <label>الصف</label>
                                <div class="input-icon-group">
                                    <i class="bi bi-layers-half"></i>
                                    <select name="configs[0][grade_id]" class="form-control grade-config-select" onchange="loadConfigSubjects(this, 0)">
                                        <option value="">-- الصف --</option>
                                        @foreach($grades as $g)
                                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label>المادة *</label>
                                <div class="input-icon-group">
                                    <i class="bi bi-journal-text"></i>
                                    <select name="configs[0][subject_id]" class="form-control subject-config-select" onchange="validateGrades(); loadSubjectStats(this);" required>
                                        <option value="">-- اختر الصف أولاً --</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label>الأسئلة *</label>
                                <div class="input-icon-group">
                                    <i class="bi bi-question-circle"></i>
                                    <input type="number" name="configs[0][question_count]" class="form-control question-count-input" value="5" min="1" oninput="validateConfigStats(this)" required>
                                </div>
                                <small class="total-available-label text-muted" style="display: block; margin-top: 4px; font-weight: 800; font-size: 11px;"></small>
                            </div>
                            
                            <div>
                                <label>درجة كل سؤال *</label>
                                <div class="input-icon-group">
                                    <i class="bi bi-award"></i>
                                    <input type="number" name="configs[0][marks_per_question]" class="form-control" value="1" min="1" required>
                                </div>
                            </div>
                        </div>

                        <!-- خيارات متقدمة للتوزيع -->
                        <div class="advanced-config-panel">
                            <div class="advanced-config-header">
                                <h4><i class="bi bi-sliders"></i> التوزيع المتقدم (اختياري)</h4>
                                <span class="badge badge-gray" style="font-size: 10px; font-weight: bold;"><i class="bi bi-magic text-primary"></i> تلقائي إذا تُرك فارغاً</span>
                            </div>
                            
                            <div class="advanced-grid" style="display: grid; grid-template-columns: 1fr; gap: 16px; padding: 16px;">
                                <!-- الصعوبة -->
                                <div class="config-section">
                                    <h5 class="config-section-title"><i class="bi bi-bar-chart-fill text-warning"></i> المستويات المعرفية وتوزيع الدرجات</h5>
                                    <div class="config-inputs-row" style="display: flex; flex-direction: column; gap: 8px;">
                                        <!-- سهل -->
                                        <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                            <span style="font-size: 11px; font-weight: bold; color: #475569;"><i class="bi bi-circle-fill text-success"></i> سهل:</span>
                                            <div style="display: flex; gap: 6px; align-items: center;">
                                                <input type="number" name="configs[0][easy_count]" class="form-control" min="0" placeholder="العدد" style="width: 70px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                                <span style="font-size: 10px; color: #94a3b8;">درجة:</span>
                                                <input type="number" name="configs[0][easy_marks]" class="form-control" min="1" value="1" placeholder="الدرجة" style="width: 50px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                            </div>
                                            <span class="stats-badge stats-easy text-muted" style="font-size: 10px; font-weight: bold;">متوفر: -</span>
                                        </div>

                                        <!-- متوسط -->
                                        <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                            <span style="font-size: 11px; font-weight: bold; color: #475569;"><i class="bi bi-circle-fill text-warning"></i> متوسط:</span>
                                            <div style="display: flex; gap: 6px; align-items: center;">
                                                <input type="number" name="configs[0][medium_count]" class="form-control" min="0" placeholder="العدد" style="width: 70px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                                <span style="font-size: 10px; color: #94a3b8;">درجة:</span>
                                                <input type="number" name="configs[0][medium_marks]" class="form-control" min="1" value="1" placeholder="الدرجة" style="width: 50px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                            </div>
                                            <span class="stats-badge stats-medium text-muted" style="font-size: 10px; font-weight: bold;">متوفر: -</span>
                                        </div>

                                        <!-- صعب -->
                                        <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                            <span style="font-size: 11px; font-weight: bold; color: #475569;"><i class="bi bi-circle-fill text-danger"></i> صعب:</span>
                                            <div style="display: flex; gap: 6px; align-items: center;">
                                                <input type="number" name="configs[0][hard_count]" class="form-control" min="0" placeholder="العدد" style="width: 70px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                                <span style="font-size: 10px; color: #94a3b8;">درجة:</span>
                                                <input type="number" name="configs[0][hard_marks]" class="form-control" min="1" value="1" placeholder="الدرجة" style="width: 50px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                            </div>
                                            <span class="stats-badge stats-hard text-muted" style="font-size: 10px; font-weight: bold;">متوفر: -</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- النوع -->
                                <div class="config-section">
                                    <h5 class="config-section-title"><i class="bi bi-ui-checks-grid text-primary"></i> نوع الأسئلة المتوفرة</h5>
                                    <div class="config-inputs-row" style="display: flex; flex-direction: column; gap: 8px;">
                                        <!-- خيارات -->
                                        <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                            <span style="font-size: 11px; font-weight: bold; color: #475569;"><i class="bi bi-list-check text-primary"></i> خيارات (MCQ):</span>
                                            <input type="number" name="configs[0][mcq_count]" class="form-control" min="0" placeholder="العدد" style="width: 70px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                            <span class="stats-badge stats-mcq text-muted" style="font-size: 10px; font-weight: bold;">متوفر: -</span>
                                        </div>

                                        <!-- صح/خطأ -->
                                        <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                            <span style="font-size: 11px; font-weight: bold; color: #475569;"><i class="bi bi-check-circle text-info"></i> صح / خطأ:</span>
                                            <input type="number" name="configs[0][tf_count]" class="form-control" min="0" placeholder="العدد" style="width: 70px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                            <span class="stats-badge stats-tf text-muted" style="font-size: 10px; font-weight: bold;">متوفر: -</span>
                                        </div>

                                        <!-- توصيل -->
                                        <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                            <span style="font-size: 11px; font-weight: bold; color: #475569;"><i class="bi bi-arrow-left-right text-secondary"></i> توصيل (Matching):</span>
                                            <input type="number" name="configs[0][matching_count]" class="form-control" min="0" placeholder="العدد" style="width: 70px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                            <span class="stats-badge stats-matching text-muted" style="font-size: 10px; font-weight: bold;">متوفر: -</span>
                                        </div>

                                        <!-- مقالي -->
                                        <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                            <span style="font-size: 11px; font-weight: bold; color: #475569;"><i class="bi bi-file-earmark-text text-dark"></i> مقالي (Essay):</span>
                                            <input type="number" name="configs[0][essay_count]" class="form-control" min="0" placeholder="العدد" style="width: 70px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                            <span class="stats-badge stats-essay text-muted" style="font-size: 10px; font-weight: bold;">متوفر: -</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- صندوق التنبيهات المباشر للمادة -->
                        <div class="validation-summary" style="margin-top: 10px; font-size: 12px; font-weight: bold; padding: 0 10px;"></div>

                    </div>
                    
                </div>
                
                <button type="button" class="btn-add-source mb-3" onclick="addConfig()">
                    <i class="bi bi-plus-circle"></i> إضافة مادة وسحب آخر
                </button>
                
                <!-- صندوق التنبيه الذكي للمانع البرمجي -->
                <div class="guard-alert" id="guard-alert-box">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 20px; flex-shrink: 0;"></i>
                    <div>
                        <span>خطأ في التحديد: لا يمكن سحب الأسئلة من نفس الصف المستهدف للاختبار. يرجى تعديل صف المادة المصدر أو الصف المستهدف.</span>
                    </div>
                </div>

                <div class="divider" style="margin: 24px 0 16px;"></div>
                <button type="submit" id="submit-btn" class="btn btn-primary btn-lg" style="width: 100%; border-radius: 12px; background: linear-gradient(135deg, #76b51b, #5f9416); border: none; box-shadow: 0 6px 20px rgba(118, 181, 27, 0.25);">
                    <i class="bi bi-check-lg"></i> إنشاء الاختبار وحفظه
                </button>
            </div>
        </div>

    </div>
</form>

@endsection

@push('scripts')
<script>
    let configIndex = 1;
    const grades = @json($grades);

    // إضافة مصدر سحب مادة أخرى
    window.addConfig = function() {
        const list = document.getElementById('configs-list');
        const div = document.createElement('div');
        div.className = 'source-card';
        div.innerHTML = `
            <div class="source-card-header">
                <span class="source-card-title"><i class="bi bi-book"></i> المادة المصدر #${configIndex + 1}</span>
                <button type="button" class="btn-remove-source" onclick="this.closest('.source-card').remove(); validateGrades();">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            
            <div class="source-inputs">
                <div>
                    <label>الصف</label>
                    <div class="input-icon-group">
                        <i class="bi bi-layers-half"></i>
                        <select name="configs[${configIndex}][grade_id]" class="form-control grade-config-select" onchange="loadConfigSubjects(this, ${configIndex})">
                            <option value="">-- الصف --</option>
                            ${grades.map(g => `<option value="${g.id}">${g.name}</option>`).join('')}
                        </select>
                    </div>
                </div>
                
                <div>
                    <label>المادة *</label>
                    <div class="input-icon-group">
                        <i class="bi bi-journal-text"></i>
                        <select name="configs[${configIndex}][subject_id]" class="form-control subject-config-select" onchange="validateGrades(); loadSubjectStats(this);" required>
                            <option value="">-- اختر الصف أولاً --</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label>الأسئلة *</label>
                    <div class="input-icon-group">
                        <i class="bi bi-question-circle"></i>
                        <input type="number" name="configs[${configIndex}][question_count]" class="form-control question-count-input" value="5" min="1" oninput="validateConfigStats(this)" required>
                    </div>
                    <small class="total-available-label text-muted" style="display: block; margin-top: 4px; font-weight: 800; font-size: 11px;"></small>
                </div>
                <div>
                    <label>درجة كل سؤال *</label>
                    <div class="input-icon-group">
                        <i class="bi bi-award"></i>
                        <input type="number" name="configs[${configIndex}][marks_per_question]" class="form-control" value="1" min="1" required>
                    </div>
                </div>
            </div>

            <!-- خيارات متقدمة للتوزيع -->
            <div class="advanced-config-panel">
                <div class="advanced-config-header">
                    <h4><i class="bi bi-sliders"></i> التوزيع المتقدم (اختياري)</h4>
                    <span class="badge badge-gray" style="font-size: 10px; font-weight: bold;"><i class="bi bi-magic text-primary"></i> تلقائي إذا تُرك فارغاً</span>
                </div>
                
                <div class="advanced-grid" style="display: grid; grid-template-columns: 1fr; gap: 16px; padding: 16px;">
                    <!-- الصعوبة -->
                    <div class="config-section">
                        <h5 class="config-section-title"><i class="bi bi-bar-chart-fill text-warning"></i> المستويات المعرفية وتوزيع الدرجات</h5>
                        <div class="config-inputs-row" style="display: flex; flex-direction: column; gap: 8px;">
                            <!-- سهل -->
                            <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <span style="font-size: 11px; font-weight: bold; color: #475569;"><i class="bi bi-circle-fill text-success"></i> سهل:</span>
                                <div style="display: flex; gap: 6px; align-items: center;">
                                    <input type="number" name="configs[${configIndex}][easy_count]" class="form-control" min="0" placeholder="العدد" style="width: 70px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                    <span style="font-size: 10px; color: #94a3b8;">درجة:</span>
                                    <input type="number" name="configs[${configIndex}][easy_marks]" class="form-control" min="1" value="1" placeholder="الدرجة" style="width: 50px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                </div>
                                <span class="stats-badge stats-easy text-muted" style="font-size: 10px; font-weight: bold;">متوفر: -</span>
                            </div>

                            <!-- متوسط -->
                            <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <span style="font-size: 11px; font-weight: bold; color: #475569;"><i class="bi bi-circle-fill text-warning"></i> متوسط:</span>
                                <div style="display: flex; gap: 6px; align-items: center;">
                                    <input type="number" name="configs[${configIndex}][medium_count]" class="form-control" min="0" placeholder="العدد" style="width: 70px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                    <span style="font-size: 10px; color: #94a3b8;">درجة:</span>
                                    <input type="number" name="configs[${configIndex}][medium_marks]" class="form-control" min="1" value="1" placeholder="الدرجة" style="width: 50px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                </div>
                                <span class="stats-badge stats-medium text-muted" style="font-size: 10px; font-weight: bold;">متوفر: -</span>
                            </div>

                            <!-- صعب -->
                            <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <span style="font-size: 11px; font-weight: bold; color: #475569;"><i class="bi bi-circle-fill text-danger"></i> صعب:</span>
                                <div style="display: flex; gap: 6px; align-items: center;">
                                    <input type="number" name="configs[${configIndex}][hard_count]" class="form-control" min="0" placeholder="العدد" style="width: 70px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                    <span style="font-size: 10px; color: #94a3b8;">درجة:</span>
                                    <input type="number" name="configs[${configIndex}][hard_marks]" class="form-control" min="1" value="1" placeholder="الدرجة" style="width: 50px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                </div>
                                <span class="stats-badge stats-hard text-muted" style="font-size: 10px; font-weight: bold;">متوفر: -</span>
                            </div>
                        </div>
                    </div>

                    <!-- النوع -->
                    <div class="config-section">
                        <h5 class="config-section-title"><i class="bi bi-ui-checks-grid text-primary"></i> نوع الأسئلة المتوفرة</h5>
                        <div class="config-inputs-row" style="display: flex; flex-direction: column; gap: 8px;">
                            <!-- خيارات -->
                            <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <span style="font-size: 11px; font-weight: bold; color: #475569;"><i class="bi bi-list-check text-primary"></i> خيارات (MCQ):</span>
                                <input type="number" name="configs[${configIndex}][mcq_count]" class="form-control" min="0" placeholder="العدد" style="width: 70px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                <span class="stats-badge stats-mcq text-muted" style="font-size: 10px; font-weight: bold;">متوفر: -</span>
                            </div>

                            <!-- صح/خطأ -->
                            <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <span style="font-size: 11px; font-weight: bold; color: #475569;"><i class="bi bi-check-circle text-info"></i> صح / خطأ:</span>
                                <input type="number" name="configs[${configIndex}][tf_count]" class="form-control" min="0" placeholder="العدد" style="width: 70px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                <span class="stats-badge stats-tf text-muted" style="font-size: 10px; font-weight: bold;">متوفر: -</span>
                            </div>

                            <!-- توصيل -->
                            <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <span style="font-size: 11px; font-weight: bold; color: #475569;"><i class="bi bi-arrow-left-right text-secondary"></i> توصيل (Matching):</span>
                                <input type="number" name="configs[${configIndex}][matching_count]" class="form-control" min="0" placeholder="العدد" style="width: 70px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                <span class="stats-badge stats-matching text-muted" style="font-size: 10px; font-weight: bold;">متوفر: -</span>
                            </div>

                            <!-- مقالي -->
                            <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <span style="font-size: 11px; font-weight: bold; color: #475569;"><i class="bi bi-file-earmark-text text-dark"></i> مقالي (Essay):</span>
                                <input type="number" name="configs[${configIndex}][essay_count]" class="form-control" min="0" placeholder="العدد" style="width: 70px; height: 30px; font-size: 11px; padding: 2px 6px;" oninput="validateConfigStats(this)">
                                <span class="stats-badge stats-essay text-muted" style="font-size: 10px; font-weight: bold;">متوفر: -</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- صندوق التنبيهات المباشر للمادة -->
            <div class="validation-summary" style="margin-top: 10px; font-size: 12px; font-weight: bold; padding: 0 10px;"></div>
        `;
        list.appendChild(div);
        configIndex++;
        validateGrades();
    }

    // جلب المواد التابعة للصف المختار
    window.loadConfigSubjects = function(selectEl, index) {
        const gradeId = selectEl.value;
        const card = selectEl.closest('.source-card');
        const subjectSel = card.querySelector('.subject-config-select');
        subjectSel.innerHTML = '<option value="">-- جاري التحميل --</option>';
        
        if (!gradeId) { 
            subjectSel.innerHTML = '<option value="">-- اختر الصف أولاً --</option>'; 
            validateGrades();
            loadSubjectStats(subjectSel);
            return; 
        }
        
        fetch(`{{ url('admin/subjects/by-grade') }}/${gradeId}`)
            .then(r => r.json())
            .then(subs => {
                subjectSel.innerHTML = '<option value="">-- اختر المادة --</option>';
                subs.forEach(s => { 
                    subjectSel.innerHTML += `<option value="${s.id}">${s.name}</option>`; 
                });
                validateGrades();
                loadSubjectStats(subjectSel);
            });
    }

    // جلب إحصائيات الأسئلة التابعة للمادة المختارة حية
    window.loadSubjectStats = function(selectEl) {
        const card = selectEl.closest('.source-card');
        const subjectId = selectEl.value;
        const gradeSelect = card.querySelector('.grade-config-select');
        const targetGradeId = gradeSelect ? gradeSelect.value : document.getElementById('target-grade').value;
        const advPanel = card.querySelector('.advanced-config-panel');
        const totalLabel = card.querySelector('.total-available-label');

        if (!subjectId || !targetGradeId) {
            card.querySelectorAll('.stats-badge').forEach(b => {
                b.textContent = 'متوفر: -';
                b.className = 'stats-badge text-muted';
                delete b.dataset.max;
            });
            delete selectEl.dataset.total;
            if (advPanel) advPanel.classList.remove('active');
            if (totalLabel) totalLabel.innerHTML = '';
            return;
        }

        fetch(`{{ url('admin/subjects') }}/${subjectId}/question-stats/${targetGradeId}`)
            .then(r => r.json())
            .then(stats => {
                // حفظ البيانات
                card.dataset.stats = JSON.stringify(stats);
                selectEl.dataset.total = stats.total;

                // تفعيل التوزيع المتقدم
                if (advPanel) advPanel.classList.add('active');

                // عرض إجمالي الأسئلة المتواجدة
                if (totalLabel) {
                    if (stats.total > 0) {
                        totalLabel.innerHTML = `<span class="text-success"><i class="bi bi-info-circle-fill"></i> إجمالي الأسئلة المتاحة: ${stats.total} سؤال</span>`;
                    } else {
                        totalLabel.innerHTML = `<span class="text-danger"><i class="bi bi-x-circle-fill"></i> لا توجد أي أسئلة متوفرة!</span>`;
                    }
                }

                const setStat = (selector, count, colorClass) => {
                    const el = card.querySelector(selector);
                    el.innerHTML = `<span class="${colorClass}">متوفر: ${count}</span>`;
                    el.dataset.max = count;
                };

                setStat('.stats-easy', stats.difficulties.easy, 'text-success');
                setStat('.stats-medium', stats.difficulties.medium, 'text-warning');
                setStat('.stats-hard', stats.difficulties.hard, 'text-danger');

                setStat('.stats-mcq', stats.types.mcq, 'text-primary');
                setStat('.stats-tf', stats.types.tf, 'text-info');
                setStat('.stats-matching', stats.types.matching, 'text-secondary');
                setStat('.stats-essay', stats.types.essay, 'text-dark');

                validateConfigStats(selectEl);
            });
    }

    // تحديث إحصائيات كل الكروت عند تغيير الصف المستهدف للاختبار
    window.updateAllStats = function() {
        document.querySelectorAll('.source-card').forEach(card => {
            const subjectSel = card.querySelector('.subject-config-select');
            if (subjectSel && subjectSel.value) {
                loadSubjectStats(subjectSel);
            }
        });
    }

    // التحقق الذكي من الإدخال مقارنة بالمتوفر في بنك الأسئلة
    window.validateConfigStats = function(el) {
        const card = el.closest('.source-card');
        if (!card) return;

        const qCountInput = card.querySelector('.question-count-input');
        const qCount = parseInt(qCountInput.value) || 0;
        const subjectSel = card.querySelector('.subject-config-select');
        const totalMax = parseInt(subjectSel.dataset.total);

        let hasError = false;
        let errorMsg = "";

        // التحقق من تواجد الأسئلة أصلاً
        if (!isNaN(totalMax)) {
            if (totalMax === 0) {
                qCountInput.style.borderColor = '#ef4444';
                qCountInput.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.15)';
                errorMsg += `<div class="text-danger" style="margin-bottom: 4px;"><i class="bi bi-x-circle-fill"></i> لا توجد أي أسئلة متوفرة لهذه المادة إطلاقاً في الصف المختار!</div>`;
                hasError = true;
            } else if (qCount > totalMax) {
                qCountInput.style.borderColor = '#ef4444';
                qCountInput.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.15)';
                errorMsg += `<div class="text-danger" style="margin-bottom: 4px;"><i class="bi bi-exclamation-circle-fill"></i> العدد المطلوب (${qCount}) يتجاوز المتاح للمادة (${totalMax}).</div>`;
                hasError = true;
            } else {
                qCountInput.style.borderColor = '';
                qCountInput.style.boxShadow = '';
            }
        }

        const getVal = (selector) => {
            const input = card.querySelector(selector);
            return input ? (parseInt(input.value) || 0) : 0;
        };
        const getMax = (selector) => {
            const stat = card.querySelector(selector);
            return stat ? (parseInt(stat.dataset.max) || 0) : 0;
        };

        const easyVal = getVal('input[name*="[easy_count]"]');
        const easyMax = getMax('.stats-easy');
        const mediumVal = getVal('input[name*="[medium_count]"]');
        const mediumMax = getMax('.stats-medium');
        const hardVal = getVal('input[name*="[hard_count]"]');
        const hardMax = getMax('.stats-hard');

        const mcqVal = getVal('input[name*="[mcq_count]"]');
        const mcqMax = getMax('.stats-mcq');
        const tfVal = getVal('input[name*="[tf_count]"]');
        const tfMax = getMax('.stats-tf');
        const matchingVal = getVal('input[name*="[matching_count]"]');
        const matchingMax = getMax('.stats-matching');
        const essayVal = getVal('input[name*="[essay_count]"]');
        const essayMax = getMax('.stats-essay');



        const validateField = (inputSelector, val, max, statsSelector, label) => {
            const input = card.querySelector(inputSelector);
            const statsEl = card.querySelector(statsSelector);
            if (!input || !statsEl) return;
            if (val > max) {
                input.style.borderColor = '#ef4444';
                input.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.15)';
                statsEl.innerHTML = `<span class="text-danger" style="font-weight:900;"><i class="bi bi-x-circle-fill"></i> تجاوز المتوفر (${max})</span>`;
                hasError = true;
            } else {
                input.style.borderColor = '';
                input.style.boxShadow = '';
                // إرجاع اللون الأصلي
                const colorMap = {
                    '.stats-easy': ['text-success', 'سهل'],
                    '.stats-medium': ['text-warning', 'متوسط'],
                    '.stats-hard': ['text-danger', 'صعب'],
                    '.stats-mcq': ['text-primary', 'خيارات'],
                    '.stats-tf': ['text-info', 'صح/خطأ'],
                    '.stats-matching': ['text-secondary', 'توصيل'],
                    '.stats-essay': ['text-dark', 'مقالي']
                };
                if (colorMap[statsSelector]) {
                    statsEl.innerHTML = `<span class="${colorMap[statsSelector][0]}">متوفر: ${max}</span>`;
                }
            }
        };

        validateField('input[name*="[easy_count]"]', easyVal, easyMax, '.stats-easy');
        validateField('input[name*="[medium_count]"]', mediumVal, mediumMax, '.stats-medium');
        validateField('input[name*="[hard_count]"]', hardVal, hardMax, '.stats-hard');

        validateField('input[name*="[mcq_count]"]', mcqVal, mcqMax, '.stats-mcq');
        validateField('input[name*="[tf_count]"]', tfVal, tfMax, '.stats-tf');
        validateField('input[name*="[matching_count]"]', matchingVal, matchingMax, '.stats-matching');
        validateField('input[name*="[essay_count]"]', essayVal, essayMax, '.stats-essay');

        // التحقق من تماشي المجموع الكلي
        const diffSum = easyVal + mediumVal + hardVal;
        const typeSum = mcqVal + tfVal + matchingVal + essayVal;

        if (diffSum > 0 && diffSum !== qCount) {
            errorMsg += `<div class="text-danger" style="margin-bottom: 4px;"><i class="bi bi-exclamation-triangle-fill"></i> مجموع مستويات الصعوبة (${diffSum}) لا يساوي إجمالي الأسئلة المطلوب (${qCount}).</div>`;
            hasError = true;
        }

        if (typeSum > 0 && typeSum !== qCount) {
            errorMsg += `<div class="text-danger"><i class="bi bi-exclamation-triangle-fill"></i> مجموع أنواع الأسئلة (${typeSum}) لا يساوي إجمالي الأسئلة المطلوب (${qCount}).</div>`;
            hasError = true;
        }

        card.querySelector('.validation-summary').innerHTML = errorMsg;

        // تعطيل أو تمكين زر الإرسال بناءً على كل الكروت
        const submitBtn = document.getElementById('submit-btn');
        let anyCardError = false;
        
        // التحقق مما إذا كان هناك أي كرت يحتوي على أخطاء
        document.querySelectorAll('.source-card').forEach(c => {
            const summary = c.querySelector('.validation-summary').textContent.trim();
            if (summary !== "") anyCardError = true;
            
            // تحقق إذا كان أي مدخل أحمر
            c.querySelectorAll('input').forEach(inp => {
                if (inp.style.borderColor === 'rgb(239, 68, 68)' || inp.style.borderColor === '#ef4444') {
                    anyCardError = true;
                }
            });
        });

        if (anyCardError) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
        } else {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        }
    }

    // التحقق الذكي من حظر سحب الأسئلة من نفس الصف المستهدف
    window.validateGrades = function() {
        const targetGradeId = document.getElementById('target-grade').value;
        const gradeSelects = document.querySelectorAll('.grade-config-select');
        const alertBox = document.getElementById('guard-alert-box');
        const submitBtn = document.getElementById('submit-btn');
        
        let hasConflict = false;
        
        if (targetGradeId) {
            gradeSelects.forEach(select => {
                if (select.value && select.value === targetGradeId) {
                    hasConflict = true;
                }
            });
        }
        
        if (hasConflict) {
            alertBox.style.display = 'flex';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
        } else {
            alertBox.style.display = 'none';
            // نقوم بالتحقق من أخطاء الكروت لتحديث الزر
            const anyCard = document.querySelector('.source-card input');
            if (anyCard) validateConfigStats(anyCard);
        }
    }

    // ربط تغيير الصف المستهدف لتحديث الإحصائيات فورياً
    document.getElementById('target-grade').addEventListener('change', () => {
        validateGrades();
        updateAllStats();
    });
</script>
@endpush
