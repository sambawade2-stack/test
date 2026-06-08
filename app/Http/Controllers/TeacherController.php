<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Models\Payroll;
use App\Models\Teacher;
use App\Models\User;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TeacherController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        // Consultation pour comptable+ ; création/édition/suppression réservées admin & directeur.
        return [
            new Middleware('role:' . User::rolesFor('personnel_view')),
            new Middleware('role:' . User::rolesFor('personnel_manage'),
                only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = Teacher::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) =>
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('subject', 'like', "%$search%")
                  ->orWhere('employee_number', 'like', "%$search%")
            );
        }

        $teachers = $query->orderBy('last_name')->paginate(20)->withQueryString();

        return view('teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('teachers.create');
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['employee_number'] = Teacher::generateEmployeeNumber();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('teachers/photos', 'public');
        }

        Teacher::create($data);

        return redirect()->route('teachers.index')
            ->with('success', 'Enseignant ajouté avec succès.');
    }

    public function show(Teacher $teacher): View
    {
        $teacher->load(['payrolls' => fn ($q) => $q->orderByDesc('year')->orderByDesc('month')]);

        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher): View
    {
        return view('teachers.edit', compact('teacher'));
    }

    public function update(StoreTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($teacher->photo) Storage::disk('public')->delete($teacher->photo);
            $data['photo'] = $request->file('photo')->store('teachers/photos', 'public');
        }

        $teacher->update($data);

        return redirect()->route('teachers.show', $teacher)
            ->with('success', 'Enseignant mis à jour.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->delete();

        return redirect()->route('teachers.index')
            ->with('success', 'Enseignant supprimé.');
    }

    public function payrollHistory(Request $request, Teacher $teacher): View
    {
        $query = $teacher->payrolls()->orderByDesc('year')->orderByDesc('month');

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('month_from') && $request->filled('month_to')) {
            $query->whereBetween('month', [$request->month_from, $request->month_to]);
        }

        $payrolls  = $query->get();
        $years     = $teacher->payrolls()->selectRaw('DISTINCT year')->orderByDesc('year')->pluck('year');
        $totalNet  = $payrolls->sum('net_salary');
        $totalPaid = $payrolls->where('status', 'paid')->sum('net_salary');

        return view('payrolls.history', compact('payrolls', 'years', 'totalNet', 'totalPaid', 'teacher'))->with('employeeType', 'teacher');
    }

    public function payrollHistoryPdf(Request $request, Teacher $teacher): Response
    {
        $query = $teacher->payrolls()->orderByDesc('year')->orderByDesc('month');

        if ($request->filled('year'))       $query->where('year', $request->year);
        if ($request->filled('month_from')) $query->where('month', '>=', $request->month_from);
        if ($request->filled('month_to'))   $query->where('month', '<=', $request->month_to);

        $payrolls = $query->get();
        $pdfService = app(PdfService::class);
        return $pdfService->generatePayrollHistory($teacher, $payrolls, $request->all());
    }
}
