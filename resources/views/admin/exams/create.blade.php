@extends('layouts.admin')
@section('title', 'إنشاء اختبار')
@section('page-title', 'إنشاء اختبار جديد')

@push('styles')
<style>
.config-row {
    display: grid; grid-template-columns: 1fr 1fr auto;
    gap: 12px; align-items: end;
    padding: 14px; background: #f8fafc;
    border: 1.5px solid var(--border); border-radius: 10px;
    margin-bottom: 10px;
}
.config-row label { font-size:12px; margin-bottom:4px; }
.btn-remove-config { background: none; border: 1px solid var(--danger); color: var(--danger); border-radius: 8px; padding: 8px 10px; cursor: pointer; transition: all .2s; }
.btn-remove-config:hover { background: var(--danger); color: #fff; }
.btn-add-config { display: flex; align-items: center; gap: 6px; padding: 8px 16px; border: 2px dashed var(--border); border-radius: 10px; background: transparent; color: var(--primary); font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; width: fit-content; transition: all .2s; }
.btn-add-config:hover { border-color: var(--primary); }
.info-box { background: rgba(37,99,235,.06); border: 1px solid rgba(37,99,235,.15); border-radius: 10px; padding: 12px 16px; font-size: 13px; color: var(--primary); display: flex; align-items: flex-start; gap: 8px; margin-bottom: 20px; }
</style>
@endpush

@section('content')
<div class="card" style="max-width:800px">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-plus-circle text-primary"></i> بيانات الاختبار</div>
        <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-right"></i> رجوع</a>
    </div>
    <div class="card-body">
        <div class="info-box">
            <i class="bi bi-info-circle-fill" style="flex-shrink:0;margin-top:1px"></i>
            <span>ملاحظة: لا يمكن سحب الأسئلة من نفس الصف المستهدف للاختبار. تأكد من وجود أسئلة كافية في المواد المختارة.</span>
        </div>

        <form method="POST" action="{{ route('admin.exams.store') }}" id="exam-form">
            @csrf

            <div class="grid-2">
                <div class="form-group">
                    <label>السنة الدراسية *</label>
                    <select name="academic_year_id" class="form-control" required>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $year->id == $currentYearId ? 'selected' : '' }}>
                                {{ $year->name }} {{ $year->is_current ? '(الحالية)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>الصف المستهدف للاختبار *</label>
                    <select name="grade_id" id="target-grade" class="form-control" required>
                        <option value="">-- اختر الصف --</option>
                        @foreach($grades as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>عنوان الاختبار *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="مثال: اختبار القبول للصف الأول المتوسط 2024" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>الدرجة الكلية *</label>
                    <input type="number" name="total_marks" class="form-control" value="{{ old('total_marks', 100) }}" min="1" required>
                </div>
                <div class="form-group">
                    <label>درجة النجاح *</label>
                    <input type="number" name="pass_marks" class="form-control" value="{{ old('pass_marks', 60) }}" min="1" required>
                </div>
            </div>

            <div class="divider"></div>
            <label style="font-size:15px;font-weight:700;margin-bottom:12px;display:block">مصادر الأسئلة</label>

            <div id="configs-list">
                <div class="config-row">
                    <div>
                        <label>الصف</label>
                        <select name="configs[0][grade_id]" class="form-control grade-config-select" onchange="loadConfigSubjects(this, 0)">
                            <option value="">-- اختر الصف --</option>
                            @foreach($grades as $g)
                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>المادة *</label>
                        <select name="configs[0][subject_id]" class="form-control subject-config-select" required>
                            <option value="">-- اختر أولاً الصف --</option>
                        </select>
                    </div>
                    <div>
                        <label>عدد الأسئلة *</label>
                        <input type="number" name="configs[0][question_count]" class="form-control" value="5" min="1" required>
                    </div>
                </div>
            </div>

            <button type="button" class="btn-add-config" onclick="addConfig()">
                <i class="bi bi-plus-circle"></i> إضافة مادة أخرى
            </button>

            <div class="divider"></div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> إنشاء الاختبار</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let configIndex = 1;
const grades = @json($grades);

function addConfig() {
    const list = document.getElementById('configs-list');
    const div = document.createElement('div');
    div.className = 'config-row';
    div.innerHTML = `
        <div>
            <label>الصف</label>
            <select name="configs[${configIndex}][grade_id]" class="form-control grade-config-select" onchange="loadConfigSubjects(this, ${configIndex})">
                <option value="">-- اختر الصف --</option>
                ${grades.map(g => `<option value="${g.id}">${g.name}</option>`).join('')}
            </select>
        </div>
        <div>
            <label>المادة *</label>
            <select name="configs[${configIndex}][subject_id]" class="form-control subject-config-select" required>
                <option value="">-- اختر أولاً الصف --</option>
            </select>
        </div>
        <div>
            <label>عدد الأسئلة *</label>
            <input type="number" name="configs[${configIndex}][question_count]" class="form-control" value="5" min="1" required>
        </div>
        <button type="button" class="btn-remove-config" onclick="this.closest('.config-row').remove()">
            <i class="bi bi-trash"></i>
        </button>
    `;
    list.appendChild(div);
    configIndex++;
}

function loadConfigSubjects(selectEl, index) {
    const gradeId = selectEl.value;
    const row = selectEl.closest('.config-row');
    const subjectSel = row.querySelector('.subject-config-select');
    subjectSel.innerHTML = '<option value="">-- جاري التحميل --</option>';
    if (!gradeId) { subjectSel.innerHTML = '<option value="">-- اختر المادة --</option>'; return; }
    fetch(`/admin/subjects/by-grade/${gradeId}`)
        .then(r => r.json())
        .then(subs => {
            subjectSel.innerHTML = '<option value="">-- اختر المادة --</option>';
            subs.forEach(s => { subjectSel.innerHTML += `<option value="${s.id}">${s.icon ?? ''} ${s.name}</option>`; });
        });
}
</script>
@endpush
