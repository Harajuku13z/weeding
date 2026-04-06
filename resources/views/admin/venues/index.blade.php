@extends('layouts.admin')
@section('title', 'Lieux')
@section('breadcrumb')
<a href="{{ route('admin.weddings.show', $wedding) }}">{{ $wedding->getCoupleName() }}</a>
<i class="bi bi-chevron-right mx-2"></i><span>Lieux</span>
@endsection
@section('topbar-actions')
<a href="{{ route('admin.weddings.venues.create', $wedding) }}" class="btn btn-sm btn-primary-custom">
    <i class="bi bi-plus-circle me-1"></i>Ajouter un lieu
</a>
@endsection

@section('content')
<div class="admin-card">
    @forelse($venues as $venue)
    <div class="d-flex align-items-start gap-3 p-4 border-bottom">
        @if($venue->photo)
        <img src="{{ Storage::url($venue->photo) }}" style="width:80px;height:80px;object-fit:cover;border-radius:10px;flex-shrink:0">
        @else
        <div style="width:80px;height:80px;background:var(--color-secondary);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi bi-building" style="font-size:28px;color:var(--color-primary)"></i>
        </div>
        @endif
        <div class="flex-grow-1">
            <h4 class="font-serif mb-1">{{ $venue->name }}</h4>
            <div class="text-muted small">
                @if($venue->address) <i class="bi bi-geo-alt me-1"></i>{{ $venue->address }} @endif
                @if($venue->city) · {{ $venue->city }} @endif
            </div>
            <div class="mt-2 d-flex gap-2">
                @if($venue->google_maps_url) <a href="{{ $venue->google_maps_url }}" target="_blank" class="badge bg-primary text-white text-decoration-none"><i class="bi bi-map me-1"></i>Maps</a> @endif
                @if($venue->waze_url) <a href="{{ $venue->waze_url }}" target="_blank" class="badge bg-info text-white text-decoration-none"><i class="bi bi-signpost me-1"></i>Waze</a> @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.weddings.venues.edit', [$wedding, $venue]) }}" class="btn btn-xs btn-outline-secondary"><i class="bi bi-pencil"></i></a>
            <form method="POST" action="{{ route('admin.weddings.venues.destroy', [$wedding, $venue]) }}" onsubmit="return confirm('Supprimer ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
        </div>
    </div>
    @empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-geo-alt fs-1 d-block mb-2" style="color:var(--color-primary)"></i>
        <h4 class="font-serif">Aucun lieu ajouté</h4>
        <a href="{{ route('admin.weddings.venues.create', $wedding) }}" class="btn btn-primary-custom mt-3">
            <i class="bi bi-plus-circle me-2"></i>Ajouter un lieu
        </a>
    </div>
    @endforelse
</div>
@endsection
