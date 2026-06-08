<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; line-height: 1.6; }

        .page { padding: 40px 50px; min-height: 100vh; position: relative; }

        /* Bordure décorative */
        .page-border { border: 4px double #4f46e5; padding: 30px; min-height: calc(100vh - 80px); }

        .header { text-align: center; margin-bottom: 30px; }
        .republic { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #374151; }
        .republic-sub { font-size: 10px; color: #6b7280; margin-bottom: 16px; }
        .school-name { font-size: 18px; font-weight: bold; color: #4f46e5; text-transform: uppercase; }
        .school-info { font-size: 10px; color: #6b7280; margin-top: 4px; }

        .cert-title { text-align: center; margin: 30px 0 24px; }
        .cert-title h1 { font-size: 20px; font-weight: bold; text-transform: uppercase; letter-spacing: 3px; color: #1e1b4b; }
        .cert-title .underline-deco { height: 2px; width: 120px; background: #4f46e5; margin: 8px auto 0; }

        .cert-body { text-align: center; font-size: 13px; line-height: 2.2; margin: 20px 0 30px; }
        .cert-body .highlight { font-weight: bold; font-size: 15px; color: #1e1b4b; border-bottom: 1px solid #4f46e5; padding: 0 4px; }

        .signature-area { margin-top: 40px; display: table; width: 100%; }
        .sig-date { display: table-cell; width: 45%; font-size: 11px; color: #6b7280; vertical-align: top; }
        .sig-director { display: table-cell; width: 55%; text-align: center; font-size: 11px; }
        .sig-label { font-weight: bold; color: #374151; margin-bottom: 50px; }
        .sig-line { border-top: 1px solid #6b7280; padding-top: 4px; font-size: 10px; color: #6b7280; width: 160px; margin: auto; }

        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #9ca3af; border-top: 1px dashed #d1d5db; padding-top: 8px; }
        .stamp-area { width: 80px; height: 80px; border: 2px dashed #d1d5db; border-radius: 50%; margin: 0 auto 8px; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #d1d5db; }
    </style>
</head>
<body>
<div class="page">
<div class="page-border">

    {{-- En-tête officiel --}}
    <div class="header">
        <div class="republic">République du Sénégal</div>
        <div class="republic-sub">Un peuple – Un but – Une foi</div>
        <div class="republic-sub">Ministère de l'Éducation Nationale</div>
        <div style="margin-top: 12px; font-size: 11px; color: #4b5563;">
            {{ $school['address'] }}
            @if($school['phone']) · Tél : {{ $school['phone'] }} @endif
        </div>
        <div class="school-name" style="margin-top: 8px;">{{ $school['name'] }}</div>
    </div>

    {{-- Titre --}}
    <div class="cert-title">
        <h1>Certificat de Scolarité</h1>
        <div class="underline-deco"></div>
    </div>

    {{-- Corps du certificat --}}
    <div class="cert-body">
        <p>Le Directeur de l'établissement <span class="highlight">{{ $school['name'] }}</span></p>
        <p>soussigné, certifie que l'élève</p>
        <p style="margin: 8px 0;">
            <span class="highlight">{{ $student->full_name }}</span>
        </p>
        <p>
            né(e) le <span class="highlight">{{ $student->date_of_birth?->format('d/m/Y') }}</span>
            à <span class="highlight">{{ $student->place_of_birth ?? '—' }}</span>
        </p>
        <p>
            est régulièrement inscrit(e) dans notre établissement
        </p>
        <p>
            en classe de <span class="highlight">{{ $student->classroom?->name }}</span>
        </p>
        <p>
            pour l'année scolaire <span class="highlight">{{ $student->schoolYear?->name }}</span>.
        </p>
        <p style="margin-top: 12px; font-size: 11px; color: #6b7280;">
            Matricule : {{ $student->registration_number }}
        </p>
    </div>

    <p style="text-align:center; font-size: 12px; color: #374151; margin-bottom: 4px;">
        Ce certificat est délivré pour servir et valoir ce que de droit.
    </p>

    {{-- Signature --}}
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
        @if($school['email']) · {{ $school['email'] }} @endif
    </div>

</div>
</div>
</body>
</html>
