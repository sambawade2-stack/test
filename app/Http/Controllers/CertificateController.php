<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CertificateController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:' . User::rolesFor('certificates'))];
    }

    public function __construct(private PdfService $pdfService) {}

    public function scolarite(Student $student): Response
    {
        return $this->pdfService->generateScolariteCertificate($student);
    }

    public function assiduite(Request $request, Student $student): Response
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year'  => 'required|integer|min:2000',
        ]);

        return $this->pdfService->generateAssiduiteCertificate(
            $student,
            $request->integer('month'),
            $request->integer('year')
        );
    }
}
