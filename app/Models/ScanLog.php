<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanLog extends Model
{
    protected $fillable = [
        'student_id', 'scanned_by', 'matricule', 'found',
        'in_order', 'inscription_paid', 'month_paid', 'balance',
        'classroom', 'scanned_at',
    ];

    protected $casts = [
        'found'            => 'boolean',
        'in_order'         => 'boolean',
        'inscription_paid' => 'boolean',
        'month_paid'       => 'boolean',
        'balance'          => 'decimal:2',
        'scanned_at'       => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scanned_at', today());
    }
}
