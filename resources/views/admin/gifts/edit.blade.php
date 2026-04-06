@extends('layouts.admin')
@section('title', 'Modifier le cadeau')
@section('breadcrumb') <a href="{{ route('admin.weddings.gifts.index', $wedding) }}">Cadeaux</a> <i class="bi bi-chevron-right mx-2"></i><span>Modifier</span> @endsection

@section('content')
<div class="admin-card" style="max-width:700px;margin:0 auto">
    <div class="card-body-custom">
        <form method="POST" action="{{ route('admin.weddings.gifts.update', [$wedding, $gift]) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Nom *</label><input type="text" name="name" class="form-control" value="{{ old('name', $gift->name) }}" required></div>
                <div class="col-md-6"><label class="form-label">Catégorie</label><select name="gift_category_id" class="form-select"><option value="">Sans catégorie</option>@foreach($categories as $cat)<option value="{{ $cat->id }}" {{ $gift->gift_category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>@endforeach</select></div>
                <div class="col-md-4"><label class="form-label">Prix (€)</label><input type="number" name="price" class="form-control" value="{{ old('price', $gift->price) }}" step="0.01"></div>
                <div class="col-md-6"><label class="form-label">Lien externe</label><input type="url" name="external_link" class="form-control" value="{{ old('external_link', $gift->external_link) }}"></div>
                <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2">{{ old('description', $gift->description) }}</textarea></div>
                <div class="col-12">
                    <label class="form-label">Photo</label>
                    @if($gift->image) <img src="{{ Storage::url($gift->image) }}" style="height:60px;border-radius:8px;display:block;margin-bottom:8px"> @endif
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <div class="col-md-6"><div class="form-check form-switch"><input type="hidden" name="free_contribution" value="0"><input class="form-check-input" type="checkbox" name="free_contribution" value="1" id="fc" {{ $gift->free_contribution ? 'checked' : '' }}><label class="form-check-label" for="fc">Participation libre</label></div></div>
                <div class="col-md-6"><div class="form-check form-switch"><input type="hidden" name="is_reserved" value="0"><input class="form-check-input" type="checkbox" name="is_reserved" value="1" id="res" {{ $gift->is_reserved ? 'checked' : '' }}><label class="form-check-label" for="res">Réservé</label></div></div>
            </div>
            <div class="d-flex gap-3 mt-4"><button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-circle me-2"></i>Enregistrer</button><a href="{{ route('admin.weddings.gifts.index', $wedding) }}" class="btn btn-outline-secondary">Annuler</a></div>
        </form>
    </div>
</div>
@endsection
