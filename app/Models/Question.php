<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = ['subject_id', 'text'];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function choices(): HasMany
    {
        return $this->hasMany(Choice::class)->orderBy('order');
    }

    public function correctChoice(): ?Choice
    {
        return $this->choices()->where('is_correct', true)->first();
    }

    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }
}
