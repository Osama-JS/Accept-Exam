@extends('layouts.admin')
@section('title', 'تعديل الصف الدراسي')
@section('page-title', 'تعديل الصف الدراسي')

@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-pencil text-warning"></i> تعديل: {{ $grade->name }}</div>
        <a href="{{ route('admin.grades.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-right"></i> رجوع</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.grades.update', $grade) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label for="name">اسم الصف الدراسي *</label>
                <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name', $grade->name) }}" required>
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="order">الترتيب التسلسلي *</label>
                <input type="number" id="order" name="order" class="form-control"
                    value="{{ old('order', $grade->order) }}" min="1" required>
            </div>
            <div class="form-group">
                <label for="description">وصف (اختياري)</label>
                <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $grade->description) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> حفظ التعديلات</button>
        </form>
    </div>
</div>
@endsection
