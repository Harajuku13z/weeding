@extends('layouts.admin')
@section('title', 'Invités')
@section('breadcrumb')
<a href="{{ route('admin.weddings.show', $wedding) }}">{{ $wedding->getCoupleName() }}</a>
<i class="bi bi-chevron-right mx-2"></i><span>Invités</span>
@endsection
@section('topbar-actions')
<a href="{{ route('admin.weddings.guests.export', $wedding) }}" class="btn btn-sm btn-outline-secondary">
    <i class="bi bi-download me-1"></i>CSV
</a>
<a href="{{ route('admin.weddings.guests.create', $wedding) }}" class="btn btn-sm btn-primary-custom">
    <i class="bi bi-person-plus-fill me-1"></i>Ajouter
</a>
@endsection

@section('content')
<!-- Filtres -->
<div class="admin-card mb-3">
    <div class="card-body-custom py-3">
        <form method="GET" class="d-flex gap-3 flex-wrap align-items-end">
            <div class="flex-grow-1" style="min-width:200px">
                <label class="form-label form-label-sm">Rechercher</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="Nom, prénom, email...">
            </div>
            <div>
                <label class="form-label form-label-sm">Statut</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Acceptés</option>
                    <option value="declined" {{ request('status') === 'declined' ? 'selected' : '' }}>Déclinés</option>
                    <option value="maybe" {{ request('status') === 'maybe' ? 'selected' : '' }}>À confirmer</option>
                </select>
            </div>
            <button type="submit" class="btn btn-sm btn-primary-custom">Filtrer</button>
            @if(request()->hasAny(['search', 'status', 'tag']))
            <a href="{{ route('admin.weddings.guests.index', $wedding) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x"></i>
            </a>
            @endif
        </form>
    </div>
</div>

<!-- Stats rapides -->
<div class="stats-grid stats-grid-sm mb-3">
    <div class="stat-card stat-total"><div class="stat-icon"><i class="bi bi-people"></i></div><div class="stat-content"><div class="stat-number">{{ $stats['total'] }}</div><div class="stat-label">Total</div></div></div>
    <div class="stat-card stat-accepted"><div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div><div class="stat-content"><div class="stat-number">{{ $stats['accepted'] }}</div><div class="stat-label">Oui</div></div></div>
    <div class="stat-card stat-declined"><div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div><div class="stat-content"><div class="stat-number">{{ $stats['declined'] }}</div><div class="stat-label">Non</div></div></div>
    <div class="stat-card stat-maybe"><div class="stat-icon"><i class="bi bi-question-circle-fill"></i></div><div class="stat-content"><div class="stat-number">{{ $stats['maybe'] }}</div><div class="stat-label">À confirmer</div></div></div>
    <div class="stat-card stat-pending"><div class="stat-icon"><i class="bi bi-clock"></i></div><div class="stat-content"><div class="stat-number">{{ $stats['pending'] }}</div><div class="stat-label">En attente</div></div></div>
</div>

<!-- Table -->
<div class="admin-card">
    <div class="card-body-custom p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Invité</th>
                        <th>Contact</th>
                        <th>Statut RSVP</th>
                        <th>Accompagnants</th>
                        <th>Code</th>
                        <th>Répondu le</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guests as $guest)
                    <tr class="{{ $guest->is_suspended ? 'table-secondary opacity-50' : '' }}">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="guest-avatar-sm">{{ substr($guest->first_name, 0, 1) }}</div>
                                <div>
                                    <div class="fw-semibold">{{ $guest->full_name }}</div>
                                    @if($guest->dietary_restrictions)
                                    <small class="text-warning"><i class="bi bi-exclamation-circle"></i> Restrictions alimentaires</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($guest->email) <div class="small text-muted"><i class="bi bi-envelope"></i> {{ $guest->email }}</div> @endif
                            @if($guest->phone) <div class="small text-muted"><i class="bi bi-phone"></i> {{ $guest->phone }}</div> @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $guest->status_color }}">{{ $guest->status_label }}</span>
                        </td>
                        <td>
                            @if($guest->companions_allowed)
                            <span class="text-muted">{{ $guest->companions_count }} / {{ $guest->max_companions }}</span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <code class="small">{{ $guest->invitation_code }}</code>
                                <a href="{{ route('guest.personal', $guest->invitation_code) }}" target="_blank"
                                   class="text-muted" title="Voir la page personnelle" style="font-size:12px">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </div>
                        </td>
                        <td class="text-muted small">
                            {{ $guest->rsvp_at ? $guest->rsvp_at->format('d/m/Y H:i') : '—' }}
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('admin.weddings.guests.show', [$wedding, $guest]) }}"
                                   class="btn btn-xs btn-outline-primary" title="Voir"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.weddings.guests.edit', [$wedding, $guest]) }}"
                                   class="btn btn-xs btn-outline-secondary" title="Modifier"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.weddings.guests.suspend', [$wedding, $guest]) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-outline-{{ $guest->is_suspended ? 'success' : 'warning' }}"
                                            title="{{ $guest->is_suspended ? 'Réactiver' : 'Suspendre' }}">
                                        <i class="bi bi-{{ $guest->is_suspended ? 'play-circle' : 'pause-circle' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.weddings.guests.destroy', [$wedding, $guest]) }}" class="d-inline"
                                      onsubmit="return confirm('Supprimer {{ $guest->full_name }} ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>Aucun invité trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{ $guests->withQueryString()->links() }}
@endsection
