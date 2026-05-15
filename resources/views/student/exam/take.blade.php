@extends('layouts.student')
@section('title', 'جلسة الاختبار')

@push('styles')
<style>
.exam-header {
    background: linear-gradient(135deg, #0f172a, #1e3a5f);
    color: #fff;
    padding: 20px 32px;
    display: flex; align-items: center; justify-content: space-between; gap: 20px;
    position: sticky; top: 64px; z-index: 100;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.exam-title { font-size: 16px; font-weight: 700; }
.exam-progress-info { font-size: 13px; color: rgba(255,255,255,.7); margin-top: 4px; }
.progress-bar-wrap { flex: 1; max-width: 300px; }
.progress-bar { height: 8px; background: rgba(255,255,255,.2); border-radius: 4px; overflow: hidden; }
.progress-fill { height: 100%; background: #10b981; border-radius: 4px; transition: width .3s; }

.exam-body { max-width: 820px; margin: 40px auto; padding: 0 24px; min-height: 60vh; }

.question-wizard {
    display: none;
    animation: fadeIn 0.4s ease-out;
}
.question-wizard.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.question-card {
    background: #fff; border-radius: 20px; border: 1.5px solid var(--border);
    padding: 40px; margin-bottom: 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,.04);
}
.question-num { 
    font-size: 14px; font-weight: 800; color: var(--primary); 
    margin-bottom: 16px; display: inline-block;
    padding: 4px 12px; background: rgba(37,99,235,0.1); border-radius: 30px;
}
.question-text { font-size: 18px; font-weight: 700; margin-bottom: 30px; line-height: 1.6; color: var(--text-main); }

.choices-grid { display: flex; flex-direction: column; gap: 12px; }
.choice-label {
    display: flex; align-items: center; gap: 16px;
    padding: 16px 20px; border: 2px solid var(--border);
    border-radius: 12px; cursor: pointer; transition: all .2s;
    font-size: 15px; font-weight: 600;
    position: relative;
}
.choice-label:hover { border-color: var(--primary); background: rgba(37,99,235,.02); }
.choice-label input[type=radio] { 
    position: absolute; opacity: 0; 
}
.choice-label.selected { 
    border-color: var(--primary); background: rgba(37,99,235,.05); 
    color: var(--primary); box-shadow: 0 4px 12px rgba(37,99,235,0.1);
}
.choice-letter { 
    width: 32px; height: 32px; background: var(--body-bg); 
    border-radius: 8px; display: flex; align-items: center; 
    justify-content: center; font-size: 14px; font-weight: 800; 
    color: var(--text-muted); flex-shrink: 0; 
}
.choice-label.selected .choice-letter { background: var(--primary); color: #fff; }

.wizard-nav {
    display: flex; justify-content: space-between; align-items: center;
    margin-top: 30px; gap: 16px;
}
.btn-nav {
    padding: 12px 28px; border-radius: 12px; font-weight: 700;
    font-family: 'Cairo', sans-serif; cursor: pointer; border: none;
    display: flex; align-items: center; gap: 8px; transition: all 0.2s;
    font-size: 15px;
}
.btn-prev { background: #fff; color: var(--text-muted); border: 1.5px solid var(--border); }
.btn-prev:hover { background: var(--body-bg); color: var(--text-main); }
.btn-next { background: var(--primary); color: #fff; border: 1.5px solid var(--primary); }
.btn-next:hover { background: var(--primary-dark); transform: translateX(-4px); }

.btn-submit-final { 
    background: linear-gradient(135deg, #10b981, #059669); color: #fff; 
    padding: 12px 32px; border: none; border-radius: 12px;
    font-weight: 700; font-size: 16px;
    cursor: pointer; display: flex; align-items: center; gap: 8px;
    transition: all 0.2s;
}
.btn-submit-final:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(16,185,129,0.3); }

/* Custom Modal Styles */
.custom-modal-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(15, 23, 42, 0.8);
    backdrop-filter: blur(8px);
    display: none; align-items: center; justify-content: center;
    z-index: 1000; padding: 20px;
}
.custom-modal {
    background: #fff; width: 100%; max-width: 480px;
    border-radius: 24px; padding: 40px; text-align: center;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
    animation: modalIn 0.3s ease-out;
}
@keyframes modalIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}
.modal-icon {
    width: 80px; height: 80px; background: rgba(16, 185, 129, 0.1);
    color: #10b981; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 40px; margin: 0 auto 24px;
}
.modal-title { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 12px; }
.modal-desc { font-size: 15px; color: #64748b; line-height: 1.6; margin-bottom: 32px; }
.modal-actions { display: flex; gap: 12px; justify-content: center; }
.modal-btn {
    flex: 1; padding: 12px 24px; border-radius: 12px;
    font-weight: 700; font-family: 'Cairo', sans-serif;
    cursor: pointer; border: none; font-size: 15px; transition: all 0.2s;
}
.btn-confirm { background: #10b981; color: #fff; }
.btn-confirm:hover { background: #059669; }
.btn-cancel { background: #f1f5f9; color: #64748b; }
.btn-cancel:hover { background: #e2e8f0; }

/* Steps map update from user request */
.steps-map {
    display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;
    margin-bottom: 30px;
}
.step-num-btn {
    width: 36px; height: 36px; border-radius: 50%; 
    background: #fff; border: 1.5px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: var(--text-muted);
    cursor: pointer; transition: all 0.2s;
}
.step-num-btn:hover { border-color: var(--primary); color: var(--primary); background: rgba(37,99,235,0.05); }
.step-num-btn.answered { background: #10b981; color: #fff; border-color: #10b981; }
.step-num-btn.active { background: var(--primary); color: #fff; border-color: var(--primary); transform: scale(1.1); box-shadow: 0 4px 12px rgba(37,99,235,0.2); z-index: 10; }
.step-num-btn.answered.active { background: #059669; border-color: #047857; box-shadow: 0 4px 12px rgba(16,185,129,0.3); }

.hidden { display: none !important; }
</style>
@endpush

@section('content')
<div class="exam-header">
    <div>
        <div class="exam-title">{{ $exam->title }}</div>
        <div class="exam-progress-info" id="progress-text">0 / {{ count($questions) }} تمت الإجابة</div>
    </div>
    <div class="progress-bar-wrap">
        <div class="progress-bar">
            <div class="progress-fill" id="progress-bar-fill" style="width:0%"></div>
        </div>
    </div>
</div>

<div class="exam-body">
    <div class="steps-map" id="steps-map">
        @foreach($questions as $i => $question)
            <div class="step-num-btn {{ $i === 0 ? 'active' : '' }}" id="dot-{{ $i }}" onclick="goToStep({{ $i }})">
                {{ $i + 1 }}
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('exam.submit') }}" id="exam-form">
        @csrf

        @foreach($questions as $i => $question)
        <div class="question-wizard {{ $i === 0 ? 'active' : '' }}" id="step-{{ $i }}">
            <div class="question-card">
                <div class="question-num">السؤال {{ $i + 1 }} من {{ count($questions) }}</div>
                <div class="question-text">{{ $question['text'] }}</div>
                <div class="choices-grid">
                    @php $letters = ['أ', 'ب', 'ج', 'د', 'هـ', 'و']; @endphp
                    @foreach($question['choices'] as $ci => $choice)
                    <label class="choice-label" id="lbl-{{ $question['id'] }}-{{ $choice['id'] }}">
                        <input type="radio" name="answers[{{ $question['id'] }}]" 
                               value="{{ $choice['id'] }}"
                               onchange="onAnswerSelected({{ $i }}, {{ $question['id'] }}, this)">
                        <span class="choice-letter">{{ $letters[$ci] ?? ($ci+1) }}</span>
                        <span>{{ $choice['text'] }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="wizard-nav">
                @if($i > 0)
                    <button type="button" class="btn-nav btn-prev" onclick="prevStep()">
                        <i class="bi bi-arrow-right"></i> السابق
                    </button>
                @else
                    <div></div>
                @endif
                
                @if($i < count($questions) - 1)
                    <button type="button" class="btn-nav btn-next" onclick="nextStep()">
                        التالي <i class="bi bi-arrow-left"></i>
                    </button>
                @else
                    <button type="button" class="btn-submit-final" onclick="showConfirmModal()">
                        <i class="bi bi-check2-circle"></i> تسليم الاختبار
                    </button>
                @endif
            </div>
        </div>
        @endforeach
    </form>
</div>

<!-- Custom Confirmation Modal -->
<div class="custom-modal-overlay" id="confirm-modal">
    <div class="custom-modal">
        <div class="modal-icon">
            <i class="bi bi-send-check"></i>
        </div>
        <div class="modal-title">تسليم الاختبار النهائي</div>
        <div class="modal-desc" id="modal-message">هل أنت متأكد من رغبتك في إنهاء الاختبار وتسليم الإجابات؟ لن تتمكن من العودة مجدداً.</div>
        <div class="modal-actions">
            <button type="button" class="modal-btn btn-confirm" id="btn-final-confirm" onclick="finalSubmitForm()">نعم، تسليم الإجابات</button>
            <button type="button" class="modal-btn btn-cancel" onclick="hideConfirmModal()">إلغاء</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentStep = 0;
const totalSteps = {{ count($questions) }};
const answeredQuestions = new Set();

function updateUI() {
    document.querySelectorAll('.question-wizard').forEach((el, index) => {
        el.classList.toggle('active', index === currentStep);
    });

    document.querySelectorAll('.step-num-btn').forEach((dot, index) => {
        dot.classList.toggle('active', index === currentStep);
    });

    const progressPct = (answeredQuestions.size / totalSteps) * 100;
    const bar = document.getElementById('progress-bar-fill');
    if (bar) bar.style.width = progressPct + '%';
    
    const text = document.getElementById('progress-text');
    if (text) text.textContent = `${answeredQuestions.size} / ${totalSteps} تمت الإجابة`;
}

function nextStep() {
    if (currentStep < totalSteps - 1) {
        currentStep++;
        updateUI();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function prevStep() {
    if (currentStep > 0) {
        currentStep--;
        updateUI();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function goToStep(index) {
    currentStep = index;
    updateUI();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function onAnswerSelected(stepIndex, questionId, input) {
    const labels = input.closest('.choices-grid').querySelectorAll('.choice-label');
    labels.forEach(l => l.classList.remove('selected'));
    input.closest('.choice-label').classList.add('selected');

    answeredQuestions.add(questionId);
    const dot = document.getElementById(`dot-${stepIndex}`);
    if (dot) dot.classList.add('answered');
    
    updateUI();

    setTimeout(() => {
        if (currentStep < totalSteps - 1) {
            nextStep();
        }
    }, 600);
}

// Custom Modal Functions
function showConfirmModal() {
    const unansweredCount = totalSteps - answeredQuestions.size;
    const modalMessage = document.getElementById('modal-message');
    
    if (unansweredCount > 0) {
        modalMessage.innerHTML = `تنبيه: لم تجب على <strong style="color:#ef4444">${unansweredCount}</strong> سؤال. <br> هل أنت متأكد من رغبتك في تسليم الاختبار على أي حال؟`;
    } else {
        modalMessage.textContent = 'أحسنت! لقد أجبت على جميع الأسئلة. هل أنت متأكد من تسليم الاختبار الآن؟';
    }
    
    document.getElementById('confirm-modal').style.display = 'flex';
}

function hideConfirmModal() {
    document.getElementById('confirm-modal').style.display = 'none';
}

function finalSubmitForm() {
    const btn = document.getElementById('btn-final-confirm');
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> جاري الإرسال...';
    btn.disabled = true;
    
    console.log('Final submission confirmed. Submitting form now.');
    document.getElementById('exam-form').submit();
}

// Initial UI sync
updateUI();
</script>
@endpush
