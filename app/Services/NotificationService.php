<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Notifie les parents des élèves en retard de paiement.
     * Structure extensible : email aujourd'hui, SMS demain.
     */
    public function notifyUnpaidStudents(Collection $students, int $month, int $year): array
    {
        $results = ['sent' => 0, 'failed' => 0, 'skipped' => 0];

        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];
        $monthLabel = $months[$month] ?? $month;

        foreach ($students as $student) {
            try {
                if ($student->parent_email) {
                    $this->sendEmailNotification($student, $monthLabel, $year);
                    $results['sent']++;
                } else {
                    $results['skipped']++;
                    Log::info("Pas d'email pour l'élève {$student->full_name} (ID:{$student->id})");
                }
            } catch (\Throwable $e) {
                $results['failed']++;
                Log::error("Échec notification élève {$student->id}: {$e->getMessage()}");
            }
        }

        return $results;
    }

    // ─── Canaux ───────────────────────────────────────────────────────────────

    private function sendEmailNotification(Student $student, string $month, int $year): void
    {
        Mail::send('emails.unpaid_reminder', [
            'student'    => $student,
            'month'      => $month,
            'year'       => $year,
            'school'     => env('SCHOOL_NAME', 'Établissement Scolaire'),
            'schoolPhone'=> env('SCHOOL_PHONE', ''),
        ], function ($mail) use ($student) {
            $mail->to($student->parent_email, $student->parent_name)
                 ->subject("Rappel de paiement - {$student->full_name}");
        });
    }

    /**
     * Point d'extension futur pour SMS (ex: Twilio, Orange API).
     */
    public function sendSmsNotification(Student $student, string $message): bool
    {
        // TODO: Implémenter avec un provider SMS (Twilio, Orange, etc.)
        Log::info("SMS [non configuré] pour {$student->parent_phone}: {$message}");
        return false;
    }
}
