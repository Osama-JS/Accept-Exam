<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'icon'];

    public function grades(): BelongsToMany
    {
        return $this->belongsToMany(Grade::class)->withTimestamps();
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
