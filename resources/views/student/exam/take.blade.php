@extends('layouts.student')
@section('title', 'جلسة الاختبار الآمنة')

@push('styles')
<style>
    /* ── شريط التقدم الزجاجي الفاخر (Glassmorphic Sticky Header) ── */
    .exam-header {
        background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        color: #0f172a; padding: 20px 32px;
        display: flex; align-items: center; justify-content: space-between; gap: 20px;
        position: sticky; top: 80px; z-index: 100;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border-bottom: 1.5px solid rgba(226, 232, 240, 0.8);
    }
    
    .exam-title { font-size: 17px; font-weight: 900; color: #0f172a; display: flex; align-items: center; gap: 8px; }
    .exam-title i { color: var(--primary); font-size: 20px; animation: pulseIcon 2s infinite; }
    @keyframes pulseIcon {
        0%, 100% { opacity: 0.6; }
        50% { opacity: 1; }
    }
    
    .exam-progress-info {
        font-size: 13px; font-weight: 800; color: #64748b; margin-top: 4px;
        display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    }
    
    .progress-badge {
        background: #f1f5f9; padding: 3px 12px; border-radius: 20px; width: fit-content;
    }
    
    .timer-badge {
        background: rgba(118, 181, 27, 0.1); color: var(--primary-dark);
        padding: 3px 12px; border-radius: 20px; font-family: 'Inter', sans-serif !important;
        display: inline-flex; align-items: center; gap: 5px; font-weight: 850;
        box-shadow: 0 2px 8px rgba(118,181,27,0.05);
        animation: pulseTimer 1s infinite alternate;
    }
    @keyframes pulseTimer {
        0% { opacity: 0.9; }
        100% { opacity: 1; }
    }

    .progress-bar-wrap { flex: 1; max-width: 320px; display: flex; align-items: center; gap: 12px; }
    .progress-bar { height: 8px; background: #e2e8f0; border-radius: 10px; overflow: hidden; flex: 1; }
    .progress-fill { height: 100%; background: linear-gradient(90deg, var(--primary), var(--primary-dark)); border-radius: 10px; transition: width .4s cubic-bezier(.4,0,.2,1); }

    /* ── جسم منطقة الاختبار (Exam Container) ── */
    .exam-body { max-width: 860px; margin: 40px auto 100px; padding: 0 24px; min-height: 60vh; }
    
    /* شبكة خريطة الأسئلة المتميزة */
    .steps-map-wrapper {
        background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 20px;
        margin-bottom: 30px; box-shadow: var(--shadow-sm);
    }
    .sm-header { font-size: 13.5px; font-weight: 850; color: #64748b; margin-bottom: 14px; display: flex; align-items: center; gap: 6px; }
    .steps-map { display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; }
    
    .step-num-btn {
        width: 40px; height: 40px; border-radius: 12px; 
        background: #f8fafc; border: 1.5px solid #e2e8f0;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 800; color: #64748b;
        cursor: pointer; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: 'Inter', sans-serif !important;
    }
    .step-num-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    
    /* 🟢 تمت الإجابة عليها سابقاً */
    .step-num-btn.answered { background: #ecfdf5; color: #10b981; border-color: #a7f3d0; }
    
    /* 🔴 نشطة حالياً ولم تجب بعد */
    .step-num-btn.active {
        background: linear-gradient(135deg, var(--danger) 0%, #ef4444 100%);
        color: #fff; border-color: transparent; transform: scale(1.1);
        box-shadow: 0 8px 16px rgba(195,14,20,.25); z-index: 10;
    }
    
    /* 🟢 نشطة حالياً وتمت الإجابة عليها */
    .step-num-btn.answered.active {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #fff; border-color: transparent;
        box-shadow: 0 8px 16px rgba(118,181,27,.3);
    }

    /* ── معالج الأسئلة (Question Wizard) ── */
    .question-wizard {
        display: none; animation: slideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .question-wizard.active { display: block; }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .question-card {
        background: #fff; border-radius: 24px; border: 1.5px solid #e2e8f0;
        padding: 40px; margin-bottom: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,.02);
        position: relative;
    }
    
    .question-num { 
        font-size: 13px; font-weight: 850; color: var(--primary); 
        margin-bottom: 20px; display: inline-block;
        padding: 5px 16px; background: var(--primary-light); border-radius: 30px;
    }
    
    .question-text { font-size: 19px; font-weight: 850; margin-bottom: 32px; line-height: 1.7; color: #0f172a; }
    
    /* خيارات الإجابة الراقية */
    .choices-grid { display: flex; flex-direction: column; gap: 14px; }
    
    .choice-label {
        display: flex; align-items: center; gap: 18px;
        padding: 18px 24px; border: 2px solid #e2e8f0;
        border-radius: 16px; cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 15.5px; font-weight: 700; color: #334155;
        position: relative; user-select: none;
    }
    .choice-label:hover { border-color: var(--primary); background: #f8fafc; color: #0f172a; }
    .choice-label input[type=radio] { position: absolute; opacity: 0; }
    
    /* الحالة عند تحديد الخيار */
    .choice-label.selected { 
        border-color: var(--primary); background: rgba(118, 181, 27, 0.05); 
        color: var(--primary-dark); box-shadow: 0 6px 16px rgba(118, 181, 27, 0.08);
    }
    
    .choice-letter { 
        width: 36px; height: 36px; background: #f1f5f9; 
        border-radius: 10px; display: flex; align-items: center; 
        justify-content: center; font-size: 14.5px; font-weight: 900; 
        color: #64748b; flex-shrink: 0; transition: all 0.2s;
    }
    .choice-label.selected .choice-letter {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #fff;
    }

    /* ── أزرار التنقل بين الأسئلة ── */
    .wizard-nav { display: flex; justify-content: space-between; align-items: center; margin-top: 36px; gap: 16px; }
    
    .btn-nav {
        padding: 12px 32px; border-radius: 14px; font-weight: 800;
        font-family: 'Cairo', sans-serif; cursor: pointer; border: none;
        display: flex; align-items: center; gap: 8px; transition: all 0.3s;
        font-size: 15.5px; height: 48px;
    }
    
    .btn-prev { background: #fff; color: #64748b; border: 1.5px solid #e2e8f0; }
    .btn-prev:hover { background: #f8fafc; color: #1e293b; border-color: #cbd5e1; }
    
    .btn-next { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: #fff; box-shadow: var(--shadow-primary); }
    .btn-next:hover { box-shadow: 0 10px 20px rgba(118, 181, 27, 0.35); transform: translateY(-2px); }
    
    .btn-submit-final { 
        background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; 
        padding: 12px 36px; border: none; border-radius: 14px;
        font-weight: 850; font-size: 16px; height: 48px;
        cursor: pointer; display: flex; align-items: center; gap: 8px;
        transition: all 0.3s cubic-bezier(.4,0,.2,1);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25);
    }
    .btn-submit-final:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(16, 185, 129, 0.35); }

    /* ── النافذة التأكيدية النهائية للتقديم (Glassmorphic Confirm Modal) ── */
    .custom-modal-overlay {
        position: fixed; inset: 0; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
        display: none; align-items: center; justify-content: center;
        z-index: 1000; padding: 20px;
        opacity: 0; transition: opacity 0.3s ease-out;
    }
    .custom-modal-overlay.show { display: flex; opacity: 1; }
    
    .custom-modal {
        background: #fff; width: 100%; max-width: 480px;
        border-radius: 24px; padding: 40px; text-align: center;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
        border: 1px solid rgba(226, 232, 240, 0.8);
        transform: scale(0.95) translateY(10px); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .custom-modal-overlay.show .custom-modal { transform: scale(1) translateY(0); }
    
    .modal-icon {
        width: 80px; height: 80px; background: rgba(16, 185, 129, 0.08);
        color: #10b981; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 40px; margin: 0 auto 24px;
    }
    .modal-icon.warning {
        background: rgba(239, 68, 68, 0.08); color: #ef4444;
    }
    
    .modal-title { font-size: 20px; font-weight: 900; color: #0f172a; margin-bottom: 12px; }
    .modal-desc { font-size: 14.5px; color: #64748b; line-height: 1.6; margin-bottom: 32px; }
    .modal-actions { display: flex; gap: 12px; justify-content: center; }
    .modal-btn {
        flex: 1; padding: 12px 24px; border-radius: 12px;
        font-weight: 800; font-family: 'Cairo', sans-serif;
        cursor: pointer; border: none; font-size: 15px; transition: all 0.2s;
    }
    .btn-confirm { background: #10b981; color: #fff; box-shadow: 0 4px 12px rgba(16,185,129,0.2); }
    .btn-confirm:hover { background: #059669; }
    .btn-cancel { background: #f1f5f9; color: #64748b; }
    .btn-cancel:hover { background: #e2e8f0; }
</style>
@endpush

@section('content')
<!-- شريط التقدم الفخم المثبت علوياً -->
<div class="exam-header">
    <div class="exam-title">
        <i class="bi bi-shield-lock-fill"></i>
        <span>{{ $exam->title }}</span>
    </div>
    
    <div class="progress-bar-wrap">
        <div class="exam-progress-info">
            <span class="progress-badge" id="progress-text">0 / {{ count($questions) }} تمت الإجابة</span>
            <span class="timer-badge" id="elapsed-timer">
                <i class="bi bi-stopwatch-fill"></i>
                <span id="timer-counter">00:00</span>
            </span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" id="progress-bar-fill" style="width:0%"></div>
        </div>
    </div>
</div>

<div class="exam-body">
    <!-- شبكة خريطة الأسئلة للتحرك السريع والمباشر -->
    <div class="steps-map-wrapper">
        <span class="sm-header"><i class="bi bi-grid-3x3-gap-fill"></i> خريطة الأسئلة المتاحة</span>
        <div class="steps-map" id="steps-map">
            @foreach($questions as $i => $question)
                <div class="step-num-btn {{ $i === 0 ? 'active' : '' }}" id="dot-{{ $i }}" onclick="goToStep({{ $i }})">
                    {{ $i + 1 }}
                </div>
            @endforeach
        </div>
    </div>

    <!-- نموذج الاختبار -->
    <form method="POST" action="{{ route('exam.submit') }}" id="exam-form">
        @csrf

        @foreach($questions as $i => $question)
        <div class="question-wizard {{ $i === 0 ? 'active' : '' }}" id="step-{{ $i }}">
            <div class="question-card">
                <div class="question-num">السؤال {{ $i + 1 }} من {{ count($questions) }}</div>
                <div class="question-text">{{ $question['text'] }}</div>
                
                <div class="choices-grid">
                    @if($question['type'] === 'essay')
                        <!-- السؤال المقالي -->
                        <div class="essay-container" style="width: 100%;">
                            <label class="ws-label" style="font-weight: 800; color: #64748b; margin-bottom: 12px; display: block; font-size: 14.5px;">أدخل إجابتك النصية هنا:</label>
                            <textarea name="answers[{{ $question['id'] }}]" 
                                      class="ws-textarea essay-textarea" 
                                      placeholder="اكتب إجابتك هنا بدقة ووضوح..." 
                                      style="width: 100%; min-height: 160px; padding: 18px 24px; border: 2px solid #e2e8f0; border-radius: 16px; font-family: inherit; font-size: 15.5px; font-weight: 600; line-height: 1.6; color: #334155; transition: all 0.2s; resize: vertical;" 
                                      oninput="onEssayInput({{ $i }}, {{ $question['id'] }}, this)"></textarea>
                        </div>
                    @elseif($question['type'] === 'matching')
                        <!-- سؤال التوصيل -->
                        <div class="matching-container" style="display: flex; flex-direction: column; gap: 14px; width: 100%;">
                            <div style="font-size: 14.5px; font-weight: 850; color: #64748b; margin-bottom: 8px;">صل عناصر العمود الأيمن بما يناسبها من العمود الأيسر:</div>
                            @php
                                $choices = $question['choices'];
                                $leftTexts = collect($choices)->map(function($c) {
                                    $parts = explode('|', $c['text']);
                                    return trim($parts[1] ?? '');
                                })->filter()->unique()->shuffle();
                            @endphp

                            @foreach($choices as $ci => $choice)
                                @php
                                    $parts = explode('|', $choice['text']);
                                    $rightText = trim($parts[0] ?? '');
                                @endphp
                                <div class="matching-row-item" style="display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 16px 24px; border: 2px solid #e2e8f0; border-radius: 16px; background: #fff; transition: all 0.2s;">
                                    <div class="right-item" style="font-size: 15.5px; font-weight: 700; color: #0f172a; width: 45%;">
                                        <span class="choice-letter" style="display: inline-flex; margin-left: 8px; font-size: 13px; width: 28px; height: 28px; align-items: center; justify-content: center; background: #f1f5f9; border-radius: 50%; color: #64748b; font-weight: 800;">{{ $ci + 1 }}</span>
                                        {{ $rightText }}
                                    </div>
                                    <div style="color: var(--primary); font-weight: 800;"><i class="bi bi-arrow-left-right"></i></div>
                                    <div class="left-dropdown" style="width: 45%;">
                                        <select name="answers[{{ $question['id'] }}][{{ $choice['id'] }}]" 
                                                class="ws-select matching-select" 
                                                style="width: 100%; padding: 10px 16px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-family: inherit; font-size: 14.5px; font-weight: 600; color: #334155; cursor: pointer; transition: border-color 0.2s; background: #fff;"
                                                onchange="onMatchingSelected({{ $i }}, {{ $question['id'] }}, this)"
                                                required>
                                            <option value="">-- اختر التطابق المناسب --</option>
                                            @foreach($leftTexts as $lt)
                                                <option value="{{ $lt }}">{{ $lt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- اختيار من متعدد أو صح أو خطأ -->
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
                    @endif
                </div>
            </div>

            <!-- أزرار الإبحار بين الأسئلة -->
            <div class="wizard-nav">
                @if($i > 0)
                    <button type="button" class="btn-nav btn-prev" onclick="prevStep()">
                        <i class="bi bi-arrow-right"></i> السؤال السابق
                    </button>
                @else
                    <div></div>
                @endif
                
                @if($i < count($questions) - 1)
                    <button type="button" class="btn-nav btn-next" onclick="nextStep()">
                        السؤال التالي <i class="bi bi-arrow-left"></i>
                    </button>
                @else
                    <button type="button" class="btn-submit-final" onclick="showConfirmModal()">
                        <i class="bi bi-send-check"></i> إنهاء وتسليم الإجابات
                    </button>
                @endif
            </div>
        </div>
        @endforeach
    </form>
</div>

<!-- النافذة التأكيدية للتقديم النهائي -->
<div class="custom-modal-overlay" id="confirm-modal">
    <div class="custom-modal">
        <div class="modal-icon" id="modal-icon-container">
            <i class="bi bi-send-check"></i>
        </div>
        <div class="modal-title" id="modal-title-text">تسليم الإجابات وإنهاء الاختبار</div>
        <div class="modal-desc" id="modal-message">هل أنت متأكد من رغبتك في تسليم الاختبار نهائياً؟ يرجى العلم أنه لا يمكن تعديل الإجابات بعد التسليم.</div>
        <div class="modal-actions">
            <button type="button" class="modal-btn btn-confirm" id="btn-final-confirm" onclick="finalSubmitForm()">نعم، تسليم الإجابات</button>
            <button type="button" class="modal-btn btn-cancel" onclick="hideConfirmModal()">مراجعة الإجابات</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentStep = 0;
        const totalSteps = {{ count($questions) }};
        const answeredQuestions = new Set();
        
        // ⏱️ محرك المؤقت التنازلي
        const durationMinutes = {{ $exam->duration_minutes ?? 60 }};
        let remainingSeconds = durationMinutes * 60;
        const timerCounter = document.getElementById('timer-counter');
        
        const countdownInterval = setInterval(() => {
            if (remainingSeconds <= 0) {
                clearInterval(countdownInterval);
                if (timerCounter) timerCounter.textContent = "00:00";
                
                Swal.fire({
                    title: 'انتهى الوقت!',
                    text: 'عذراً، لقد انتهى وقت الاختبار. سيتم تسليم إجاباتك تلقائياً...',
                    icon: 'warning',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    timer: 3500
                }).then(() => {
                    document.getElementById('exam-form').submit();
                });
                return;
            }

            remainingSeconds--;
            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = remainingSeconds % 60;
            
            const formattedMinutes = String(minutes).padStart(2, '0');
            const formattedSeconds = String(seconds).padStart(2, '0');
            
            if (timerCounter) {
                timerCounter.textContent = `${formattedMinutes}:${formattedSeconds}`;
                if (remainingSeconds < 300 && remainingSeconds % 2 === 0) {
                    timerCounter.style.color = '#ef4444';
                } else {
                    timerCounter.style.color = '';
                }
            }
        }, 1000);

        window.updateUI = function() {
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

        window.nextStep = function() {
            if (currentStep < totalSteps - 1) {
                currentStep++;
                updateUI();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        window.prevStep = function() {
            if (currentStep > 0) {
                currentStep--;
                updateUI();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        window.goToStep = function(index) {
            currentStep = index;
            updateUI();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        window.onAnswerSelected = function(stepIndex, questionId, input) {
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

        window.onEssayInput = function(stepIndex, questionId, textarea) {
            if (textarea.value.trim().length > 0) {
                answeredQuestions.add(questionId);
                const dot = document.getElementById(`dot-${stepIndex}`);
                if (dot) dot.classList.add('answered');
            } else {
                answeredQuestions.delete(questionId);
                const dot = document.getElementById(`dot-${stepIndex}`);
                if (dot) dot.classList.remove('answered');
            }
            updateUI();
        }

        window.onMatchingSelected = function(stepIndex, questionId, selectElement) {
            const wizardCard = selectElement.closest('.question-wizard');
            if (!wizardCard) return;
            
            const selects = wizardCard.querySelectorAll('.matching-select');
            let allSelected = true;
            selects.forEach(sel => {
                if (sel.value === '') {
                    allSelected = false;
                }
            });
            
            const rowItem = selectElement.closest('.matching-row-item');
            if (rowItem) {
                if (selectElement.value !== '') {
                    rowItem.style.borderColor = 'var(--primary)';
                    rowItem.style.background = 'rgba(118, 181, 27, 0.02)';
                } else {
                    rowItem.style.borderColor = '#e2e8f0';
                    rowItem.style.background = '#fff';
                }
            }
            
            if (allSelected) {
                answeredQuestions.add(questionId);
                const dot = document.getElementById(`dot-${stepIndex}`);
                if (dot) dot.classList.add('answered');
            } else {
                answeredQuestions.delete(questionId);
                const dot = document.getElementById(`dot-${stepIndex}`);
                if (dot) dot.classList.remove('answered');
            }
            updateUI();
        }

        // ── دوال التحكم بالنافذة المنبثقة الذكية ──
        const confirmModal = document.getElementById('confirm-modal');
        const modalIconContainer = document.getElementById('modal-icon-container');
        const modalTitleText = document.getElementById('modal-title-text');
        const modalMessage = document.getElementById('modal-message');

        window.showConfirmModal = function() {
            const unansweredCount = totalSteps - answeredQuestions.size;
            
            if (unansweredCount > 0) {
                modalIconContainer.className = 'modal-icon warning';
                modalIconContainer.innerHTML = '<i class="bi bi-exclamation-triangle"></i>';
                modalTitleText.textContent = 'تنبيه: أسئلة غير مكتملة!';
                modalMessage.innerHTML = `مرحباً، انتبه! لم تقم بالإجابة على <strong style="color:#ef4444; font-size: 16px;">${unansweredCount}</strong> سؤال من أصل <strong style="color: var(--primary);">${totalSteps}</strong> أسئلة.<br><small style="display:block; margin-top:8px; color:#64748b;">هل أنت متأكد من تسليم الإجابات الحالية وإنهاء الجلسة؟</small>`;
            } else {
                modalIconContainer.className = 'modal-icon';
                modalIconContainer.innerHTML = '<i class="bi bi-send-check-fill"></i>';
                modalTitleText.textContent = 'عمل رائع ومكتمل!';
                modalMessage.textContent = 'أحسنت! لقد أجبت على جميع الأسئلة بنجاح. هل أنت جاهز لتسليم الاختبار والحصول على نتيجتك الرقمية الفورية؟';
            }
            
            confirmModal.style.display = 'flex';
            setTimeout(() => confirmModal.classList.add('show'), 10);
        }

        window.hideConfirmModal = function() {
            confirmModal.classList.remove('show');
            setTimeout(() => confirmModal.style.display = 'none', 300);
        }

        window.finalSubmitForm = function() {
            // إيقاف مؤقت الثواني
            clearInterval(countdownInterval);
            
            const btn = document.getElementById('btn-final-confirm');
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> جاري إرسال إجاباتك الحالية...';
            btn.disabled = true;
            document.getElementById('exam-form').submit();
        }

        // تفعيل المستمع للنقرات الخارجية لإلغاء المودال
        confirmModal.addEventListener('click', function(e) {
            if (e.target === confirmModal) { hideConfirmModal(); }
        });

        // تشغيل التهيئة الابتدائية
        updateUI();
    });
</script>
@endpush
