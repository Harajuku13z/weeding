@extends('layouts.admin')
@section('title', 'Ajouter un invité')
@section('breadcrumb')
<a href="{{ route('admin.weddings.guests.index', $wedding) }}">Invités</a>
<i class="bi bi-chevron-right mx-2"></i><span>Ajouter</span>
@endsection

@section('content')
<div class="admin-card" style="max-width:700px;margin:0 auto">
    <div class="card-header-custom">
        <h2 class="card-title-custom font-serif">Ajouter un invité</h2>
    </div>
    <div class="card-body-custom">
        <form method="POST" action="{{ route('admin.weddings.guests.store', $wedding) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Prénom *</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Canal de contact</label>
                    <select name="contact_channel" class="form-select">
                        <option value="email">Email</option>
                        <option value="sms">SMS</option>
                        <option value="whatsapp">WhatsApp</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Accompagnants max</label>
                    <input type="number" name="max_companions" class="form-control" value="{{ old('max_companions', 0) }}" min="0">
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input type="hidden" name="companions_allowed" value="0">
                        <input class="form-check-input" type="checkbox" name="companions_allowed" value="1" id="comp_allowed">
                        <label class="form-check-label" for="comp_allowed">Accompagnants autorisés</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Message personnel</label>
                    <textarea name="personal_message" class="form-control" rows="2"
                              placeholder="Message visible uniquement par cet invité...">{{ old('personal_message') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes admin (internes)</label>
                    <textarea name="notes_admin" class="form-control" rows="2"
                              placeholder="Notes internes, non visibles par l'invité...">{{ old('notes_admin') }}</textarea>
                </div>
            </div>
            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-person-plus me-2"></i>Ajouter l'invité
                </button>
                <a href="{{ route('admin.weddings.guests.index', $wedding) }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
