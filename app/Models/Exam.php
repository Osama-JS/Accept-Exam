<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'academic_year_id', 'grade_id', 'title',
        'total_marks', 'pass_marks', 'is_active',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'total_marks'  => 'integer',
        'pass_marks'   => 'integer',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function subjectConfigs(): HasMany
    {
        return $this->hasMany(ExamSubjectConfig::class);
    }

    public function studentExams(): HasMany
    {
        return $this->hasMany(StudentExam::class);
    }

    // إجمالي عدد الأسئلة في الاختبار
    public function totalQuestionsCount(): int
    {
        return $this->subjectConfigs->sum('question_count');
    }

    // درجة كل سؤال
    public function markPerQuestion(): float
    {
        $total = $this->totalQuestionsCount();
        return $total > 0 ? $this->total_marks / $total : 0;
    }
}
