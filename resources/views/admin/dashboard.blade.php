@extends('layouts.admin')

@section('title', 'Dashboard')

@section('breadcrumb')
<span>Dashboard</span>
@endsection

@section('topbar-actions')
@if($wedding)
<a href="{{ route('admin.weddings.guests.create', $wedding) }}" class="btn btn-sm btn-primary-custom">
    <i class="bi bi-person-plus-fill me-1"></i>Ajouter un invité
</a>
@endif
@endsection

@section('content')
@if(!$wedding)
<div class="empty-state text-center py-5">
    <div class="empty-icon mb-4"><i class="bi bi-heart" style="font-size:4rem;color:var(--color-primary)"></i></div>
    <h2 class="font-serif">Bienvenue sur votre plateforme mariage</h2>
    <p class="text-muted mb-4">Commencez par créer votre première invitation de mariage.</p>
    <a href="{{ route('admin.weddings.create') }}" class="btn btn-primary-custom btn-lg">
        <i class="bi bi-plus-circle me-2"></i>Créer mon mariage
    </a>
</div>
@else
<!-- Sélecteur de mariage si plusieurs -->
@if($weddings->count() > 1)
<div class="wedding-selector mb-4">
    <select class="form-select" onchange="window.location.href='/admin/weddings/'+this.value">
        @foreach($weddings as $w)
        <option value="{{ $w->id }}" {{ $w->id === $wedding->id ? 'selected' : '' }}>
            {{ $w->bride_name }} & {{ $w->groom_name }}
        </option>
        @endforeach
    </select>
</div>
@endif

<!-- Hero stats -->
<div class="dashboard-hero mb-4">
    <div class="hero-info">
        <h1 class="hero-names font-serif">{{ $wedding->getCoupleName() }}</h1>
        <div class="hero-date">
            <i class="bi bi-calendar-heart me-2"></i>
            {{ $wedding->getWeddingDateFormatted() }}
            @if($wedding->getDaysUntilWedding() !== null && $wedding->getDaysUntilWedding() > 0)
            <span class="countdown-badge">{{ $wedding->getDaysUntilWedding() }} jours</span>
            @endif
        </div>
        <div class="hero-badges mt-2">
            @if($wedding->is_published)
            <span class="badge badge-success"><i class="bi bi-globe me-1"></i>Publié</span>
            @else
            <span class="badge badge-secondary"><i class="bi bi-pencil me-1"></i>Brouillon</span>
            @endif
            <a href="{{ route('wedding.public', $wedding->slug) }}" class="badge badge-primary" target="_blank">
                <i class="bi bi-box-arrow-up-right me-1"></i>Voir l'invitation
            </a>
        </div>
    </div>
    <div class="hero-actions">
        <form method="POST" action="{{ route('admin.weddings.publish', $wedding) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-{{ $wedding->is_published ? 'warning' : 'success' }} btn-sm">
                <i class="bi bi-{{ $wedding->is_published ? 'eye-slash' : 'globe' }} me-1"></i>
                {{ $wedding->is_published ? 'Dépublier' : 'Publier' }}
            </button>
        </form>
        <a href="{{ route('admin.weddings.edit', $wedding) }}" class="btn btn-primary-custom btn-sm">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
    </div>
</div>

<!-- Stats RSVP -->
<div class="stats-grid mb-4">
    <div class="stat-card stat-total">
        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['total'] ?? 0 }}</div>
            <div class="stat-label">Invités total</div>
        </div>
    </div>
    <div class="stat-card stat-accepted">
        <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['accepted'] ?? 0 }}</div>
            <div class="stat-label">Ont accepté</div>
        </div>
    </div>
    <div class="stat-card stat-declined">
        <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['declined'] ?? 0 }}</div>
            <div class="stat-label">Ont décliné</div>
        </div>
    </div>
    <div class="stat-card stat-maybe">
        <div class="stat-icon"><i class="bi bi-question-circle-fill"></i></div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['maybe'] ?? 0 }}</div>
            <div class="stat-label">À confirmer</div>
        </div>
    </div>
    <div class="stat-card stat-pending">
        <div class="stat-icon"><i class="bi bi-clock-fill"></i></div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['pending'] ?? 0 }}</div>
            <div class="stat-label">Sans réponse</div>
        </div>
    </div>
    <div class="stat-card stat-expected">
        <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['expected_count'] ?? 0 }}</div>
            <div class="stat-label">Personnes attendues</div>
        </div>
    </div>
</div>

<!-- Taux de réponse -->
@if(($stats['total'] ?? 0) > 0)
<div class="response-rate-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="fw-semibold">Taux de réponse</span>
        <span class="fw-bold" style="color:var(--color-primary)">{{ $stats['response_rate'] }}%</span>
    </div>
    <div class="progress" style="height:8px;border-radius:10px">
        <div class="progress-bar" style="width:{{ $stats['response_rate'] }}%;background:var(--color-primary)"></div>
    </div>
    <div class="d-flex justify-content-between mt-2 text-muted small">
        <span>{{ $stats['responded'] }} répondu(s)</span>
        <span>{{ $stats['pending'] }} en attente</span>
    </div>
</div>
@endif

<div class="row g-4">
    <!-- Dernières réponses -->
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="card-header-custom">
                <h3 class="card-title-custom"><i class="bi bi-clock-history me-2"></i>Dernières réponses</h3>
                <a href="{{ route('admin.weddings.guests.index', $wedding) }}" class="btn btn-sm btn-outline-primary">
                    Tous les invités
                </a>
            </div>
            <div class="card-body-custom">
                @forelse($recentGuests as $guest)
                <div class="guest-row">
                    <div class="guest-avatar">{{ substr($guest->first_name, 0, 1) }}</div>
                    <div class="guest-info">
                        <span class="guest-name">{{ $guest->full_name }}</span>
                        <span class="guest-time">{{ $guest->rsvp_at?->diffForHumans() }}</span>
                    </div>
                    <span class="badge bg-{{ $guest->status_color }}">{{ $guest->status_label }}</span>
                </div>
                @empty
                <div class="empty-state-sm">
                    <i class="bi bi-inbox"></i>
                    <span>Aucune réponse pour l'instant</span>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="card-header-custom">
                <h3 class="card-title-custom"><i class="bi bi-lightning-fill me-2"></i>Actions rapides</h3>
            </div>
            <div class="card-body-custom">
                <div class="quick-actions">
                    <a href="{{ route('admin.weddings.guests.create', $wedding) }}" class="quick-action">
                        <i class="bi bi-person-plus-fill"></i>
                        <span>Ajouter un invité</span>
                    </a>
                    <a href="{{ route('admin.weddings.reminders.index', $wedding) }}" class="quick-action">
                        <i class="bi bi-send-fill"></i>
                        <span>Envoyer des relances</span>
                    </a>
                    <a href="{{ route('admin.weddings.guests.export', $wedding) }}" class="quick-action">
                        <i class="bi bi-download"></i>
                        <span>Exporter CSV</span>
                    </a>
                    <a href="{{ route('admin.weddings.theme.edit', $wedding) }}" class="quick-action">
                        <i class="bi bi-palette-fill"></i>
                        <span>Personnaliser le thème</span>
                    </a>
                    <a href="{{ route('admin.weddings.program.create', $wedding) }}" class="quick-action">
                        <i class="bi bi-calendar-plus"></i>
                        <span>Ajouter une étape</span>
                    </a>
                    <a href="{{ route('admin.weddings.gallery.create', $wedding) }}" class="quick-action">
                        <i class="bi bi-image-fill"></i>
                        <span>Ajouter des photos</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="admin-card mt-4">
            <div class="card-header-custom">
                <h3 class="card-title-custom"><i class="bi bi-bar-chart-fill me-2"></i>Invités à relancer</h3>
            </div>
            <div class="card-body-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Sans réponse</span>
                    <span class="fw-bold text-warning">{{ $stats['pending'] ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span>À confirmer</span>
                    <span class="fw-bold text-info">{{ $stats['maybe'] ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span>Relances envoyées</span>
                    <span class="fw-bold" style="color:var(--color-primary)">{{ $remindersSent }}</span>
                </div>
                @if(($stats['pending'] ?? 0) + ($stats['maybe'] ?? 0) > 0)
                <a href="{{ route('admin.weddings.reminders.index', $wedding) }}" class="btn btn-warning btn-sm w-100 mt-3">
                    <i class="bi bi-bell-fill me-1"></i>Lancer une relance groupée
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection
