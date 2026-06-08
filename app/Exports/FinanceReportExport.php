<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinanceReportExport implements FromArray, WithStyles, WithTitle, WithColumnWidths
{
    public function __construct(private array $data) {}

    public function array(): array
    {
        $d = $this->data;
        $rows = [];

        $rows[] = ['RAPPORT FINANCIER', '', ''];
        $rows[] = ['Établissement', $d['school'], ''];
        $rows[] = ['Généré le', now()->format('d/m/Y H:i'), ''];
        $rows[] = ['', '', ''];

        // Indicateurs clés
        $rows[] = ['INDICATEURS', 'Montant (FCFA)', ''];
        $rows[] = ['Encaissé ce mois', $d['stats']['this_month'], ''];
        $rows[] = ['Encaissé cette année', $d['stats']['this_year'], ''];
        $rows[] = ['Impayés (solde dû)', $d['stats']['outstanding'], ''];
        $rows[] = ['Salaires payés ce mois', $d['stats']['payroll_month'], ''];
        $rows[] = ['Net ce mois', $d['stats']['net_month'], ''];
        $rows[] = ['', '', ''];

        // Recettes mensuelles
        $rows[] = ['RECETTES MENSUELLES (12 mois)', 'Montant (FCFA)', ''];
        foreach ($d['chartLabels'] as $i => $label) {
            $rows[] = [$label, $d['chartData'][$i] ?? 0, ''];
        }
        $rows[] = ['', '', ''];

        // Par type
        $rows[] = ['PAR TYPE', 'Montant (FCFA)', ''];
        $rows[] = ['Inscription', $d['byType']['inscription'] ?? 0, ''];
        $rows[] = ['Mensualité', $d['byType']['mensualite'] ?? 0, ''];
        $rows[] = ['', '', ''];

        // Par mode de paiement
        $rows[] = ['PAR MODE DE PAIEMENT', 'Montant (FCFA)', ''];
        $methods = ['cash'=>'Espèces','virement'=>'Virement','mobile_money'=>'Mobile Money','cheque'=>'Chèque'];
        foreach ($d['byMethod'] as $m => $total) {
            $rows[] = [$methods[$m] ?? $m, $total, ''];
        }
        $rows[] = ['', '', ''];

        // Par classe
        $rows[] = ['RECETTES PAR CLASSE', 'Montant (FCFA)', ''];
        foreach ($d['byClass'] as $row) {
            $rows[] = [$row->cname, (float) $row->total, ''];
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return ['A' => 38, 'B' => 22, 'C' => 5];
    }

    public function styles(Worksheet $sheet): array
    {
        // Titre principal
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Met en gras toutes les lignes d'en-tête de section (texte en MAJUSCULES col A, col B vide ou "Montant")
        $highestRow = $sheet->getHighestRow();
        for ($r = 5; $r <= $highestRow; $r++) {
            $a = (string) $sheet->getCell("A{$r}")->getValue();
            $b = (string) $sheet->getCell("B{$r}")->getValue();
            if ($a !== '' && ($b === 'Montant (FCFA)' || mb_strtoupper($a) === $a) && preg_match('/[A-ZÀ-Ÿ]/u', $a)) {
                $sheet->getStyle("A{$r}:C{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']],
                ]);
            }
            // Format nombre pour la colonne B numérique
            if (is_numeric($b)) {
                $sheet->getStyle("B{$r}")->getNumberFormat()->setFormatCode('#,##0');
            }
        }

        return [];
    }

    public function title(): string
    {
        return 'Rapport financier';
    }
}
