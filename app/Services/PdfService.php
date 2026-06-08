<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Payment;
use App\Models\Payroll;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class PdfService
{
    // ─── Badges élèves avec QR code ───────────────────────────────────────────

    /**
     * Génère le data-URI PNG (base64) d'un QR code, intégrable dans une <img> DomPDF.
     */
    public function qrDataUri(string $payload, int $size = 220): string
    {
        $result = (new Builder(
            writer: new PngWriter(),
            data: $payload,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $size,
            margin: 6,
        ))->build();

        return $result->getDataUri();
    }

    /**
     * Badge individuel d'un élève (format carte).
     */
    public function generateStudentBadge(Student $student): Response
    {
        $student->loadMissing(['classroom', 'schoolYear']);

        $pdf = Pdf::loadView('pdf.badge', [
            'badges' => [[
                'student' => $student,
                'qr'      => $this->qrDataUri($student->qrPayload()),
            ]],
            'school' => $this->schoolInfo(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download("badge-{$student->registration_number}.pdf");
    }

    /**
     * Planche de badges pour toute une classe (plusieurs par page).
     */
    public function generateClassBadges(Collection $students, string $classLabel = ''): Response
    {
        $badges = $students->map(fn ($student) => [
            'student' => $student->loadMissing(['classroom', 'schoolYear']),
            'qr'      => $this->qrDataUri($student->qrPayload()),
        ])->all();

        $pdf = Pdf::loadView('pdf.badge', [
            'badges' => $badges,
            'school' => $this->schoolInfo(),
        ])->setPaper('a4', 'portrait');

        $slug = $classLabel ? '-' . strtolower(str_replace(' ', '', $classLabel)) : '';
        return $pdf->download("badges{$slug}.pdf");
    }

    // ─── Reçus paiements élèves ───────────────────────────────────────────────

    public function generatePaymentReceipt(Payment $payment): Response
    {
        $payment->load(['student.classroom', 'schoolYear', 'creator']);

        $pdf = Pdf::loadView('pdf.receipt', [
            'payment'    => $payment,
            'school'     => $this->schoolInfo(),
        ])->setPaper('a5', 'portrait');

        return $pdf->download("recu-{$payment->receipt_number}.pdf");
    }

    // ─── Reçu fiche de paie ───────────────────────────────────────────────────

    public function generatePayslip(Payroll $payroll): Response
    {
        $payroll->load(['payable', 'creator']);

        $pdf = Pdf::loadView('pdf.payslip', [
            'payroll' => $payroll,
            'school'  => $this->schoolInfo(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download("fiche-paie-{$payroll->payroll_number}.pdf");
    }

    // ─── Certificat de scolarité ──────────────────────────────────────────────

    public function generateScolariteCertificate(Student $student): Response
    {
        $student->load(['classroom', 'schoolYear']);

        $pdf = Pdf::loadView('pdf.certificate_scolarite', [
            'student' => $student,
            'school'  => $this->schoolInfo(),
            'date'    => now()->locale('fr')->isoFormat('D MMMM YYYY'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download("certificat-scolarite-{$student->registration_number}.pdf");
    }

    // ─── Certificat d'assiduité ───────────────────────────────────────────────

    public function generateAssiduiteCertificate(Student $student, int $month, int $year): Response
    {
        $student->load(['classroom', 'schoolYear']);

        $absences   = Attendance::where('student_id', $student->id)
            ->forMonth($month, $year)
            ->absent()
            ->count();
        $totalDays  = Attendance::where('student_id', $student->id)
            ->forMonth($month, $year)
            ->count();
        $tauxPresence = $totalDays > 0
            ? round((($totalDays - $absences) / $totalDays) * 100, 1)
            : 100;

        $pdf = Pdf::loadView('pdf.certificate_assiduite', [
            'student'      => $student,
            'school'       => $this->schoolInfo(),
            'month'        => $month,
            'year'         => $year,
            'absences'     => $absences,
            'total_days'   => $totalDays,
            'taux_presence'=> $tauxPresence,
            'date'         => now()->locale('fr')->isoFormat('D MMMM YYYY'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download("certificat-assiduite-{$student->registration_number}.pdf");
    }

    // ─── Historique des paies d'un employé ───────────────────────────────────

    public function generatePayrollHistory($employee, $payrolls, array $filters = []): Response
    {
        $months = [
            1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',
            5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',
            9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre',
        ];

        $period = '';
        if (!empty($filters['year'])) {
            $period = $filters['year'];
            if (!empty($filters['month_from']) && !empty($filters['month_to'])) {
                $period = ($months[$filters['month_from']] ?? '') . ' - ' . ($months[$filters['month_to']] ?? '') . ' ' . $filters['year'];
            }
        }

        $pdf = Pdf::loadView('pdf.payroll_history', [
            'employee'  => $employee,
            'payrolls'  => $payrolls,
            'period'    => $period,
            'months'    => $months,
            'school'    => $this->schoolInfo(),
            'date'      => now()->locale('fr')->isoFormat('D MMMM YYYY'),
            'totalNet'  => $payrolls->sum('net_salary'),
            'totalPaid' => $payrolls->where('status', 'paid')->sum('net_salary'),
        ])->setPaper('a4', 'portrait');

        $slug = strtolower(str_replace(' ', '-', $employee->full_name));
        return $pdf->download("historique-paie-{$slug}.pdf");
    }

    // ─── Liste élèves impayés (rapport) ──────────────────────────────────────

    public function generateUnpaidReport(array $students, int $month, int $year): Response
    {
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];

        $pdf = Pdf::loadView('pdf.unpaid_report', [
            'students'    => $students,
            'month_label' => $months[$month] ?? $month,
            'year'        => $year,
            'school'      => $this->schoolInfo(),
            'date'        => now()->locale('fr')->isoFormat('D MMMM YYYY'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download("impayés-{$month}-{$year}.pdf");
    }

    // ─── Privé ────────────────────────────────────────────────────────────────

    public function schoolInfo(): array
    {
        return [
            'name'    => config('app.school_name', env('SCHOOL_NAME', 'Établissement Scolaire')),
            'phone'   => env('SCHOOL_PHONE', ''),
            'address' => env('SCHOOL_ADDRESS', ''),
            'email'   => env('SCHOOL_EMAIL', ''),
        ];
    }
}
