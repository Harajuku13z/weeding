<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Invitation Mariage</title>
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
            <h1 class="auth-title">Invitation Mariage</h1>
            <p class="auth-subtitle">Espace Administrateur</p>
        </div>

        @if(session('error'))
        <div class="alert alert-danger mb-3"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group mb-3">
                <label class="form-label">Adresse email</label>
                <div class="input-icon">
                    <i class="bi bi-envelope-fill"></i>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="votre@email.com" required autofocus>
                </div>
                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="form-group mb-4">
                <label class="form-label">Mot de passe</label>
                <div class="input-icon">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••" required>
                </div>
                @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <label class="form-check-label d-flex align-items-center gap-2">
                    <input type="checkbox" name="remember" class="form-check-input"> Se souvenir de moi
                </label>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100">
                <i class="bi bi-arrow-right-circle me-2"></i>Se connecter
            </button>
        </form>

        <div class="auth-footer">
            Pas encore de compte ? <a href="{{ route('register') }}">Créer un compte</a>
        </div>
    </div>
</div>
</body>
</html>
