<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamSubjectConfig extends Model
{
    protected $fillable = [
        'exam_id', 'subject_id', 'question_count',
        'marks_per_question', 'difficulties', 'types'
    ];

    protected $casts = [
        'question_count'     => 'integer',
        'marks_per_question' => 'integer',
        'difficulties'       => 'array',
        'types'              => 'array',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
