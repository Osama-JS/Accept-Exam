@extends('layouts.admin')
@section('title', 'إضافة سؤال')
@section('page-title', 'إضافة سؤال جديد')

@push('styles')
<style>
.choices-list { display: flex; flex-direction: column; gap: 10px; }
.choice-item {
    display: flex; align-items: center; gap: 10px;
    background: #f8fafc; border: 1.5px solid var(--border);
    border-radius: 10px; padding: 10px 14px;
    transition: border-color .2s;
}
.choice-item.selected { border-color: var(--success); background: rgba(16,185,129,.05); }
.choice-radio { width: 18px; height: 18px; accent-color: var(--success); cursor: pointer; flex-shrink: 0; }
.choice-text { flex: 1; border: none; background: transparent; font-family: 'Cairo', sans-serif; font-size: 14px; outline: none; }
.btn-remove-choice { background: none; border: none; color: var(--danger); cursor: pointer; font-size: 18px; padding: 0 4px; transition: transform .2s; }
.btn-remove-choice:hover { transform: scale(1.2); }
.btn-add-choice {
    display: flex; align-items: center; gap: 6px;
    padding: 8px 16px; border: 2px dashed var(--border);
    border-radius: 10px; background: transparent;
    color: var(--primary); font-family: 'Cairo', sans-serif;
    font-size: 13px; font-weight: 600; cursor: pointer;
    transition: all .2s; width: fit-content;
}
.btn-add-choice:hover { border-color: var(--primary); background: rgba(37,99,235,.04); }
</style>
@endpush

@section('content')
<div class="card" style="max-width:760px">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-plus-circle text-primary"></i> بيانات السؤال</div>
        <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-right"></i> رجوع</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.questions.store') }}" id="question-form">
            @csrf

            <div class="grid-2">
                <div class="form-group">
                    <label>الصف الدراسي *</label>
                    <select id="grade-select" class="form-control" onchange="loadSubjects(this.value)">
                        <option value="">-- اختر الصف --</option>
                        @foreach($grades as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>المادة الدراسية *</label>
                    <select name="subject_id" id="subject-select" class="form-control {{ $errors->has('subject_id') ? 'is-invalid' : '' }}" required>
                        <option value="">-- اختر المادة --</option>
                    </select>
                    @error('subject_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>نص السؤال *</label>
                <textarea name="text" class="form-control {{ $errors->has('text') ? 'is-invalid' : '' }}"
                    rows="3" placeholder="اكتب نص السؤال هنا..." required>{{ old('text') }}</textarea>
                @error('text')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>الخيارات * (حدد الإجابة الصحيحة بالنقر على الدائرة)</label>

                <input type="hidden" name="correct_choice" id="correct-choice-input" value="{{ old('correct_choice', 0) }}">

                <div class="choices-list" id="choices-list">
                    @if(old('choices'))
                        @foreach(old('choices') as $i => $ch)
                        <div class="choice-item {{ old('correct_choice') == $i ? 'selected' : '' }}" data-index="{{ $i }}">
                            <input type="radio" class="choice-radio" name="_correct" value="{{ $i }}"
                                {{ old('correct_choice') == $i ? 'checked' : '' }} onchange="setCorrect({{ $i }})">
                            <input type="text" class="choice-text" name="choices[{{ $i }}][text]"
                                value="{{ $ch['text'] }}" placeholder="نص الخيار..." required>
                            <button type="button" class="btn-remove-choice" onclick="removeChoice(this)">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                        @endforeach
                    @else
                        @for($i = 0; $i < 4; $i++)
                        <div class="choice-item {{ $i === 0 ? 'selected' : '' }}" data-index="{{ $i }}">
                            <input type="radio" class="choice-radio" name="_correct" value="{{ $i }}"
                                {{ $i === 0 ? 'checked' : '' }} onchange="setCorrect({{ $i }})">
                            <input type="text" class="choice-text" name="choices[{{ $i }}][text]"
                                placeholder="الخيار {{ $i + 1 }}..." required>
                            <button type="button" class="btn-remove-choice" onclick="removeChoice(this)">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                        @endfor
                    @endif
                </div>

                <button type="button" class="btn-add-choice" onclick="addChoice()" style="margin-top:10px">
                    <i class="bi bi-plus-circle"></i> إضافة خيار آخر
                </button>

                @error('choices')<span class="invalid-feedback" style="display:block;margin-top:6px">{{ $message }}</span>@enderror
                @error('correct_choice')<span class="invalid-feedback" style="display:block;margin-top:6px">{{ $message }}</span>@enderror
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> حفظ السؤال</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let choiceIndex = document.querySelectorAll('.choice-item').length;

function setCorrect(index) {
    document.getElementById('correct-choice-input').value = index;
    document.querySelectorAll('.choice-item').forEach((el, i) => {
        el.classList.toggle('selected', el.dataset.index == index);
    });
}

function addChoice() {
    const list = document.getElementById('choices-list');
    const div = document.createElement('div');
    div.className = 'choice-item';
    div.dataset.index = choiceIndex;
    div.innerHTML = `
        <input type="radio" class="choice-radio" name="_correct" value="${choiceIndex}" onchange="setCorrect(${choiceIndex})">
        <input type="text" class="choice-text" name="choices[${choiceIndex}][text]" placeholder="الخيار ${choiceIndex + 1}..." required>
        <button type="button" class="btn-remove-choice" onclick="removeChoice(this)"><i class="bi bi-x-circle"></i></button>
    `;
    list.appendChild(div);
    choiceIndex++;
}

function removeChoice(btn) {
    const items = document.querySelectorAll('.choice-item');
    if (items.length <= 2) { alert('يجب أن يكون هناك خياران على الأقل.'); return; }
    btn.closest('.choice-item').remove();
    reindexChoices();
}

function reindexChoices() {
    document.querySelectorAll('.choice-item').forEach((el, i) => {
        el.dataset.index = i;
        el.querySelector('.choice-radio').value = i;
        el.querySelector('.choice-radio').setAttribute('onchange', `setCorrect(${i})`);
        const textInput = el.querySelector('.choice-text');
        textInput.name = `choices[${i}][text]`;
    });
    choiceIndex = document.querySelectorAll('.choice-item').length;
    // reset correct choice to first
    document.getElementById('correct-choice-input').value = 0;
    const first = document.querySelector('.choice-item');
    if (first) { first.querySelector('.choice-radio').checked = true; first.classList.add('selected'); }
}

function loadSubjects(gradeId) {
    const sel = document.getElementById('subject-select');
    sel.innerHTML = '<option value="">-- جاري التحميل --</option>';
    if (!gradeId) { sel.innerHTML = '<option value="">-- اختر المادة --</option>'; return; }
    fetch(`{{ url('admin/subjects/by-grade') }}/${gradeId}`)
        .then(r => r.json())
        .then(subjects => {
            sel.innerHTML = '<option value="">-- اختر المادة --</option>';
            subjects.forEach(s => {
                sel.innerHTML += `<option value="${s.id}">${s.icon ?? ''} ${s.name}</option>`;
            });
        });
}
</script>
@endpush
