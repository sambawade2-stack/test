<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_number', 'first_name', 'last_name', 'email', 'phone',
        'address', 'gender', 'date_of_birth', 'nationality', 'photo',
        'category', 'position', 'hire_date',
        'base_salary', 'contract_type', 'status', 'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date'     => 'date',
        'base_salary'   => 'decimal:2',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function payrolls(): MorphMany
    {
        return $this->morphMany(Payroll::class, 'payable');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAdministratif($query)
    {
        return $query->where('category', 'administratif');
    }

    public function scopeAppoint($query)
    {
        return $query->where('category', 'appoint');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public static function generateEmployeeNumber(string $category): string
    {
        $prefix = $category === 'administratif' ? 'ADM' : 'APP';
        $last   = static::withTrashed()->max('id') ?? 0;
        return sprintf('%s-%05d', $prefix, $last + 1);
    }
}
