@extends('layouts.admin')
@section('title', 'إعدادات النظام')
@section('page-title', 'إعدادات النظام')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- General Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="card-title"><i class="bi bi-gear-wide-connected text-primary"></i> الإعدادات العامة</div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="school_name">اسم المدرسة / المؤسسة</label>
                        <input type="text" name="school_name" id="school_name" class="form-control" value="{{ $settings['school_name'] }}" required>
                    </div>

                    <div class="form-group">
                        <label for="welcome_message">رسالة الترحيب للطلاب</label>
                        <textarea name="welcome_message" id="welcome_message" class="form-control" rows="3">{{ $settings['welcome_message'] }}</textarea>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label for="contact_email">بريد التواصل</label>
                            <input type="email" name="contact_email" id="contact_email" class="form-control" value="{{ $settings['contact_email'] }}">
                        </div>
                        <div class="form-group">
                            <label for="contact_phone">رقم الهاتف</label>
                            <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="{{ $settings['contact_phone'] }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Control -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="card-title"><i class="bi bi-shield-lock text-primary"></i> التحكم في النظام</div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>حالة النظام (فتح/إغلاق الامتحانات)</label>
                        <div class="d-flex gap-3 mt-2">
                            <label class="d-flex align-center gap-2" style="cursor:pointer">
                                <input type="radio" name="system_enabled" value="1" {{ $settings['system_enabled'] == '1' ? 'checked' : '' }}>
                                <span class="badge badge-success">مفعّل (يسمح للطلاب بدخول النظام)</span>
                            </label>
                            <label class="d-flex align-center gap-2" style="cursor:pointer">
                                <input type="radio" name="system_enabled" value="0" {{ $settings['system_enabled'] == '0' ? 'checked' : '' }}>
                                <span class="badge badge-danger">مغلق (وضع الصيانة / توقف الامتحانات)</span>
                            </label>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="form-group">
                        <label>إظهار النتيجة للطالب فور الانتهاء</label>
                        <div class="d-flex gap-3 mt-2">
                            <label class="d-flex align-center gap-2" style="cursor:pointer">
                                <input type="radio" name="show_results_instantly" value="1" {{ $settings['show_results_instantly'] == '1' ? 'checked' : '' }}>
                                <span>نعم، إظهار النتيجة</span>
                            </label>
                            <label class="d-flex align-center gap-2" style="cursor:pointer">
                                <input type="radio" name="show_results_instantly" value="0" {{ $settings['show_results_instantly'] == '0' ? 'checked' : '' }}>
                                <span>لا، إخفاء النتيجة (تظهر للمدير فقط)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appearance -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="card-title"><i class="bi bi-palette text-primary"></i> المظهر والشعار</div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="school_logo">شعار المدرسة</label>
                        <div class="d-flex align-center gap-4 mt-2">
                            <div style="width: 100px; height: 100px; border: 2px dashed var(--border); border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f8fafc;">
                                @if($settings['school_logo'])
                                    <img src="{{ asset('storage/' . $settings['school_logo']) }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                @else
                                    <i class="bi bi-image text-muted" style="font-size: 30px;"></i>
                                @endif
                            </div>
                            <div style="flex: 1;">
                                <input type="file" name="school_logo" id="school_logo" class="form-control">
                                <p class="text-muted mt-2" style="font-size: 12px;">يفضل استخدام صورة بخلفية شفافة (PNG) وبحجم لا يتجاوز 2 ميجابايت.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-between align-center mb-5">
                <p class="text-muted small">آخر تحديث: {{ date('Y-m-d H:i') }}</p>
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-cloud-arrow-up"></i> حفظ جميع الإعدادات
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .col-md-8 { max-width: 800px; margin: 0 auto; }
    .btn-lg { padding: 12px 30px; font-size: 16px; border-radius: 12px; }
    input[type="radio"] { width: 18px; height: 18px; accent-color: var(--primary); }
</style>
@endsection
