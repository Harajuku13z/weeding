@extends('layouts.admin')
@section('title', 'Nouveau modèle')
@section('breadcrumb') <a href="{{ route('admin.weddings.templates.index', $wedding) }}">Modèles</a> <i class="bi bi-chevron-right mx-2"></i><span>Nouveau</span> @endsection

@section('content')
<div class="admin-card" style="max-width:700px;margin:0 auto">
    <div class="card-header-custom"><h2 class="card-title-custom font-serif">Nouveau modèle de message</h2></div>
    <div class="card-body-custom">
        <form method="POST" action="{{ route('admin.weddings.templates.store', $wedding) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Nom *</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
                <div class="col-md-3"><label class="form-label">Canal *</label><select name="channel" class="form-select" required><option value="email">Email</option><option value="sms">SMS</option><option value="whatsapp">WhatsApp</option></select></div>
                <div class="col-md-3"><label class="form-label">Type *</label><select name="type" class="form-select" required><option value="reminder">Relance</option><option value="invitation">Invitation</option><option value="confirmation">Confirmation</option></select></div>
                <div class="col-12"><label class="form-label">Sujet (email)</label><input type="text" name="subject" class="form-control" value="{{ old('subject') }}"></div>
                <div class="col-12">
                    <label class="form-label">Message *</label>
                    <textarea name="body" class="form-control" rows="8" required>{{ old('body') }}</textarea>
                    <div class="form-text">Variables : <code>{prenom}</code> <code>{nom}</code> <code>{nom_maries}</code> <code>{date_evenement}</code> <code>{lien_rsvp}</code> <code>{code_invitation}</code></div>
                </div>
            </div>
            <div class="d-flex gap-3 mt-4"><button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-circle me-2"></i>Créer</button><a href="{{ route('admin.weddings.templates.index', $wedding) }}" class="btn btn-outline-secondary">Annuler</a></div>
        </form>
    </div>
</div>
@endsection
