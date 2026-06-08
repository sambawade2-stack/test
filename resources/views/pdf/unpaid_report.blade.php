<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        .page { padding: 20px 30px; }

        .header { display: table; width: 100%; border-bottom: 2px solid #dc2626; padding-bottom: 8px; margin-bottom: 12px; }
        .header-left { display: table-cell; width: 60%; }
        .header-right { display: table-cell; width: 40%; text-align: right; font-size: 10px; color: #6b7280; }
        .school-name { font-size: 14px; font-weight: bold; color: #dc2626; }

        h1 { text-align: center; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #7f1d1d; margin-bottom: 4px; }
        .subtitle { text-align: center; font-size: 11px; color: #b91c1c; font-weight: bold; margin-bottom: 12px; }

        .summary-box { display: table; width: 100%; background: #fef2f2; border: 1px solid #fecaca; border-radius: 4px; padding: 8px; margin-bottom: 12px; }
        .summary-item { display: table-cell; text-align: center; }
        .summary-value { font-size: 18px; font-weight: bold; color: #991b1b; }
        .summary-label { font-size: 9px; color: #7f1d1d; }

        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        thead th { background: #dc2626; color: white; padding: 6px 8px; text-align: left; }
        tbody tr:nth-child(even) { background: #fff5f5; }
        tbody td { padding: 5px 8px; border-bottom: 1px solid #fecaca; }

        .footer { margin-top: 16px; text-align: center; font-size: 9px; color: #9ca3af; border-top: 1px dashed #d1d5db; padding-top: 6px; }
    </style>
</head>
<body>
<div class="page">

    <div class="header">
        <div class="header-left">
            <div class="school-name">{{ $school['name'] }}</div>
            <div style="font-size: 9px; color: #6b7280;">{{ $school['address'] }}</div>
        </div>
        <div class="header-right">
            Généré le {{ $date }}
        </div>
    </div>

    <h1>Rapport des Impayés</h1>
    <div class="subtitle">Mensualité — {{ $month_label }} {{ $year }}</div>

    <div class="summary-box">
        <div class="summary-item">
            <div class="summary-value">{{ count($students) }}</div>
            <div class="summary-label">Élèves impayés</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Matricule</th>
                <th>Élève</th>
                <th>Classe</th>
                <th>Parent</th>
                <th>Téléphone</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $i => $student)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td style="font-family: monospace;">{{ $student['registration_number'] }}</td>
                <td><strong>{{ $student['first_name'] }} {{ $student['last_name'] }}</strong></td>
                <td>{{ $student['classroom']['name'] ?? '—' }}</td>
                <td>{{ $student['parent_name'] ?? '—' }}</td>
                <td>{{ $student['parent_phone'] ?? '—' }}</td>
                <td>{{ $student['parent_email'] ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ $school['name'] }} · Rapport impayés {{ $month_label }} {{ $year }} · Généré le {{ $date }}
    </div>

</div>
</body>
</html>
