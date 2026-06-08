<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }

        .page { padding: 20px; max-width: 540px; margin: auto; }

        .header { border-bottom: 3px solid #4f46e5; padding-bottom: 12px; margin-bottom: 16px; }
        .school-name { font-size: 16px; font-weight: bold; color: #4f46e5; }
        .school-info { font-size: 10px; color: #6b7280; margin-top: 2px; }

        .receipt-title { text-align: center; margin: 14px 0; }
        .receipt-title h2 { font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #374151; }
        .receipt-number { font-size: 10px; color: #6b7280; margin-top: 3px; }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-partial { background: #fef3c7; color: #92400e; }
        .badge-unpaid { background: #fee2e2; color: #991b1b; }

        .info-grid { display: table; width: 100%; margin-bottom: 14px; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; font-weight: bold; color: #4b5563; padding: 4px 8px 4px 0; width: 40%; }
        .info-value { display: table-cell; color: #111827; padding: 4px 0; }

        .amount-box { border: 2px solid #4f46e5; border-radius: 6px; padding: 12px; text-align: center; margin: 14px 0; background: #eef2ff; }
        .amount-label { font-size: 11px; color: #4f46e5; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .amount-value { font-size: 22px; font-weight: bold; color: #1e1b4b; margin-top: 4px; }

        .signature-area { margin-top: 24px; display: table; width: 100%; }
        .sig-left { display: table-cell; width: 50%; }
        .sig-right { display: table-cell; width: 50%; text-align: right; }
        .sig-line { border-top: 1px solid #9ca3af; margin-top: 24px; padding-top: 4px; font-size: 10px; color: #6b7280; }

        .footer { margin-top: 20px; padding-top: 8px; border-top: 1px dashed #d1d5db; text-align: center; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="page">

    {{-- En-tête --}}
    <div class="header">
        <div class="school-name">{{ $school['name'] }}</div>
        <div class="school-info">
            {{ $school['address'] }}
            @if($school['phone']) · Tél : {{ $school['phone'] }} @endif
            @if($school['email']) · {{ $school['email'] }} @endif
        </div>
    </div>

    {{-- Titre reçu --}}
    <div class="receipt-title">
        <h2>Reçu de Paiement</h2>
        <div class="receipt-number">N° {{ $payment->receipt_number }}</div>
    </div>

    {{-- Infos élève --}}
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Élève</div>
            <div class="info-value"><strong>{{ $payment->student?->full_name }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Matricule</div>
            <div class="info-value">{{ $payment->student?->registration_number }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Classe</div>
            <div class="info-value">{{ $payment->classroom?->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Année scolaire</div>
            <div class="info-value">{{ $payment->schoolYear?->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Type</div>
            <div class="info-value">
                {{ $payment->payment_type === 'inscription' ? 'Frais d\'inscription' : 'Mensualité — ' . $payment->month_label . ' ' . $payment->year }}
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Date de paiement</div>
            <div class="info-value">{{ $payment->payment_date?->format('d/m/Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Mode de paiement</div>
            <div class="info-value">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</div>
        </div>
    </div>

    {{-- Montants --}}
    <div class="amount-box">
        {{-- Ligne montant dû --}}
        <div style="display: table; width: 100%; margin-bottom: 6px; padding-bottom: 6px; border-bottom: 1px solid #c7d2fe;">
            <div style="display: table-cell; text-align: left; font-size: 10px; color: #6366f1; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">
                Montant dû
            </div>
            <div style="display: table-cell; text-align: right; font-size: 13px; font-weight: bold; color: #374151;">
                {{ number_format($payment->amount_due, 0, ',', ' ') }} FCFA
            </div>
        </div>

        {{-- Ligne montant payé --}}
        <div class="amount-label">Montant payé</div>
        <div class="amount-value">{{ number_format($payment->amount_paid, 0, ',', ' ') }} FCFA</div>

        @if($payment->balance > 0)
        {{-- Reste à payer --}}
        <div style="margin-top: 8px; padding-top: 6px; border-top: 1px solid #c7d2fe; display: table; width: 100%;">
            <div style="display: table-cell; text-align: left; font-size: 10px; color: #dc2626; font-weight: bold;">
                Solde restant
            </div>
            <div style="display: table-cell; text-align: right; font-size: 13px; font-weight: bold; color: #dc2626;">
                {{ number_format($payment->balance, 0, ',', ' ') }} FCFA
            </div>
        </div>
        @endif
    </div>

    {{-- Statut --}}
    <div style="text-align: center; margin-bottom: 10px;">
        @if($payment->status === 'paid')
            <span class="badge badge-paid">✓ PAYÉ INTÉGRALEMENT</span>
        @elseif($payment->status === 'partial')
            <span class="badge badge-partial">⚠ PAIEMENT PARTIEL</span>
        @else
            <span class="badge badge-unpaid">✗ NON PAYÉ</span>
        @endif
    </div>

    {{-- Signatures --}}
    <div class="signature-area">
        <div class="sig-left">
            <div class="sig-line">Le caissier / comptable</div>
        </div>
        <div class="sig-right">
            <div class="sig-line">Le bénéficiaire</div>
        </div>
    </div>

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }}
        @if($payment->creator) · Par {{ $payment->creator->name }} @endif
        · {{ $school['name'] }}
    </div>

</div>
</body>
</html>
