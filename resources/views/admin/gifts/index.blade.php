@extends('layouts.admin')
@section('title', 'Cadeaux')
@section('breadcrumb') <a href="{{ route('admin.weddings.show', $wedding) }}">{{ $wedding->getCoupleName() }}</a> <i class="bi bi-chevron-right mx-2"></i><span>Cadeaux</span> @endsection
@section('topbar-actions')
<a href="{{ route('admin.weddings.gifts.create', $wedding) }}" class="btn btn-sm btn-primary-custom"><i class="bi bi-plus-circle me-1"></i>Ajouter</a>
@endsection

@section('content')
@foreach($categories as $cat)
<div class="admin-card mb-4">
    <div class="card-header-custom"><h3 class="card-title-custom font-serif">{{ $cat->name }}</h3></div>
    <div class="card-body-custom p-0">
        @foreach($cat->items as $gift)
        <div class="d-flex align-items-center gap-3 p-3 border-bottom">
            @if($gift->image) <img src="{{ Storage::url($gift->image) }}" style="width:50px;height:50px;object-fit:cover;border-radius:8px"> @else <div style="width:50px;height:50px;background:var(--color-secondary);border-radius:8px;display:flex;align-items:center;justify-content:center"><i class="bi bi-gift" style="color:var(--color-primary)"></i></div> @endif
            <div class="flex-grow-1">
                <div class="fw-semibold">{{ $gift->name }}</div>
                @if($gift->price) <div class="text-muted small">{{ number_format($gift->price, 0) }} €</div> @endif
                @if($gift->is_reserved) <span class="badge bg-success">Réservé</span> @endif
                @if($gift->free_contribution) <span class="badge bg-info">Libre</span> @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.weddings.gifts.edit', [$wedding, $gift]) }}" class="btn btn-xs btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="{{ route('admin.weddings.gifts.destroy', [$wedding, $gift]) }}" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button type="submit" class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button></form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach
@if($categories->isEmpty() && $uncategorized->isEmpty())
<div class="admin-card text-center py-5"><i class="bi bi-gift fs-1 d-block mb-3" style="color:var(--color-primary)"></i><h4 class="font-serif">Aucun cadeau ajouté</h4><a href="{{ route('admin.weddings.gifts.create', $wedding) }}" class="btn btn-primary-custom mt-3"><i class="bi bi-plus-circle me-2"></i>Ajouter un cadeau</a></div>
@endif
@endsection
