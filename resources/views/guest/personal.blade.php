<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre invitation — {{ $guest->wedding->getCoupleName() }}</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/css/glightbox.min.css">
    <link rel="stylesheet" href="{{ asset('css/wedding.css') }}">
    @php $wedding = $guest->wedding; $theme = $wedding->theme; @endphp
    @if($theme)
    <style>
        :root {
            --color-primary: {{ $theme->color_primary }};
            --color-secondary: {{ $theme->color_secondary }};
            --color-accent: {{ $theme->color_accent }};
            --color-background: {{ $theme->color_background }};
            --color-text: {{ $theme->color_text }};
            --font-title: '{{ $theme->font_title }}', Georgia, serif;
            --font-body: '{{ $theme->font_body }}', sans-serif;
        }
    </style>
    @endif
    <style>
        body { background: var(--wbg, #fdfaf7); font-family: var(--fb, 'Lato', sans-serif); }

        .personal-header {
            background: linear-gradient(160deg, #1a1018 0%, #2d1f2b 60%, #3d2b1f 100%);
            padding: 40px 20px 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .personal-header::before {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'%3E%3Ccircle cx='50' cy='50' r='1' fill='%23c8a97e' opacity='0.3'/%3E%3Ccircle cx='150' cy='100' r='1.5' fill='%23c8a97e' opacity='0.2'/%3E%3Ccircle cx='300' cy='80' r='1' fill='%23c8a97e' opacity='0.3'/%3E%3Ccircle cx='200' cy='200' r='2' fill='%23c8a97e' opacity='0.15'/%3E%3Ccircle cx='350' cy='300' r='1.5' fill='%23c8a97e' opacity='0.2'/%3E%3C/svg%3E");
            background-size: 400px;
        }

        .personal-greeting {
            font-family: var(--ft, 'Playfair Display', serif);
            font-size: 13px;
            color: var(--wp, #c8a97e);
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 12px;
            display: block;
        }

        .personal-name {
            font-family: var(--ft, 'Playfair Display', serif);
            font-size: clamp(28px, 6vw, 52px);
            color: white;
            margin: 0 0 8px;
            font-style: italic;
        }

        .personal-couple {
            font-family: var(--ft, 'Playfair Display', serif);
            font-size: 20px;
            color: var(--wp, #c8a97e);
            margin: 0 0 6px;
        }

        .personal-date {
            color: rgba(255,255,255,.6);
            font-size: 15px;
        }

        .personal-body {
            max-width: 760px;
            margin: -30px auto 0;
            padding: 0 16px 60px;
            position: relative;
            z-index: 10;
        }

        .personal-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0,0,0,.12);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-section-title {
            font-family: var(--ft, 'Playfair Display', serif);
            font-size: 16px;
            color: var(--wt, #3d2b1f);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-section-title i { color: var(--wp, #c8a97e); font-size: 18px; }

        .pc-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f0e8e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pc-body { padding: 20px 24px; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 700;
        }

        .status-accepted { background: #d1fae5; color: #065f46; }
        .status-declined { background: #fee2e2; color: #991b1b; }
        .status-maybe { background: #fef3c7; color: #92400e; }
        .status-pending { background: #f3f4f6; color: #374151; }

        .program-step {
            display: flex;
            gap: 16px;
            padding: 14px 0;
            border-bottom: 1px solid #f0e8e0;
        }
        .program-step:last-child { border-bottom: none; }

        .step-icon {
            width: 40px; height: 40px;
            background: var(--ws, #f5e6d3);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: var(--wp, #c8a97e);
            flex-shrink: 0;
        }

        .step-title { font-weight: 700; font-size: 15px; color: var(--wt, #3d2b1f); }
        .step-time { font-size: 12px; color: var(--wp, #c8a97e); letter-spacing: 1px; }
        .step-venue { font-size: 13px; color: #9e8e82; margin-top: 2px; }

        .rsvp-btn-group {
            display: flex;
            gap: 10px;
        }

        .rsvp-choice {
            flex: 1;
            border: 2px solid #e8ddd5;
            background: white;
            border-radius: 12px;
            padding: 14px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s;
            font-family: inherit;
        }

        .rsvp-choice:hover { border-color: var(--wp, #c8a97e); background: var(--ws, #f5e6d3); }
        .rsvp-choice.selected { border-color: var(--wp, #c8a97e); background: var(--wp, #c8a97e); color: white; }
        .rsvp-choice i { font-size: 24px; display: block; margin-bottom: 6px; }
        .rsvp-choice span { font-size: 13px; font-weight: 700; }

        .rsvp-input {
            width: 100%;
            border: 1.5px solid #e8ddd5;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 15px;
            font-family: inherit;
            color: var(--wt, #3d2b1f);
            background: white;
            transition: all 0.25s;
        }

        .rsvp-input:focus {
            outline: none;
            border-color: var(--wp, #c8a97e);
            box-shadow: 0 0 0 3px rgba(200,169,126,.12);
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--wp, #c8a97e), var(--wa, #8b6355));
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s;
            font-family: inherit;
            letter-spacing: 0.5px;
        }

        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(200,169,126,.35); }

        .swatch { width: 50px; height: 50px; border-radius: 50%; box-shadow: 0 2px 10px rgba(0,0,0,.15); }

        .venue-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: 50px; font-size: 13px;
            text-decoration: none; font-weight: 600; transition: all 0.2s;
        }
        .venue-btn-maps { background: var(--ws, #f5e6d3); color: var(--wt, #3d2b1f); }
        .venue-btn-waze { background: #e0f7fa; color: #006064; }
        .venue-btn:hover { transform: scale(1.04); }

        .gift-mini {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 0; border-bottom: 1px solid #f0e8e0;
        }
        .gift-mini:last-child { border-bottom: none; }

        .rule-row {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 8px 0;
        }

        .nav-back {
            display: inline-flex; align-items: center; gap: 6px;
            color: rgba(255,255,255,.6); font-size: 13px;
            text-decoration: none; margin-bottom: 20px;
        }
        .nav-back:hover { color: white; }

        @media (max-width: 640px) {
            .rsvp-btn-group { flex-direction: column; }
            .personal-header { padding: 30px 16px 50px; }
        }
    </style>
</head>
<body>

{{-- ═══════ EN-TÊTE PERSONNALISÉ ══════════════════════════════ --}}
<div class="personal-header">
    <a href="{{ route('wedding.public', $wedding->slug) }}" class="nav-back">
        <i class="bi bi-arrow-left"></i> Voir l'invitation complète
    </a>

    <span class="personal-greeting">Votre invitation personnelle</span>
    <h1 class="personal-name">{{ $guest->first_name }}</h1>
    <div class="personal-couple">{{ $wedding->bride_name }} & {{ $wedding->groom_name }}</div>
    <div class="personal-date">
        <i class="bi bi-calendar-heart me-1"></i>{{ $wedding->getWeddingDateFormatted() }}
    </div>

    @if($guest->personal_message)
    <p style="color:rgba(255,255,255,.7);font-style:italic;margin-top:16px;max-width:500px;margin-left:auto;margin-right:auto;font-size:15px">
        « {{ $guest->personal_message }} »
    </p>
    @endif
</div>

<div class="personal-body">

    {{-- ─── Alertes ─────────────────────────────────────────── --}}
    @if(session('success'))
    <div style="background:#d1fae5;color:#065f46;padding:16px 20px;border-radius:12px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-weight:600">
        <i class="bi bi-check-circle-fill" style="font-size:20px"></i>{{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#fee2e2;color:#991b1b;padding:16px 20px;border-radius:12px;margin-bottom:20px">
        <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
    </div>
    @endif

    {{-- ─── Statut RSVP ─────────────────────────────────────── --}}
    <div class="personal-card">
        <div class="pc-header">
            <h3 class="card-section-title">
                <i class="bi bi-envelope-heart-fill"></i>Votre réponse
            </h3>
            @if($guest->rsvp_at)
            <span style="font-size:12px;color:#9e8e82">
                Répondu {{ $guest->rsvp_at->diffForHumans() }}
            </span>
            @endif
        </div>
        <div class="pc-body">
            @if($guest->rsvp_status !== 'pending')
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                <span class="status-badge status-{{ $guest->rsvp_status }}">
                    @if($guest->rsvp_status === 'accepted') <i class="bi bi-check-circle-fill"></i> Vous avez accepté
                    @elseif($guest->rsvp_status === 'declined') <i class="bi bi-x-circle-fill"></i> Vous avez décliné
                    @else <i class="bi bi-question-circle-fill"></i> À confirmer
                    @endif
                </span>
                @if($guest->companions_count > 0)
                <span style="color:#9e8e82;font-size:14px">
                    <i class="bi bi-people-fill me-1"></i>+{{ $guest->companions_count }} accompagnant(s)
                </span>
                @endif
            </div>
            @endif

            {{-- Formulaire RSVP --}}
            @if($guest->rsvp_status === 'pending' || ($wedding->rsvp_modification_allowed && (!$wedding->rsvp_deadline || now()->isBefore($wedding->rsvp_deadline))))
            <form method="POST" action="{{ route('guest.rsvp.submit', $guest->invitation_code) }}" id="rsvpForm">
                @csrf
                @if($errors->any())
                <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:16px;font-size:13px">
                    @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                </div>
                @endif

                <div class="mb-4">
                    <div style="font-weight:700;font-size:13px;margin-bottom:12px;color:var(--wt,#3d2b1f)">
                        {{ $guest->rsvp_status === 'pending' ? 'Serez-vous des nôtres ?' : 'Modifier votre réponse' }}
                    </div>
                    <div class="rsvp-btn-group">
                        <button type="button" class="rsvp-choice {{ old('rsvp_status', $guest->rsvp_status) === 'accepted' ? 'selected' : '' }}" data-status="accepted">
                            <i class="bi bi-check-circle-fill" style="color:#10b981"></i>
                            <span>Avec joie !</span>
                        </button>
                        <button type="button" class="rsvp-choice {{ old('rsvp_status', $guest->rsvp_status) === 'maybe' ? 'selected' : '' }}" data-status="maybe">
                            <i class="bi bi-question-circle-fill" style="color:#f59e0b"></i>
                            <span>À confirmer</span>
                        </button>
                        <button type="button" class="rsvp-choice {{ old('rsvp_status', $guest->rsvp_status) === 'declined' ? 'selected' : '' }}" data-status="declined">
                            <i class="bi bi-x-circle-fill" style="color:#ef4444"></i>
                            <span>Je ne pourrai pas</span>
                        </button>
                    </div>
                    <input type="hidden" name="rsvp_status" id="rsvpStatusInput"
                           value="{{ old('rsvp_status', $guest->rsvp_status === 'pending' ? '' : $guest->rsvp_status) }}" required>
                </div>

                {{-- Présence par étape --}}
                @if($wedding->programItems->count())
                <div class="mb-4" id="programChoices" style="{{ old('rsvp_status', $guest->rsvp_status) === 'declined' ? 'display:none' : '' }}">
                    <div style="font-weight:700;font-size:13px;margin-bottom:12px;color:var(--wt,#3d2b1f)">
                        Présence par étape
                    </div>
                    @foreach($wedding->programItems as $item)
                    @php $existingResp = $guest->responses->firstWhere('wedding_program_item_id', $item->id); @endphp
                    <div style="border:1px solid #e8ddd5;border-radius:10px;padding:14px;margin-bottom:10px">
                        <div class="program-step" style="padding:0 0 12px;border-bottom:1px solid #f0e8e0">
                            <div class="step-icon"><i class="bi {{ $item->icon ?? 'bi-star-fill' }}"></i></div>
                            <div>
                                <div class="step-title">{{ $item->title }}</div>
                                <div class="step-time">{{ $item->getDateTimeFormatted() }}</div>
                            </div>
                        </div>
                        <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap">
                            @foreach(['attending' => ['Présent(e)', '#10b981'], 'not_attending' => ['Absent(e)', '#ef4444'], 'pending' => ['Incertain(e)', '#f59e0b']] as $s => [$sl, $sc])
                            @php $checked = $existingResp && $existingResp->status === $s; @endphp
                            <label style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;padding:6px 14px;border-radius:30px;border:1.5px solid {{ $checked ? $sc : '#e8ddd5' }};background:{{ $checked ? $sc.'15' : 'white' }};font-size:13px;transition:all 0.2s">
                                <input type="radio" name="program_responses[{{ $item->id }}]" value="{{ $s }}" {{ $checked ? 'checked' : '' }} style="display:none" class="program-radio">
                                <span style="color:{{ $checked ? $sc : '#9e8e82' }};font-weight:{{ $checked ? '700' : '400' }}">{{ $sl }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Accompagnants --}}
                @if($guest->companions_allowed)
                <div class="mb-4" id="companionsSection" style="{{ old('rsvp_status', $guest->rsvp_status) === 'declined' ? 'display:none' : '' }}">
                    <label style="font-weight:700;font-size:13px;margin-bottom:8px;display:block;color:var(--wt,#3d2b1f)">
                        Accompagnants (max {{ $guest->max_companions }})
                    </label>
                    <input type="number" name="companions_count" class="rsvp-input mb-3"
                           value="{{ old('companions_count', $guest->companions_count) }}"
                           placeholder="Nombre d'accompagnants" min="0" max="{{ $guest->max_companions }}">

                    <div id="companionsContainer">
                        @foreach($guest->companions as $i => $companion)
                        <div class="companion-row mb-2" style="display:grid;grid-template-columns:1fr 1fr auto;gap:8px">
                            <input type="text" name="companions[{{ $i }}][first_name]" class="rsvp-input"
                                   placeholder="Prénom" value="{{ $companion->first_name }}" required>
                            <input type="text" name="companions[{{ $i }}][last_name]" class="rsvp-input"
                                   placeholder="Nom" value="{{ $companion->last_name }}">
                            <button type="button" onclick="this.closest('.companion-row').remove()"
                                    style="background:#fee2e2;color:#991b1b;border:none;border-radius:8px;padding:0 12px;cursor:pointer;font-size:16px">×</button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="addCompanionBtn" style="background:none;border:1.5px dashed #e8ddd5;color:#9e8e82;padding:10px 16px;border-radius:10px;cursor:pointer;width:100%;margin-top:8px;font-size:13px;transition:all 0.2s">
                        <i class="bi bi-person-plus me-1"></i>Ajouter un accompagnant
                    </button>
                    <input type="hidden" id="maxComp" value="{{ $guest->max_companions }}">
                </div>
                @endif

                {{-- Régime alimentaire --}}
                <div class="mb-4">
                    <label style="font-weight:700;font-size:13px;margin-bottom:8px;display:block;color:var(--wt,#3d2b1f)">
                        Restrictions alimentaires
                    </label>
                    <input type="text" name="dietary_restrictions" class="rsvp-input"
                           value="{{ old('dietary_restrictions', $guest->dietary_restrictions) }}"
                           placeholder="Végétarien, sans gluten, allergie aux noix...">
                </div>

                {{-- Message --}}
                <div class="mb-5">
                    <label style="font-weight:700;font-size:13px;margin-bottom:8px;display:block;color:var(--wt,#3d2b1f)">
                        Un mot pour les mariés <span style="color:#9e8e82;font-weight:400">(optionnel)</span>
                    </label>
                    <textarea name="message" class="rsvp-input" rows="3"
                              placeholder="Votre message...">{{ old('message') }}</textarea>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="bi bi-send me-2"></i>Envoyer ma réponse
                </button>

                @if($wedding->rsvp_deadline)
                <div style="text-align:center;margin-top:12px;font-size:12px;color:#9e8e82">
                    <i class="bi bi-clock me-1"></i>Date limite : {{ $wedding->rsvp_deadline->translatedFormat('d F Y') }}
                </div>
                @endif
            </form>
            @elseif($guest->rsvp_status !== 'pending')
            <div style="background:var(--ws,#f5e6d3);border-radius:10px;padding:12px 16px;font-size:13px;color:#9e8e82;text-align:center">
                <i class="bi bi-lock me-1"></i>La modification de réponse n'est plus disponible.
            </div>
            @endif
        </div>
    </div>

    {{-- ─── Programme ─────────────────────────────────────────── --}}
    @if($wedding->programItems->count())
    <div class="personal-card">
        <div class="pc-header">
            <h3 class="card-section-title"><i class="bi bi-calendar-event-fill"></i>Programme de la journée</h3>
        </div>
        <div class="pc-body" style="padding-top:10px;padding-bottom:10px">
            @foreach($wedding->programItems as $item)
            <div class="program-step">
                <div class="step-icon"><i class="bi {{ $item->icon ?? 'bi-star-fill' }}"></i></div>
                <div class="flex-grow-1">
                    <div class="step-title">{{ $item->title }}</div>
                    <div class="step-time">{{ $item->getDateTimeFormatted() }}</div>
                    @if($item->venue_name) <div class="step-venue"><i class="bi bi-geo-alt me-1"></i>{{ $item->venue_name }}</div> @endif
                    @if($item->description) <div style="font-size:13px;color:#9e8e82;margin-top:4px">{{ $item->description }}</div> @endif
                </div>
                @php $resp = $guest->responses->firstWhere('wedding_program_item_id', $item->id); @endphp
                @if($resp)
                <div style="flex-shrink:0">
                    <span style="font-size:11px;padding:4px 10px;border-radius:20px;font-weight:700;background:{{ $resp->status === 'attending' ? '#d1fae5' : ($resp->status === 'not_attending' ? '#fee2e2' : '#fef3c7') }};color:{{ $resp->status === 'attending' ? '#065f46' : ($resp->status === 'not_attending' ? '#991b1b' : '#92400e') }}">
                        {{ $resp->status === 'attending' ? '✓ Présent(e)' : ($resp->status === 'not_attending' ? '✗ Absent(e)' : '? Incertain(e)') }}
                    </span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ─── Lieux ─────────────────────────────────────────────── --}}
    @if($wedding->venues->count())
    <div class="personal-card">
        <div class="pc-header">
            <h3 class="card-section-title"><i class="bi bi-geo-alt-fill"></i>Lieux</h3>
        </div>
        <div class="pc-body">
            @foreach($wedding->venues as $venue)
            <div style="margin-bottom:16px;{{ !$loop->last ? 'padding-bottom:16px;border-bottom:1px solid #f0e8e0' : '' }}">
                <div style="font-weight:700;color:var(--wt,#3d2b1f);margin-bottom:4px">{{ $venue->name }}</div>
                @if($venue->address) <div style="color:#9e8e82;font-size:13px;margin-bottom:10px"><i class="bi bi-geo-alt me-1"></i>{{ $venue->address }}@if($venue->city), {{ $venue->city }}@endif</div> @endif
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    @if($venue->google_maps_url) <a href="{{ $venue->google_maps_url }}" target="_blank" class="venue-btn venue-btn-maps"><i class="bi bi-map-fill"></i>Google Maps</a> @endif
                    @if($venue->waze_url) <a href="{{ $venue->waze_url }}" target="_blank" class="venue-btn venue-btn-waze"><i class="bi bi-signpost-fill"></i>Waze</a> @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ─── Dress code --}}
    @if($theme?->dress_code_description)
    <div class="personal-card">
        <div class="pc-header">
            <h3 class="card-section-title"><i class="bi bi-stars"></i>Ambiance & dress code</h3>
        </div>
        <div class="pc-body">
            @if($theme->dress_code_style)
            <div style="font-family:var(--ft,'Playfair Display',serif);font-size:20px;color:var(--wt,#3d2b1f);margin-bottom:6px">
                {{ $theme->dress_code_style }}
            </div>
            @endif
            <p style="color:#9e8e82;font-size:14px;line-height:1.7;margin-bottom:16px">{{ $theme->dress_code_description }}</p>

            @if($theme->dress_code_men || $theme->dress_code_women)
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                @if($theme->dress_code_men)
                <div style="background:var(--ws,#f5e6d3);border-radius:10px;padding:14px">
                    <div style="font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--wp,#c8a97e);margin-bottom:6px"><i class="bi bi-person-fill me-1"></i>Hommes</div>
                    <p style="font-size:13px;color:var(--wt,#3d2b1f);margin:0">{{ $theme->dress_code_men }}</p>
                </div>
                @endif
                @if($theme->dress_code_women)
                <div style="background:var(--ws,#f5e6d3);border-radius:10px;padding:14px">
                    <div style="font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--wp,#c8a97e);margin-bottom:6px"><i class="bi bi-person-dress me-1"></i>Femmes</div>
                    <p style="font-size:13px;color:var(--wt,#3d2b1f);margin:0">{{ $theme->dress_code_women }}</p>
                </div>
                @endif
            </div>
            @endif

            @if($theme->forbidden_colors)
            <div style="background:#fff5f5;border:1px solid #fecaca;border-radius:10px;padding:12px 14px;font-size:13px;color:#991b1b">
                <i class="bi bi-x-circle-fill me-2"></i><strong>À éviter :</strong> {{ $theme->forbidden_colors }}
            </div>
            @endif

            {{-- Palette --}}
            @if($wedding->colorPalette->count())
            <div style="margin-top:20px">
                <div style="font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--wp,#c8a97e);margin-bottom:12px">Notre palette</div>
                <div style="display:flex;flex-wrap:wrap;gap:14px">
                    @foreach($wedding->colorPalette as $color)
                    <div style="text-align:center">
                        <div class="swatch" style="background:{{ $color->hex_color }}"></div>
                        <div style="font-size:11px;color:#9e8e82;margin-top:4px">{{ $color->name }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ─── Règles ─────────────────────────────────────────────── --}}
    @if($wedding->rules->count())
    <div class="personal-card">
        <div class="pc-header"><h3 class="card-section-title"><i class="bi bi-shield-check-fill"></i>Consignes importantes</h3></div>
        <div class="pc-body" style="padding-top:12px;padding-bottom:12px">
            @foreach($wedding->rules->where('is_active', true) as $rule)
            <div class="rule-row">
                <i class="bi {{ $rule->icon ?? ($rule->type === 'allowed' ? 'bi-check-circle-fill' : ($rule->type === 'forbidden' ? 'bi-x-circle-fill' : 'bi-star-fill')) }}"
                   style="color:{{ $rule->type === 'allowed' ? '#10b981' : ($rule->type === 'forbidden' ? '#ef4444' : '#f59e0b') }};font-size:16px;margin-top:2px"></i>
                <div>
                    <div style="font-weight:700;font-size:14px;color:var(--wt,#3d2b1f)">{{ $rule->title }}</div>
                    @if($rule->description) <div style="font-size:13px;color:#9e8e82;margin-top:2px">{{ $rule->description }}</div> @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ─── Cadeaux ──────────────────────────────────────────── --}}
    @if($wedding->giftCategories->count())
    <div class="personal-card">
        <div class="pc-header"><h3 class="card-section-title"><i class="bi bi-gift-fill"></i>Liste de mariage</h3></div>
        <div class="pc-body" style="padding-top:10px;padding-bottom:10px">
            <p style="color:#9e8e82;font-size:13px;margin-bottom:16px;font-style:italic">Votre présence est le plus beau des cadeaux. Si vous souhaitez néanmoins nous gâter…</p>
            @foreach($wedding->giftCategories as $cat)
                @foreach($cat->items as $gift)
                <div class="gift-mini">
                    @if($gift->image)
                    <img src="{{ Storage::url($gift->image) }}" style="width:48px;height:48px;object-fit:cover;border-radius:8px;flex-shrink:0">
                    @else
                    <div style="width:48px;height:48px;background:var(--ws,#f5e6d3);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="bi bi-gift" style="color:var(--wp,#c8a97e)"></i>
                    </div>
                    @endif
                    <div class="flex-grow-1">
                        <div style="font-weight:600;font-size:14px;color:var(--wt,#3d2b1f)">{{ $gift->name }}</div>
                        @if($gift->price) <div style="font-size:12px;color:var(--wp,#c8a97e)">{{ number_format($gift->price, 0) }} €</div>
                        @elseif($gift->free_contribution) <div style="font-size:12px;color:var(--wp,#c8a97e)">Participation libre</div> @endif
                    </div>
                    @if($gift->is_reserved)
                    <span style="background:#d1fae5;color:#065f46;font-size:11px;padding:3px 8px;border-radius:20px;font-weight:700">Réservé</span>
                    @elseif($gift->external_link)
                    <a href="{{ $gift->external_link }}" target="_blank" style="color:var(--wp,#c8a97e);font-size:13px;text-decoration:none">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                    @endif
                </div>
                @endforeach
            @endforeach
        </div>
    </div>
    @endif

    {{-- ─── Hébergement ────────────────────────────────────────── --}}
    @if($wedding->accommodation_details)
    <div class="personal-card">
        <div class="pc-header"><h3 class="card-section-title"><i class="bi bi-house-heart-fill"></i>{{ $wedding->accommodation_info ?? 'Hébergement' }}</h3></div>
        <div class="pc-body" style="font-size:14px;line-height:1.8;color:#9e8e82">{!! nl2br(e($wedding->accommodation_details)) !!}</div>
    </div>
    @endif

    {{-- ─── Partage --}}
    <div style="text-align:center;padding:20px 0;color:#9e8e82;font-size:13px">
        <div style="font-family:var(--ft,'Playfair Display',serif);font-size:20px;color:var(--wt,#3d2b1f);margin-bottom:8px">
            {{ $wedding->getCoupleName() }}
        </div>
        <div>{{ $wedding->getWeddingDateFormatted() }}</div>
        <div style="margin-top:16px;display:flex;justify-content:center;gap:12px">
            <a href="{{ route('wedding.public', $wedding->slug) }}"
               style="background:var(--ws,#f5e6d3);color:var(--wt,#3d2b1f);padding:10px 18px;border-radius:30px;text-decoration:none;font-size:13px;font-weight:600">
                <i class="bi bi-heart me-1"></i>Voir l'invitation
            </a>
            @php
                $shareText = urlencode('Je suis invité(e) au mariage de ' . $wedding->getCoupleName() . ' ! ' . $wedding->getWeddingDateFormatted());
                $shareUrl = urlencode(route('wedding.public', $wedding->slug));
            @endphp
            <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}"
               target="_blank"
               style="background:#25d366;color:white;padding:10px 18px;border-radius:30px;text-decoration:none;font-size:13px;font-weight:600">
                <i class="bi bi-whatsapp me-1"></i>Partager
            </a>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/js/glightbox.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ─── Choix RSVP ──────────────────────────────────────────
    const choices = document.querySelectorAll('.rsvp-choice');
    const statusInput = document.getElementById('rsvpStatusInput');

    choices.forEach(btn => {
        btn.addEventListener('click', function () {
            choices.forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            if (statusInput) statusInput.value = this.dataset.status;

            // Afficher/masquer accompagnants et programme selon le statut
            const isDeclined = this.dataset.status === 'declined';
            const progSection = document.getElementById('programChoices');
            const compSection = document.getElementById('companionsSection');
            if (progSection) progSection.style.display = isDeclined ? 'none' : '';
            if (compSection) compSection.style.display = isDeclined ? 'none' : '';
        });
    });

    // ─── Radio buttons programme ─────────────────────────────
    document.querySelectorAll('.program-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            const group = this.closest('[style*="margin-bottom:10px"]') || this.closest('div[style*="border"]');
            group?.querySelectorAll('label').forEach(label => {
                const r = label.querySelector('.program-radio');
                const isChecked = r === this;
                const color = { attending: '#10b981', not_attending: '#ef4444', pending: '#f59e0b' }[r?.value] || '#e8ddd5';
                label.style.borderColor = isChecked ? color : '#e8ddd5';
                label.style.background = isChecked ? color + '15' : 'white';
                const span = label.querySelector('span');
                if (span) { span.style.color = isChecked ? color : '#9e8e82'; span.style.fontWeight = isChecked ? '700' : '400'; }
            });
        });
    });

    // ─── Ajouter accompagnant ─────────────────────────────────
    let compCount = {{ $guest->companions->count() }};
    const addBtn = document.getElementById('addCompanionBtn');
    const maxComp = parseInt(document.getElementById('maxComp')?.value || '5');

    if (addBtn) {
        addBtn.addEventListener('click', () => {
            if (compCount >= maxComp) { addBtn.textContent = 'Nombre maximum atteint'; return; }
            compCount++;
            const container = document.getElementById('companionsContainer');
            const row = document.createElement('div');
            row.className = 'companion-row mb-2';
            row.style.cssText = 'display:grid;grid-template-columns:1fr 1fr auto;gap:8px';
            row.innerHTML = `
                <input type="text" name="companions[${compCount}][first_name]" class="rsvp-input" placeholder="Prénom *" required>
                <input type="text" name="companions[${compCount}][last_name]" class="rsvp-input" placeholder="Nom">
                <button type="button" onclick="this.closest('.companion-row').remove()" style="background:#fee2e2;color:#991b1b;border:none;border-radius:8px;padding:0 12px;cursor:pointer;font-size:16px">×</button>
            `;
            container?.appendChild(row);
        });
    }

    // ─── Animation d'entrée ───────────────────────────────────
    if (typeof gsap !== 'undefined') {
        gsap.from('.personal-card', {
            opacity: 0, y: 30, stagger: 0.1, duration: 0.7, ease: 'power2.out', delay: 0.2
        });
    }
});
</script>
</body>
</html>
