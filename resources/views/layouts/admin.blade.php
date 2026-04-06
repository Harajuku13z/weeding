<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Invitation Mariage</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-body">

<!-- Sidebar -->
<div class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <div class="brand-logo">
            <i class="bi bi-heart-fill"></i>
        </div>
        <div class="brand-text">
            <span class="brand-title">Mariages</span>
            <span class="brand-sub">Administration</span>
        </div>
    </div>

    @php $currentWedding = session('current_wedding_id') ? \App\Models\Wedding::find(session('current_wedding_id')) : auth()->user()->weddings()->first(); @endphp

    @if($currentWedding)
    <div class="sidebar-wedding">
        <div class="wedding-badge">
            <i class="bi bi-stars"></i>
            <span>{{ $currentWedding->bride_name }} & {{ $currentWedding->groom_name }}</span>
        </div>
    </div>
    @endif

    <nav class="sidebar-nav">
        <div class="nav-section-label">Vue d'ensemble</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.weddings.index') }}" class="nav-link {{ request()->routeIs('admin.weddings*') && !$currentWedding ? 'active' : '' }}">
            <i class="bi bi-heart-fill"></i> <span>Mes mariages</span>
        </a>

        @if($currentWedding)
        <div class="nav-section-label mt-3">Mariage actuel</div>
        <a href="{{ route('admin.weddings.show', $currentWedding) }}" class="nav-link {{ request()->routeIs('admin.weddings.show') ? 'active' : '' }}">
            <i class="bi bi-info-circle-fill"></i> <span>Vue générale</span>
        </a>
        <a href="{{ route('admin.weddings.edit', $currentWedding) }}" class="nav-link {{ request()->routeIs('admin.weddings.edit') ? 'active' : '' }}">
            <i class="bi bi-pencil-fill"></i> <span>Infos du mariage</span>
        </a>
        <a href="{{ route('admin.weddings.guests.index', $currentWedding) }}" class="nav-link {{ request()->routeIs('admin.weddings.guests*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> <span>Invités</span>
            <span class="nav-badge">{{ $currentWedding->guests()->count() }}</span>
        </a>
        <a href="{{ route('admin.weddings.program.index', $currentWedding) }}" class="nav-link {{ request()->routeIs('admin.weddings.program*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event-fill"></i> <span>Programme</span>
        </a>
        <a href="{{ route('admin.weddings.gallery.index', $currentWedding) }}" class="nav-link {{ request()->routeIs('admin.weddings.gallery*') ? 'active' : '' }}">
            <i class="bi bi-images"></i> <span>Galerie photos</span>
        </a>
        <a href="{{ route('admin.weddings.venues.index', $currentWedding) }}" class="nav-link {{ request()->routeIs('admin.weddings.venues*') ? 'active' : '' }}">
            <i class="bi bi-geo-alt-fill"></i> <span>Lieux</span>
        </a>
        <a href="{{ route('admin.weddings.theme.edit', $currentWedding) }}" class="nav-link {{ request()->routeIs('admin.weddings.theme*') ? 'active' : '' }}">
            <i class="bi bi-palette-fill"></i> <span>Thème & design</span>
        </a>
        <a href="{{ route('admin.weddings.gifts.index', $currentWedding) }}" class="nav-link {{ request()->routeIs('admin.weddings.gifts*') ? 'active' : '' }}">
            <i class="bi bi-gift-fill"></i> <span>Cadeaux</span>
        </a>
        <a href="{{ route('admin.weddings.rules.index', $currentWedding) }}" class="nav-link {{ request()->routeIs('admin.weddings.rules*') ? 'active' : '' }}">
            <i class="bi bi-shield-check-fill"></i> <span>Règles & consignes</span>
        </a>
        <a href="{{ route('admin.weddings.reminders.index', $currentWedding) }}" class="nav-link {{ request()->routeIs('admin.weddings.reminders*') ? 'active' : '' }}">
            <i class="bi bi-send-fill"></i> <span>Relances</span>
        </a>
        <a href="{{ route('admin.weddings.templates.index', $currentWedding) }}" class="nav-link {{ request()->routeIs('admin.weddings.templates*') ? 'active' : '' }}">
            <i class="bi bi-file-text-fill"></i> <span>Modèles de messages</span>
        </a>

        <div class="nav-divider"></div>
        <a href="{{ route('wedding.public', $currentWedding->slug) }}" class="nav-link" target="_blank">
            <i class="bi bi-box-arrow-up-right"></i> <span>Voir l'invitation</span>
        </a>
        <a href="{{ route('home') }}" class="nav-link" target="_blank">
            <i class="bi bi-house-heart"></i> <span>Page d'accueil</span>
        </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
            <div class="user-details">
                <span class="user-name">{{ auth()->user()->name }}</span>
                <span class="user-role">Administrateur</span>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout" title="Déconnexion">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</div>

<!-- Main Content -->
<div class="admin-main" id="adminMain">
    <!-- Top bar -->
    <div class="admin-topbar">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-breadcrumb">
            @yield('breadcrumb')
        </div>
        <div class="topbar-actions">
            @yield('topbar-actions')
        </div>
    </div>

    <!-- Alerts -->
    <div class="admin-alerts">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
    </div>

    <!-- Content -->
    <div class="admin-content">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.to/flatpickr/dist/l10n/fr.js"></script>
<script src="{{ asset('js/admin.js') }}"></script>
@stack('scripts')
</body>
</html>
