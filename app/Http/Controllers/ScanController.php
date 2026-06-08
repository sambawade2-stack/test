<?php

namespace App\Http\Controllers;

use App\Models\ScanLog;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function index(): View
    {
        return view('scan.index');
    }

    public function history(Request $request): View
    {
        $query = ScanLog::with(['student', 'scanner'])->latest('scanned_at');

        if ($request->filled('filter') && $request->filter === 'denied') {
            $query->where('in_order', false);
        }
        if ($request->filled('date')) {
            $query->whereDate('scanned_at', $request->date);
        }

        $logs = $query->paginate(30)->withQueryString();

        $todayTotal   = ScanLog::today()->count();
        $todayDenied  = ScanLog::today()->where('in_order', false)->count();

        return view('scan.history', compact('logs', 'todayTotal', 'todayDenied'));
    }

    /**
     * Vérifie un élève à partir du contenu scanné (ou matricule saisi).
     * Retourne le statut "en règle" en JSON.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string|max:500']);

        $matricule = $this->extractMatricule($request->code);

        if (!$matricule) {
            return response()->json([
                'found'   => false,
                'message' => 'Code illisible ou matricule introuvable dans le QR.',
            ], 200);
        }

        $student = Student::with(['classroom', 'schoolYear'])
            ->where('registration_number', $matricule)
            ->first();

        if (!$student) {
            ScanLog::create([
                'scanned_by' => $request->user()->id,
                'matricule'  => $matricule,
                'found'      => false,
            ]);

            return response()->json([
                'found'     => false,
                'matricule' => $matricule,
                'message'   => "Aucun élève avec le matricule {$matricule}.",
            ], 200);
        }

        $standing = $student->standing();

        // Journalise le passage
        ScanLog::create([
            'student_id'       => $student->id,
            'scanned_by'       => $request->user()->id,
            'matricule'        => $student->registration_number,
            'found'            => true,
            'in_order'         => $standing['in_order'],
            'inscription_paid' => $standing['inscription_paid'],
            'month_paid'       => $standing['month_paid'],
            'balance'          => $standing['balance'],
            'classroom'        => $student->classroom?->name,
        ]);

        return response()->json([
            'found'    => true,
            'standing' => $standing,
            'student'  => [
                'id'           => $student->id,
                'name'         => $student->full_name,
                'matricule'    => $student->registration_number,
                'classroom'    => $student->classroom?->name,
                'school_year'  => $student->schoolYear?->name,
                'photo'        => $student->photo ? asset('storage/'.$student->photo) : null,
                'parent_name'  => $student->parent_name,
                'parent_phone' => $student->parent_phone,
                'is_active'    => $student->is_active,
                'profile_url'  => route('students.show', $student),
            ],
        ]);
    }

    /**
     * Extrait le matricule du contenu scanné.
     * Gère : QR complet ("MATRICULE: STU-...") ou matricule brut saisi à la main.
     */
    private function extractMatricule(string $code): ?string
    {
        $code = trim($code);

        // Cas QR complet : ligne "MATRICULE: STU-2026-00001"
        if (preg_match('/MATRICULE\s*:\s*([A-Z0-9\-]+)/i', $code, $m)) {
            return strtoupper(trim($m[1]));
        }

        // Cas saisie manuelle : une seule ligne, sans espace → on l'utilise telle quelle
        if (!str_contains($code, "\n") && !str_contains($code, ' ') && strlen($code) >= 3 && strlen($code) <= 40) {
            return strtoupper($code);
        }

        return null;
    }
}
