@extends('layouts.student')
@section('title', 'بيانات الطالب - ' . $exam->title)

@push('styles')
<style>
.register-section { max-width: 680px; margin: 40px auto; padding: 0 24px; }
.exam-summary {
    background: linear-gradient(135deg, #1e3a5f, #2563eb);
    border-radius: 16px; padding: 24px; color: #fff; margin-bottom: 28px;
    display: flex; align-items: center; gap: 20px;
}
.exam-summary .icon { font-size: 48px; }
.exam-summary h2 { font-size: 18px; font-weight: 800; margin-bottom: 8px; }
.exam-meta-list { display: flex; gap: 16px; flex-wrap: wrap; }
.exam-meta-list span { background: rgba(255,255,255,.15); border-radius: 20px; padding: 3px 12px; font-size: 12px; }
.register-card { background: #fff; border-radius: 16px; border: 1.5px solid var(--border); overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.06); }
.register-card .card-head { background: #f8fafc; border-bottom: 1px solid var(--border); padding: 18px 24px; display: flex; align-items: center; gap: 10px; }
.register-card .card-head h3 { font-size: 16px; font-weight: 700; }
.register-card .card-body { padding: 28px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.submit-btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #2563eb, #3b82f6); color: #fff; border: none; border-radius: 12px; font-family: 'Cairo', sans-serif; font-size: 16px; font-weight: 700; cursor: pointer; transition: all .2s; margin-top: 8px; display: flex; align-items: center; justify-content: center; gap: 10px; }
.submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(37,99,235,.4); }
@media(max-width:600px){ .form-row { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="register-section">
    <a href="{{ route('student.exams', $exam->grade) }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--text-muted);text-decoration:none;font-size:13px;margin-bottom:20px">
        <i class="bi bi-arrow-right"></i> رجوع
    </a>

    <div class="exam-summary">
        <div class="icon">📝</div>
        <div>
            <h2>{{ $exam->title }}</h2>
            <div class="exam-meta-list">
                <span>📚 {{ $exam->grade->name }}</span>
                <span>🏆 الدرجة الكلية: {{ $exam->total_marks }}</span>
                <span>✅ درجة النجاح: {{ $exam->pass_marks }}</span>
                <span>❓ {{ $exam->totalQuestionsCount() }} سؤال</span>
            </div>
        </div>
    </div>

    <div class="register-card">
        <div class="card-head">
            <i class="bi bi-person-fill" style="color:var(--primary);font-size:20px"></i>
            <h3>أدخل بياناتك للبدء</h3>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#7f1d1d;font-size:13px">
                    <i class="bi bi-exclamation-triangle"></i>
                    @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('exam.start', $exam) }}">
                @csrf
                <div class="form-group">
                    <label for="name">اسم الطالب الكامل *</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="مثال: محمد أحمد علي" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>المدرسة السابقة *</label>
                        <input type="text" name="previous_school" class="form-control" value="{{ old('previous_school') }}" placeholder="اسم المدرسة" required>
                    </div>
                    <div class="form-group">
                        <label>المعدل في آخر صف (%) *</label>
                        <input type="number" name="last_grade_average" class="form-control" value="{{ old('last_grade_average') }}" min="0" max="100" step="0.1" placeholder="مثال: 85.5" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>اسم ولي الأمر *</label>
                        <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name') }}" placeholder="اسم ولي الأمر" required>
                    </div>
                    <div class="form-group">
                        <label>رقم هاتف ولي الأمر *</label>
                        <input type="tel" name="guardian_phone" class="form-control" value="{{ old('guardian_phone') }}" placeholder="05xxxxxxxx" required>
                    </div>
                </div>

                <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#0369a1">
                    <i class="bi bi-info-circle"></i>
                    <strong>تنبيه:</strong> بعد الضغط على زر البدء لن تستطيع العودة لهذه الصفحة. تأكد من صحة بياناتك قبل المتابعة.
                </div>

                <button type="submit" class="submit-btn">
                    <i class="bi bi-play-circle-fill"></i>
                    بدء الاختبار الآن
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
