<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        .page { padding: 30px 40px; }

        .header { display: table; width: 100%; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; margin-bottom: 16px; }
        .header-left { display: table-cell; width: 60%; }
        .header-right { display: table-cell; width: 40%; text-align: right; }
        .school-name { font-size: 15px; font-weight: bold; color: #4f46e5; }
        .school-info { font-size: 10px; color: #6b7280; margin-top: 2px; }

        .slip-title { text-align: center; margin: 14px 0; }
        .slip-title h2 { font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .slip-period { font-size: 12px; color: #4f46e5; font-weight: bold; margin-top: 2px; }

        .employee-box { background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 4px; padding: 10px; margin-bottom: 14px; display: table; width: 100%; }
        .emp-col { display: table-cell; width: 50%; padding: 3px 6px; }
        .emp-label { font-size: 10px; color: #6b7280; }
        .emp-value { font-size: 11px; font-weight: bold; color: #111827; }

        table.salary { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.salary th { background: #4f46e5; color: white; text-align: left; padding: 7px 10px; font-size: 11px; }
        table.salary td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; }
        table.salary tr.total-row td { font-weight: bold; background: #eef2ff; border-top: 2px solid #4f46e5; font-size: 13px; }
        table.salary .plus { color: #059669; }
        table.salary .minus { color: #dc2626; }

        .net-box { border: 2px solid #4f46e5; border-radius: 6px; padding: 12px; text-align: center; background: #eef2ff; }
        .net-label { font-size: 11px; color: #4f46e5; font-weight: bold; text-transform: uppercase; }
        .net-amount { font-size: 22px; font-weight: bold; color: #1e1b4b; margin-top: 4px; }

        .status-badge { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 10px; font-weight: bold; margin-top: 8px; }
        .paid { background: #d1fae5; color: #065f46; }
        .pending { background: #fef3c7; color: #92400e; }

        .signature-area { margin-top: 30px; display: table; width: 100%; }
        .sig-cell { display: table-cell; width: 50%; text-align: center; }
        .sig-line { border-top: 1px solid #9ca3af; padding-top: 4px; font-size: 10px; color: #6b7280; width: 140px; margin: auto; }
        .sig-spacer { margin-bottom: 40px; }

        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #9ca3af; border-top: 1px dashed #d1d5db; padding-top: 8px; }
    </style>
</head>
<body>
<div class="page">

    <div class="header">
        <div class="header-left">
            <div class="school-name">{{ $school['name'] }}</div>
            <div class="school-info">{{ $school['address'] }}</div>
            @if($school['phone'])<div class="school-info">Tél : {{ $school['phone'] }}</div>@endif
        </div>
        <div class="header-right">
            <div style="font-size: 11px; color: #6b7280;">N° {{ $payroll->payroll_number }}</div>
        </div>
    </div>

    <div class="slip-title">
        <h2>Bulletin de Salaire</h2>
        <div class="slip-period">{{ $payroll->month_label }} {{ $payroll->year }}</div>
    </div>

    {{-- Infos employé --}}
    @php $emp = $payroll->payable; @endphp
    <div class="employee-box">
        <div class="emp-col">
            <div class="emp-label">Employé(e)</div>
            <div class="emp-value">{{ $emp->full_name }}</div>
        </div>
        <div class="emp-col">
            <div class="emp-label">N° Matricule</div>
            <div class="emp-value">{{ $emp->employee_number }}</div>
        </div>
        <div class="emp-col" style="padding-top: 6px;">
            <div class="emp-label">Poste / Matière</div>
            <div class="emp-value">{{ $emp->subject ?? $emp->position ?? '—' }}</div>
        </div>
        <div class="emp-col" style="padding-top: 6px;">
            <div class="emp-label">Type de contrat</div>
            <div class="emp-value">{{ ucfirst($emp->contract_type) }}</div>
        </div>
    </div>

    {{-- Tableau des éléments de paie --}}
    <table class="salary">
        <thead>
            <tr>
                <th>Désignation</th>
                <th style="text-align: right;">Montant (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Salaire de base</td>
                <td style="text-align: right;">{{ number_format($payroll->base_salary, 0, ',', ' ') }}</td>
            </tr>
            @if($payroll->bonuses > 0)
            <tr>
                <td class="plus">+ Primes / Indemnités</td>
                <td style="text-align: right;" class="plus">+ {{ number_format($payroll->bonuses, 0, ',', ' ') }}</td>
            </tr>
            @endif
            @if($payroll->deductions > 0)
            <tr>
                <td class="minus">- Retenues / Déductions</td>
                <td style="text-align: right;" class="minus">- {{ number_format($payroll->deductions, 0, ',', ' ') }}</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>SALAIRE NET À PAYER</td>
                <td style="text-align: right;">{{ number_format($payroll->net_salary, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tfoot>
    </table>

    <div class="net-box">
        <div class="net-label">Net à payer</div>
        <div class="net-amount">{{ number_format($payroll->net_salary, 0, ',', ' ') }} FCFA</div>
        @if($payroll->status === 'paid')
            <div><span class="status-badge paid">✓ PAYÉ le {{ $payroll->payment_date?->format('d/m/Y') }}</span></div>
        @else
            <div><span class="status-badge pending">⏳ EN ATTENTE</span></div>
        @endif
    </div>

    {{-- Signatures --}}
    <div class="signature-area">
        <div class="sig-cell">
            <div class="sig-spacer"></div>
            <div class="sig-line">L'employé(e)</div>
        </div>
        <div class="sig-cell">
            <div class="sig-spacer"></div>
            <div class="sig-line">La Direction</div>
        </div>
    </div>

    <div class="footer">
        Fiche générée le {{ now()->format('d/m/Y à H:i') }}
        @if($payroll->creator) · Par {{ $payroll->creator->name }} @endif
        · {{ $school['name'] }}
    </div>

</div>
</body>
</html>
