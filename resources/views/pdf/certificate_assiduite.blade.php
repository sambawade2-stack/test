<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; line-height: 1.6; }
        .page { padding: 40px 50px; }
        .page-border { border: 4px double #059669; padding: 30px; }

        .header { text-align: center; margin-bottom: 24px; }
        .school-name { font-size: 18px; font-weight: bold; color: #059669; text-transform: uppercase; }
        .school-info { font-size: 10px; color: #6b7280; margin-top: 4px; }
        .republic { font-size: 11px; text-transform: uppercase; color: #374151; }

        .cert-title { text-align: center; margin: 24px 0 20px; }
        .cert-title h1 { font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #064e3b; }
        .underline-deco { height: 2px; width: 120px; background: #059669; margin: 8px auto 0; }

        .cert-body { text-align: center; font-size: 13px; line-height: 2.2; margin: 20px 0; }
        .highlight { font-weight: bold; color: #064e3b; border-bottom: 1px solid #059669; padding: 0 4px; }

        .stats-box { border: 1px solid #a7f3d0; background: #f0fdf4; border-radius: 6px; padding: 14px; margin: 20px 0; text-align: center; }
        .stat-item { display: inline-block; margin: 0 16px; text-align: center; }
        .stat-value { font-size: 22px; font-weight: bold; color: #065f46; }
        .stat-label { font-size: 10px; color: #6b7280; }

        .taux-bar { background: #d1fae5; border-radius: 20px; height: 12px; margin: 8px auto; max-width: 300px; overflow: hidden; }
        .taux-fill { height: 100%; border-radius: 20px; transition: width 0.3s; }

        .signature-area { margin-top: 40px; display: table; width: 100%; }
        .sig-date { display: table-cell; width: 45%; font-size: 11px; vertical-align: top; }
        .sig-director { display: table-cell; width: 55%; text-align: center; font-size: 11px; }
        .sig-label { font-weight: bold; color: #374151; margin-bottom: 50px; }
        .sig-line { border-top: 1px solid #6b7280; padding-top: 4px; width: 160px; margin: auto; font-size: 10px; color: #6b7280; }
        .stamp-area { width: 70px; height: 70px; border: 2px dashed #d1d5db; border-radius: 50%; margin: 0 auto 8px; font-size: 9px; color: #d1d5db; display: flex; align-items: center; justify-content: center; }

        .footer { margin-top: 16px; text-align: center; font-size: 9px; color: #9ca3af; border-top: 1px dashed #d1d5db; padding-top: 8px; }
    </style>
</head>
<body>
<div class="page">
<div class="page-border">

    <div class="header">
        <div class="republic">République du Sénégal</div>
        <div style="font-size: 10px; color: #6b7280; margin-bottom: 12px;">Un peuple – Un but – Une foi</div>
        <div class="school-name">{{ $school['name'] }}</div>
        <div class="school-info">{{ $school['address'] }} @if($school['phone'])· Tél : {{ $school['phone'] }}@endif</div>
    </div>

    <div class="cert-title">
        <h1>Certificat d'Assiduité</h1>
        <div class="underline-deco"></div>
    </div>

    @php
        $monthLabels = ['','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
    @endphp

    <div class="cert-body">
        <p>Le Directeur de l'établissement <span class="highlight">{{ $school['name'] }}</span></p>
        <p>certifie que l'élève <span class="highlight">{{ $student->full_name }}</span></p>
        <p>matricule <span class="highlight">{{ $student->registration_number }}</span></p>
        <p>en classe de <span class="highlight">{{ $student->classroom?->name }}</span></p>
        <p>
            a fait preuve d'une assiduité
            <span class="highlight">{{ $taux_presence >= 90 ? 'excellente' : ($taux_presence >= 75 ? 'satisfaisante' : 'insuffisante') }}</span>
        </p>
        <p>
            au mois de <span class="highlight">{{ $monthLabels[$month] }} {{ $year }}</span>.
        </p>
    </div>

    {{-- Statistiques --}}
    <div class="stats-box">
        <div class="stat-item">
            <div class="stat-value">{{ $total_days }}</div>
            <div class="stat-label">Jours suivis</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color: #dc2626;">{{ $absences }}</div>
            <div class="stat-label">Absences</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color: #065f46;">{{ $taux_presence }}%</div>
            <div class="stat-label">Taux de présence</div>
        </div>

        <div class="taux-bar" style="margin-top: 12px;">
            <div class="taux-fill"
                 style="width: {{ $taux_presence }}%; background: {{ $taux_presence >= 90 ? '#10b981' : ($taux_presence >= 75 ? '#f59e0b' : '#ef4444') }};"></div>
        </div>
    </div>

    <div class="signature-area">
        <div class="sig-date">
            <p>Fait à {{ explode(',', $school['address'])[0] ?? 'Dakar' }},</p>
            <p>le {{ $date }}</p>
        </div>
        <div class="sig-director">
            <div class="stamp-area">CACHET</div>
            <div class="sig-label">Le Directeur / La Directrice</div>
            <div class="sig-line">Signature et cachet</div>
        </div>
    </div>

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} · {{ $school['name'] }}
    </div>

</div>
</div>
</body>
</html>
