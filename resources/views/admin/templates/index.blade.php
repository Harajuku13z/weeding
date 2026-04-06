@extends('layouts.admin')
@section('title', 'Modèles de messages')
@section('breadcrumb') <a href="{{ route('admin.weddings.show', $wedding) }}">{{ $wedding->getCoupleName() }}</a> <i class="bi bi-chevron-right mx-2"></i><span>Modèles</span> @endsection
@section('topbar-actions') <a href="{{ route('admin.weddings.templates.create', $wedding) }}" class="btn btn-sm btn-primary-custom"><i class="bi bi-plus-circle me-1"></i>Ajouter</a> @endsection

@section('content')
<div class="admin-card">
    @forelse($templates as $template)
    <div class="d-flex align-items-start gap-3 p-3 border-bottom">
        <div class="flex-grow-1">
            <div class="fw-semibold">{{ $template->name }}</div>
            <div class="d-flex gap-2 mt-1">
                <span class="badge bg-secondary">{{ ucfirst($template->channel) }}</span>
                <span class="badge bg-primary-subtle text-primary-emphasis">{{ ucfirst($template->type) }}</span>
            </div>
            @if($template->subject) <div class="text-muted small mt-1">Sujet : {{ $template->subject }}</div> @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.weddings.templates.edit', [$wedding, $template]) }}" class="btn btn-xs btn-outline-secondary"><i class="bi bi-pencil"></i></a>
            <form method="POST" action="{{ route('admin.weddings.templates.destroy', [$wedding, $template]) }}" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button type="submit" class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button></form>
        </div>
    </div>
    @empty
    <div class="text-center py-5 text-muted"><i class="bi bi-file-text fs-1 d-block mb-2" style="color:var(--color-primary)"></i><h4 class="font-serif">Aucun modèle de message</h4><a href="{{ route('admin.weddings.templates.create', $wedding) }}" class="btn btn-primary-custom mt-3"><i class="bi bi-plus-circle me-2"></i>Créer un modèle</a></div>
    @endforelse
</div>
@endsection
