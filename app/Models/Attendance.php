<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'student_id', 'classroom_id', 'date',
        'status', 'period', 'reason', 'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->whereMonth('date', $month)->whereYear('date', $year);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public static function getAbsenceRate(int $studentId, int $month, int $year): float
    {
        $total   = static::where('student_id', $studentId)->forMonth($month, $year)->count();
        $absents = static::where('student_id', $studentId)->forMonth($month, $year)->absent()->count();

        return $total > 0 ? round(($absents / $total) * 100, 1) : 0;
    }
}
