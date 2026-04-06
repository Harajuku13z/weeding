@extends('layouts.admin')
@section('title', $guest->full_name)
@section('breadcrumb')
<a href="{{ route('admin.weddings.guests.index', $wedding) }}">Invités</a>
<i class="bi bi-chevron-right mx-2"></i><span>{{ $guest->full_name }}</span>
@endsection
@section('topbar-actions')
<a href="{{ route('admin.weddings.guests.edit', [$wedding, $guest]) }}" class="btn btn-sm btn-primary-custom">
    <i class="bi bi-pencil me-1"></i>Modifier
</a>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="admin-card text-center">
            <div class="guest-avatar-xl mb-3">{{ substr($guest->first_name, 0, 1) }}</div>
            <h2 class="font-serif">{{ $guest->full_name }}</h2>
            <span class="badge bg-{{ $guest->status_color }} fs-6">{{ $guest->status_label }}</span>
            @if($guest->is_suspended)
            <div class="mt-2"><span class="badge bg-dark">Suspendu</span></div>
            @endif
            <div class="mt-3 text-muted">
                @if($guest->email) <div><i class="bi bi-envelope me-1"></i>{{ $guest->email }}</div> @endif
                @if($guest->phone) <div><i class="bi bi-phone me-1"></i>{{ $guest->phone }}</div> @endif
            </div>
            <div class="mt-3">
                <code class="small">Code : {{ $guest->invitation_code }}</code>
            </div>
            @if($guest->rsvp_at)
            <div class="mt-2 text-muted small">
                <i class="bi bi-clock me-1"></i>Répondu {{ $guest->rsvp_at->diffForHumans() }}
            </div>
            @endif
        </div>

        <!-- Lien personnel invité -->
        <div class="admin-card mt-3">
            <div class="card-header-custom">
                <h4 class="card-title-custom">
                    <i class="bi bi-link-45deg me-1" style="color:var(--color-primary)"></i>Lien personnel
                </h4>
            </div>
            <div class="card-body-custom">
                <p class="text-muted small mb-2">Envoyez ce lien unique à {{ $guest->first_name }}. Il donne accès à sa page personnelle sans mot de passe.</p>
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm"
                           value="{{ route('guest.personal', $guest->invitation_code) }}"
                           readonly id="personalUrl">
                    <button class="btn btn-outline-secondary btn-sm"
                            onclick="navigator.clipboard.writeText(document.getElementById('personalUrl').value);this.innerHTML='<i class=\'bi bi-check\'>'" title="Copier">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>
                <div class="d-flex gap-2 mt-2">
                    <a href="{{ route('guest.personal', $guest->invitation_code) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>Voir la page
                    </a>
                    @php
                        $whatsappMsg = urlencode('Bonjour ' . $guest->first_name . " ! Voici votre invitation personnelle pour le mariage de " . $wedding->getCoupleName() . " : " . route('guest.personal', $guest->invitation_code));
                    @endphp
                    @if($guest->phone)
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $guest->phone) }}?text={{ $whatsappMsg }}" target="_blank" class="btn btn-sm btn-success">
                        <i class="bi bi-whatsapp me-1"></i>WhatsApp
                    </a>
                    @endif
                    @if($guest->email)
                    <a href="mailto:{{ $guest->email }}?subject=Votre invitation - {{ $wedding->getCoupleName() }}&body={{ urlencode('Bonjour ' . $guest->first_name . ",\n\nVoici votre lien d'invitation personnel :\n" . route('guest.personal', $guest->invitation_code) . "\n\nÀ très bientôt !") }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-envelope me-1"></i>Email
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Détails réponse -->
        <div class="admin-card mb-3">
            <div class="card-header-custom"><h4 class="card-title-custom">Réponse RSVP</h4></div>
            <div class="card-body-custom">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-muted small">Statut</div>
                        <span class="badge bg-{{ $guest->status_color }}">{{ $guest->status_label }}</span>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Accompagnants</div>
                        <strong>{{ $guest->companions_count }}</strong>
                        @if($guest->companions_allowed) / {{ $guest->max_companions }} autorisés @endif
                    </div>
                    @if($guest->dietary_restrictions)
                    <div class="col-12">
                        <div class="text-muted small">Restrictions alimentaires</div>
                        <p class="mb-0">{{ $guest->dietary_restrictions }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Accompagnants -->
        @if($guest->companions->count())
        <div class="admin-card mb-3">
            <div class="card-header-custom"><h4 class="card-title-custom">Accompagnants ({{ $guest->companions->count() }})</h4></div>
            <div class="card-body-custom">
                @foreach($guest->companions as $companion)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span>{{ $companion->first_name }} {{ $companion->last_name }}</span>
                    @if($companion->dietary_restrictions)
                    <span class="text-muted small">{{ $companion->dietary_restrictions }}</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Réponses par étape -->
        @if($guest->responses->count())
        <div class="admin-card mb-3">
            <div class="card-header-custom"><h4 class="card-title-custom">Réponses par étape</h4></div>
            <div class="card-body-custom">
                @foreach($guest->responses as $response)
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span>{{ $response->programItem?->title ?? 'Message général' }}</span>
                    <span class="badge bg-{{ $response->status === 'attending' ? 'success' : ($response->status === 'not_attending' ? 'danger' : 'warning') }}">
                        {{ $response->status === 'attending' ? 'Présent' : ($response->status === 'not_attending' ? 'Absent' : 'Incertain') }}
                    </span>
                </div>
                @if($response->message)
                <div class="p-2 bg-light rounded mt-1 mb-2 small fst-italic">« {{ $response->message }} »</div>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        <!-- Historique relances -->
        @if($guest->reminders->count())
        <div class="admin-card mb-3">
            <div class="card-header-custom"><h4 class="card-title-custom">Historique des relances</h4></div>
            <div class="card-body-custom">
                @foreach($guest->reminders as $reminder)
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <div>
                        <span class="badge bg-{{ $reminder->status === 'sent' ? 'success' : 'danger' }}">{{ $reminder->status }}</span>
                        <span class="ms-2 small text-muted">{{ ucfirst($reminder->channel) }}</span>
                    </div>
                    <span class="text-muted small">{{ $reminder->sent_at?->format('d/m/Y H:i') ?? $reminder->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Notes admin -->
        @if($guest->notes_admin)
        <div class="admin-card">
            <div class="card-header-custom"><h4 class="card-title-custom">Notes internes</h4></div>
            <div class="card-body-custom">
                <p class="mb-0">{{ $guest->notes_admin }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
