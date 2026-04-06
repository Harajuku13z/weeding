<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace invité — Connexion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/wedding.css') }}">
</head>
<body class="guest-login-body">
<div class="guest-login-card">
    <div style="text-align:center;margin-bottom:32px">
        <div style="width:60px;height:60px;background:linear-gradient(135deg,#c8a97e,#8b6355);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px;color:white">
            <i class="bi bi-envelope-heart-fill"></i>
        </div>
        <h1 style="font-family:var(--ft,serif);font-size:22px;margin:0;color:#3d2b1f">Mon espace invité</h1>
        <p style="color:#9e8e82;margin-top:6px;font-size:14px">Accédez à votre espace personnel</p>
    </div>

    @if(session('error'))
    <div class="alert alert-danger mb-4"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>
    @endif
    @if(session('success'))
    <div class="alert alert-success mb-4"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('guest.login.post') }}">
        @csrf
        <div class="mb-4">
            <label style="font-weight:600;font-size:13px;margin-bottom:6px;display:block;color:#3d2b1f">
                Code d'invitation
            </label>
            <input type="text" name="invitation_code" class="rsvp-input"
                   placeholder="Entrez votre code (ex: ABCD1234)"
                   style="text-transform:uppercase;letter-spacing:2px;font-size:16px;text-align:center"
                   required autofocus>
            @error('invitation_code')
            <div style="color:#ef4444;font-size:12px;margin-top:4px">{{ $message }}</div>
            @enderror
            <div style="color:#9e8e82;font-size:12px;margin-top:6px;text-align:center">
                Vous trouverez votre code sur votre carton d'invitation.
            </div>
        </div>

        <button type="submit" class="rsvp-btn">
            <i class="bi bi-arrow-right-circle me-2"></i>Accéder à mon espace
        </button>
    </form>

    <div style="text-align:center;margin-top:20px;padding-top:20px;border-top:1px solid #e8ddd5">
        <p style="color:#9e8e82;font-size:13px">Vous avez reçu un lien magique dans votre email ? <br>Cliquez simplement dessus.</p>
    </div>
</div>
</body>
</html>
