<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class AttendanceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:' . User::rolesFor('attendance'))];
    }

    /**
     * Feuille d'appel : sélection classe + date, puis saisie des présences.
     */
    public function index(Request $request): View
    {
        $classrooms = Classroom::when(SchoolYear::current(), fn ($q, $y) =>
                $q->where('school_year_id', SchoolYear::current()->id))
            ->orderBy('level')->orderBy('section')->get();

        $classroomId = $request->integer('classroom_id') ?: $classrooms->first()?->id;
        $date        = $request->date('date')?->format('Y-m-d') ?? now()->format('Y-m-d');

        $students = collect();
        $existing = collect();

        if ($classroomId) {
            $students = Student::active()
                ->where('classroom_id', $classroomId)
                ->orderBy('last_name')->orderBy('first_name')
                ->get();

            $existing = Attendance::where('classroom_id', $classroomId)
                ->whereDate('date', $date)
                ->get()
                ->keyBy('student_id');
        }

        return view('attendance.index', compact('classrooms', 'classroomId', 'date', 'students', 'existing'));
    }

    /**
     * Enregistre la feuille d'appel (upsert par élève/date).
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'date'         => 'required|date',
            'period'       => 'nullable|string|max:20',
            'status'       => 'required|array',
            'status.*'     => 'required|in:present,absent,late,excused',
            'reason'       => 'nullable|array',
        ]);

        $period = $data['period'] ?? 'journée';
        $count  = 0;

        foreach ($data['status'] as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date'       => $data['date'],
                    'period'     => $period,
                ],
                [
                    'classroom_id' => $data['classroom_id'],
                    'status'       => $status,
                    'reason'       => $data['reason'][$studentId] ?? null,
                    'recorded_by'  => auth()->id(),
                ]
            );
            $count++;
        }

        return redirect()
            ->route('attendance.index', ['classroom_id' => $data['classroom_id'], 'date' => $data['date']])
            ->with('success', "Appel enregistré pour {$count} élève(s).");
    }

    /**
     * Historique des présences d'un élève (résumé mensuel).
     */
    public function student(Request $request, Student $student): View
    {
        $month = $request->integer('month', now()->month);
        $year  = $request->integer('year', now()->year);

        $records = Attendance::where('student_id', $student->id)
            ->forMonth($month, $year)
            ->orderBy('date', 'desc')
            ->get();

        $stats = [
            'present' => $records->where('status', 'present')->count(),
            'absent'  => $records->where('status', 'absent')->count(),
            'late'    => $records->where('status', 'late')->count(),
            'excused' => $records->where('status', 'excused')->count(),
            'total'   => $records->count(),
        ];
        $stats['rate'] = $stats['total'] > 0
            ? round((($stats['total'] - $stats['absent']) / $stats['total']) * 100, 1)
            : 100;

        return view('attendance.student', compact('student', 'records', 'stats', 'month', 'year'));
    }
}
