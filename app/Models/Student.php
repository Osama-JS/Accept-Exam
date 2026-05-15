<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'name', 'applying_grade_id', 'previous_school',
        'last_grade_average', 'guardian_name', 'guardian_phone',
    ];

    protected $casts = ['last_grade_average' => 'float'];

    public function applyingGrade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'applying_grade_id');
    }

    public function studentExams(): HasMany
    {
        return $this->hasMany(StudentExam::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'student_exams');
    }
}
