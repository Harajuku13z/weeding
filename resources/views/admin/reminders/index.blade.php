@extends('layouts.admin')
@section('title', 'Relances')
@section('breadcrumb')
<a href="{{ route('admin.weddings.show', $wedding) }}">{{ $wedding->getCoupleName() }}</a>
<i class="bi bi-chevron-right mx-2"></i><span>Relances</span>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <!-- Envoi individuel -->
        <div class="admin-card mb-4">
            <div class="card-header-custom">
                <h4 class="card-title-custom"><i class="bi bi-send me-2"></i>Envoyer une relance</h4>
            </div>
            <div class="card-body-custom">
                <form method="POST" action="{{ route('admin.weddings.reminders.send', $wedding) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Invité</label>
                        <select name="guest_id" class="form-select" required>
                            <option value="">Choisir un invité</option>
                            @foreach($pendingGuests as $guest)
                            <option value="{{ $guest->id }}">{{ $guest->full_name }} (En attente)</option>
                            @endforeach
                            @foreach($maybeGuests as $guest)
                            <option value="{{ $guest->id }}">{{ $guest->full_name }} (À confirmer)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Canal</label>
                        <select name="channel" class="form-select" required>
                            <option value="email">Email</option>
                            <option value="sms">SMS</option>
                            <option value="whatsapp">WhatsApp</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Modèle de message</label>
                        <select name="template_id" class="form-select" id="templateSelect" onchange="previewTemplate(this.value)">
                            <option value="">Aucun modèle</option>
                            @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="4" id="messageArea"
                                  placeholder="Bonjour {prenom}, nous attendons votre confirmation..."></textarea>
                        <div class="form-text">Variables : {prenom}, {nom}, {nom_maries}, {date_evenement}, {lien_rsvp}</div>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100">
                        <i class="bi bi-send me-2"></i>Envoyer
                    </button>
                </form>
            </div>
        </div>

        <!-- Relance groupée -->
        <div class="admin-card">
            <div class="card-header-custom">
                <h4 class="card-title-custom"><i class="bi bi-broadcast me-2"></i>Relance groupée</h4>
            </div>
            <div class="card-body-custom">
                <form method="POST" action="{{ route('admin.weddings.reminders.send-bulk', $wedding) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Segment cible</label>
                        <select name="segment" class="form-select" required>
                            <option value="pending">Sans réponse ({{ $pendingGuests->count() }} invités)</option>
                            <option value="maybe">À confirmer ({{ $maybeGuests->count() }} invités)</option>
                            <option value="all_no_response">Les deux ({{ $pendingGuests->count() + $maybeGuests->count() }} invités)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Canal</label>
                        <select name="channel" class="form-select" required>
                            <option value="email">Email</option>
                            <option value="sms">SMS</option>
                            <option value="whatsapp">WhatsApp</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Modèle</label>
                        <select name="template_id" class="form-select">
                            <option value="">Aucun</option>
                            @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100"
                            onclick="return confirm('Envoyer une relance groupée ?')">
                        <i class="bi bi-broadcast me-2"></i>Lancer la relance groupée
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <!-- Historique -->
        <div class="admin-card">
            <div class="card-header-custom">
                <h4 class="card-title-custom"><i class="bi bi-clock-history me-2"></i>Historique des envois</h4>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Invité</th>
                                <th>Canal</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reminders as $reminder)
                            <tr>
                                <td>{{ $reminder->guest?->full_name ?? '—' }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($reminder->channel) }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $reminder->status === 'sent' ? 'success' : ($reminder->status === 'failed' ? 'danger' : 'warning') }}">
                                        {{ $reminder->status }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $reminder->sent_at?->format('d/m H:i') ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-3 text-muted">Aucune relance envoyée</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{ $reminders->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewTemplate(templateId) {
    if (!templateId) return;
    fetch('{{ route('admin.weddings.reminders.preview', $wedding) }}?template_id=' + templateId)
        .then(r => r.json())
        .then(data => {
            document.getElementById('messageArea').value = data.preview;
        });
}
</script>
@endpush
