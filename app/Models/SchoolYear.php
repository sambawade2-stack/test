<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolYear extends Model
{
    protected $fillable = [
        'name', 'start_date', 'end_date', 'is_active',
        'inscription_fee', 'monthly_fee',
    ];

    protected $casts = [
        'start_date'      => 'date',
        'end_date'        => 'date',
        'is_active'       => 'boolean',
        'inscription_fee' => 'decimal:2',
        'monthly_fee'     => 'decimal:2',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public static function current(): ?self
    {
        return static::where('is_active', true)->first();
    }

    public function activate(): void
    {
        static::query()->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }
}
