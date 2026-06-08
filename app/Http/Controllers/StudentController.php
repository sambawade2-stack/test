<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Models\Classroom;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        // Consultation (liste, fiche, badge) ouverte à tous ; gestion réservée.
        return [
            new Middleware('role:' . User::rolesFor('students_manage'),
                except: ['index', 'show', 'badge', 'export']),
        ];
    }

    public function index(Request $request): View
    {
        $query = Student::with(['classroom', 'schoolYear'])
            ->active();

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        if ($request->filled('school_year_id')) {
            $query->where('school_year_id', $request->school_year_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('registration_number', 'like', "%$search%");
            });
        }

        $students    = $query->orderBy('last_name')->paginate(20)->withQueryString();
        $classrooms  = Classroom::orderBy('name')->get();
        $schoolYears = SchoolYear::orderByDesc('name')->get();

        return view('students.index', compact('students', 'classrooms', 'schoolYears'));
    }

    public function create(): View
    {
        $classrooms  = Classroom::orderBy('name')->get();
        $schoolYears = SchoolYear::orderByDesc('name')->get();
        $currentYear = SchoolYear::current();

        return view('students.create', compact('classrooms', 'schoolYears', 'currentYear'));
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        // Matricule basé sur l'année d'inscription (CODE-ANNÉE-SÉQUENCE)
        $enrollYear = (int) date('Y', strtotime($data['enrolled_at'] ?? 'now'));
        $data['registration_number'] = Student::generateRegistrationNumber($enrollYear);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        Student::create($data);

        return redirect()->route('students.index')
            ->with('success', 'Élève ajouté avec succès.');
    }

    public function show(Student $student): View
    {
        $student->load(['classroom', 'schoolYear', 'payments' => fn ($q) => $q->orderByDesc('payment_date')]);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $classrooms  = Classroom::orderBy('name')->get();
        $schoolYears = SchoolYear::orderByDesc('name')->get();

        return view('students.edit', compact('student', 'classrooms', 'schoolYears'));
    }

    public function update(StoreStudentRequest $request, Student $student): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $data['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $student->update($data);

        return redirect()->route('students.show', $student)
            ->with('success', 'Élève mis à jour avec succès.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Élève supprimé.');
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\StudentsExport($request->only(['classroom_id', 'school_year_id', 'search'])),
            'eleves-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function badge(Request $request, Student $student): Response|View
    {
        $pdfService = app(PdfService::class);

        // Téléchargement direct du PDF
        if ($request->boolean('download')) {
            return $pdfService->generateStudentBadge($student);
        }

        // Prévisualisation à l'écran
        $student->loadMissing(['classroom', 'schoolYear']);

        return view('students.badge', [
            'student' => $student,
            'qr'      => $pdfService->qrDataUri($student->qrPayload()),
            'school'  => $pdfService->schoolInfo(),
        ]);
    }
}
