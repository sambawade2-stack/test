<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Student;
use App\Models\Payment;
use App\Models\SchoolYear;

/*
|--------------------------------------------------------------------------
| API Routes — base pour extension mobile future
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Auth utilisateur courant
    Route::get('/user', fn (Request $request) => $request->user());

    // ─── Statistiques dashboard ───────────────────────────────────────────────
    Route::get('/stats', function () {
        $now  = now();
        $year = SchoolYear::current();

        return response()->json([
            'students_total'   => Student::active()->count(),
            'unpaid_this_month'=> Student::active()
                ->withUnpaidMonthly($now->month, $now->year)
                ->count(),
            'collected_this_month' => Payment::whereMonth('payment_date', $now->month)
                ->whereYear('payment_date', $now->year)
                ->sum('amount_paid'),
            'school_year' => $year?->name,
        ]);
    });

    // ─── Élèves ───────────────────────────────────────────────────────────────
    Route::get('/students', function (Request $request) {
        $students = Student::active()
            ->with(['classroom'])
            ->when($request->classroom_id, fn ($q) => $q->where('classroom_id', $request->classroom_id))
            ->when($request->search, fn ($q, $s) =>
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('registration_number', 'like', "%$s%")
            )
            ->orderBy('last_name')
            ->paginate(50);

        return response()->json($students);
    });

    Route::get('/students/{student}/payments', function (Student $student) {
        return response()->json($student->payments()->latest()->get());
    });

    // ─── Impayés ─────────────────────────────────────────────────────────────
    Route::get('/unpaid', function (Request $request) {
        $month    = $request->integer('month', now()->month);
        $year     = $request->integer('year', now()->year);
        $students = Student::active()
            ->withUnpaidMonthly($month, $year)
            ->with('classroom')
            ->get(['id', 'registration_number', 'first_name', 'last_name', 'classroom_id', 'parent_phone']);

        return response()->json([
            'month'   => $month,
            'year'    => $year,
            'total'   => $students->count(),
            'students'=> $students,
        ]);
    });
});
