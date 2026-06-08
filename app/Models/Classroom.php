<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    protected $fillable = [
        'name', 'level', 'section', 'cycle',
        'max_students', 'school_year_id', 'main_teacher',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeCollege($query)
    {
        return $query->where('cycle', 'collège');
    }

    public function scopeLycee($query)
    {
        return $query->where('cycle', 'lycée');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function activeStudentsCount(): int
    {
        return $this->students()->where('is_active', true)->count();
    }

    public function isFull(): bool
    {
        return $this->activeStudentsCount() >= $this->max_students;
    }
}
