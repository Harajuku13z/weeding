@extends('layouts.admin')
@section('title', 'Galerie')
@section('breadcrumb')
<a href="{{ route('admin.weddings.show', $wedding) }}">{{ $wedding->getCoupleName() }}</a>
<i class="bi bi-chevron-right mx-2"></i><span>Galerie</span>
@endsection
@section('topbar-actions')
<a href="{{ route('admin.weddings.gallery.create', $wedding) }}" class="btn btn-sm btn-primary-custom">
    <i class="bi bi-plus-circle me-1"></i>Ajouter photos / vidéos
</a>
@endsection

@section('content')
@if($items->count())
<div class="admin-card">
    <div class="card-header-custom">
        <h3 class="card-title-custom">Un peu de nous — {{ $items->count() }} élément(s)</h3>
    </div>
    <div class="card-body-custom">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px">
            @foreach($items as $item)
            <div style="position:relative;border-radius:10px;overflow:hidden;aspect-ratio:3/4;background:#f0e8e0">
                @if($item->isVideo())
                <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:linear-gradient(135deg,#2a2319,#1a1612);color:rgba(255,255,255,.8)">
                    <i class="bi bi-play-circle-fill" style="font-size:2.5rem"></i>
                    <span class="small mt-1">Vidéo MP4</span>
                </div>
                @else
                <img src="{{ Storage::url($item->image) }}" alt="" style="width:100%;height:100%;object-fit:cover">
                @endif
                <div style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(to top,rgba(0,0,0,.6),transparent);padding:10px;display:flex;justify-content:flex-end;gap:6px">
                    <a href="{{ route('admin.weddings.gallery.edit', [$wedding, $item]) }}"
                       style="background:rgba(255,255,255,.9);color:#3d2b1f;border:none;width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:12px">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.weddings.gallery.destroy', [$wedding, $item]) }}"
                          onsubmit="return confirm('Supprimer cet élément ?')" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:rgba(239,68,68,.9);color:white;border:none;width:28px;height:28px;border-radius:6px;cursor:pointer;font-size:12px">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@else
<div class="admin-card text-center py-5">
    <i class="bi bi-images fs-1 d-block mb-3" style="color:var(--color-primary)"></i>
    <h4 class="font-serif">Aucun élément dans « Un peu de nous »</h4>
    <p class="text-muted">Ajoutez des photos et des vidéos MP4 pour cette section.</p>
    <a href="{{ route('admin.weddings.gallery.create', $wedding) }}" class="btn btn-primary-custom mt-3">
        <i class="bi bi-plus-circle me-2"></i>Ajouter des photos et vidéos
    </a>
</div>
@endif
@endsection
