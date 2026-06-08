<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        .page { padding: 24px; }

        .header { border-bottom: 3px solid #4f46e5; padding-bottom: 12px; margin-bottom: 16px; }
        .school-name { font-size: 16px; font-weight: bold; color: #4f46e5; }
        .school-info { font-size: 9px; color: #6b7280; margin-top: 2px; }

        .doc-title { text-align: center; margin: 14px 0; }
        .doc-title h2 { font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #374151; }
        .doc-title .period { font-size: 10px; color: #6b7280; margin-top: 3px; }

        .employee-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 12px; margin-bottom: 14px; }
        .employee-box table { width: 100%; }
        .employee-box td { padding: 2px 0; font-size: 10px; }
        .employee-box .lbl { color: #6b7280; width: 110px; }
        .employee-box .val { font-weight: bold; color: #111827; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.data th { background: #4f46e5; color: white; font-size: 9px; text-transform: uppercase; padding: 6px 8px; text-align: left; }
        table.data th.r { text-align: right; }
        table.data th.c { text-align: center; }
        table.data td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        table.data td.r { text-align: right; }
        table.data td.c { text-align: center; }
        table.data tr:nth-child(even) { background: #f9fafb; }

        .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 8px; font-weight: bold; }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef3c7; color: #92400e; }

        .totals { margin-top: 4px; }
        .totals table { width: 50%; margin-left: auto; border-collapse: collapse; }
        .totals td { padding: 5px 10px; font-size: 11px; }
        .totals .lbl { color: #6b7280; }
        .totals .val { text-align: right; font-weight: bold; }
        .totals .grand { border-top: 2px solid #4f46e5; }
        .totals .grand td { font-size: 13px; color: #1e1b4b; padding-top: 8px; }

        .footer { margin-top: 24px; padding-top: 8px; border-top: 1px dashed #d1d5db; text-align: center; font-size: 8px; color: #9ca3af; }
        .sig { margin-top: 30px; display: table; width: 100%; }
        .sig-cell { display: table-cell; width: 50%; text-align: center; font-size: 9px; color: #6b7280; }
        .sig-line { border-top: 1px solid #9ca3af; margin: 28px 20px 4px; }
    </style>
</head>
<body>
<div class="page">

    {{-- En-tête établissement --}}
    <div class="header">
        <div class="school-name">{{ $school['name'] }}</div>
        <div class="school-info">
            {{ $school['address'] }}
            @if($school['phone']) · Tél : {{ $school['phone'] }} @endif
            @if($school['email']) · {{ $school['email'] }} @endif
        </div>
    </div>

    {{-- Titre --}}
    <div class="doc-title">
        <h2>Historique des Paiements</h2>
        @if($period)
        <div class="period">Période : {{ $period }}</div>
        @endif
    </div>

    {{-- Infos employé --}}
    <div class="employee-box">
        <table>
            <tr>
                <td class="lbl">Nom complet</td>
                <td class="val">{{ $employee->full_name }}</td>
                <td class="lbl">Matricule</td>
                <td class="val">{{ $employee->employee_number }}</td>
            </tr>
            <tr>
                <td class="lbl">Fonction</td>
                <td class="val">{{ $employee->subject ?? $employee->position ?? '—' }}</td>
                <td class="lbl">Type de contrat</td>
                <td class="val">{{ ucfirst($employee->contract_type) }}</td>
            </tr>
        </table>
    </div>

    {{-- Tableau des paies --}}
    <table class="data">
        <thead>
            <tr>
                <th>Période</th>
                <th>N° Fiche</th>
                <th class="r">Salaire base</th>
                <th class="r">Primes</th>
                <th class="r">Retenues</th>
                <th class="r">Net payé</th>
                <th class="c">Statut</th>
                <th class="c">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payrolls as $payroll)
            <tr>
                <td>{{ $months[$payroll->month] ?? $payroll->month }} {{ $payroll->year }}</td>
                <td>{{ $payroll->payroll_number }}</td>
                <td class="r">{{ number_format($payroll->base_salary, 0, ',', ' ') }}</td>
                <td class="r">{{ $payroll->bonuses > 0 ? number_format($payroll->bonuses, 0, ',', ' ') : '—' }}</td>
                <td class="r">{{ $payroll->deductions > 0 ? number_format($payroll->deductions, 0, ',', ' ') : '—' }}</td>
                <td class="r"><strong>{{ number_format($payroll->net_salary, 0, ',', ' ') }}</strong></td>
                <td class="c">
                    @if($payroll->status === 'paid')
                        <span class="badge badge-paid">Payé</span>
                    @else
                        <span class="badge badge-pending">En attente</span>
                    @endif
                </td>
                <td class="c">{{ $payroll->payment_date ? $payroll->payment_date->format('d/m/Y') : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center; padding:20px; color:#9ca3af;">Aucune fiche de paie pour cette période</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Totaux --}}
    @if($payrolls->count() > 0)
    <div class="totals">
        <table>
            <tr>
                <td class="lbl">Nombre de fiches</td>
                <td class="val">{{ $payrolls->count() }}</td>
            </tr>
            <tr>
                <td class="lbl">Total payé</td>
                <td class="val" style="color:#065f46;">{{ number_format($totalPaid, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr class="grand">
                <td class="lbl"><strong>Montant total net</strong></td>
                <td class="val">{{ number_format($totalNet, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>
    @endif

    {{-- Signatures --}}
    <div class="sig">
        <div class="sig-cell">
            <div class="sig-line"></div>
            Le comptable
        </div>
        <div class="sig-cell">
            <div class="sig-line"></div>
            La direction
        </div>
    </div>

    <div class="footer">
        Document généré le {{ $date }} · {{ $school['name'] }}
    </div>

</div>
</body>
</html>
