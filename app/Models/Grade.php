<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'order', 'description'];

    protected $casts = ['order' => 'integer'];

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'applying_grade_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // عدد الأسئلة في هذا الصف
    public function questionsCount(): int
    {
        return $this->subjects->sum(fn($s) => $s->questions()->count());
    }
}
