@extends('layouts.admin')
@section('title', 'Modifier ' . $wedding->getCoupleName())
@section('breadcrumb')
<a href="{{ route('admin.weddings.show', $wedding) }}">{{ $wedding->getCoupleName() }}</a>
<i class="bi bi-chevron-right mx-2"></i><span>Modifier</span>
@endsection

@section('content')
<div class="admin-card">
    <div class="card-header-custom">
        <h2 class="card-title-custom font-serif">Informations du mariage</h2>
    </div>
    <div class="card-body-custom">
        <form method="POST" action="{{ route('admin.weddings.update', $wedding) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Prénom de la mariée *</label>
                    <input type="text" name="bride_name" class="form-control" value="{{ old('bride_name', $wedding->bride_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Prénom du marié *</label>
                    <input type="text" name="groom_name" class="form-control" value="{{ old('groom_name', $wedding->groom_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date du mariage</label>
                    <input type="text" name="wedding_date" class="form-control datepicker" value="{{ old('wedding_date', $wedding->wedding_date?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date limite RSVP</label>
                    <input type="text" name="rsvp_deadline" class="form-control datepicker" value="{{ old('rsvp_deadline', $wedding->rsvp_deadline?->format('Y-m-d')) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Citation du couple</label>
                    <input type="text" name="quote" class="form-control" value="{{ old('quote', $wedding->quote) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Texte d'introduction</label>
                    <textarea name="intro_text" class="form-control" rows="4">{{ old('intro_text', $wedding->intro_text) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Message de bienvenue</label>
                    <textarea name="welcome_message" class="form-control" rows="3">{{ old('welcome_message', $wedding->welcome_message) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Notre histoire</label>
                    <textarea name="story_text" class="form-control" rows="4">{{ old('story_text', $wedding->story_text) }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Logo / Monogramme</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    @if($wedding->logo) <img src="{{ Storage::url($wedding->logo) }}" height="50" class="mt-2"> @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Photo du couple</label>
                    <input type="file" name="couple_photo" class="form-control" accept="image/*">
                    @if($wedding->couple_photo) <img src="{{ Storage::url($wedding->couple_photo) }}" height="50" class="mt-2 rounded"> @endif
                </div>
                <div class="col-12">
                    <label class="form-label">Image hero (fond de la page)</label>
                    <input type="file" name="hero_image" class="form-control" accept="image/*">
                    @if($wedding->hero_image) <img src="{{ Storage::url($wedding->hero_image) }}" height="80" class="mt-2 rounded"> @endif
                </div>

                <!-- SEO -->
                <div class="col-12"><hr class="my-3"><h5 class="font-serif">SEO</h5></div>
                <div class="col-md-6">
                    <label class="form-label">Titre SEO</label>
                    <input type="text" name="seo_title" class="form-control" value="{{ old('seo_title', $wedding->seo_title) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Description SEO</label>
                    <textarea name="seo_description" class="form-control" rows="2">{{ old('seo_description', $wedding->seo_description) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Image de partage social</label>
                    <input type="file" name="social_image" class="form-control" accept="image/*">
                    @if($wedding->social_image) <img src="{{ Storage::url($wedding->social_image) }}" height="50" class="mt-2 rounded"> @endif
                </div>

                <!-- Options -->
                <div class="col-12"><hr class="my-3"><h5 class="font-serif">Options</h5></div>
                <div class="col-md-4">
                    <div class="form-check form-switch">
                        <input type="hidden" name="envelope_animation" value="0">
                        <input class="form-check-input" type="checkbox" name="envelope_animation" value="1"
                               id="envelope_animation" {{ $wedding->envelope_animation ? 'checked' : '' }}>
                        <label class="form-check-label" for="envelope_animation">Animation enveloppe</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch">
                        <input type="hidden" name="floral_decor" value="0">
                        <input class="form-check-input" type="checkbox" name="floral_decor" value="1"
                               id="floral_decor" {{ $wedding->floral_decor ? 'checked' : '' }}>
                        <label class="form-check-label" for="floral_decor">Décor floral</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch">
                        <input type="hidden" name="rsvp_modification_allowed" value="0">
                        <input class="form-check-input" type="checkbox" name="rsvp_modification_allowed" value="1"
                               id="rsvp_mod" {{ $wedding->rsvp_modification_allowed ? 'checked' : '' }}>
                        <label class="form-check-label" for="rsvp_mod">Modification RSVP autorisée</label>
                    </div>
                </div>

                <!-- Hébergement -->
                <div class="col-12"><hr class="my-3"><h5 class="font-serif">Hébergement & infos pratiques</h5></div>
                <div class="col-md-6">
                    <label class="form-label">Titre section hébergement</label>
                    <input type="text" name="accommodation_info" class="form-control" value="{{ old('accommodation_info', $wedding->accommodation_info) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Détails hébergement</label>
                    <textarea name="accommodation_details" class="form-control" rows="4">{{ old('accommodation_details', $wedding->accommodation_details) }}</textarea>
                </div>
            </div>

            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-check-circle me-2"></i>Enregistrer
                </button>
                <a href="{{ route('admin.weddings.show', $wedding) }}" class="btn btn-outline-secondary">Annuler</a>
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
