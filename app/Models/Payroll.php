<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payroll extends Model
{
    protected $fillable = [
        'payroll_number', 'payable_id', 'payable_type',
        'month', 'year',
        'base_salary', 'bonuses', 'deductions', 'net_salary',
        'status', 'payment_date', 'payment_method', 'notes', 'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'base_salary'  => 'decimal:2',
        'bonuses'      => 'decimal:2',
        'deductions'   => 'decimal:2',
        'net_salary'   => 'decimal:2',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getMonthLabelAttribute(): string
    {
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];
        return $months[$this->month] ?? '';
    }

    public function computeNet(): float
    {
        return (float) ($this->base_salary + $this->bonuses - $this->deductions);
    }

    public static function generatePayrollNumber(): string
    {
        $year  = now()->format('Y');
        $month = now()->format('m');
        $last  = static::whereYear('created_at', $year)->max('id') ?? 0;
        return sprintf('PAY-%s%s-%05d', $year, $month, $last + 1);
    }
}
