<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Classroom;
use App\Models\Payment;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class PaymentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:' . User::rolesFor('payments'))];
    }

    public function __construct(
        private PaymentService $paymentService,
        private PdfService $pdfService
    ) {}

    public function index(Request $request): View
    {
        $query = Payment::with(['student', 'classroom', 'schoolYear'])
            ->latest('payment_date');

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }
        if ($request->filled('month') && $request->filled('year')) {
            $query->forMonth($request->month, $request->year);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', fn ($q) =>
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('registration_number', 'like', "%$search%")
            );
        }

        $payments    = $query->paginate(20)->withQueryString();
        $classrooms  = Classroom::orderBy('name')->get();
        $schoolYears = SchoolYear::orderByDesc('name')->get();

        return view('payments.index', compact('payments', 'classrooms', 'schoolYears'));
    }

    public function create(Request $request): View
    {
        $students    = Student::active()->with(['classroom', 'payments' => fn ($q) => $q->where('payment_type', 'inscription')])->orderBy('last_name')->get();
        $classrooms  = Classroom::orderBy('name')->get();
        $schoolYears = SchoolYear::orderByDesc('name')->get();
        $currentYear = SchoolYear::current();
        $student     = $request->filled('student_id')
            ? Student::find($request->student_id)
            : null;

        // Vérifie si l'élève sélectionné a déjà payé son inscription (année active)
        $inscriptionPaid = $student && $currentYear
            ? $student->payments()
                ->where('payment_type', 'inscription')
                ->where('school_year_id', $currentYear->id)
                ->whereIn('status', ['paid', 'partial'])
                ->exists()
            : false;

        // Pré-sélectionne mensualité si inscription déjà payée
        $defaultType = $inscriptionPaid ? 'mensualite' : 'inscription';

        return view('payments.create', compact(
            'students', 'classrooms', 'schoolYears', 'currentYear',
            'student', 'inscriptionPaid', 'defaultType'
        ));
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $payment = $this->paymentService->createPayment(
            $request->validated(),
            auth()->id()
        );

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Paiement enregistré avec succès. Reçu N° ' . $payment->receipt_number);
    }

    public function show(Payment $payment): View
    {
        $payment->load(['student.classroom', 'schoolYear', 'creator']);

        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment): View
    {
        $students    = Student::active()->orderBy('last_name')->get();
        $classrooms  = Classroom::orderBy('name')->get();
        $schoolYears = SchoolYear::orderByDesc('name')->get();

        return view('payments.edit', compact('payment', 'students', 'classrooms', 'schoolYears'));
    }

    public function update(StorePaymentRequest $request, Payment $payment): RedirectResponse
    {
        $this->paymentService->updatePayment($payment, $request->validated());

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Paiement mis à jour.');
    }

    public function receipt(Payment $payment): Response
    {
        return $this->pdfService->generatePaymentReceipt($payment);
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PaymentsExport($request->only(['payment_type', 'status', 'classroom_id', 'month', 'year', 'search'])),
            'paiements-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function complement(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate([
            'complement_amount' => "required|numeric|min:1|max:{$payment->balance}",
            'payment_method'    => 'required|in:cash,virement,mobile_money,cheque',
            'payment_date'      => 'required|date',
            'notes'             => 'nullable|string',
        ]);

        $newPaid   = $payment->amount_paid + $request->complement_amount;
        $newBalance = max(0, $payment->amount_due - $newPaid);
        $newStatus  = $newBalance <= 0 ? 'paid' : 'partial';

        $notes = collect(array_filter([
            $payment->notes,
            "Complément du " . now()->format('d/m/Y') . " : " . number_format($request->complement_amount, 0, ',', ' ') . " F",
            $request->notes,
        ]))->join(' | ');

        $payment->update([
            'amount_paid'    => $newPaid,
            'balance'        => $newBalance,
            'status'         => $newStatus,
            'payment_method' => $request->payment_method,
            'payment_date'   => $request->payment_date,
            'notes'          => $notes,
        ]);

        $msg = $newStatus === 'paid'
            ? 'Paiement soldé intégralement. ✅'
            : 'Complément enregistré. Reste : ' . number_format($newBalance, 0, ',', ' ') . ' F';

        return redirect()->route('payments.show', $payment)->with('success', $msg);
    }

    // ─── Impayés ─────────────────────────────────────────────────────────────

    public function unpaid(Request $request): View
    {
        $month      = $request->integer('month', now()->month);
        $year       = $request->integer('year', now()->year);
        $classroomId= $request->integer('classroom_id') ?: null;

        $students   = $this->paymentService->getUnpaidStudents($month, $year, $classroomId);
        $classrooms = Classroom::orderBy('name')->get();

        return view('payments.unpaid', compact('students', 'classrooms', 'month', 'year'));
    }

    public function unpaidReport(Request $request): Response
    {
        $month    = $request->integer('month', now()->month);
        $year     = $request->integer('year', now()->year);
        $students = $this->paymentService->getUnpaidStudents($month, $year);

        return $this->pdfService->generateUnpaidReport($students->toArray(), $month, $year);
    }
}
