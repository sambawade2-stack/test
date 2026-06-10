<?php

use App\Http\Controllers\CertificateController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

// ─── Authentification ─────────────────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ─── Routes protégées ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'active'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ─── Années scolaires ─────────────────────────────────────────────────────
    Route::resource('school-years', SchoolYearController::class);
    Route::post('school-years/{schoolYear}/activate', [SchoolYearController::class, 'activate'])
        ->name('school-years.activate');
    Route::post('school-years/{schoolYear}/close', [SchoolYearController::class, 'close'])
        ->name('school-years.close');

    // ─── Classes ──────────────────────────────────────────────────────────────
    Route::resource('classrooms', ClassroomController::class);
    Route::get('classrooms/{classroom}/badges', [ClassroomController::class, 'badges'])
        ->name('classrooms.badges');

    // ─── Tableau de bord financier ────────────────────────────────────────────
    Route::get('finances', [\App\Http\Controllers\FinanceController::class, 'index'])->name('finance.index');
    Route::get('finances/export/excel', [\App\Http\Controllers\FinanceController::class, 'exportExcel'])->name('finance.export.excel');
    Route::get('finances/export/pdf', [\App\Http\Controllers\FinanceController::class, 'exportPdf'])->name('finance.export.pdf');

    // ─── Présences / Assiduité ────────────────────────────────────────────────
    Route::get('presences', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('presences', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('presences/eleve/{student}', [\App\Http\Controllers\AttendanceController::class, 'student'])->name('attendance.student');

    // ─── Scan de badge (vérification "en règle") ──────────────────────────────
    // Réservé : surveillant, caissier, comptable, directeur, admin
    Route::middleware('role:admin,directeur,comptable,surveillant,caissier')->group(function () {
        Route::get('scan', [\App\Http\Controllers\ScanController::class, 'index'])->name('scan.index');
        Route::post('scan/verify', [\App\Http\Controllers\ScanController::class, 'verify'])->name('scan.verify');
        Route::get('scan/historique', [\App\Http\Controllers\ScanController::class, 'history'])->name('scan.history');
    });

    // ─── Élèves ───────────────────────────────────────────────────────────────
    Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
    Route::resource('students', StudentController::class);
    Route::get('students/{student}/badge', [StudentController::class, 'badge'])
        ->name('students.badge');
    Route::get('students/{student}/certificat-scolarite', [CertificateController::class, 'scolarite'])
        ->name('students.certificate.scolarite');
    Route::get('students/{student}/certificat-assiduite', [CertificateController::class, 'assiduite'])
        ->name('students.certificate.assiduite');

    // ─── Enseignants ──────────────────────────────────────────────────────────
    Route::resource('teachers', TeacherController::class);
    Route::get('teachers/{teacher}/historique-paie', [TeacherController::class, 'payrollHistory'])->name('teachers.payroll-history');
    Route::get('teachers/{teacher}/historique-paie/pdf', [TeacherController::class, 'payrollHistoryPdf'])->name('teachers.payroll-history.pdf');

    // ─── Personnel administratif & d'appoint ──────────────────────────────────
    Route::resource('staff', StaffController::class);
    Route::get('staff/{staff}/historique-paie', [StaffController::class, 'payrollHistory'])->name('staff.payroll-history');
    Route::get('staff/{staff}/historique-paie/pdf', [StaffController::class, 'payrollHistoryPdf'])->name('staff.payroll-history.pdf');

    // ─── Paiements élèves ─────────────────────────────────────────────────────
    Route::get('payments/export', [PaymentController::class, 'export'])->name('payments.export');
    Route::resource('payments', PaymentController::class)->except(['destroy']);
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])
        ->name('payments.receipt');
    Route::post('payments/{payment}/complement', [PaymentController::class, 'complement'])
        ->name('payments.complement');
    Route::get('unpaid', [PaymentController::class, 'unpaid'])->name('payments.unpaid');
    Route::get('unpaid/report', [PaymentController::class, 'unpaidReport'])->name('payments.unpaid.report');

    // ─── Paie du personnel ────────────────────────────────────────────────────
    Route::resource('payrolls', PayrollController::class)->except(['edit', 'update', 'destroy']);
    Route::post('payrolls/{payroll}/mark-paid', [PayrollController::class, 'markPaid'])
        ->name('payrolls.mark-paid');
    Route::get('payrolls/{payroll}/payslip', [PayrollController::class, 'payslip'])
        ->name('payrolls.payslip');
    Route::post('payrolls/bulk-create', [PayrollController::class, 'bulkCreate'])
        ->name('payrolls.bulk-create');

    // ─── Utilisateurs / Profils ───────────────────────────────────────────────
    Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show']);
    Route::post('users/{user}/toggle-active', [\App\Http\Controllers\UserController::class, 'toggleActive'])
        ->name('users.toggle-active');

    // ─── Paramètres ───────────────────────────────────────────────────────────
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::put('settings/fees', [SettingsController::class, 'updateFees'])->name('settings.update-fees');
});
