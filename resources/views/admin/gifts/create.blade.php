@extends('layouts.admin')
@section('title', 'Ajouter un cadeau')
@section('breadcrumb') <a href="{{ route('admin.weddings.gifts.index', $wedding) }}">Cadeaux</a> <i class="bi bi-chevron-right mx-2"></i><span>Ajouter</span> @endsection

@section('content')
<div class="admin-card" style="max-width:700px;margin:0 auto">
    <div class="card-body-custom">
        <form method="POST" action="{{ route('admin.weddings.gifts.store', $wedding) }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Nom *</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
                <div class="col-md-6">
                    <label class="form-label">Catégorie</label>
                    <select name="gift_category_id" class="form-select">
                        <option value="">Sans catégorie</option>
                        @foreach($categories as $cat) <option value="{{ $cat->id }}" {{ old('gift_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-6"><label class="form-label">Nouvelle catégorie</label><input type="text" name="new_category" class="form-control" placeholder="Créer une nouvelle catégorie"></div>
                <div class="col-md-4"><label class="form-label">Prix (€)</label><input type="number" name="price" class="form-control" value="{{ old('price') }}" step="0.01"></div>
                <div class="col-md-4"><label class="form-label">Lien externe</label><input type="url" name="external_link" class="form-control" value="{{ old('external_link') }}" placeholder="https://..."></div>
                <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea></div>
                <div class="col-12"><label class="form-label">Instructions</label><textarea name="instructions" class="form-control" rows="2" placeholder="Comment offrir ce cadeau...">{{ old('instructions') }}</textarea></div>
                <div class="col-12"><label class="form-label">Photo</label><input type="file" name="image" class="form-control" accept="image/*"></div>
                <div class="col-md-6"><div class="form-check form-switch"><input type="hidden" name="free_contribution" value="0"><input class="form-check-input" type="checkbox" name="free_contribution" value="1" id="fc" {{ old('free_contribution') ? 'checked' : '' }}><label class="form-check-label" for="fc">Participation libre</label></div></div>
                <div class="col-md-6"><div class="form-check form-switch"><input type="hidden" name="is_reserved" value="0"><input class="form-check-input" type="checkbox" name="is_reserved" value="1" id="res" {{ old('is_reserved') ? 'checked' : '' }}><label class="form-check-label" for="res">Déjà réservé</label></div></div>
            </div>
            <div class="d-flex gap-3 mt-4"><button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-circle me-2"></i>Ajouter</button><a href="{{ route('admin.weddings.gifts.index', $wedding) }}" class="btn btn-outline-secondary">Annuler</a></div>
        </form>
    </div>
</div>
@endsection
