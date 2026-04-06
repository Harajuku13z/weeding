@extends('layouts.admin')
@section('title', $wedding->getCoupleName())
@section('breadcrumb')
<a href="{{ route('admin.weddings.index') }}">Mariages</a> <i class="bi bi-chevron-right mx-2"></i>
<span>{{ $wedding->getCoupleName() }}</span>
@endsection
@section('topbar-actions')
<a href="{{ route('wedding.public', $wedding->slug) }}" target="_blank" class="btn btn-sm btn-outline-primary">
    <i class="bi bi-box-arrow-up-right me-1"></i>Voir l'invitation
</a>
<a href="{{ route('admin.weddings.edit', $wedding) }}" class="btn btn-sm btn-primary-custom">
    <i class="bi bi-pencil me-1"></i>Modifier
</a>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="admin-card text-center">
            @if($wedding->couple_photo)
            <img src="{{ Storage::url($wedding->couple_photo) }}" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover">
            @else
            <div class="wedding-avatar-lg mb-3"><i class="bi bi-heart-fill"></i></div>
            @endif
            <h2 class="font-serif">{{ $wedding->getCoupleName() }}</h2>
            <p class="text-muted"><i class="bi bi-calendar-heart me-1"></i>{{ $wedding->getWeddingDateFormatted() }}</p>
            @if($wedding->quote)
            <p class="fst-italic text-muted">« {{ $wedding->quote }} »</p>
            @endif
            <div class="mt-3">
                @if($wedding->is_published)
                <span class="badge bg-success fs-6"><i class="bi bi-globe me-1"></i>Publié</span>
                @else
                <span class="badge bg-secondary fs-6"><i class="bi bi-pencil me-1"></i>Brouillon</span>
                @endif
            </div>
            <div class="mt-3 d-flex justify-content-center gap-2">
                <form method="POST" action="{{ route('admin.weddings.publish', $wedding) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-{{ $wedding->is_published ? 'warning' : 'success' }}">
                        {{ $wedding->is_published ? 'Dépublier' : 'Publier' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.weddings.duplicate', $wedding) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-copy"></i> Dupliquer
                    </button>
                </form>
            </div>
        </div>

        <!-- Lien public -->
        <div class="admin-card mt-3">
            <div class="card-header-custom"><h4 class="card-title-custom">Lien public</h4></div>
            <div class="card-body-custom">
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm"
                           value="{{ route('wedding.public', $wedding->slug) }}" readonly id="publicUrl">
                    <button class="btn btn-outline-secondary btn-sm" onclick="copyUrl()">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>
                <p class="text-muted small mt-2">URL d'invitation : /mariage/{{ $wedding->slug }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Stats RSVP -->
        <div class="stats-grid stats-grid-sm mb-4">
            <div class="stat-card stat-accepted">
                <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['accepted'] }}</div>
                    <div class="stat-label">Acceptés</div>
                </div>
            </div>
            <div class="stat-card stat-declined">
                <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['declined'] }}</div>
                    <div class="stat-label">Déclinés</div>
                </div>
            </div>
            <div class="stat-card stat-maybe">
                <div class="stat-icon"><i class="bi bi-question-circle-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['maybe'] }}</div>
                    <div class="stat-label">À confirmer</div>
                </div>
            </div>
            <div class="stat-card stat-pending">
                <div class="stat-icon"><i class="bi bi-clock-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['pending'] }}</div>
                    <div class="stat-label">En attente</div>
                </div>
            </div>
        </div>

        <!-- Modules -->
        <div class="admin-card">
            <div class="card-header-custom">
                <h4 class="card-title-custom"><i class="bi bi-grid-fill me-2"></i>Modules</h4>
            </div>
            <div class="card-body-custom">
                <div class="modules-grid">
                    @php
                    $modules = [
                        ['route' => route('admin.weddings.guests.index', $wedding), 'icon' => 'bi-people-fill', 'label' => 'Invités', 'count' => $wedding->guests->count()],
                        ['route' => route('admin.weddings.program.index', $wedding), 'icon' => 'bi-calendar-event-fill', 'label' => 'Programme', 'count' => $wedding->programItems->count()],
                        ['route' => route('admin.weddings.gallery.index', $wedding), 'icon' => 'bi-images', 'label' => 'Galerie', 'count' => $wedding->galleryItems->count()],
                        ['route' => route('admin.weddings.venues.index', $wedding), 'icon' => 'bi-geo-alt-fill', 'label' => 'Lieux', 'count' => null],
                        ['route' => route('admin.weddings.theme.edit', $wedding), 'icon' => 'bi-palette-fill', 'label' => 'Thème', 'count' => null],
                        ['route' => route('admin.weddings.gifts.index', $wedding), 'icon' => 'bi-gift-fill', 'label' => 'Cadeaux', 'count' => null],
                        ['route' => route('admin.weddings.rules.index', $wedding), 'icon' => 'bi-shield-check-fill', 'label' => 'Règles', 'count' => null],
                        ['route' => route('admin.weddings.reminders.index', $wedding), 'icon' => 'bi-send-fill', 'label' => 'Relances', 'count' => null],
                    ];
                    @endphp
                    @foreach($modules as $module)
                    <a href="{{ $module['route'] }}" class="module-card">
                        <i class="bi {{ $module['icon'] }}"></i>
                        <span>{{ $module['label'] }}</span>
                        @if($module['count'] !== null)
                        <span class="module-count">{{ $module['count'] }}</span>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyUrl() {
    const url = document.getElementById('publicUrl');
    url.select();
    document.execCommand('copy');
    alert('URL copiée !');
}
</script>
@endpush
@endsection
