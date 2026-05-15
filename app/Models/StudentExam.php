<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentExam extends Model
{
    protected $fillable = [
        'student_id', 'exam_id', 'result_token',
        'score', 'total_marks', 'pass_marks',
        'correct_answers', 'total_questions',
        'status', 'started_at', 'submitted_at',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
        'score'        => 'integer',
        'total_marks'  => 'integer',
        'pass_marks'   => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }

    public function isPassed(): bool
    {
        return $this->status === 'pass';
    }

    public function percentage(): float
    {
        return $this->total_marks > 0
            ? round(($this->score / $this->total_marks) * 100, 1)
            : 0;
    }
}
