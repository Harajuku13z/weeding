@extends('layouts.admin')
@section('title', 'Ajouter des photos et vidéos')
@section('breadcrumb')
<a href="{{ route('admin.weddings.gallery.index', $wedding) }}">Un peu de nous</a>
<i class="bi bi-chevron-right mx-2"></i><span>Ajouter</span>
@endsection

@section('content')
<div class="admin-card" style="max-width:700px;margin:0 auto">
    <div class="card-header-custom">
        <h2 class="card-title-custom font-serif">Ajouter des photos et vidéos</h2>
        <small class="text-muted ms-2">Ils apparaissent dans la section « Un peu de nous » sur l'invitation.</small>
    </div>
    <div class="card-body-custom">
        @if($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.weddings.gallery.store', $wedding) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="form-label">Photos</label>
                <input type="file" name="images[]" class="form-control @error('images') is-invalid @enderror" accept="image/*" multiple>
                <div class="form-text">JPG, PNG, WebP. Max 5 Mo par photo.</div>
                @error('images') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-4">
                <label class="form-label">Vidéos MP4</label>
                <input type="file" name="videos[]" class="form-control" accept="video/mp4,.mp4" multiple>
                <div class="form-text">Format MP4 uniquement. Max 100 Mo par vidéo.</div>
            </div>
            <p class="text-muted small mb-3">Ajoutez au moins une photo ou une vidéo.</p>
            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary-custom"><i class="bi bi-upload me-2"></i>Enregistrer</button>
                <a href="{{ route('admin.weddings.gallery.index', $wedding) }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
