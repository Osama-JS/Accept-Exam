@extends('layouts.admin')
@section('title', 'تعديل السنة الدراسية')
@section('breadcrumb', 'تعديل السنة الدراسية')
@section('page-title', 'تعديل السنة الدراسية')

@section('content')
<div class="card" style="max-width:480px">
    <div class="card-header">
        <div class="card-title"><i class="bi bi-pencil text-warning"></i> تعديل: {{ $academicYear->name }}</div>
        <a href="{{ route('admin.academic-years.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-right"></i> رجوع</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.academic-years.update', $academicYear) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label>اسم السنة الدراسية *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $academicYear->name) }}" required>
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> حفظ التعديلات</button>
        </form>
    </div>
</div>
@endsection
