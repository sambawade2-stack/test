<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'registration_number', 'first_name', 'last_name',
        'date_of_birth', 'place_of_birth', 'gender', 'photo',
        'address', 'phone', 'nationality',
        'parent_name', 'parent_phone', 'parent_email', 'parent_relation',
        'classroom_id', 'school_year_id', 'enrolled_at',
        'is_active', 'previous_school', 'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrolled_at'   => 'date',
        'is_active'     => 'boolean',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithUnpaidMonthly($query, int $month, int $year)
    {
        return $query->whereDoesntHave('payments', function ($q) use ($month, $year) {
            $q->where('payment_type', 'mensualite')
              ->where('month', $month)
              ->where('year', $year)
              ->where('status', 'paid');
        });
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }

    public function hasPayedInscription(): bool
    {
        return $this->payments()
            ->where('payment_type', 'inscription')
            ->where('school_year_id', $this->school_year_id)
            ->where('status', 'paid')
            ->exists();
    }

    public function hasPayedMonthly(int $month, int $year): bool
    {
        return $this->payments()
            ->where('payment_type', 'mensualite')
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'paid')
            ->exists();
    }

    public function totalPaid(): float
    {
        return (float) $this->payments()->sum('amount_paid');
    }

    public function totalDue(): float
    {
        return (float) $this->payments()->sum('amount_due');
    }

    public function outstandingBalance(): float
    {
        return (float) $this->payments()->sum('balance');
    }

    /**
     * Code de l'établissement (préfixe du matricule).
     * Défini par SCHOOL_CODE, sinon déduit des initiales du nom de l'école.
     * Ex : "Lycée Technique" → "LT".
     */
    public static function schoolCode(): string
    {
        if ($code = env('SCHOOL_CODE')) {
            return strtoupper(trim($code));
        }

        $name  = env('SCHOOL_NAME', 'École');
        $stop  = ['de', 'des', 'du', 'la', 'le', 'les', 'et', 'd', 'l'];
        $initials = collect(preg_split('/[\s\-\/]+/', trim($name)))
            ->reject(fn ($w) => $w === '' || in_array(mb_strtolower($w), $stop, true))
            ->map(fn ($w) => mb_substr($w, 0, 1))
            ->implode('');
        $initials = strtoupper(preg_replace('/[^A-Za-z]/', '', $initials));

        return $initials !== '' ? mb_substr($initials, 0, 4) : 'EC';
    }

    /**
     * Génère un matricule professionnel : CODE-ANNÉE-SÉQUENCE (ex: LT-2026-0042).
     * La séquence repart de 0001 à chaque nouvelle année.
     */
    public static function generateRegistrationNumber(?int $year = null): string
    {
        $code   = static::schoolCode();
        $year   = $year ?? (int) now()->format('Y');
        $prefix = "{$code}-{$year}-";

        $last = static::withTrashed()
            ->where('registration_number', 'like', $prefix . '%')
            ->orderByDesc('registration_number')
            ->value('registration_number');

        $seq = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Vérifie si l'élève est "en règle" : inscription payée + mensualité du mois courant
     * + aucun solde impayé. Retourne le détail pour l'affichage au scan.
     */
    public function standing(?int $month = null, ?int $year = null): array
    {
        $month = $month ?? now()->month;
        $year  = $year  ?? now()->year;

        $inscriptionPaid = $this->hasPayedInscription();
        $monthPaid       = $this->hasPayedMonthly($month, $year);
        $balance         = $this->outstandingBalance();

        $inOrder = $inscriptionPaid && $monthPaid && $balance <= 0;

        $reasons = [];
        if (!$inscriptionPaid) $reasons[] = "Inscription non réglée";
        if (!$monthPaid)       $reasons[] = "Mensualité du mois non réglée";
        if ($balance > 0)      $reasons[] = "Solde impayé : " . number_format($balance, 0, ',', ' ') . " FCFA";

        return [
            'in_order'         => $inOrder,
            'inscription_paid' => $inscriptionPaid,
            'month_paid'       => $monthPaid,
            'balance'          => $balance,
            'reasons'          => $reasons,
            'month'            => $month,
            'year'             => $year,
        ];
    }

    /**
     * Contenu encodé dans le QR code du badge.
     * Texte multi-lignes lisible par n'importe quel scanner.
     */
    public function qrPayload(): string
    {
        $this->loadMissing(['classroom', 'schoolYear']);

        $lines = [
            'ETABLISSEMENT: ' . env('SCHOOL_NAME', 'Établissement'),
            'MATRICULE: ' . $this->registration_number,
            'NOM: ' . $this->full_name,
            'NE(E) LE: ' . $this->date_of_birth?->format('d/m/Y'),
            'CLASSE: ' . ($this->classroom?->name ?? '—'),
            'ANNEE: ' . ($this->schoolYear?->name ?? '—'),
            'TUTEUR: ' . ($this->parent_name ?? '—'),
            'CONTACT: ' . ($this->parent_phone ?? '—'),
        ];

        return implode("\n", $lines);
    }
}
