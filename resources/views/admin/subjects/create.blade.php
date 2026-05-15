@extends('layouts.admin')
@section('title', 'إضافة مادة')
@section('page-title', 'إضافة مادة دراسية')

@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-plus-circle text-primary"></i> بيانات المادة</div>
        <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-right"></i> رجوع</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.subjects.store') }}">
            @csrf
            <div class="form-group">
                <label>الصف الدراسي *</label>
                <select name="grade_id" class="form-control {{ $errors->has('grade_id') ? 'is-invalid' : '' }}" required>
                    <option value="">-- اختر الصف --</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id ? 'selected' : '' }}>
                            {{ $grade->name }}
                        </option>
                    @endforeach
                </select>
                @error('grade_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>اسم المادة *</label>
                <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name') }}" placeholder="مثال: الرياضيات" required>
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>أيقونة (Emoji اختياري)</label>
                <input type="text" name="icon" class="form-control" value="{{ old('icon') }}" placeholder="مثال: 📐">
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> حفظ</button>
        </form>
    </div>
</div>
@endsection
