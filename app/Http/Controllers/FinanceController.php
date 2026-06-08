<?php

namespace App\Http\Controllers;

use App\Exports\FinanceReportExport;
use App\Models\Payment;
use App\Models\Payroll;
use App\Models\User;
use App\Services\PdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FinanceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:' . User::rolesFor('finance'))];
    }

    public function index(): View
    {
        return view('finance.index', $this->computeData());
    }

    public function exportExcel(): BinaryFileResponse
    {
        $data = $this->computeData();
        $data['school'] = app(PdfService::class)->schoolInfo()['name'];

        return Excel::download(
            new FinanceReportExport($data),
            'rapport-financier-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(): Response
    {
        $data = $this->computeData();
        $data['school'] = app(PdfService::class)->schoolInfo();
        $data['date']   = now()->locale('fr')->isoFormat('D MMMM YYYY');

        $pdf = Pdf::loadView('pdf.finance_report', $data)->setPaper('a4', 'portrait');

        return $pdf->download('rapport-financier-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Calcul unique des données du rapport (réutilisé par l'affichage et les exports).
     */
    private function computeData(): array
    {
        $now = now();

        $raw = Payment::selectRaw("DATE_FORMAT(payment_date, '%Y-%m') as ym, SUM(amount_paid) as total")
            ->where('payment_date', '>=', $now->copy()->subMonths(11)->startOfMonth())
            ->groupBy('ym')->pluck('total', 'ym');

        $monthsFr = ['01'=>'Jan','02'=>'Fév','03'=>'Mar','04'=>'Avr','05'=>'Mai','06'=>'Juin','07'=>'Juil','08'=>'Aoû','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Déc'];
        $chartLabels = [];
        $chartData   = [];
        for ($i = 11; $i >= 0; $i--) {
            $d  = $now->copy()->subMonths($i);
            $ym = $d->format('Y-m');
            $chartLabels[] = $monthsFr[$d->format('m')] . ' ' . $d->format('y');
            $chartData[]   = (float) ($raw[$ym] ?? 0);
        }

        $byType = Payment::selectRaw('payment_type, SUM(amount_paid) as total')
            ->groupBy('payment_type')->pluck('total', 'payment_type');

        $byMethod = Payment::selectRaw('payment_method, SUM(amount_paid) as total')
            ->groupBy('payment_method')->pluck('total', 'payment_method');

        $byClass = Payment::selectRaw('classrooms.name as cname, SUM(payments.amount_paid) as total')
            ->join('classrooms', 'classrooms.id', '=', 'payments.classroom_id')
            ->groupBy('classrooms.name')
            ->orderByDesc('total')
            ->limit(8)->get();

        $stats = [
            'this_month'   => (float) Payment::whereYear('payment_date', $now->year)->whereMonth('payment_date', $now->month)->sum('amount_paid'),
            'this_year'    => (float) Payment::whereYear('payment_date', $now->year)->sum('amount_paid'),
            'outstanding'  => (float) Payment::sum('balance'),
            'total_all'    => (float) Payment::sum('amount_paid'),
            'payroll_month'=> (float) Payroll::forMonth($now->month, $now->year)->paid()->sum('net_salary'),
        ];
        $stats['net_month'] = $stats['this_month'] - $stats['payroll_month'];

        $methodLabels = ['cash'=>'Espèces','virement'=>'Virement','mobile_money'=>'Mobile Money','cheque'=>'Chèque'];

        return compact('chartLabels', 'chartData', 'byType', 'byMethod', 'byClass', 'stats', 'methodLabels');
    }
}
