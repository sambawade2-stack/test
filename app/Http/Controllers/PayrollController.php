<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayrollRequest;
use App\Models\Payroll;
use App\Models\Staff;
use App\Models\Teacher;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class PayrollController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:' . User::rolesFor('payrolls'))];
    }

    public function __construct(
        private PaymentService $paymentService,
        private PdfService $pdfService
    ) {}

    public function index(Request $request): View
    {
        $month = $request->integer('month', now()->month);
        $year  = $request->integer('year', now()->year);

        $payrolls = Payroll::with('payable')
            ->forMonth($month, $year)
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalPaid    = Payroll::forMonth($month, $year)->paid()->sum('net_salary');
        $totalPending = Payroll::forMonth($month, $year)->pending()->sum('net_salary');

        return view('payrolls.index', compact('payrolls', 'month', 'year', 'totalPaid', 'totalPending'));
    }

    public function create(): View
    {
        $teachers = Teacher::active()->orderBy('last_name')->get();
        $staff    = Staff::active()->orderBy('last_name')->get();

        return view('payrolls.create', compact('teachers', 'staff'));
    }

    public function store(StorePayrollRequest $request): RedirectResponse
    {
        $payroll = $this->paymentService->createPayroll(
            $request->validated(),
            auth()->id()
        );

        return redirect()->route('payrolls.show', $payroll)
            ->with('success', 'Fiche de paie créée. N° ' . $payroll->payroll_number);
    }

    public function show(Payroll $payroll): View
    {
        $payroll->load(['payable', 'creator']);

        return view('payrolls.show', compact('payroll'));
    }

    public function markPaid(Request $request, Payroll $payroll): RedirectResponse
    {
        $request->validate(['payment_date' => 'required|date']);

        $this->paymentService->markPayrollAsPaid($payroll, $request->payment_date);

        return redirect()->route('payrolls.show', $payroll)
            ->with('success', 'Paie marquée comme payée.');
    }

    public function payslip(Payroll $payroll): Response
    {
        return $this->pdfService->generatePayslip($payroll);
    }

    public function bulkCreate(Request $request): RedirectResponse
    {
        $request->validate([
            'month'    => 'required|integer|between:1,12',
            'year'     => 'required|integer|min:2000',
            'type'     => 'required|in:teacher,staff',
        ]);

        $month = $request->month;
        $year  = $request->year;
        $type  = $request->type;

        $employees = $type === 'teacher'
            ? Teacher::active()->get()
            : Staff::active()->get();

        $created = 0;
        foreach ($employees as $employee) {
            if ($employee->hasPayrollForMonth($month, $year)) {
                continue;
            }
            $this->paymentService->createPayroll([
                'payable_id'    => $employee->id,
                'payable_type'  => $type,
                'month'         => $month,
                'year'          => $year,
                'base_salary'   => $employee->base_salary,
                'bonuses'       => 0,
                'deductions'    => 0,
                'payment_method'=> 'virement',
            ], auth()->id());
            $created++;
        }

        return redirect()->route('payrolls.index', compact('month', 'year'))
            ->with('success', "$created fiche(s) de paie créée(s).");
    }
}
