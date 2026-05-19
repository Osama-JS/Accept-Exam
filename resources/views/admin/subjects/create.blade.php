@extends('layouts.admin')

@section('title', 'إضافة مادة دراسية')
@section('page-title', 'إضافة مادة دراسية جديدة')

@section('breadcrumb')
    <a href="{{ route('admin.subjects.index') }}" style="color: var(--text-muted); font-weight: 500; text-decoration: none;">قائمة المواد</a>
    <span style="color: #cbd5e0; margin: 0 4px;"><i class="bi bi-chevron-left" style="font-size: 10px;"></i></span>
    <span style="color: var(--text-main); font-weight: 700;">إضافة مادة</span>
@endsection

@push('styles')
<style>
    .form-group label {
        font-weight: 700;
        color: #334155;
        margin-bottom: 10px;
        font-size: 13.5px;
        display: block;
    }
    .form-control {
        padding: 12px 16px;
        font-size: 14px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        background: #f8fafc;
        transition: all 0.2s ease-in-out;
        width: 100%;
        outline: none;
    }
    .form-control:focus {
        background: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(118, 181, 27, 0.12);
    }
    .form-control.is-invalid {
        border-color: var(--danger) !important;
        background-color: #fdf2f2 !important;
    }
    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(195, 14, 20, 0.1) !important;
    }
    .invalid-feedback {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--danger);
        font-size: 12px;
        font-weight: 700;
        margin-top: 6px;
    }
    .helper-text {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }
    
    /* ── تأثيرات الحقول الاحترافية ── */
    .input-wrapper { position: relative; }
    .input-wrapper .input-icon {
        position: absolute; right: 16px; top: 50%;
        transform: translateY(-50%);
        color: #94a3b8; font-size: 18px; transition: color 0.3s ease;
        pointer-events: none;
    }
    .input-wrapper .form-control { padding-right: 48px; } /* ترك مساحة للأيقونة */
    .input-wrapper:focus-within .input-icon { color: var(--primary); }
    .input-wrapper .form-control.is-invalid ~ .input-icon {
        color: var(--danger) !important;
    }
    
    /* ── علامة التحميل (Spinner) لزر الحفظ ── */
    .spinner-border {
        display: none; width: 1.1rem; height: 1.1rem; margin-left: 8px;
        vertical-align: text-bottom; border: 0.2em solid currentColor;
        border-right-color: transparent; border-radius: 50%;
        animation: spinner-border .75s linear infinite;
    }
    @keyframes spinner-border { to { transform: rotate(360deg); } }
    .btn.is-loading { pointer-events: none; opacity: 0.85; }
    .btn.is-loading .spinner-border { display: inline-block; }
    .btn.is-loading .btn-icon { display: none; }
</style>
@endpush

@section('content')
<div style="max-width: 650px; margin: 0 auto; animation: pageIn .4s cubic-bezier(0.16, 1, 0.3, 1);">
    <div class="card" style="border-radius: 20px; border: 1.5px solid rgba(118, 181, 27, 0.15); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02); overflow: hidden;">
        
        <div class="card-header" style="background: #ffffff; border-bottom: 1.5px solid rgba(118, 181, 27, 0.1); padding: 22px 28px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div class="card-title" style="font-size: 18px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 10px; margin: 0;">
                <div style="width: 36px; height: 36px; background: var(--primary-light); color: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-left: 12px; margin-right: 0;">
                    <i class="bi bi-journal-plus" style="font-size: 18px;"></i>
                </div>
                بيانات المادة الدراسية
            </div>
            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary" style="border: 1.5px solid var(--border); background: #ffffff; color: var(--text-main); font-weight: 700; border-radius: 12px; padding: 8px 16px; display: inline-flex; align-items: center; gap: 8px; height: 38px; font-size: 13px;">
                <i class="bi bi-arrow-right" style="color: var(--primary);"></i> رجوع
            </a>
        </div>

        <div class="card-body" style="padding: 32px 28px;">
            <form method="POST" action="{{ route('admin.subjects.store') }}" id="subjectForm">
                @csrf
                
                <div class="form-group" style="margin-bottom: 24px;">
                    <label>
                        الصفوف الدراسية المرتبطة <span class="text-danger">*</span>
                    </label>
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 10px;">
                        @foreach($grades ?? [] as $grade)
                            <label class="d-flex align-items-center gap-2" style="cursor: pointer; background: #f8fafc; padding: 10px 16px; border: 1.5px solid #cbd5e1; border-radius: 12px; transition: all 0.2s; min-width: 140px; display: inline-flex; justify-content: flex-start; align-items: center; margin: 4px 0;">
                                <input type="checkbox" name="grade_ids[]" value="{{ $grade->id }}" 
                                       {{ (is_array(old('grade_ids')) && in_array($grade->id, old('grade_ids'))) ? 'checked' : '' }}
                                       style="width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer;">
                                <span style="font-size: 13.5px; font-weight: 700; color: #334155; margin-right: 6px;">{{ $grade->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="helper-text">
                        <i class="bi bi-info-circle-fill text-primary" style="font-size: 13px;"></i> حدد الصفوف الدراسية التي تدرس فيها هذه المادة (يمكنك اختيار صف واحد أو أكثر).
                    </div>
                    @error('grade_ids')
                        <span class="invalid-feedback" style="display:block;"><i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="name">
                        اسم المادة الدراسية <span class="text-danger">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="name" name="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" 
                               placeholder="مثال: الرياضيات، اللغة العربية..." 
                               required autofocus 
                               autocomplete="off">
                        <i class="bi bi-fonts input-icon"></i>
                    </div>
                    @error('name')
                        <span class="invalid-feedback"><i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 32px;">
                    <label for="icon">
                        رمز تعبيري (Emoji اختياري)
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="icon" name="icon" 
                               class="form-control @error('icon') is-invalid @enderror"
                               value="{{ old('icon') }}" 
                               placeholder="مثال: 📐، 🔬، 📖" 
                               autocomplete="off">
                        <i class="bi bi-emoji-smile input-icon"></i>
                    </div>
                    <div class="helper-text">
                        <i class="bi bi-lightbulb text-warning" style="font-size: 13px;"></i> الرمز التعبيري يظهر كأيقونة مميزة للمادة في لوحة التحكم وبطاقات المواد.
                    </div>
                    @error('icon')
                        <span class="invalid-feedback"><i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 24px; border-top: 1.5px solid var(--border); margin-top: 8px;">
                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary" style="border-radius: 12px; padding: 10px 24px; font-weight: 700;">إلغاء</a>
                    <button type="submit" id="submitBtn" class="btn btn-primary" style="border-radius: 12px; padding: 10px 28px; font-weight: 700; background: linear-gradient(135deg, #76b51b, #5f9416); border: none; box-shadow: 0 6px 20px rgba(118, 181, 27, 0.25);">
                        <span class="spinner-border"></span>
                        <i class="bi bi-check-circle-fill btn-icon" style="font-size: 16px;"></i> 
                        <span id="btnText">حفظ المادة</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // حماية زر الحفظ (منع الإرسال المزدوج)
        const form = document.getElementById('subjectForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');

        if (form && submitBtn && btnText) {
            form.addEventListener('submit', function() {
                submitBtn.classList.add('is-loading');
                btnText.textContent = 'جاري الحفظ...';
            });
        }
    });
</script>
@endpush
