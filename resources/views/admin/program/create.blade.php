@extends('layouts.admin')
@section('title', 'Ajouter une étape')
@section('breadcrumb')
<a href="{{ route('admin.weddings.program.index', $wedding) }}">Programme</a>
<i class="bi bi-chevron-right mx-2"></i><span>Ajouter</span>
@endsection

@section('content')
<div class="admin-card" style="max-width:700px;margin:0 auto">
    <div class="card-header-custom">
        <h2 class="card-title-custom font-serif">Nouvelle étape du programme</h2>
    </div>
    <div class="card-body-custom">
        <form method="POST" action="{{ route('admin.weddings.program.store', $wedding) }}">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Titre *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="Cérémonie civile, Vin d'honneur...">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date</label>
                    <input type="text" name="date" class="form-control datepicker" value="{{ old('date') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Heure</label>
                    <input type="text" name="time" class="form-control timepicker" value="{{ old('time') }}" placeholder="14:00">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nom du lieu</label>
                    <input type="text" name="venue_name" class="form-control" value="{{ old('venue_name') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Icône Bootstrap</label>
                    <input type="text" name="icon" class="form-control" value="{{ old('icon', 'bi-star-fill') }}" placeholder="bi-star-fill">
                    <div class="form-text">Voir <a href="https://icons.getbootstrap.com" target="_blank">Bootstrap Icons</a></div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ordre</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_published" value="0">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="is_pub" checked>
                        <label class="form-check-label" for="is_pub">Visible sur l'invitation</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-circle me-2"></i>Ajouter</button>
                <a href="{{ route('admin.weddings.program.index', $wedding) }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
flatpickr('.datepicker', { locale: 'fr', dateFormat: 'Y-m-d', allowInput: true });
flatpickr('.timepicker', { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true });
</script>
@endpush
