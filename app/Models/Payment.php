<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'receipt_number', 'student_id', 'school_year_id', 'classroom_id',
        'payment_type', 'month', 'year',
        'amount_due', 'amount_paid', 'balance',
        'status', 'payment_method', 'payment_date', 'notes', 'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_due'   => 'decimal:2',
        'amount_paid'  => 'decimal:2',
        'balance'      => 'decimal:2',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
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

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    public function scopeInscriptions($query)
    {
        return $query->where('payment_type', 'inscription');
    }

    public function scopeMensualites($query)
    {
        return $query->where('payment_type', 'mensualite');
    }

    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getMonthLabelAttribute(): string
    {
        if (!$this->month) {
            return '';
        }
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];
        return $months[$this->month] ?? '';
    }

    public static function generateReceiptNumber(): string
    {
        $year    = now()->format('Y');
        $month   = now()->format('m');
        $last    = static::whereYear('created_at', $year)->max('id') ?? 0;
        return sprintf('REC-%s%s-%05d', $year, $month, $last + 1);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function computeStatus(): string
    {
        if ($this->amount_paid <= 0) {
            return 'unpaid';
        }
        if ($this->amount_paid >= $this->amount_due) {
            return 'paid';
        }
        return 'partial';
    }
}
