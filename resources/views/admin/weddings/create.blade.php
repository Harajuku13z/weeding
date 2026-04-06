@extends('layouts.admin')
@section('title', 'Nouveau mariage')
@section('breadcrumb')
<a href="{{ route('admin.weddings.index') }}">Mariages</a> <i class="bi bi-chevron-right mx-2"></i> <span>Nouveau</span>
@endsection

@section('content')
<div class="admin-card" style="max-width:700px;margin:0 auto">
    <div class="card-header-custom">
        <h2 class="card-title-custom font-serif"><i class="bi bi-heart me-2" style="color:var(--color-primary)"></i>Créer une invitation de mariage</h2>
    </div>
    <div class="card-body-custom">
        <form method="POST" action="{{ route('admin.weddings.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Prénom de la mariée *</label>
                    <input type="text" name="bride_name" class="form-control" value="{{ old('bride_name') }}" placeholder="Sophia" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Prénom du marié *</label>
                    <input type="text" name="groom_name" class="form-control" value="{{ old('groom_name') }}" placeholder="Alexandre" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date du mariage</label>
                    <input type="text" name="wedding_date" class="form-control datepicker" value="{{ old('wedding_date') }}" placeholder="Date à venir">
                </div>
                <div class="col-12">
                    <label class="form-label">Citation / devise du couple</label>
                    <input type="text" name="quote" class="form-control" value="{{ old('quote') }}" placeholder="« L'amour est notre seul vrai trésor »">
                </div>
                <div class="col-12">
                    <label class="form-label">Texte d'introduction</label>
                    <textarea name="intro_text" class="form-control" rows="3" placeholder="Nous avons l'immense joie de vous convier...">{{ old('intro_text') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Message de bienvenue</label>
                    <textarea name="welcome_message" class="form-control" rows="3" placeholder="Bienvenue sur notre page d'invitation...">{{ old('welcome_message') }}</textarea>
                </div>
            </div>
            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-check-circle me-2"></i>Créer le mariage
                </button>
                <a href="{{ route('admin.weddings.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
flatpickr('.datepicker', { locale: 'fr', dateFormat: 'Y-m-d', allowInput: true });
</script>
@endpush
