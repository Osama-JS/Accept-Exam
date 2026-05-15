@extends('layouts.admin')
@section('title', 'إضافة صف دراسي')
@section('page-title', 'إضافة صف دراسي جديد')

@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-plus-circle text-primary"></i> بيانات الصف الدراسي</div>
        <a href="{{ route('admin.grades.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-right"></i> رجوع</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.grades.store') }}">
            @csrf
            <div class="form-group">
                <label for="name">اسم الصف الدراسي *</label>
                <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name') }}" placeholder="مثال: الصف الأول الابتدائي" required>
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="order">الترتيب التسلسلي *</label>
                <input type="number" id="order" name="order" class="form-control {{ $errors->has('order') ? 'is-invalid' : '' }}"
                    value="{{ old('order', 1) }}" min="1" required>
                @error('order')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="description">وصف (اختياري)</label>
                <textarea id="description" name="description" class="form-control" rows="3"
                    placeholder="وصف مختصر للصف الدراسي...">{{ old('description') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> حفظ الصف</button>
        </form>
    </div>
</div>
@endsection
