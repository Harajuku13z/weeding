@extends('layouts.admin')
@section('title', 'Ajouter un lieu')
@section('breadcrumb') <a href="{{ route('admin.weddings.venues.index', $wedding) }}">Lieux</a> <i class="bi bi-chevron-right mx-2"></i><span>Ajouter</span> @endsection

@section('content')
<div class="admin-card" style="max-width:700px;margin:0 auto">
    <div class="card-header-custom"><h2 class="card-title-custom font-serif">Nouveau lieu</h2></div>
    <div class="card-body-custom">
        <form method="POST" action="{{ route('admin.weddings.venues.store', $wedding) }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Nom *</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Château de Vaux-le-Pénil"></div>
                <div class="col-md-8"><label class="form-label">Adresse</label><input type="text" name="address" class="form-control" value="{{ old('address') }}"></div>
                <div class="col-md-4"><label class="form-label">Ville</label><input type="text" name="city" class="form-control" value="{{ old('city') }}"></div>
                <div class="col-md-6"><label class="form-label">Lien Google Maps</label><input type="url" name="google_maps_url" class="form-control" value="{{ old('google_maps_url') }}" placeholder="https://maps.google.com/..."></div>
                <div class="col-md-6"><label class="form-label">Lien Waze</label><input type="url" name="waze_url" class="form-control" value="{{ old('waze_url') }}" placeholder="https://waze.com/..."></div>
                <div class="col-md-6"><label class="form-label">Type</label><input type="text" name="type" class="form-control" value="{{ old('type', 'réception') }}" placeholder="cérémonie, réception..."></div>
                <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea></div>
                <div class="col-12"><label class="form-label">Photo</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
            </div>
            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-circle me-2"></i>Ajouter</button>
                <a href="{{ route('admin.weddings.venues.index', $wedding) }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
