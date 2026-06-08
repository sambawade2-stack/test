<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Payroll;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use Illuminate\Support\Collection;

class PaymentService
{
    // ─── Paiements élèves ─────────────────────────────────────────────────────

    public function createPayment(array $data, int $createdBy): Payment
    {
        $data['receipt_number'] = Payment::generateReceiptNumber();
        $data['balance']        = max(0, $data['amount_due'] - $data['amount_paid']);
        $data['status']         = $this->resolveStatus($data['amount_paid'], $data['amount_due']);
        $data['created_by']     = $createdBy;

        return Payment::create($data);
    }

    public function updatePayment(Payment $payment, array $data): Payment
    {
        $data['balance'] = max(0, $data['amount_due'] - $data['amount_paid']);
        $data['status']  = $this->resolveStatus($data['amount_paid'], $data['amount_due']);

        $payment->update($data);
        return $payment->fresh();
    }

    // ─── Impayés ─────────────────────────────────────────────────────────────

    /**
     * Retourne les élèves n'ayant pas payé la mensualité d'un mois donné.
     * Appelé typiquement après le 10 du mois.
     */
    public function getUnpaidStudents(int $month, int $year, ?int $classroomId = null): Collection
    {
        $query = Student::active()
            ->with(['classroom', 'schoolYear'])
            ->whereDoesntHave('payments', function ($q) use ($month, $year) {
                $q->where('payment_type', 'mensualite')
                  ->where('month', $month)
                  ->where('year', $year)
                  ->whereIn('status', ['paid', 'partial']);
            });

        if ($classroomId) {
            $query->where('classroom_id', $classroomId);
        }

        return $query->get();
    }

    /**
     * Retourne tous les élèves ayant un solde impayé (balance > 0).
     */
    public function getStudentsWithBalance(): Collection
    {
        return Student::active()
            ->with(['classroom', 'payments'])
            ->whereHas('payments', fn ($q) => $q->where('balance', '>', 0))
            ->get()
            ->map(function ($student) {
                $student->total_balance = $student->payments()->sum('balance');
                return $student;
            });
    }

    /**
     * Statistiques paiements pour le dashboard.
     */
    public function getMonthlyStats(int $month, int $year): array
    {
        $schoolYear = SchoolYear::current();

        $totalDue   = Payment::where('month', $month)->where('year', $year)->sum('amount_due');
        $totalPaid  = Payment::where('month', $month)->where('year', $year)->sum('amount_paid');
        $unpaidCount= $this->getUnpaidStudents($month, $year)->count();

        return [
            'total_due'    => $totalDue,
            'total_paid'   => $totalPaid,
            'total_balance'=> $totalDue - $totalPaid,
            'unpaid_count' => $unpaidCount,
            'paid_count'   => Payment::paid()->forMonth($month, $year)->count(),
        ];
    }

    // ─── Paie personnel ───────────────────────────────────────────────────────

    public function createPayroll(array $data, int $createdBy): Payroll
    {
        $data['payable_type']   = $data['payable_type'] === 'teacher'
            ? Teacher::class
            : Staff::class;
        $data['payroll_number'] = Payroll::generatePayrollNumber();
        $data['bonuses']        = $data['bonuses'] ?? 0;
        $data['deductions']     = $data['deductions'] ?? 0;
        $data['net_salary']     = $data['base_salary'] + $data['bonuses'] - $data['deductions'];
        $data['created_by']     = $createdBy;

        return Payroll::create($data);
    }

    public function markPayrollAsPaid(Payroll $payroll, string $paymentDate): Payroll
    {
        $payroll->update([
            'status'       => 'paid',
            'payment_date' => $paymentDate,
        ]);
        return $payroll->fresh();
    }

    // ─── Privé ────────────────────────────────────────────────────────────────

    private function resolveStatus(float $paid, float $due): string
    {
        if ($paid <= 0)      return 'unpaid';
        if ($paid >= $due)   return 'paid';
        return 'partial';
    }
}
