<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation RSVP — {{ $guest->wedding->getCoupleName() }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/wedding.css') }}">
    @php $theme = $guest->wedding->theme; @endphp
    @if($theme) <style>:root { {{ $theme->getCssVariables() }} }</style> @endif
</head>
<body style="background: linear-gradient(135deg, #1a1018 0%, #2d1f2b 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px;">

<div class="rsvp-form-card" style="max-width:520px;width:100%;text-align:center">
    @if($guest->rsvp_status === 'accepted')
    <div style="font-size:60px;margin-bottom:16px">💍</div>
    <h2 style="font-family:var(--ft);font-size:28px;color:var(--wt)">Merci {{ $guest->first_name }} !</h2>
    <p style="color:#9e8e82;font-size:16px;margin-bottom:24px">
        Votre présence nous comble de joie. Nous avons hâte de célébrer ce jour avec vous !
    </p>
    @elseif($guest->rsvp_status === 'maybe')
    <div style="font-size:60px;margin-bottom:16px">🌸</div>
    <h2 style="font-family:var(--ft);font-size:28px;color:var(--wt)">À bientôt, {{ $guest->first_name }}</h2>
    <p style="color:#9e8e82;font-size:16px;margin-bottom:24px">
        Nous attendons votre confirmation. N'hésitez pas à revenir confirmer votre réponse.
    </p>
    @else
    <div style="font-size:60px;margin-bottom:16px">🌹</div>
    <h2 style="font-family:var(--ft);font-size:28px;color:var(--wt)">Dommage, {{ $guest->first_name }}</h2>
    <p style="color:#9e8e82;font-size:16px;margin-bottom:24px">
        Votre décision est bien enregistrée. Vous serez avec nous en pensée.
    </p>
    @endif

    <div style="background:var(--ws);border-radius:12px;padding:20px;margin-bottom:24px;text-align:left">
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #e8ddd5">
            <span style="color:#9e8e82">Mariage</span>
            <strong>{{ $guest->wedding->getCoupleName() }}</strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #e8ddd5">
            <span style="color:#9e8e82">Date</span>
            <strong>{{ $guest->wedding->getWeddingDateFormatted() }}</strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:8px 0">
            <span style="color:#9e8e82">Votre réponse</span>
            <span class="badge bg-{{ $guest->status_color }}">{{ $guest->status_label }}</span>
        </div>
        @if($guest->companions_count > 0)
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-top:1px solid #e8ddd5">
            <span style="color:#9e8e82">Accompagnants</span>
            <strong>{{ $guest->companions_count }}</strong>
        </div>
        @endif
    </div>

    <div style="display:flex;flex-direction:column;gap:10px">
        <a href="{{ route('wedding.public', $guest->wedding->slug) }}" class="rsvp-btn" style="text-decoration:none;display:block">
            <i class="bi bi-heart me-2"></i>Voir l'invitation complète
        </a>
        <a href="{{ route('guest.personal', $guest->invitation_code) }}" style="color:#9e8e82;font-size:13px;text-decoration:none">
            <i class="bi bi-person me-1"></i>Ma page personnelle
        </a>
    </div>
</div>

</body>
</html>
