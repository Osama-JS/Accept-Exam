@extends('layouts.admin')
@section('title', 'تعديل السؤال')
@section('page-title', 'تعديل السؤال')

@push('styles')
<style>
.choices-list { display: flex; flex-direction: column; gap: 10px; }
.choice-item { display: flex; align-items: center; gap: 10px; background: #f8fafc; border: 1.5px solid var(--border); border-radius: 10px; padding: 10px 14px; transition: border-color .2s; }
.choice-item.selected { border-color: var(--success); background: rgba(16,185,129,.05); }
.choice-radio { width: 18px; height: 18px; accent-color: var(--success); cursor: pointer; flex-shrink: 0; }
.choice-text { flex: 1; border: none; background: transparent; font-family: 'Cairo', sans-serif; font-size: 14px; outline: none; }
.btn-remove-choice { background: none; border: none; color: var(--danger); cursor: pointer; font-size: 18px; padding: 0 4px; }
.btn-add-choice { display: flex; align-items: center; gap: 6px; padding: 8px 16px; border: 2px dashed var(--border); border-radius: 10px; background: transparent; color: var(--primary); font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .2s; width: fit-content; }
.btn-add-choice:hover { border-color: var(--primary); }
</style>
@endpush

@section('content')
<div class="card" style="max-width:760px">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-pencil text-warning"></i> تعديل السؤال</div>
        <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-right"></i> رجوع</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.questions.update', $question) }}">
            @csrf @method('PUT')

            <div class="grid-2">
                <div class="form-group">
                    <label>الصف الدراسي</label>
                    <select id="grade-select" class="form-control" onchange="loadSubjects(this.value, {{ $question->subject->grade_id }})">
                        <option value="">-- اختر الصف --</option>
                        @foreach($grades as $g)
                            <option value="{{ $g->id }}" {{ $question->subject->grade_id == $g->id ? 'selected' : '' }}>
                                {{ $g->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>المادة الدراسية *</label>
                    <select name="subject_id" id="subject-select" class="form-control" required>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" {{ $question->subject_id == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>نص السؤال *</label>
                <textarea name="text" class="form-control" rows="3" required>{{ old('text', $question->text) }}</textarea>
            </div>

            <div class="form-group">
                <label>الخيارات *</label>
                <input type="hidden" name="correct_choice" id="correct-choice-input" value="0">
                <div class="choices-list" id="choices-list">
                    @foreach($question->choices as $i => $choice)
                    <div class="choice-item {{ $choice->is_correct ? 'selected' : '' }}" data-index="{{ $i }}">
                        <input type="radio" class="choice-radio" name="_correct" value="{{ $i }}"
                            {{ $choice->is_correct ? 'checked' : '' }} onchange="setCorrect({{ $i }})">
                        <input type="text" class="choice-text" name="choices[{{ $i }}][text]"
                            value="{{ $choice->text }}" required>
                        <button type="button" class="btn-remove-choice" onclick="removeChoice(this)">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="btn-add-choice" onclick="addChoice()" style="margin-top:10px">
                    <i class="bi bi-plus-circle"></i> إضافة خيار
                </button>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> حفظ التعديلات</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let choiceIndex = {{ $question->choices->count() }};

// Set correct choice on load
document.querySelectorAll('.choice-radio:checked').forEach(r => {
    document.getElementById('correct-choice-input').value = r.value;
});

function setCorrect(index) {
    document.getElementById('correct-choice-input').value = index;
    document.querySelectorAll('.choice-item').forEach(el => {
        el.classList.toggle('selected', el.dataset.index == index);
    });
}
function addChoice() {
    const list = document.getElementById('choices-list');
    const div = document.createElement('div');
    div.className = 'choice-item';
    div.dataset.index = choiceIndex;
    div.innerHTML = `<input type="radio" class="choice-radio" name="_correct" value="${choiceIndex}" onchange="setCorrect(${choiceIndex})"><input type="text" class="choice-text" name="choices[${choiceIndex}][text]" placeholder="خيار جديد..." required><button type="button" class="btn-remove-choice" onclick="removeChoice(this)"><i class="bi bi-x-circle"></i></button>`;
    list.appendChild(div);
    choiceIndex++;
}
function removeChoice(btn) {
    if (document.querySelectorAll('.choice-item').length <= 2) { alert('يجب خياران على الأقل.'); return; }
    btn.closest('.choice-item').remove();
}
function loadSubjects(gradeId, selectedId) {
    const sel = document.getElementById('subject-select');
    if (!gradeId) return;
    fetch(`/admin/subjects/by-grade/${gradeId}`)
        .then(r => r.json())
        .then(subs => {
            sel.innerHTML = '';
            subs.forEach(s => {
                sel.innerHTML += `<option value="${s.id}" ${s.id==selectedId?'selected':''}>${s.name}</option>`;
            });
        });
}
</script>
@endpush
