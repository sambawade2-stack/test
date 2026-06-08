<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(private array $filters = []) {}

    public function query()
    {
        $query = Payment::query()->with(['student', 'classroom', 'schoolYear'])->latest('payment_date');

        $f = $this->filters;
        if (!empty($f['payment_type'])) $query->where('payment_type', $f['payment_type']);
        if (!empty($f['status']))       $query->where('status', $f['status']);
        if (!empty($f['classroom_id'])) $query->where('classroom_id', $f['classroom_id']);
        if (!empty($f['month']) && !empty($f['year'])) {
            $query->where('month', $f['month'])->where('year', $f['year']);
        }
        if (!empty($f['search'])) {
            $s = $f['search'];
            $query->whereHas('student', fn ($q) =>
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('registration_number', 'like', "%$s%"));
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Reçu N°', 'Matricule', 'Élève', 'Classe', 'Type', 'Période',
                'Montant dû', 'Montant payé', 'Solde', 'Statut', 'Mode', 'Date'];
    }

    public function map($p): array
    {
        $months = [1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'];
        $methods = ['cash'=>'Espèces','virement'=>'Virement','mobile_money'=>'Mobile Money','cheque'=>'Chèque'];
        $statuses = ['paid'=>'Payé','partial'=>'Partiel','unpaid'=>'Impayé'];

        return [
            $p->receipt_number,
            $p->student?->registration_number,
            $p->student?->full_name,
            $p->classroom?->name,
            $p->payment_type === 'inscription' ? 'Inscription' : 'Mensualité',
            $p->payment_type === 'mensualite' ? ($months[$p->month] ?? '') . ' ' . $p->year : ($p->schoolYear?->name ?? ''),
            (float) $p->amount_due,
            (float) $p->amount_paid,
            (float) $p->balance,
            $statuses[$p->status] ?? $p->status,
            $methods[$p->payment_method] ?? $p->payment_method,
            $p->payment_date?->format('d/m/Y'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '#,##0',
            'H' => '#,##0',
            'I' => '#,##0',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']]],
        ];
    }

    public function title(): string
    {
        return 'Paiements';
    }
}
