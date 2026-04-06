@extends('layouts.admin')
@section('title', 'Mes mariages')
@section('breadcrumb') <span>Mes mariages</span> @endsection
@section('topbar-actions')
<a href="{{ route('admin.weddings.create') }}" class="btn btn-sm btn-primary-custom">
    <i class="bi bi-plus-circle me-1"></i>Nouveau mariage
</a>
@endsection

@section('content')
<div class="admin-card">
    <div class="card-header-custom">
        <h2 class="card-title-custom font-serif">Mes invitations de mariage</h2>
    </div>
    <div class="card-body-custom">
        @forelse($weddings as $wedding)
        <div class="wedding-list-item">
            <div class="wedding-list-couple">
                <div class="wedding-avatar"><i class="bi bi-heart-fill"></i></div>
                <div>
                    <h4 class="wedding-couple-name font-serif">{{ $wedding->getCoupleName() }}</h4>
                    <div class="text-muted small">
                        <i class="bi bi-calendar3 me-1"></i>{{ $wedding->getWeddingDateFormatted() }}
                        &nbsp;·&nbsp;
                        <i class="bi bi-people me-1"></i>{{ $wedding->guests()->count() }} invités
                        &nbsp;·&nbsp;
                        <span class="badge bg-{{ $wedding->is_published ? 'success' : 'secondary' }} badge-sm">
                            {{ $wedding->is_published ? 'Publié' : 'Brouillon' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="wedding-list-actions">
                @php $stats = $wedding->getRsvpStats(); @endphp
                <div class="rsvp-mini-stats">
                    <span class="text-success" title="Acceptés"><i class="bi bi-check-circle-fill"></i> {{ $stats['accepted'] }}</span>
                    <span class="text-danger" title="Déclinés"><i class="bi bi-x-circle-fill"></i> {{ $stats['declined'] }}</span>
                    <span class="text-warning" title="À confirmer"><i class="bi bi-question-circle-fill"></i> {{ $stats['maybe'] }}</span>
                </div>
                <a href="{{ route('admin.weddings.show', $wedding) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('admin.weddings.edit', $wedding) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="{{ route('admin.weddings.destroy', $wedding) }}" class="d-inline"
                      onsubmit="return confirm('Supprimer ce mariage ? Cette action est irréversible.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
        @empty
        <div class="empty-state text-center py-5">
            <i class="bi bi-heart" style="font-size:3rem;color:var(--color-primary)"></i>
            <h4 class="mt-3 font-serif">Aucun mariage créé</h4>
            <a href="{{ route('admin.weddings.create') }}" class="btn btn-primary-custom mt-3">
                <i class="bi bi-plus-circle me-2"></i>Créer mon premier mariage
            </a>
        </div>
        @endforelse
    </div>
</div>
{{ $weddings->links() }}
@endsection
