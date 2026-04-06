@extends('layouts.admin')
@section('title', 'Modifier ' . $guest->full_name)
@section('breadcrumb')
<a href="{{ route('admin.weddings.guests.index', $wedding) }}">Invités</a>
<i class="bi bi-chevron-right mx-2"></i>
<a href="{{ route('admin.weddings.guests.show', [$wedding, $guest]) }}">{{ $guest->full_name }}</a>
<i class="bi bi-chevron-right mx-2"></i><span>Modifier</span>
@endsection

@section('content')
<div class="admin-card" style="max-width:700px;margin:0 auto">
    <div class="card-body-custom">
        <form method="POST" action="{{ route('admin.weddings.guests.update', [$wedding, $guest]) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Prénom *</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $guest->first_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $guest->last_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $guest->email) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $guest->phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Canal de contact</label>
                    <select name="contact_channel" class="form-select">
                        <option value="email" {{ $guest->contact_channel === 'email' ? 'selected' : '' }}>Email</option>
                        <option value="sms" {{ $guest->contact_channel === 'sms' ? 'selected' : '' }}>SMS</option>
                        <option value="whatsapp" {{ $guest->contact_channel === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Statut RSVP</label>
                    <select name="rsvp_status" class="form-select">
                        <option value="pending" {{ $guest->rsvp_status === 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="accepted" {{ $guest->rsvp_status === 'accepted' ? 'selected' : '' }}>Accepté</option>
                        <option value="declined" {{ $guest->rsvp_status === 'declined' ? 'selected' : '' }}>Décliné</option>
                        <option value="maybe" {{ $guest->rsvp_status === 'maybe' ? 'selected' : '' }}>À confirmer</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Accompagnants max</label>
                    <input type="number" name="max_companions" class="form-control" value="{{ old('max_companions', $guest->max_companions) }}" min="0">
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input type="hidden" name="companions_allowed" value="0">
                        <input class="form-check-input" type="checkbox" name="companions_allowed" value="1"
                               id="comp" {{ $guest->companions_allowed ? 'checked' : '' }}>
                        <label class="form-check-label" for="comp">Accompagnants autorisés</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Message personnel</label>
                    <textarea name="personal_message" class="form-control" rows="2">{{ old('personal_message', $guest->personal_message) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes admin</label>
                    <textarea name="notes_admin" class="form-control" rows="2">{{ old('notes_admin', $guest->notes_admin) }}</textarea>
                </div>
            </div>
            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-check-circle me-2"></i>Enregistrer
                </button>
                <a href="{{ route('admin.weddings.guests.show', [$wedding, $guest]) }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
