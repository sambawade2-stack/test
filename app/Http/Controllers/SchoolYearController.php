<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SchoolYearController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:' . User::rolesFor('school_years'))];
    }

    public function index(): View
    {
        $schoolYears = SchoolYear::withCount(['students', 'classrooms'])
            ->orderByDesc('name')
            ->get();

        return view('school-years.index', compact('schoolYears'));
    }

    public function create(): View
    {
        return view('school-years.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'             => 'required|string|max:20|unique:school_years',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
            'inscription_fee'  => 'required|numeric|min:0',
            'monthly_fee'      => 'required|numeric|min:0',
        ]);

        SchoolYear::create($data);

        return redirect()->route('school-years.index')
            ->with('success', "Année scolaire {$data['name']} créée.");
    }

    public function edit(SchoolYear $schoolYear): View
    {
        return view('school-years.edit', compact('schoolYear'));
    }

    public function update(Request $request, SchoolYear $schoolYear): RedirectResponse
    {
        $data = $request->validate([
            'name'            => 'required|string|max:20|unique:school_years,name,' . $schoolYear->id,
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after:start_date',
            'inscription_fee' => 'required|numeric|min:0',
            'monthly_fee'     => 'required|numeric|min:0',
        ]);

        $schoolYear->update($data);

        return redirect()->route('school-years.index')
            ->with('success', 'Année scolaire mise à jour.');
    }

    public function destroy(SchoolYear $schoolYear): RedirectResponse
    {
        if ($schoolYear->students()->exists()) {
            return back()->with('error', 'Impossible de supprimer une année contenant des élèves.');
        }

        $schoolYear->delete();

        return redirect()->route('school-years.index')
            ->with('success', 'Année scolaire supprimée.');
    }

    public function activate(SchoolYear $schoolYear): RedirectResponse
    {
        $schoolYear->activate();

        return redirect()->route('school-years.index')
            ->with('success', "Année {$schoolYear->name} définie comme active.");
    }

    public function close(SchoolYear $schoolYear): RedirectResponse
    {
        if (!$schoolYear->is_active) {
            return back()->with('error', "Cette année n'est pas active.");
        }

        $schoolYear->update(['is_active' => false]);

        return redirect()->route('school-years.index')
            ->with('success', "Année scolaire {$schoolYear->name} fermée avec succès.");
    }

    public function show(SchoolYear $schoolYear): View
    {
        $schoolYear->loadCount(['students', 'classrooms']);
        $schoolYear->load(['classrooms.students']);

        return view('school-years.show', compact('schoolYear'));
    }
}
