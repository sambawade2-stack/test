<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use App\Services\PaymentService;
use Illuminate\Console\Command;

class DetectUnpaidStudents extends Command
{
    protected $signature = 'school:detect-unpaid
                            {--month= : Mois ciblé (1-12), défaut = mois courant}
                            {--year=  : Année, défaut = année courante}
                            {--notify : Envoyer les notifications aux parents}
                            {--dry-run : Afficher sans envoyer de notifications}';

    protected $description = 'Détecte les élèves n\'ayant pas payé leur mensualité et notifie optionnellement les parents.';

    public function __construct(
        private PaymentService $paymentService,
        private NotificationService $notificationService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $month = (int) ($this->option('month') ?? now()->month);
        $year  = (int) ($this->option('year')  ?? now()->year);
        $notify = $this->option('notify');
        $dryRun = $this->option('dry-run');

        // On lance la détection uniquement après le 10 du mois (sauf si forcé)
        if (now()->month === $month && now()->year === $year && now()->day < 10 && !$dryRun) {
            $this->warn("⚠  Avant le 10 du mois. Utiliser --dry-run pour forcer.");
            return self::FAILURE;
        }

        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];

        $this->info("🔍  Détection des impayés — {$months[$month]} {$year}");
        $this->line('');

        $students = $this->paymentService->getUnpaidStudents($month, $year);

        if ($students->isEmpty()) {
            $this->info('✅  Aucun élève impayé trouvé pour cette période.');
            return self::SUCCESS;
        }

        $this->error("❌  {$students->count()} élève(s) n'ont pas payé :");
        $this->table(
            ['ID', 'Matricule', 'Nom', 'Classe', 'Email Parent'],
            $students->map(fn ($s) => [
                $s->id,
                $s->registration_number,
                $s->full_name,
                $s->classroom?->name ?? '-',
                $s->parent_email ?? 'N/A',
            ])->toArray()
        );

        if ($notify && !$dryRun) {
            $this->line('');
            $this->info('📧  Envoi des notifications...');
            $results = $this->notificationService->notifyUnpaidStudents($students, $month, $year);

            $this->table(
                ['Envoyés', 'Échoués', 'Sans email'],
                [[$results['sent'], $results['failed'], $results['skipped']]]
            );
        } elseif ($dryRun) {
            $this->warn('Mode dry-run : aucune notification envoyée.');
        }

        return self::SUCCESS;
    }
}
