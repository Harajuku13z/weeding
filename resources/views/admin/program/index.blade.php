@extends('layouts.admin')
@section('title', 'Programme')
@section('breadcrumb')
<a href="{{ route('admin.weddings.show', $wedding) }}">{{ $wedding->getCoupleName() }}</a>
<i class="bi bi-chevron-right mx-2"></i><span>Programme</span>
@endsection
@section('topbar-actions')
<a href="{{ route('admin.weddings.program.create', $wedding) }}" class="btn btn-sm btn-primary-custom">
    <i class="bi bi-plus-circle me-1"></i>Ajouter une étape
</a>
@endsection

@section('content')
<div class="admin-card">
    <div class="card-body-custom p-0">
        @forelse($items as $item)
        <div class="d-flex align-items-center gap-3 p-3 border-bottom">
            <div class="d-flex align-items-center justify-content-center"
                 style="width:44px;height:44px;background:var(--color-secondary);border-radius:10px;flex-shrink:0">
                <i class="bi {{ $item->icon ?? 'bi-star-fill' }}" style="color:var(--color-primary);font-size:18px"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold">{{ $item->title }}</div>
                <div class="text-muted small">{{ $item->getDateTimeFormatted() }}
                    @if($item->venue_name) · {{ $item->venue_name }} @endif
                </div>
            </div>
            <div>
                @if(!$item->is_published) <span class="badge bg-secondary">Masqué</span> @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.weddings.program.edit', [$wedding, $item]) }}" class="btn btn-xs btn-outline-secondary">
                    <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="{{ route('admin.weddings.program.destroy', [$wedding, $item]) }}"
                      onsubmit="return confirm('Supprimer cette étape ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
            Aucune étape dans le programme
        </div>
        @endforelse
    </div>
</div>
@endsection
