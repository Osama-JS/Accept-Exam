<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'is_current'];

    protected $casts = ['is_current' => 'boolean'];

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    // تعيين سنة كالسنة الحالية وإلغاء الأخريات
    public function makeCurrent(): void
    {
        static::query()->update(['is_current' => false]);
        $this->update(['is_current' => true]);
    }

    public static function getCurrent(): ?static
    {
        return static::where('is_current', true)->first();
    }

    public function getLifecycleStatusAttribute(): string
    {
        if ($this->is_current) {
            return 'active';
        }
        
        $currentYear = static::getCurrent();
        
        if ($currentYear && $this->name < $currentYear->name) {
            return 'archived';
        }
        
        return 'upcoming';
    }
}

