<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte — Invitation Mariage</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="auth-body">
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-brand">
            <div class="auth-logo"><i class="bi bi-heart-fill"></i></div>
            <h1 class="auth-title">Créer votre compte</h1>
            <p class="auth-subtitle">Plateforme d'invitation mariage premium</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group mb-3">
                <label class="form-label">Votre nom</label>
                <div class="input-icon">
                    <i class="bi bi-person-fill"></i>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="Votre nom complet" required autofocus>
                </div>
                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Adresse email</label>
                <div class="input-icon">
                    <i class="bi bi-envelope-fill"></i>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="votre@email.com" required>
                </div>
                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Mot de passe</label>
                <div class="input-icon">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimum 8 caractères" required>
                </div>
                @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="form-group mb-4">
                <label class="form-label">Confirmer le mot de passe</label>
                <div class="input-icon">
                    <i class="bi bi-shield-lock-fill"></i>
                    <input type="password" name="password_confirmation" class="form-control"
                           placeholder="Répétez le mot de passe" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100">
                <i class="bi bi-check-circle me-2"></i>Créer mon compte
            </button>
        </form>

        <div class="auth-footer">
            Déjà un compte ? <a href="{{ route('login') }}">Se connecter</a>
        </div>
    </div>
</div>
</body>
</html>
