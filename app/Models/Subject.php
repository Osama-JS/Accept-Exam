<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = ['grade_id', 'name', 'icon'];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function examConfigs(): HasMany
    {
        return $this->hasMany(ExamSubjectConfig::class);
    }
}
