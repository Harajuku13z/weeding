@extends('layouts.admin')
@section('title', 'Ajouter une règle')
@section('breadcrumb') <a href="{{ route('admin.weddings.rules.index', $wedding) }}">Règles</a> <i class="bi bi-chevron-right mx-2"></i><span>Ajouter</span> @endsection

@section('content')
<div class="admin-card" style="max-width:600px;margin:0 auto">
    <div class="card-body-custom">
        <form method="POST" action="{{ route('admin.weddings.rules.store', $wedding) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Type *</label>
                    <select name="type" class="form-select" required>
                        <option value="allowed">Autorisé / Bienvenu</option>
                        <option value="forbidden">Interdit / À éviter</option>
                        <option value="recommendation">Recommandation</option>
                    </select>
                </div>
                <div class="col-md-6"><label class="form-label">Icône Bootstrap</label><input type="text" name="icon" class="form-control" value="{{ old('icon', 'bi-check-circle-fill') }}" placeholder="bi-check-circle-fill"></div>
                <div class="col-12"><label class="form-label">Titre *</label><input type="text" name="title" class="form-control" value="{{ old('title') }}" required></div>
                <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea></div>
                <div class="col-md-4"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}"></div>
                <div class="col-12"><div class="form-check form-switch"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="ia" checked><label class="form-check-label" for="ia">Visible</label></div></div>
            </div>
            <div class="d-flex gap-3 mt-4"><button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-circle me-2"></i>Ajouter</button><a href="{{ route('admin.weddings.rules.index', $wedding) }}" class="btn btn-outline-secondary">Annuler</a></div>
        </form>
    </div>
</div>
@endsection
