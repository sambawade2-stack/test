<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Payment;
use App\Models\Payroll;
use App\Models\SchoolYear;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\PaymentService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function index(): View
    {
        $currentYear = SchoolYear::current();
        $now         = now();

        $stats = [
            'total_students'   => Student::active()->count(),
            'total_teachers'   => Teacher::active()->count(),
            'total_staff'      => Staff::active()->count(),
            'total_classrooms' => Classroom::when($currentYear, fn ($q) => $q->where('school_year_id', $currentYear->id))->count(),

            // Paiements du mois courant
            'monthly_collected' => Payment::whereMonth('payment_date', $now->month)
                ->whereYear('payment_date', $now->year)
                ->sum('amount_paid'),

            'unpaid_this_month' => $this->paymentService
                ->getUnpaidStudents($now->month, $now->year)
                ->count(),

            // Paie du mois courant
            'payroll_total' => Payroll::forMonth($now->month, $now->year)
                ->paid()
                ->sum('net_salary'),

            'payroll_pending' => Payroll::forMonth($now->month, $now->year)
                ->pending()
                ->count(),
        ];

        $recentPayments = Payment::with(['student', 'classroom'])
            ->latest()
            ->take(10)
            ->get();

        $unpaidStudents = $this->paymentService
            ->getUnpaidStudents($now->month, $now->year)
            ->take(5);

        return view('dashboard', compact('stats', 'recentPayments', 'unpaidStudents', 'currentYear'));
    }
}
