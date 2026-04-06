@extends('layouts.admin')
@section('title', 'Modifier l\'élément')
@section('breadcrumb')
<a href="{{ route('admin.weddings.gallery.index', $wedding) }}">Un peu de nous</a>
<i class="bi bi-chevron-right mx-2"></i><span>Modifier</span>
@endsection

@section('content')
<div class="admin-card" style="max-width:600px;margin:0 auto">
    <div class="card-body-custom">
        <div style="text-align:center;margin-bottom:20px">
            @if($gallery->isVideo())
                <video src="{{ Storage::url($gallery->video) }}" controls style="max-height:300px;border-radius:12px;max-width:100%"></video>
                <div class="small text-muted mt-1">Vidéo MP4</div>
            @else
                <img src="{{ Storage::url($gallery->image) }}" alt="" style="max-height:300px;border-radius:12px;max-width:100%;object-fit:contain">
            @endif
        </div>
        <form method="POST" action="{{ route('admin.weddings.gallery.update', [$wedding, $gallery]) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Légende</label>
                <input type="text" name="caption" class="form-control" value="{{ old('caption', $gallery->caption) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Remplacer par une photo</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">
                <label class="form-label">Ou par une vidéo MP4</label>
                <input type="file" name="video" class="form-control" accept="video/mp4,.mp4">
            </div>
            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-circle me-2"></i>Enregistrer</button>
                <a href="{{ route('admin.weddings.gallery.index', $wedding) }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
