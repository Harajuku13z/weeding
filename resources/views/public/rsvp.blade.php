<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSVP — {{ $wedding->getCoupleName() }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/wedding.css') }}">
    @php $theme = $wedding->theme; @endphp
    @if($theme)
    <style>:root { {{ $theme->getCssVariables() }} }</style>
    @endif
</head>
<body style="background: linear-gradient(135deg, #1a1018 0%, #2d1f2b 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px;">

<div class="rsvp-form-card" style="max-width:640px;width:100%">
    <div style="text-align:center;margin-bottom:30px">
        <div style="font-family:var(--ft);color:var(--wp);font-size:13px;letter-spacing:4px;text-transform:uppercase;margin-bottom:8px">
            Invitation au mariage
        </div>
        <h1 style="font-family:var(--ft);font-size:28px;margin:0;color:var(--wt)">{{ $wedding->getCoupleName() }}</h1>
        <p style="color:#9e8e82;margin-top:6px">{{ $wedding->getWeddingDateFormatted() }}</p>
        <div style="border-top:1px solid #e8ddd5;margin:20px 0"></div>
        <h2 class="rsvp-form-title">Bonjour {{ $guest->first_name }} 👋</h2>
        <p class="rsvp-form-subtitle">
            @if($guest->personal_message)
            {{ $guest->personal_message }}
            @else
            Nous serions ravis de vous accueillir à notre mariage. Merci de confirmer votre présence.
            @endif
        </p>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-4"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger mb-4">
        @foreach($errors->all() as $error) <div>{{ $error }}</div> @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('rsvp.submit', $wedding->slug) }}" id="rsvpForm">
        @csrf
        <input type="hidden" name="invitation_code" value="{{ $guest->invitation_code }}">

        <!-- Réponse -->
        <div class="mb-4">
            <label style="font-weight:600;font-size:13px;margin-bottom:10px;display:block">Votre réponse *</label>
            <div class="rsvp-status-group">
                <button type="button" class="rsvp-status-btn {{ $guest->rsvp_status === 'accepted' ? 'active' : '' }}" data-status="accepted">
                    <i class="bi bi-check-circle-fill" style="color:#10b981"></i>
                    <span>Avec joie !</span>
                </button>
                <button type="button" class="rsvp-status-btn {{ $guest->rsvp_status === 'maybe' ? 'active' : '' }}" data-status="maybe">
                    <i class="bi bi-question-circle-fill" style="color:#f59e0b"></i>
                    <span>À confirmer</span>
                </button>
                <button type="button" class="rsvp-status-btn {{ $guest->rsvp_status === 'declined' ? 'active' : '' }}" data-status="declined">
                    <i class="bi bi-x-circle-fill" style="color:#ef4444"></i>
                    <span>Je ne pourrai pas</span>
                </button>
            </div>
            <input type="hidden" name="rsvp_status" id="rsvpStatusInput" value="{{ old('rsvp_status', $guest->rsvp_status) }}" required>
        </div>

        <!-- Programme -->
        @if($programItems->count())
        <div class="mb-4" id="programField">
            <label style="font-weight:600;font-size:13px;margin-bottom:10px;display:block">Participation au programme</label>
            @foreach($programItems as $item)
            <div style="border:1px solid #e8ddd5;border-radius:10px;padding:12px;margin-bottom:8px">
                <div style="font-weight:600;margin-bottom:8px">{{ $item->title }}
                    @if($item->date) <span style="color:#9e8e82;font-size:12px;font-weight:400"> — {{ $item->getDateTimeFormatted() }}</span> @endif
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap">
                    @foreach(['attending' => ['label' => 'Présent', 'color' => '#10b981'], 'not_attending' => ['label' => 'Absent', 'color' => '#ef4444'], 'pending' => ['label' => 'Incertain', 'color' => '#f59e0b']] as $s => $sc)
                    @php
                        $existingResponse = $guest->responses->firstWhere('wedding_program_item_id', $item->id);
                        $isSelected = $existingResponse && $existingResponse->status === $s;
                    @endphp
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;padding:6px 12px;border-radius:20px;border:1.5px solid {{ $isSelected ? $sc['color'] : '#e8ddd5' }};background:{{ $isSelected ? $sc['color'].'15' : 'white' }};font-size:13px">
                        <input type="radio" name="program_responses[{{ $item->id }}]" value="{{ $s }}" {{ $isSelected ? 'checked' : '' }} style="display:none">
                        <span style="color:{{ $sc['color'] }}">{{ $sc['label'] }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Accompagnants -->
        @if($guest->companions_allowed)
        <div class="mb-4" id="companionsSection">
            <label style="font-weight:600;font-size:13px;margin-bottom:6px;display:block">
                Accompagnants (max {{ $guest->max_companions }})
            </label>
            <input type="number" name="companions_count" class="rsvp-input mb-2"
                   value="{{ old('companions_count', $guest->companions_count) }}"
                   placeholder="Nombre d'accompagnants" min="0" max="{{ $guest->max_companions }}">
            <div id="companionsContainer">
                @foreach($guest->companions as $i => $companion)
                <div class="companion-row mb-2">
                    <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:8px">
                        <input type="text" name="companions[{{ $i }}][first_name]" class="rsvp-input"
                               placeholder="Prénom" value="{{ $companion->first_name }}" required>
                        <input type="text" name="companions[{{ $i }}][last_name]" class="rsvp-input"
                               placeholder="Nom" value="{{ $companion->last_name }}">
                        <button type="button" class="btn-venue" onclick="this.closest('.companion-row').remove()"
                                style="background:#fee2e2;color:#991b1b;padding:8px 12px">×</button>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" id="addCompanion" class="btn-venue btn-venue-maps mt-2">
                <i class="bi bi-person-plus"></i> Ajouter un accompagnant
            </button>
            <input type="hidden" id="maxCompanions" value="{{ $guest->max_companions }}">
        </div>
        @endif

        <!-- Restrictions alimentaires -->
        <div class="mb-4">
            <label style="font-weight:600;font-size:13px;margin-bottom:6px;display:block">Restrictions alimentaires</label>
            <input type="text" name="dietary_restrictions" class="rsvp-input"
                   value="{{ old('dietary_restrictions', $guest->dietary_restrictions) }}"
                   placeholder="Végétarien, allergies, sans gluten...">
        </div>

        <!-- Message -->
        <div class="mb-5">
            <label style="font-weight:600;font-size:13px;margin-bottom:6px;display:block">Un mot pour les mariés</label>
            <textarea name="message" class="rsvp-input" rows="3"
                      placeholder="Votre message aux mariés..."></textarea>
        </div>

        <button type="submit" class="rsvp-btn">
            <i class="bi bi-send me-2"></i>Envoyer ma réponse
        </button>
    </form>

    <div style="text-align:center;margin-top:20px">
        <a href="{{ route('wedding.public', $wedding->slug) }}" style="color:#9e8e82;font-size:13px;text-decoration:none">
            <i class="bi bi-arrow-left me-1"></i>Retour à l'invitation
        </a>
    </div>
</div>

<script src="{{ asset('js/wedding.js') }}"></script>
</body>
</html>
