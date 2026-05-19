@extends('layouts.admin')

@section('title', 'إنشاء سؤال جديد')
@section('page-title', 'إنشاء سؤال جديد')

@section('breadcrumb')
    <a href="{{ route('admin.questions.index') }}" style="color: var(--text-muted); font-weight: 500; text-decoration: none;">بنك الأسئلة</a>
    <span style="color: #cbd5e0; margin: 0 4px;"><i class="bi bi-chevron-left" style="font-size: 10px;"></i></span>
    <span style="color: var(--text-main); font-weight: 700;">إنشاء سؤال</span>
@endsection

@push('styles')
<style>
    /* ── تخطيط مساحة العمل (Workspace Layout) ── */
    .workspace-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 24px;
        align-items: start;
        margin-bottom: 100px; /* مساحة للشريط العائم */
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
        padding: 18px 24px; border-bottom: 1px solid #f1f5f9; background: #fcfcfc;
        font-size: 15px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 10px;
    }
    .ws-card-header i { font-size: 18px; }
    .ws-card-body { padding: 24px; }

    /* ── الحقول والقوائم المنسدلة ── */
    .ws-label { display: block; font-size: 13.5px; font-weight: 700; color: #475569; margin-bottom: 8px; }
    .ws-select, .ws-input {
        width: 100%; padding: 12px 16px; border-radius: 12px; border: 1.5px solid #cbd5e1;
        font-size: 13.5px; font-family: inherit; background: #f8fafc; transition: all 0.2s;
        color: #1e293b; outline: none;
    }
    .ws-select:focus, .ws-input:focus { background: #fff; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(118, 181, 27, 0.12); }
    .ws-group { margin-bottom: 20px; }
    .ws-group:last-child { margin-bottom: 0; }

    /* ── محرر النصوص (Rich Text Mockup) ── */
    .editor-container { border: 1.5px solid #cbd5e1; border-radius: 12px; overflow: hidden; transition: all 0.2s; }
    .editor-container:focus-within { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(118, 181, 27, 0.12); }
    .editor-toolbar {
        display: flex; gap: 4px; padding: 10px 14px; background: #f8fafc; border-bottom: 1.5px solid #cbd5e1; flex-wrap: wrap;
    }
    .editor-btn {
        width: 32px; height: 32px; border-radius: 8px; border: none; background: transparent;
        color: #64748b; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;
    }
    .editor-btn:hover { background: #e2e8f0; color: #1e293b; }
    .editor-textarea {
        width: 100%; min-height: 140px; padding: 16px; border: none; resize: vertical;
        font-size: 14px; line-height: 1.6; outline: none; font-family: 'Cairo', sans-serif;
    }

    /* ── تصميم الخيارات (Smart Option Rows) ── */
    .options-container { display: flex; flex-direction: column; gap: 12px; margin-top: 20px; }
    .option-row {
        display: flex; align-items: center; gap: 12px; padding: 10px 14px 10px 18px;
        border: 2px solid #e2e8f0; border-radius: 12px; background: #fff; transition: all 0.25s;
    }
    .option-row:focus-within { border-color: #cbd5e0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .option-row.is-correct { border-color: #22c55e; background: #f0fdf4; box-shadow: 0 4px 16px rgba(34,197,94,0.15); }
    
    /* زر التحديد الصحيح (Check Button) */
    .check-btn {
        width: 32px; height: 32px; border-radius: 50%; border: 2.5px solid #cbd5e0; background: #fff;
        display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; flex-shrink: 0;
    }
    .check-btn i { font-size: 16px; color: #fff; opacity: 0; transform: scale(0.5); transition: all 0.2s; }
    .check-btn:hover { border-color: var(--primary); }
    .option-row.is-correct .check-btn { background: #22c55e; border-color: #22c55e; }
    .option-row.is-correct .check-btn i { opacity: 1; transform: scale(1); }

    /* حقل نص الخيار */
    .option-input-wrapper { flex: 1; display: flex; align-items: center; gap: 8px; }
    .option-letter { font-size: 14px; font-weight: 800; color: #94a3b8; width: 24px; text-align: center; }
    .option-row.is-correct .option-letter { color: #166534; }
    .option-input { width: 100%; border: none; background: transparent; font-size: 14px; outline: none; color: #1e293b; padding: 8px 0; font-family: 'Cairo', sans-serif; font-weight: 600; }
    .option-input::placeholder { color: #cbd5e0; font-weight: 500; }

    /* زر الحذف */
    .delete-btn {
        width: 32px; height: 32px; border-radius: 8px; border: none; background: transparent;
        color: #cbd5e0; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;
    }
    .delete-btn:hover { background: #fef2f2; color: #ef4444; }

    .add-option-btn {
        display: inline-flex; align-items: center; gap: 6px; padding: 12px 20px; margin-top: 16px;
        background: transparent; color: var(--primary); font-size: 13.5px; font-weight: 700;
        border: 2px dashed var(--primary); border-radius: 12px; cursor: pointer; transition: all 0.2s;
        outline: none;
    }
    .add-option-btn:hover { background: var(--primary-light); }

    /* ── الشريط العائم (Sticky Footer Actions) ── */
    .sticky-actions {
        position: fixed; bottom: 0; left: 0; width: 100%; background: #fff;
        padding: 16px 24px; border-top: 1px solid #e2e8f0; box-shadow: 0 -4px 20px rgba(0,0,0,0.04);
        display: flex; justify-content: flex-end; gap: 12px; z-index: 100;
        padding-right: calc(var(--sidebar-width) + 24px); 
        transition: all 0.3s ease;
    }
    @media (max-width: 1024px) { .sticky-actions { padding-right: 24px; } }
</style>
@endpush

@section('content')
<form method="POST" action="{{ route('admin.questions.store') }}" id="question-form">
    @csrf
    
    <div class="workspace-grid">
        
        <div class="ws-sidebar">
            <div class="ws-card">
                <div class="ws-card-header">
                    <i class="bi bi-sliders text-primary"></i> إعدادات السؤال
                </div>
                <div class="ws-card-body">
                    
                    <div class="ws-group">
                        <label class="ws-label">الصف الدراسي <span class="text-danger">*</span></label>
                        <select name="grade_id" id="grade-select" class="ws-select" onchange="loadSubjects(this.value)" required>
                            <option value="">-- اختر الصف --</option>
                            @foreach($grades as $g)
                                <option value="{{ $g->id }}" {{ old('grade_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="ws-group">
                        <label class="ws-label">المادة الدراسية <span class="text-danger">*</span></label>
                        <select name="subject_id" id="subject-select" class="ws-select {{ $errors->has('subject_id') ? 'is-invalid' : '' }}" required disabled>
                            <option value="">حدد الصف أولاً</option>
                        </select>
                        @error('subject_id')<span class="text-danger" style="font-size: 11px; margin-top:6px; display:block; font-weight: 700;"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</span>@enderror
                    </div>

                    <hr style="border: 0; border-top: 1px dashed #e2e8f0; margin: 20px 0;">

                    <div class="ws-group">
                        <label class="ws-label">نوع السؤال</label>
                        <select name="type" class="ws-select">
                            <option value="mcq">اختيار من متعدد</option>
                            <option value="tf">صح أو خطأ</option>
                            <option value="matching">سؤال توصيل</option>
                            <option value="essay">سؤال مقالي</option>
                        </select>
                    </div>

                    <div class="ws-group">
                        <label class="ws-label">مستوى الصعوبة</label>
                        <select name="difficulty" class="ws-select">
                            <option value="easy">سهل</option>
                            <option value="medium" selected>متوسط</option>
                            <option value="hard">صعب</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>

        <div class="ws-main">
            <div class="ws-card">
                <div class="ws-card-header" style="border-bottom: none; padding-bottom: 0;">
                    <i class="bi bi-pencil-square text-primary"></i> محتوى السؤال والخيارات
                </div>
                <div class="ws-card-body">
                    
                    <div class="ws-group">
                        <label class="ws-label">نص السؤال <span class="text-danger">*</span></label>
                        <div class="editor-container">
                            <div class="editor-toolbar">
                                <button type="button" class="editor-btn" title="عريض"><i class="bi bi-type-bold"></i></button>
                                <button type="button" class="editor-btn" title="مائل"><i class="bi bi-type-italic"></i></button>
                                <button type="button" class="editor-btn" title="تسطير"><i class="bi bi-type-underline"></i></button>
                                <div style="width: 1px; background: #cbd5e0; margin: 0 6px;"></div>
                                <button type="button" class="editor-btn" title="قائمة نقطية"><i class="bi bi-list-ul"></i></button>
                                <button type="button" class="editor-btn" title="قائمة رقمية"><i class="bi bi-list-ol"></i></button>
                                <div style="width: 1px; background: #cbd5e0; margin: 0 6px;"></div>
                                <button type="button" class="editor-btn" title="إدراج صورة"><i class="bi bi-image"></i></button>
                                <button type="button" class="editor-btn" title="إدراج معادلة"><i class="bi bi-infinity"></i></button>
                            </div>
                            <textarea name="text" class="editor-textarea" placeholder="اكتب نص السؤال هنا بوضوح وسلاسة..." required>{{ old('text') }}</textarea>
                        </div>
                        @error('text')<span class="text-danger" style="font-size: 11px; margin-top:6px; display:block; font-weight: 700;"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</span>@enderror
                    </div>

                    <hr style="border: 0; border-top: 1px dashed #e2e8f0; margin: 24px 0;">

                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                            <label class="ws-label" style="margin: 0;">خيارات الإجابة <span class="text-danger">*</span></label>
                            <span style="font-size: 11.5px; color: #64748b; background: #f1f5f9; padding: 6px 12px; border-radius: 20px; font-weight: 600;">
                                <i class="bi bi-check2-circle text-success" style="font-size: 13px;"></i> انقر على الدائرة لتحديد الإجابة الصحيحة
                            </span>
                        </div>
                        
                        <input type="hidden" name="correct_choice" id="correct-choice-input" value="{{ old('correct_choice', 0) }}">
                        
                        <div class="options-container" id="options-list">
                            <!-- سيتم توليد الخيارات عبر JS -->
                        </div>

                        <button type="button" class="add-option-btn" onclick="addOption()">
                            <i class="bi bi-plus-lg"></i> إضافة خيار آخر
                        </button>

                        @error('choices')<span class="text-danger" style="font-size: 13px; font-weight: 700; margin-top:16px; display:block;"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</span>@enderror
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="sticky-actions">
        <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary" style="padding: 12px 24px; border-radius: 12px; font-weight: 700; border: 1.5px solid var(--border); background: #fff; color: var(--text-main);">
            إلغاء الأمر
        </a>
        <button type="submit" class="btn btn-primary" style="padding: 12px 32px; border-radius: 12px; font-size: 14px; font-weight: 700; background: linear-gradient(135deg, #76b51b, #5f9416); border: none; box-shadow: 0 6px 20px rgba(118, 181, 27, 0.25);">
            <i class="bi bi-check-circle-fill" style="margin-left: 6px;"></i> حفظ السؤال في البنك
        </button>
    </div>

</form>
@endsection

@push('scripts')
<script>
    // ── منطق إدارة الخيارات التفاعلية ──
    const optionsList = document.getElementById('options-list');
    const correctInput = document.getElementById('correct-choice-input');
    const letters = ['أ', 'ب', 'ج', 'د', 'هـ', 'و', 'ز', 'ح']; 
    
    const oldChoices = @json(old('choices', []));
    const oldCorrect = parseInt("{{ old('correct_choice', 0) }}");

    function initOptions() {
        if (oldChoices.length > 0) {
            oldChoices.forEach((ch, i) => createOptionRow(i, ch.text, i === oldCorrect));
        } else {
            const typeSelect = document.querySelector('select[name="type"]');
            const type = typeSelect ? typeSelect.value : 'mcq';
            const count = type === 'tf' ? 2 : (type === 'essay' ? 1 : (type === 'matching' ? 3 : 4));
            for(let i=0; i<count; i++) { createOptionRow(i, '', i === 0); }
        }
        reindexOptions();
    }

    window.updateMatchingValue = function(element, index) {
        const row = element.closest('.option-row');
        if (!row) return;
        const rightInput = row.querySelector('.matching-right');
        const leftInput = row.querySelector('.matching-left');
        const realInput = row.querySelector('.real-choice-input');
        if (rightInput && leftInput && realInput) {
            realInput.value = rightInput.value + '|' + leftInput.value;
        }
    }

    function createOptionRow(index, text = '', isCorrect = false) {
        const row = document.createElement('div');
        row.className = `option-row ${isCorrect ? 'is-correct' : ''}`;
        
        const typeSelect = document.querySelector('select[name="type"]');
        const isMatching = typeSelect && typeSelect.value === 'matching';
        const isEssay = typeSelect && typeSelect.value === 'essay';
        
        if (isMatching) {
            let rightText = '';
            let leftText = '';
            if (text && text.includes('|')) {
                const parts = text.split('|');
                rightText = parts[0];
                leftText = parts[1];
            } else {
                rightText = text;
            }
            
            row.innerHTML = `
                <div class="check-btn" style="display: none;">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="option-input-wrapper" style="display: flex; gap: 12px; width: 100%; border: none; background: transparent; padding: 0; align-items: center;">
                    <span class="option-letter" style="flex-shrink: 0; min-width: 24px;">-</span>
                    <input type="text" class="option-input matching-right" value="${rightText}" placeholder="اليمين (مثال: عطارد)" required style="flex: 1;" oninput="updateMatchingValue(this, ${index})">
                    <div style="display: flex; align-items: center; color: var(--primary); font-weight: 800;"><i class="bi bi-arrow-left-right"></i></div>
                    <input type="text" class="option-input matching-left" value="${leftText}" placeholder="اليسار (مثال: الكوكب الأقرب للشمس)" required style="flex: 1;" oninput="updateMatchingValue(this, ${index})">
                    <input type="hidden" class="option-input real-choice-input" name="choices[${index}][text]" value="${text}">
                </div>
                <button type="button" class="delete-btn" title="حذف هذا الخيار">
                    <i class="bi bi-trash3"></i>
                </button>
            `;
        } else if (isEssay) {
            row.innerHTML = `
                <div class="check-btn" style="display: none;">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="option-input-wrapper">
                    <span class="option-letter"><i class="bi bi-blockquote-left" style="color: var(--primary);"></i></span>
                    <input type="text" class="option-input" name="choices[${index}][text]" value="${text}" placeholder="اكتب الجواب النموذجي (أو الكلمات المفتاحية المعتمدة)..." required autocomplete="off">
                </div>
                <button type="button" class="delete-btn" style="display: none;">
                    <i class="bi bi-trash3"></i>
                </button>
            `;
        } else {
            row.innerHTML = `
                <div class="check-btn" title="تحديد كإجابة صحيحة">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="option-input-wrapper">
                    <span class="option-letter">-</span>
                    <input type="text" class="option-input" name="choices[${index}][text]" value="${text}" placeholder="اكتب الخيار..." required autocomplete="off">
                </div>
                <button type="button" class="delete-btn" title="حذف هذا الخيار">
                    <i class="bi bi-trash3"></i>
                </button>
            `;
        }
        optionsList.appendChild(row);
    }

    window.addOption = function() {
        if (optionsList.children.length >= 8) {
            Swal.fire({
                title: 'تنبيه',
                text: 'الحد الأقصى للخيارات هو 8 خيارات فقط.',
                icon: 'warning',
                confirmButtonText: 'حسناً',
                confirmButtonColor: 'var(--primary)'
            });
            return;
        }
        createOptionRow(optionsList.children.length);
        reindexOptions();
        
        // التركيز على الحقل المضاف
        const inputs = optionsList.querySelectorAll('.option-input');
        if(inputs.length > 0) inputs[inputs.length - 1].focus();
    }

    function reindexOptions() {
        const rows = optionsList.querySelectorAll('.option-row');
        const typeSelect = document.querySelector('select[name="type"]');
        const isMatching = typeSelect && typeSelect.value === 'matching';
        const isEssay = typeSelect && typeSelect.value === 'essay';
        
        rows.forEach((row, index) => {
            const letterSpan = row.querySelector('.option-letter');
            if (letterSpan && !isEssay) {
                letterSpan.textContent = letters[index] + '.';
            }
            
            if (isMatching) {
                const realInput = row.querySelector('.real-choice-input');
                if (realInput) {
                    realInput.name = `choices[${index}][text]`;
                }
                const rInput = row.querySelector('.matching-right');
                if (rInput) rInput.oninput = function() { updateMatchingValue(this, index); };
                const lInput = row.querySelector('.matching-left');
                if (lInput) lInput.oninput = function() { updateMatchingValue(this, index); };
            } else {
                const normalInput = row.querySelector('.option-input');
                if (normalInput) {
                    normalInput.name = `choices[${index}][text]`;
                    if (!isEssay) {
                        normalInput.placeholder = `الخيار ${letters[index]}...`;
                    }
                }
            }
            
            const checkBtn = row.querySelector('.check-btn');
            if (checkBtn) {
                checkBtn.onclick = function() {
                    rows.forEach(r => r.classList.remove('is-correct'));
                    row.classList.add('is-correct');
                    correctInput.value = index;
                };
            }

            const delBtn = row.querySelector('.delete-btn');
            if (delBtn) {
                delBtn.onclick = function() {
                    const minRows = isMatching ? 3 : 2;
                    if (optionsList.children.length <= minRows) {
                        Swal.fire({
                            title: 'تنبيه',
                            text: `يجب توفر ${minRows} خيارات على الأقل لهذا النوع من الأسئلة.`,
                            icon: 'warning',
                            confirmButtonText: 'حسناً',
                            confirmButtonColor: 'var(--primary)'
                        });
                        return;
                    }
                    
                    if (row.classList.contains('is-correct')) {
                        const nextOrPrev = row.nextElementSibling || row.previousElementSibling;
                        if(nextOrPrev) { nextOrPrev.classList.add('is-correct'); }
                    }
                    
                    row.remove();
                    reindexOptions();
                    
                    const currentCorrectRow = Array.from(optionsList.children).findIndex(r => r.classList.contains('is-correct'));
                    correctInput.value = currentCorrectRow >= 0 ? currentCorrectRow : 0;
                };
            }
        });
    }

    // ── API المواد بناءً على الصف ──
    window.loadSubjects = function(gradeId) {
        const sel = document.getElementById('subject-select');
        if (!gradeId) { 
            sel.innerHTML = '<option value="">حدد الصف أولاً</option>'; 
            sel.disabled = true; return; 
        }
        
        sel.disabled = false;
        sel.innerHTML = '<option value="">جاري تحميل المواد...</option>';
        
        fetch(`{{ url('admin/subjects/by-grade') }}/${gradeId}`)
            .then(r => r.json())
            .then(subjects => {
                sel.innerHTML = '<option value="">-- اختر المادة --</option>';
                subjects.forEach(s => {
                    const selected = s.id == "{{ old('subject_id') }}" ? 'selected' : '';
                    sel.innerHTML += `<option value="${s.id}" ${selected}>${s.name}</option>`;
                });
            })
            .catch(() => sel.innerHTML = '<option value="">خطأ في التحميل</option>');
    }

    // دالة التعامل مع تغيير نوع السؤال
    function handleTypeChange() {
        const typeSelect = document.querySelector('select[name="type"]');
        if (!typeSelect) return;
        
        const addBtn = document.querySelector('.add-option-btn');
        const type = typeSelect.value;
        
        // إفراغ الخيارات الحالية وبناء التوزيع المناسب
        optionsList.innerHTML = '';
        
        if (type === 'tf') {
            if (addBtn) addBtn.style.display = 'none';
            createOptionRow(0, 'صح', true);
            createOptionRow(1, 'خطأ', false);
            reindexOptions();
            
            const inputs = optionsList.querySelectorAll('.option-input');
            if (inputs[0]) { inputs[0].readOnly = true; inputs[0].placeholder = 'صح'; }
            if (inputs[1]) { inputs[1].readOnly = true; inputs[1].placeholder = 'خطأ'; }
            
            const delBtns = optionsList.querySelectorAll('.delete-btn');
            delBtns.forEach(btn => btn.style.display = 'none');
            
            correctInput.value = 0;
        } else if (type === 'essay') {
            if (addBtn) addBtn.style.display = 'none';
            createOptionRow(0, '', true);
            reindexOptions();
            correctInput.value = 0;
        } else if (type === 'matching') {
            if (addBtn) addBtn.style.display = 'inline-flex';
            for(let i=0; i<3; i++) {
                createOptionRow(i, '', i === 0);
            }
            reindexOptions();
            correctInput.value = 0;
        } else {
            // MCQ اختيار من متعدد
            if (addBtn) addBtn.style.display = 'inline-flex';
            for(let i=0; i<4; i++) {
                createOptionRow(i, '', i === 0);
            }
            reindexOptions();
        }
    }

    // التهيئة
    initOptions();
    const initGrade = document.getElementById('grade-select').value;
    if(initGrade) loadSubjects(initGrade);

    const typeSelect = document.querySelector('select[name="type"]');
    if (typeSelect) {
        typeSelect.addEventListener('change', handleTypeChange);
        // عند التحميل الأولي، نقوم بتفعيل النوع المحدد افتراضياً (أو MCQ)
        if (oldChoices.length === 0) {
            handleTypeChange();
        }
    }

</script>
@endpush
