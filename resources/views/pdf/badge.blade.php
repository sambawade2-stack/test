<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { margin: 12px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; }

        .badge {
            display: inline-block;
            width: 250px;
            height: 158px;
            border: 1px solid #c7d2fe;
            border-radius: 8px;
            margin: 6px;
            overflow: hidden;
            vertical-align: top;
            background: #ffffff;
        }

        /* Bandeau supérieur */
        .badge-head {
            background: #4f46e5;
            color: #ffffff;
            padding: 5px 8px;
            height: 30px;
        }
        .badge-head .sname { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-head .stype { font-size: 6.5px; color: #c7d2fe; }

        /* Corps : table pour aligner photo / infos / qr */
        .badge-body { padding: 6px 8px; }
        .badge-body table { width: 100%; border-collapse: collapse; }
        .badge-body td { vertical-align: top; }

        .photo {
            width: 46px; height: 56px;
            border: 1px solid #e5e7eb; border-radius: 4px;
            background: #eef2ff;
            text-align: center;
        }
        .photo img { width: 46px; height: 56px; border-radius: 4px; }
        .photo .initials {
            display: block; font-size: 22px; font-weight: bold;
            color: #6366f1; line-height: 56px;
        }

        .infos { padding-left: 7px; }
        .infos .name { font-size: 11px; font-weight: bold; color: #111827; }
        .infos .matricule { font-size: 7.5px; color: #4f46e5; font-weight: bold; margin: 1px 0 3px; }
        .infos .row { font-size: 7px; color: #4b5563; line-height: 1.45; }
        .infos .row b { color: #1f2937; }

        .qr { width: 60px; text-align: right; }
        .qr img { width: 58px; height: 58px; }
        .qr .scan { font-size: 5.5px; color: #9ca3af; text-align: center; margin-top: 1px; }

        /* Pied */
        .badge-foot {
            border-top: 1px dashed #e5e7eb;
            padding: 3px 8px;
            font-size: 6px; color: #9ca3af;
        }
        .badge-foot .yr { float: right; font-weight: bold; color: #4f46e5; }
    </style>
</head>
<body>

@foreach($badges as $b)
@php $student = $b['student']; @endphp
<div class="badge">

    {{-- Bandeau --}}
    <div class="badge-head">
        <div class="sname">{{ $school['name'] }}</div>
        <div class="stype">Carte d'élève · {{ $student->schoolYear?->name ?? '' }}</div>
    </div>

    {{-- Corps --}}
    <div class="badge-body">
        <table>
            <tr>
                {{-- Photo --}}
                <td style="width:46px;">
                    <div class="photo">
                        @if($student->photo && file_exists(public_path('storage/'.$student->photo)))
                            <img src="{{ public_path('storage/'.$student->photo) }}" alt="">
                        @else
                            <span class="initials">{{ strtoupper(substr($student->first_name,0,1)) }}</span>
                        @endif
                    </div>
                </td>

                {{-- Infos --}}
                <td class="infos">
                    <div class="name">{{ $student->full_name }}</div>
                    <div class="matricule">{{ $student->registration_number }}</div>
                    <div class="row"><b>Classe :</b> {{ $student->classroom?->name ?? '—' }}</div>
                    <div class="row"><b>Né(e) :</b> {{ $student->date_of_birth?->format('d/m/Y') }}</div>
                    <div class="row"><b>Tuteur :</b> {{ \Illuminate\Support\Str::limit($student->parent_name ?? '—', 18) }}</div>
                    <div class="row"><b>Tél :</b> {{ $student->parent_phone ?? '—' }}</div>
                </td>

                {{-- QR --}}
                <td class="qr">
                    <img src="{{ $b['qr'] }}" alt="QR">
                    <div class="scan">Scannez-moi</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Pied --}}
    <div class="badge-foot">
        {{ $school['phone'] ?? '' }}
        <span class="yr">{{ $student->schoolYear?->name ?? '' }}</span>
    </div>

</div>
@endforeach

</body>
</html>
