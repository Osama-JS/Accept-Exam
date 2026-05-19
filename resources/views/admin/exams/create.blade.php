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
        display: grid; grid-template-columns: 1.1fr 1.3fr 0.8fr; gap: 14px;
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
                            <select name="grade_id" id="target-grade" class="form-control" onchange="validateGrades()" required>
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
                        <label>الدرجة الكلية *</label>
                        <div class="input-icon-group">
                            <i class="bi bi-award"></i>
                            <input type="number" name="total_marks" class="form-control" value="{{ old('total_marks', 100) }}" min="1" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>درجة النجاح *</label>
                        <div class="input-icon-group">
                            <i class="bi bi-check-circle"></i>
                            <input type="number" name="pass_marks" class="form-control" value="{{ old('pass_marks', 60) }}" min="1" required>
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
                                    <select name="configs[0][subject_id]" class="form-control subject-config-select" onchange="validateGrades()" required>
                                        <option value="">-- اختر الصف أولاً --</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label>الأسئلة *</label>
                                <div class="input-icon-group">
                                    <i class="bi bi-question-circle"></i>
                                    <input type="number" name="configs[0][question_count]" class="form-control" value="5" min="1" required>
                                </div>
                            </div>
                        </div>
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
                        <select name="configs[${configIndex}][subject_id]" class="form-control subject-config-select" onchange="validateGrades()" required>
                            <option value="">-- اختر الصف أولاً --</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label>الأسئلة *</label>
                    <div class="input-icon-group">
                        <i class="bi bi-question-circle"></i>
                        <input type="number" name="configs[${configIndex}][question_count]" class="form-control" value="5" min="1" required>
                    </div>
                </div>
            </div>
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
            });
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
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        }
    }
</script>
@endpush
