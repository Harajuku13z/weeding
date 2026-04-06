<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace — {{ $guest->wedding->getCoupleName() }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/wedding.css') }}">
    @php $wedding = $guest->wedding; $theme = $wedding->theme; @endphp
    @if($theme) <style>:root { {{ $theme->getCssVariables() }} }</style> @endif
</head>
<body style="background:var(--wbg,#fdfaf7);font-family:var(--fb,'Lato',sans-serif)">

<!-- Header -->
<header style="background:linear-gradient(135deg,#1a1018,#2d1f2b);padding:20px 24px;position:sticky;top:0;z-index:100">
    <div style="max-width:900px;margin:0 auto;display:flex;align-items:center;justify-content:space-between">
        <div style="font-family:var(--ft,serif);color:white;font-size:18px">
            {{ $wedding->bride_name }} & {{ $wedding->groom_name }}
        </div>
        <div style="display:flex;align-items:center;gap:12px">
            <a href="{{ route('wedding.public', $wedding->slug) }}"
               style="color:rgba(255,255,255,.7);font-size:13px;text-decoration:none">
                <i class="bi bi-eye me-1"></i>Voir l'invitation
            </a>
            <form method="POST" action="{{ route('guest.logout') }}" style="display:inline">
                @csrf
                <button type="submit" style="background:none;border:none;color:rgba(255,255,255,.6);cursor:pointer;font-size:13px">
                    <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                </button>
            </form>
        </div>
    </div>
</header>

<div style="max-width:900px;margin:0 auto;padding:30px 20px">

    <!-- Bienvenue -->
    <div style="text-align:center;margin-bottom:36px">
        <div style="font-size:40px;margin-bottom:12px">💌</div>
        <h1 style="font-family:var(--ft,serif);font-size:28px;margin:0;color:var(--wt,#3d2b1f)">
            Bonjour {{ $guest->first_name }} !
        </h1>
        <p style="color:#9e8e82;margin-top:6px">
            Voici votre espace personnel pour le mariage de {{ $wedding->getCoupleName() }}
        </p>
    </div>

    @if(session('success'))
    <div style="background:#d1fae5;color:#065f46;padding:12px 16px;border-radius:10px;margin-bottom:20px">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
    @endif

    <!-- Statut RSVP -->
    <div style="background:white;border-radius:16px;padding:24px;box-shadow:0 2px 16px rgba(0,0,0,.08);margin-bottom:20px;border-left:4px solid {{ $guest->rsvp_status === 'accepted' ? '#10b981' : ($guest->rsvp_status === 'declined' ? '#ef4444' : '#f59e0b') }}">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
                <div style="font-size:12px;color:#9e8e82;text-transform:uppercase;letter-spacing:2px;margin-bottom:6px">Votre réponse</div>
                <span style="background:{{ $guest->rsvp_status === 'accepted' ? '#10b981' : ($guest->rsvp_status === 'declined' ? '#ef4444' : '#f59e0b') }};color:white;padding:6px 16px;border-radius:20px;font-weight:700;font-size:14px">
                    {{ $guest->status_label }}
                </span>
            </div>
            @if($wedding->rsvp_modification_allowed && (!$wedding->rsvp_deadline || now()->isBefore($wedding->rsvp_deadline)))
            <a href="{{ route('rsvp.show', ['wedding' => $wedding->slug, 'code' => $guest->invitation_code]) }}"
               style="background:linear-gradient(135deg,var(--wp,#c8a97e),var(--wa,#8b6355));color:white;padding:10px 20px;border-radius:10px;text-decoration:none;font-size:13px;font-weight:600">
                <i class="bi bi-pencil me-1"></i>Modifier ma réponse
            </a>
            @endif
        </div>
        @if($guest->rsvp_status === 'pending')
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid #e8ddd5">
            <a href="{{ route('rsvp.show', ['wedding' => $wedding->slug, 'code' => $guest->invitation_code]) }}"
               style="background:linear-gradient(135deg,var(--wp,#c8a97e),var(--wa,#8b6355));color:white;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:600;display:inline-block">
                <i class="bi bi-send me-2"></i>Répondre à l'invitation
            </a>
        </div>
        @endif
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

        <!-- Accompagnants -->
        @if($guest->companions->count())
        <div style="background:white;border-radius:16px;padding:20px;box-shadow:0 2px 16px rgba(0,0,0,.08)">
            <h3 style="font-family:var(--ft,serif);font-size:16px;margin:0 0 14px;color:var(--wt,#3d2b1f)">
                <i class="bi bi-people-fill me-2" style="color:var(--wp,#c8a97e)"></i>Vos accompagnants
            </h3>
            @foreach($guest->companions as $companion)
            <div style="padding:8px 0;border-bottom:1px solid #f0e8e0;display:flex;align-items:center;gap:10px">
                <div style="width:28px;height:28px;background:linear-gradient(135deg,var(--wp,#c8a97e),var(--wa,#8b6355));border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:700">
                    {{ substr($companion->first_name, 0, 1) }}
                </div>
                <span>{{ $companion->first_name }} {{ $companion->last_name }}</span>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Programme -->
        @if($wedding->programItems->count())
        <div style="background:white;border-radius:16px;padding:20px;box-shadow:0 2px 16px rgba(0,0,0,.08)">
            <h3 style="font-family:var(--ft,serif);font-size:16px;margin:0 0 14px;color:var(--wt,#3d2b1f)">
                <i class="bi bi-calendar-event-fill me-2" style="color:var(--wp,#c8a97e)"></i>Programme
            </h3>
            @foreach($wedding->programItems->take(5) as $item)
            <div style="padding:8px 0;border-bottom:1px solid #f0e8e0">
                <div style="font-weight:600;font-size:13px">{{ $item->title }}</div>
                <div style="color:#9e8e82;font-size:12px">{{ $item->getDateTimeFormatted() }}</div>
                @php $response = $guest->responses->firstWhere('wedding_program_item_id', $item->id); @endphp
                @if($response)
                <span style="font-size:11px;padding:2px 8px;border-radius:10px;background:{{ $response->status === 'attending' ? '#d1fae5' : '#fee2e2' }};color:{{ $response->status === 'attending' ? '#065f46' : '#991b1b' }}">
                    {{ $response->status === 'attending' ? 'Présent' : 'Absent' }}
                </span>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        <!-- Dress code -->
        @if($theme?->dress_code_description)
        <div style="background:white;border-radius:16px;padding:20px;box-shadow:0 2px 16px rgba(0,0,0,.08)">
            <h3 style="font-family:var(--ft,serif);font-size:16px;margin:0 0 14px;color:var(--wt,#3d2b1f)">
                <i class="bi bi-stars me-2" style="color:var(--wp,#c8a97e)"></i>Dress code
            </h3>
            @if($theme->dress_code_style)
            <div style="font-weight:600;margin-bottom:6px">{{ $theme->dress_code_style }}</div>
            @endif
            <p style="color:#9e8e82;font-size:13px;margin:0">{{ $theme->dress_code_description }}</p>
            @if($theme->forbidden_colors)
            <div style="margin-top:10px;padding:8px 12px;background:#fee2e2;border-radius:8px;font-size:12px;color:#991b1b">
                <i class="bi bi-x-circle me-1"></i>À éviter : {{ $theme->forbidden_colors }}
            </div>
            @endif
        </div>
        @endif

        <!-- Palette couleurs -->
        @if($wedding->colorPalette->count())
        <div style="background:white;border-radius:16px;padding:20px;box-shadow:0 2px 16px rgba(0,0,0,.08)">
            <h3 style="font-family:var(--ft,serif);font-size:16px;margin:0 0 14px;color:var(--wt,#3d2b1f)">
                <i class="bi bi-palette-fill me-2" style="color:var(--wp,#c8a97e)"></i>Palette couleurs
            </h3>
            <div style="display:flex;flex-wrap:wrap;gap:10px">
                @foreach($wedding->colorPalette as $color)
                <div style="text-align:center">
                    <div style="width:44px;height:44px;border-radius:50%;background:{{ $color->hex_color }};box-shadow:0 2px 8px rgba(0,0,0,.15);margin-bottom:4px"></div>
                    <div style="font-size:11px;color:#9e8e82">{{ $color->name }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Cadeaux -->
        @if($wedding->giftItems->count())
        <div style="background:white;border-radius:16px;padding:20px;box-shadow:0 2px 16px rgba(0,0,0,.08);grid-column:span 2">
            <h3 style="font-family:var(--ft,serif);font-size:16px;margin:0 0 14px;color:var(--wt,#3d2b1f)">
                <i class="bi bi-gift-fill me-2" style="color:var(--wp,#c8a97e)"></i>Liste de cadeaux
            </h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px">
                @foreach($wedding->giftItems->take(6) as $gift)
                <div style="border:1px solid #e8ddd5;border-radius:10px;overflow:hidden">
                    @if($gift->image)
                    <img src="{{ Storage::url($gift->image) }}" style="width:100%;height:100px;object-fit:cover">
                    @endif
                    <div style="padding:10px">
                        <div style="font-weight:600;font-size:13px;margin-bottom:4px">{{ $gift->name }}</div>
                        @if($gift->price) <div style="color:var(--wp,#c8a97e);font-size:12px">{{ number_format($gift->price, 0) }} €</div> @endif
                        @if($gift->is_reserved)
                        <span style="background:#d1fae5;color:#065f46;font-size:11px;padding:2px 8px;border-radius:10px">Réservé</span>
                        @elseif($gift->external_link)
                        <a href="{{ $gift->external_link }}" target="_blank" style="color:var(--wp,#c8a97e);font-size:12px;display:block;margin-top:4px">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Voir
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Relances reçues -->
        @if($guest->reminders->count())
        <div style="background:white;border-radius:16px;padding:20px;box-shadow:0 2px 16px rgba(0,0,0,.08);grid-column:span 2">
            <h3 style="font-family:var(--ft,serif);font-size:16px;margin:0 0 14px;color:var(--wt,#3d2b1f)">
                <i class="bi bi-bell-fill me-2" style="color:var(--wp,#c8a97e)"></i>Mes messages
            </h3>
            @foreach($guest->reminders->where('status', 'sent')->take(5) as $reminder)
            <div style="padding:12px;border:1px solid #e8ddd5;border-radius:10px;margin-bottom:10px">
                <div style="font-size:12px;color:#9e8e82;margin-bottom:6px">
                    <i class="bi bi-{{ $reminder->channel === 'email' ? 'envelope' : 'phone' }} me-1"></i>
                    {{ $reminder->sent_at?->format('d/m/Y H:i') }}
                </div>
                <p style="margin:0;font-size:14px;color:var(--wt,#3d2b1f)">{{ Str::limit($reminder->message_content, 200) }}</p>
            </div>
            @endforeach
        </div>
        @endif

    </div>

    <!-- Retour à l'invitation -->
    <div style="text-align:center;margin-top:30px">
        <a href="{{ route('wedding.public', $wedding->slug) }}"
           style="color:var(--wp,#c8a97e);text-decoration:none;font-size:14px">
            <i class="bi bi-heart me-1"></i>Voir l'invitation complète
        </a>
    </div>
</div>

</body>
</html>
