@extends('layouts.admin')
@section('title', 'إضافة سنة دراسية')
@section('breadcrumb', 'إضافة سنة دراسية')
@section('page-title', 'إضافة سنة دراسية')

@section('content')
<div class="card" style="max-width:480px">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-plus-circle text-primary"></i> سنة دراسية جديدة</div>
        <a href="{{ route('admin.academic-years.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-right"></i> رجوع</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.academic-years.store') }}">
            @csrf
            <div class="form-group">
                <label>اسم السنة الدراسية *</label>
                <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name') }}" placeholder="مثال: 2024-2025" required>
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> حفظ</button>
        </form>
    </div>
</div>
@endsection
