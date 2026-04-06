@extends('layouts.admin')
@section('title', 'Modifier le lieu')
@section('breadcrumb') <a href="{{ route('admin.weddings.venues.index', $wedding) }}">Lieux</a> <i class="bi bi-chevron-right mx-2"></i><span>Modifier</span> @endsection

@section('content')
<div class="admin-card" style="max-width:700px;margin:0 auto">
    <div class="card-header-custom"><h2 class="card-title-custom font-serif">{{ $venue->name }}</h2></div>
    <div class="card-body-custom">
        <form method="POST" action="{{ route('admin.weddings.venues.update', [$wedding, $venue]) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Nom *</label><input type="text" name="name" class="form-control" value="{{ old('name', $venue->name) }}" required></div>
                <div class="col-md-8"><label class="form-label">Adresse</label><input type="text" name="address" class="form-control" value="{{ old('address', $venue->address) }}"></div>
                <div class="col-md-4"><label class="form-label">Ville</label><input type="text" name="city" class="form-control" value="{{ old('city', $venue->city) }}"></div>
                <div class="col-md-6"><label class="form-label">Google Maps</label><input type="url" name="google_maps_url" class="form-control" value="{{ old('google_maps_url', $venue->google_maps_url) }}"></div>
                <div class="col-md-6"><label class="form-label">Waze</label><input type="url" name="waze_url" class="form-control" value="{{ old('waze_url', $venue->waze_url) }}"></div>
                <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3">{{ old('description', $venue->description) }}</textarea></div>
                <div class="col-12">
                    <label class="form-label">Photo</label>
                    @if($venue->photo) <img src="{{ Storage::url($venue->photo) }}" style="height:80px;border-radius:8px;display:block;margin-bottom:8px"> @endif
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-circle me-2"></i>Enregistrer</button>
                <a href="{{ route('admin.weddings.venues.index', $wedding) }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
