<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\SchoolYear;
use App\Models\User;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ClassroomController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:' . User::rolesFor('students_manage'),
                except: ['index', 'show', 'badges']),
        ];
    }

    public function badges(Classroom $classroom): Response
    {
        $students = $classroom->students()->active()->orderBy('last_name')->get();

        if ($students->isEmpty()) {
            abort(404, 'Aucun élève dans cette classe.');
        }

        return app(PdfService::class)->generateClassBadges($students, $classroom->name);
    }

    public function index(Request $request): View
    {
        $schoolYearId = $request->integer('school_year_id') ?: SchoolYear::current()?->id;

        $classrooms = Classroom::with(['schoolYear'])
            ->withCount('students')
            ->when($schoolYearId, fn ($q) => $q->where('school_year_id', $schoolYearId))
            ->orderBy('level')
            ->orderBy('section')
            ->get();

        $schoolYears = SchoolYear::orderByDesc('name')->get();

        return view('classrooms.index', compact('classrooms', 'schoolYears', 'schoolYearId'));
    }

    public function create(): View
    {
        $schoolYears = SchoolYear::orderByDesc('name')->get();
        $currentYear = SchoolYear::current();

        return view('classrooms.create', compact('schoolYears', 'currentYear'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'level'          => 'required|string|max:30',
            'section'        => 'nullable|string|max:10',
            'cycle'          => 'required|in:collège,lycée',
            'max_students'   => 'required|integer|min:1|max:200',
            'school_year_id' => 'required|exists:school_years,id',
            'main_teacher'   => 'nullable|string|max:100',
        ]);

        $data['name'] = trim($data['level'] . ' ' . $data['section']);
        Classroom::create($data);

        return redirect()->route('classrooms.index')
            ->with('success', 'Classe créée avec succès.');
    }

    public function show(Classroom $classroom): View
    {
        $classroom->load(['students' => fn ($q) => $q->active()->orderBy('last_name'), 'schoolYear']);

        return view('classrooms.show', compact('classroom'));
    }

    public function edit(Classroom $classroom): View
    {
        $schoolYears = SchoolYear::orderByDesc('name')->get();

        return view('classrooms.edit', compact('classroom', 'schoolYears'));
    }

    public function update(Request $request, Classroom $classroom): RedirectResponse
    {
        $data = $request->validate([
            'level'          => 'required|string|max:30',
            'section'        => 'nullable|string|max:10',
            'cycle'          => 'required|in:collège,lycée',
            'max_students'   => 'required|integer|min:1|max:200',
            'school_year_id' => 'required|exists:school_years,id',
            'main_teacher'   => 'nullable|string|max:100',
        ]);

        $data['name'] = trim($data['level'] . ' ' . $data['section']);
        $classroom->update($data);

        return redirect()->route('classrooms.show', $classroom)
            ->with('success', 'Classe mise à jour.');
    }

    public function destroy(Classroom $classroom): RedirectResponse
    {
        if ($classroom->students()->exists()) {
            return back()->with('error', 'Impossible de supprimer une classe contenant des élèves.');
        }

        $classroom->delete();

        return redirect()->route('classrooms.index')
            ->with('success', 'Classe supprimée.');
    }
}
