@extends('layouts.admin')
@section('title', 'تعديل المادة')
@section('page-title', 'تعديل المادة الدراسية')

@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-pencil text-warning"></i> تعديل: {{ $subject->name }}</div>
        <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-right"></i> رجوع</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.subjects.update', $subject) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label>الصف الدراسي *</label>
                <select name="grade_id" class="form-control" required>
                    @foreach($grades as $grade)
                        <option value="{{ $grade->id }}" {{ old('grade_id', $subject->grade_id) == $grade->id ? 'selected' : '' }}>
                            {{ $grade->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>اسم المادة *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $subject->name) }}" required>
            </div>
            <div class="form-group">
                <label>أيقونة (Emoji اختياري)</label>
                <input type="text" name="icon" class="form-control" value="{{ old('icon', $subject->icon) }}">
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> حفظ التعديلات</button>
        </form>
    </div>
</div>
@endsection
