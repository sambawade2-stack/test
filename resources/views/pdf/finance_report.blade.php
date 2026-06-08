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
        .doc-title { text-align: center; margin: 12px 0 18px; }
        .doc-title h2 { font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #374151; }
        .doc-title .date { font-size: 9px; color: #6b7280; margin-top: 3px; }

        .kpi { display: table; width: 100%; margin-bottom: 18px; border-collapse: separate; border-spacing: 6px; }
        .kpi-row { display: table-row; }
        .kpi-cell { display: table-cell; width: 25%; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px; text-align: center; }
        .kpi-cell .lbl { font-size: 7px; color: #6b7280; text-transform: uppercase; }
        .kpi-cell .val { font-size: 13px; font-weight: bold; color: #111827; margin-top: 3px; }

        .section-title { font-size: 11px; font-weight: bold; color: #4f46e5; margin: 16px 0 6px; padding-bottom: 3px; border-bottom: 1px solid #e5e7eb; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #4f46e5; color: white; font-size: 8px; text-transform: uppercase; padding: 5px 8px; text-align: left; }
        table.data th.r { text-align: right; }
        table.data td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        table.data td.r { text-align: right; }
        table.data tr:nth-child(even) { background: #f9fafb; }
        .footer { margin-top: 24px; padding-top: 8px; border-top: 1px dashed #d1d5db; text-align: center; font-size: 8px; color: #9ca3af; }
    </style>
</head>
<body>
@php
    $fmt = fn ($v) => number_format((float) $v, 0, ',', ' ');
@endphp
<div class="page">

    <div class="header">
        <div class="school-name">{{ $school['name'] }}</div>
        <div class="school-info">
            {{ $school['address'] }}
            @if($school['phone']) · Tél : {{ $school['phone'] }} @endif
        </div>
    </div>

    <div class="doc-title">
        <h2>Rapport Financier</h2>
        <div class="date">Généré le {{ $date }}</div>
    </div>

    {{-- Indicateurs --}}
    <div class="kpi">
        <div class="kpi-row">
            <div class="kpi-cell"><div class="lbl">Encaissé ce mois</div><div class="val">{{ $fmt($stats['this_month']) }}</div></div>
            <div class="kpi-cell"><div class="lbl">Encaissé cette année</div><div class="val">{{ $fmt($stats['this_year']) }}</div></div>
            <div class="kpi-cell"><div class="lbl">Impayés (solde)</div><div class="val" style="color:#dc2626;">{{ $fmt($stats['outstanding']) }}</div></div>
            <div class="kpi-cell"><div class="lbl">Net ce mois</div><div class="val" style="color:{{ $stats['net_month'] >= 0 ? '#059669' : '#dc2626' }};">{{ $fmt($stats['net_month']) }}</div></div>
        </div>
    </div>

    {{-- Recettes mensuelles --}}
    <div class="section-title">Recettes mensuelles (12 derniers mois)</div>
    <table class="data">
        <thead><tr><th>Mois</th><th class="r">Recettes (FCFA)</th></tr></thead>
        <tbody>
            @foreach($chartLabels as $i => $label)
            <tr><td>{{ $label }}</td><td class="r">{{ $fmt($chartData[$i] ?? 0) }}</td></tr>
            @endforeach
        </tbody>
    </table>

    {{-- Par type + Par mode --}}
    <table style="width:100%; margin-top:14px;"><tr>
        <td style="width:48%; vertical-align:top;">
            <div class="section-title">Par type</div>
            <table class="data">
                <thead><tr><th>Type</th><th class="r">FCFA</th></tr></thead>
                <tbody>
                    <tr><td>Inscription</td><td class="r">{{ $fmt($byType['inscription'] ?? 0) }}</td></tr>
                    <tr><td>Mensualité</td><td class="r">{{ $fmt($byType['mensualite'] ?? 0) }}</td></tr>
                </tbody>
            </table>
        </td>
        <td style="width:4%;"></td>
        <td style="width:48%; vertical-align:top;">
            <div class="section-title">Par mode de paiement</div>
            <table class="data">
                <thead><tr><th>Mode</th><th class="r">FCFA</th></tr></thead>
                <tbody>
                    @forelse($byMethod as $m => $total)
                    <tr><td>{{ $methodLabels[$m] ?? $m }}</td><td class="r">{{ $fmt($total) }}</td></tr>
                    @empty
                    <tr><td colspan="2" style="text-align:center; color:#9ca3af;">—</td></tr>
                    @endforelse
                </tbody>
            </table>
        </td>
    </tr></table>

    {{-- Par classe --}}
    <div class="section-title">Recettes par classe</div>
    <table class="data">
        <thead><tr><th>Classe</th><th class="r">Recettes (FCFA)</th></tr></thead>
        <tbody>
            @forelse($byClass as $row)
            <tr><td>{{ $row->cname }}</td><td class="r">{{ $fmt($row->total) }}</td></tr>
            @empty
            <tr><td colspan="2" style="text-align:center; color:#9ca3af;">Aucune donnée</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Document confidentiel · {{ $school['name'] }} · Généré le {{ $date }}</div>
</div>
</body>
</html>
