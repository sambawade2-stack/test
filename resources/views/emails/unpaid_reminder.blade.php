<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="font-family: Arial, sans-serif; background: #f3f4f6; padding: 20px;">

<div style="max-width: 560px; margin: auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.1);">

    <div style="background: #dc2626; padding: 20px 24px; text-align: center;">
        <h1 style="color: white; font-size: 18px; margin: 0;">{{ $school }} — Rappel de paiement</h1>
    </div>

    <div style="padding: 24px;">
        <p>Bonjour <strong>{{ $student->parent_name ?? 'Parent/Tuteur' }}</strong>,</p>
        <br>
        <p>Nous vous informons que la mensualité du mois de <strong>{{ $month }} {{ $year }}</strong> de votre enfant</p>
        <div style="background: #fef2f2; border-left: 4px solid #dc2626; padding: 12px 16px; margin: 12px 0; border-radius: 0 4px 4px 0;">
            <strong style="font-size: 16px;">{{ $student->full_name }}</strong><br>
            <span style="color: #6b7280; font-size: 13px;">Matricule : {{ $student->registration_number }} · Classe : {{ $student->classroom?->name }}</span>
        </div>
        <p>n'a pas encore été enregistrée dans notre système.</p>
        <br>
        <p>Merci de régulariser cette situation dans les plus brefs délais auprès du service comptabilité.</p>
        <br>
        @if($schoolPhone)
        <p>📞 <strong>{{ $schoolPhone }}</strong></p>
        @endif
        <br>
        <p style="color: #6b7280; font-size: 12px;">Cordialement,<br>La Direction de {{ $school }}</p>
    </div>

    <div style="background: #f9fafb; padding: 12px 24px; text-align: center; font-size: 11px; color: #9ca3af;">
        Ce message est envoyé automatiquement par le système de gestion scolaire.
    </div>
</div>

</body>
</html>
