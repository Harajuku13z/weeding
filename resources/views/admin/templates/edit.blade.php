@extends('layouts.admin')
@section('title', 'Modifier le modèle')
@section('breadcrumb') <a href="{{ route('admin.weddings.templates.index', $wedding) }}">Modèles</a> <i class="bi bi-chevron-right mx-2"></i><span>Modifier</span> @endsection

@section('content')
<div class="admin-card" style="max-width:700px;margin:0 auto">
    <div class="card-body-custom">
        <form method="POST" action="{{ route('admin.weddings.templates.update', [$wedding, $template]) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Nom *</label><input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required></div>
                <div class="col-md-3"><label class="form-label">Canal *</label><select name="channel" class="form-select" required><option value="email" {{ $template->channel === 'email' ? 'selected' : '' }}>Email</option><option value="sms" {{ $template->channel === 'sms' ? 'selected' : '' }}>SMS</option><option value="whatsapp" {{ $template->channel === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option></select></div>
                <div class="col-md-3"><label class="form-label">Type *</label><select name="type" class="form-select" required><option value="reminder" {{ $template->type === 'reminder' ? 'selected' : '' }}>Relance</option><option value="invitation" {{ $template->type === 'invitation' ? 'selected' : '' }}>Invitation</option><option value="confirmation" {{ $template->type === 'confirmation' ? 'selected' : '' }}>Confirmation</option></select></div>
                <div class="col-12"><label class="form-label">Sujet</label><input type="text" name="subject" class="form-control" value="{{ old('subject', $template->subject) }}"></div>
                <div class="col-12"><label class="form-label">Message *</label><textarea name="body" class="form-control" rows="8" required>{{ old('body', $template->body) }}</textarea></div>
            </div>
            <div class="d-flex gap-3 mt-4"><button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-circle me-2"></i>Enregistrer</button><a href="{{ route('admin.weddings.templates.index', $wedding) }}" class="btn btn-outline-secondary">Annuler</a></div>
        </form>
    </div>
</div>
@endsection
