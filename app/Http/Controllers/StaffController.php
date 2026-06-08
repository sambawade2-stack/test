<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStaffRequest;
use App\Models\Staff;
use App\Models\User;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StaffController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:' . User::rolesFor('personnel_view')),
            new Middleware('role:' . User::rolesFor('personnel_manage'),
                only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = Staff::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) =>
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('position', 'like', "%$search%")
                  ->orWhere('employee_number', 'like', "%$search%")
            );
        }

        $staff = $query->orderBy('last_name')->paginate(20)->withQueryString();

        return view('staff.index', compact('staff'));
    }

    public function create(): View
    {
        return view('staff.create');
    }

    public function store(StoreStaffRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['employee_number'] = Staff::generateEmployeeNumber($data['category']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('staff/photos', 'public');
        }

        Staff::create($data);

        return redirect()->route('staff.index')
            ->with('success', 'Personnel ajouté avec succès.');
    }

    public function show(Staff $staff): View
    {
        $staff->load(['payrolls' => fn ($q) => $q->orderByDesc('year')->orderByDesc('month')]);

        return view('staff.show', compact('staff'));
    }

    public function edit(Staff $staff): View
    {
        return view('staff.edit', compact('staff'));
    }

    public function update(StoreStaffRequest $request, Staff $staff): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($staff->photo) Storage::disk('public')->delete($staff->photo);
            $data['photo'] = $request->file('photo')->store('staff/photos', 'public');
        }

        $staff->update($data);

        return redirect()->route('staff.show', $staff)
            ->with('success', 'Personnel mis à jour.');
    }

    public function destroy(Staff $staff): RedirectResponse
    {
        $staff->delete();

        return redirect()->route('staff.index')
            ->with('success', 'Personnel supprimé.');
    }

    public function payrollHistory(Request $request, Staff $staff): View
    {
        $query = $staff->payrolls()->orderByDesc('year')->orderByDesc('month');

        if ($request->filled('year'))       $query->where('year', $request->year);
        if ($request->filled('month_from') && $request->filled('month_to')) {
            $query->whereBetween('month', [$request->month_from, $request->month_to]);
        }

        $payrolls  = $query->get();
        $years     = $staff->payrolls()->selectRaw('DISTINCT year')->orderByDesc('year')->pluck('year');
        $totalNet  = $payrolls->sum('net_salary');
        $totalPaid = $payrolls->where('status', 'paid')->sum('net_salary');

        return view('payrolls.history', compact('payrolls', 'years', 'totalNet', 'totalPaid'))->with([
            'staff'        => $staff,
            'employeeType' => 'staff',
        ]);
    }

    public function payrollHistoryPdf(Request $request, Staff $staff): Response
    {
        $query = $staff->payrolls()->orderByDesc('year')->orderByDesc('month');

        if ($request->filled('year'))       $query->where('year', $request->year);
        if ($request->filled('month_from')) $query->where('month', '>=', $request->month_from);
        if ($request->filled('month_to'))   $query->where('month', '<=', $request->month_to);

        $payrolls = $query->get();
        $pdfService = app(PdfService::class);
        return $pdfService->generatePayrollHistory($staff, $payrolls, $request->all());
    }
}
