<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAnswer extends Model
{
    protected $fillable = ['student_exam_id', 'question_id', 'chosen_choice_id', 'answer_text', 'is_correct'];

    protected $casts = ['is_correct' => 'boolean'];

    public function studentExam(): BelongsTo
    {
        return $this->belongsTo(StudentExam::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function chosenChoice(): BelongsTo
    {
        return $this->belongsTo(Choice::class, 'chosen_choice_id');
    }
}
