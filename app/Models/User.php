<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Profils disponibles dans l'application.
     * Pour ajouter un nouveau profil : ajouter une ligne ici (aucune migration nécessaire).
     */
    public const ROLES = [
        'admin'       => ['label' => 'Administrateur', 'color' => 'indigo',  'desc' => 'Accès complet à toute l\'application'],
        'directeur'   => ['label' => 'Directeur',      'color' => 'violet',  'desc' => 'Supervision générale, personnel, rapports'],
        'comptable'   => ['label' => 'Comptable',      'color' => 'emerald', 'desc' => 'Paiements, paie du personnel, finances'],
        'surveillant' => ['label' => 'Surveillant',    'color' => 'amber',   'desc' => 'Présences, discipline, suivi des élèves'],
        'secretaire'  => ['label' => 'Secrétaire',     'color' => 'sky',     'desc' => 'Inscriptions et gestion des élèves'],
        'caissier'    => ['label' => 'Caissier',       'color' => 'teal',    'desc' => 'Encaissement des paiements uniquement'],
    ];

    /**
     * Matrice des accès : module => rôles autorisés.
     * Modifier ici suffit à changer les droits partout (contrôleurs + menus).
     */
    public const PERMISSIONS = [
        'users'            => ['admin'],
        'settings'         => ['admin', 'directeur'],
        'school_years'     => ['admin', 'directeur'],
        'payrolls'         => ['admin', 'directeur', 'comptable'],
        'payments'         => ['admin', 'directeur', 'comptable', 'caissier'],
        'personnel_view'   => ['admin', 'directeur', 'comptable'],
        'personnel_manage' => ['admin', 'directeur'],
        'students_manage'  => ['admin', 'directeur', 'secretaire'],
        'certificates'     => ['admin', 'directeur', 'secretaire'],
        'scan'             => ['admin', 'directeur', 'comptable', 'surveillant', 'caissier'],
        'attendance'       => ['admin', 'directeur', 'surveillant', 'secretaire'],
        'finance'          => ['admin', 'directeur', 'comptable'],
    ];

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_active', 'phone',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function paymentsCreated(): HasMany
    {
        return $this->hasMany(Payment::class, 'created_by');
    }

    public function payrollsCreated(): HasMany
    {
        return $this->hasMany(Payroll::class, 'created_by');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDirecteur(): bool
    {
        return $this->role === 'directeur';
    }

    public function isComptable(): bool
    {
        return $this->role === 'comptable';
    }

    public function canManagePayments(): bool
    {
        return in_array($this->role, ['admin', 'comptable']);
    }

    public function canManageStaff(): bool
    {
        return in_array($this->role, ['admin', 'directeur']);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role]['label'] ?? ucfirst($this->role);
    }

    public function getRoleColorAttribute(): string
    {
        return self::ROLES[$this->role]['color'] ?? 'gray';
    }

    /**
     * L'utilisateur a-t-il accès à un module donné ?
     */
    public function canAccess(string $module): bool
    {
        return in_array($this->role, self::PERMISSIONS[$module] ?? [], true);
    }

    /**
     * Liste de rôles d'un module, formatée pour le middleware "role:..."
     */
    public static function rolesFor(string $module): string
    {
        return implode(',', self::PERMISSIONS[$module] ?? []);
    }
}
