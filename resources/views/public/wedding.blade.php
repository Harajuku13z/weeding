<!DOCTYPE html>
<html lang="{{ $wedding->language ?? 'fr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $wedding->seo_title ?? $wedding->getCoupleName() . ' — Notre mariage' }}</title>
    <meta name="description" content="{{ $wedding->seo_description ?? 'Invitation au mariage de ' . $wedding->getCoupleName() }}">

    @if($wedding->social_image)
    <meta property="og:image" content="{{ Storage::url($wedding->social_image) }}">
    @endif
    <meta property="og:title" content="{{ $wedding->seo_title ?? $wedding->getCoupleName() }}">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500&family=Jost:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/css/glightbox.min.css">
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/wedding.css') }}">

    @php $theme = $wedding->theme; @endphp
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
            --border-radius: {{ $theme->border_radius }};
        }
    </style>
    @endif

    <style>
    :root {
        --cream:      var(--color-background, #faf7f2);
        --ivory:      var(--color-secondary, #f3efe8);
        --dark:       var(--color-text, #1c1917);
        --gold:       var(--color-primary, #c5a47e);
        --gold-light: var(--color-accent, #dcc9a8);
        --wt:         var(--color-text, #2d2926);
        --wm:         #8a827a;
        --ft:         var(--font-title, 'Cormorant Garamond', Georgia, serif);
        --fb:         var(--font-body, 'Jost', sans-serif);
        --radius:     var(--border-radius, 12px);
        --ease:       cubic-bezier(.4,0,.2,1);
    }

    *, *::before, *::after { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body {
        margin: 0;
        background: var(--cream);
        color: var(--wt);
        font-family: var(--fb);
        font-weight: 300;
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
    }

    /* ═══════════════════════════════════════════════════════
       INVITATION OVERLAY — Cinematic Split Reveal
    ═══════════════════════════════════════════════════════ */
    .inv-overlay {
        position: fixed; inset: 0; z-index: 9999;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden;
    }
    .inv-panel {
        position: absolute; top: 0; width: 50%; height: 100%;
        will-change: transform;
    }
    .inv-panel--left {
        left: 0;
        background: linear-gradient(135deg, #0c0a10 0%, #16121e 50%, #0a080d 100%);
    }
    .inv-panel--right {
        right: 0;
        background: linear-gradient(225deg, #0c0a10 0%, #16121e 50%, #0a080d 100%);
    }
    .inv-split-line {
        position: absolute; top: 8%; bottom: 8%; left: 50%;
        width: 1px; z-index: 2;
        background: linear-gradient(to bottom, transparent, var(--gold) 25%, var(--gold) 75%, transparent);
        opacity: .2;
        transform: translateX(-50%);
    }
    .inv-split-glow {
        position: absolute; top: 30%; bottom: 30%; left: 50%;
        width: 100px; z-index: 1;
        background: radial-gradient(ellipse at center, rgba(197,164,126,.05), transparent 70%);
        transform: translateX(-50%);
    }
    .inv-particles { position: absolute; inset: 0; overflow: hidden; pointer-events: none; z-index: 3; }
    .inv-particle {
        position: absolute; border-radius: 50%;
        background: var(--gold); opacity: 0;
        animation: invFloat var(--dur,8s) var(--delay,0s) infinite ease-in-out;
    }
    @keyframes invFloat {
        0%   { transform: translateY(0) scale(1); opacity: 0; }
        12%  { opacity: var(--op,.12); }
        88%  { opacity: var(--op,.12); }
        100% { transform: translateY(-100vh) scale(.3); opacity: 0; }
    }
    .inv-content {
        position: relative; z-index: 4;
        text-align: center;
        padding: 40px 24px;
        max-width: 680px;
    }
    .inv-label {
        font-family: var(--fb);
        font-size: 10px;
        letter-spacing: 7px;
        text-transform: uppercase;
        color: rgba(255,255,255,.25);
        margin-bottom: 32px;
        font-weight: 300;
    }
    .inv-ornament {
        display: flex; align-items: center; justify-content: center;
        gap: 18px; margin-bottom: 36px;
    }
    .inv-orn-line {
        width: 70px; height: 1px;
        background: linear-gradient(90deg, transparent, rgba(197,164,126,.5));
    }
    .inv-orn-line:last-child {
        background: linear-gradient(270deg, transparent, rgba(197,164,126,.5));
    }
    .inv-orn-diamond {
        width: 5px; height: 5px;
        background: var(--gold);
        transform: rotate(45deg);
        box-shadow: 0 0 16px rgba(197,164,126,.35);
    }
    .inv-names {
        font-family: var(--ft);
        font-size: clamp(48px, 11vw, 90px);
        font-weight: 400;
        color: #fff;
        line-height: 1.05;
        margin: 0 0 8px;
        letter-spacing: -.02em;
    }
    .inv-name { display: inline-block; }
    .inv-amp {
        display: block;
        font-style: italic;
        color: var(--gold);
        font-size: .35em;
        line-height: 2.2;
        font-weight: 300;
    }
    .inv-date {
        display: inline-flex; align-items: center; gap: 18px;
        font-family: var(--fb);
        font-size: 11px;
        letter-spacing: 6px;
        text-transform: uppercase;
        color: rgba(255,255,255,.38);
        margin-bottom: 52px;
        font-weight: 300;
    }
    .inv-date-line {
        width: 28px; height: 1px;
        background: rgba(255,255,255,.1);
    }
    .inv-open-btn {
        display: inline-flex; align-items: center; gap: 14px;
        background: transparent;
        border: 1px solid rgba(197,164,126,.3);
        color: var(--gold);
        padding: 17px 42px;
        border-radius: 999px;
        font-family: var(--fb);
        font-size: 10px;
        letter-spacing: 5px;
        text-transform: uppercase;
        cursor: pointer;
        transition: all .5s var(--ease);
        margin-bottom: 40px;
        font-weight: 400;
    }
    .inv-open-btn:hover {
        border-color: var(--gold);
        background: rgba(197,164,126,.08);
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(197,164,126,.15);
    }
    .inv-btn-icon {
        display: flex; align-items: center;
        transition: transform .4s var(--ease);
    }
    .inv-open-btn:hover .inv-btn-icon { transform: translateX(5px); }
    .inv-hint {
        font-family: var(--fb);
        font-size: 9px;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: rgba(255,255,255,.12);
        animation: invPulse 3s ease-in-out infinite;
    }
    @keyframes invPulse {
        0%, 100% { opacity: .12; }
        50% { opacity: .3; }
    }

    /* ═══════════════════════════════════════════════════════
       NAVIGATION
    ═══════════════════════════════════════════════════════ */
    .wedding-nav {
        position: fixed; top: 0; left: 0; right: 0; z-index: 100;
        padding: 0 56px; height: 80px;
        display: flex; align-items: center; justify-content: space-between;
        transition: all .5s var(--ease);
    }
    .wedding-nav.scrolled {
        height: 68px;
        background: rgba(250,247,242,.92);
        backdrop-filter: blur(24px) saturate(1.2);
        box-shadow: 0 1px 0 rgba(197,164,126,.1);
    }
    .nav-couple {
        font-family: var(--ft);
        font-size: 24px;
        font-weight: 500;
        color: white;
        transition: color .5s var(--ease);
        letter-spacing: .02em;
    }
    .wedding-nav.scrolled .nav-couple { color: var(--dark); }
    .nav-links { display: flex; gap: 40px; }
    .nav-links a {
        font-family: var(--fb);
        font-size: 10px;
        letter-spacing: 3px;
        text-transform: uppercase;
        font-weight: 400;
        color: rgba(255,255,255,.45);
        text-decoration: none;
        transition: color .3s var(--ease);
        position: relative;
    }
    .nav-links a::after {
        content: '';
        position: absolute; bottom: -4px; left: 0; right: 0;
        height: 1px; background: var(--gold);
        transform: scaleX(0); transform-origin: center;
        transition: transform .4s var(--ease);
    }
    .nav-links a:hover::after { transform: scaleX(1); }
    .wedding-nav.scrolled .nav-links a { color: var(--wm); }
    .nav-links a:hover { color: var(--gold) !important; }

    /* ═══════════════════════════════════════════════════════
       HERO — Cinematic Luxury
    ═══════════════════════════════════════════════════════ */
    .hero-section {
        position: relative;
        height: 100vh;
        height: 100dvh;
        min-height: 700px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        isolation: isolate;
    }
    .hero-bg {
        position: absolute;
        inset: -40px;
        background-size: cover;
        background-position: center;
        transform: scale(1.08);
        will-change: transform;
    }
    .hero-overlay {
        position: absolute; inset: 0; z-index: 1;
        background:
            linear-gradient(to bottom, rgba(0,0,0,.15) 0%, rgba(0,0,0,.35) 45%, rgba(0,0,0,.65) 100%);
    }
    .hero-vignette {
        position: absolute; inset: 0; z-index: 1;
        background: radial-gradient(ellipse 70% 60% at 50% 45%, transparent 0%, rgba(0,0,0,.3) 100%);
    }
    .hero-light {
        position: absolute;
        border-radius: 50%;
        filter: blur(100px);
        pointer-events: none;
        z-index: 1;
        mix-blend-mode: soft-light;
    }
    .hero-light-1 {
        width: 400px; height: 400px;
        background: rgba(197,164,126,.2);
        top: 15%; left: 5%;
        animation: floatLight 12s ease-in-out infinite;
    }
    .hero-light-2 {
        width: 300px; height: 300px;
        background: rgba(255,255,255,.06);
        bottom: 15%; right: 8%;
        animation: floatLight 15s ease-in-out infinite reverse;
    }
    @keyframes floatLight {
        0%, 100% { transform: translate3d(0,0,0); }
        50% { transform: translate3d(0,-20px,0); }
    }
    .hero-particles {
        position: absolute; inset: 0; z-index: 1;
        pointer-events: none; overflow: hidden;
    }
    .hero-particle {
        position: absolute; border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,.9), rgba(197,164,126,.15));
        opacity: 0;
        animation: heroFloat var(--dur, 12s) linear infinite;
    }
    @keyframes heroFloat {
        0%   { transform: translateY(20px) scale(.5); opacity: 0; }
        10%  { opacity: var(--op, .12); }
        90%  { opacity: var(--op, .12); }
        100% { transform: translateY(-110vh) scale(1.2); opacity: 0; }
    }
    .hero-ring {
        position: absolute;
        border-radius: 50%;
        border: 1px solid rgba(197,164,126,.1);
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1;
        animation: ringPulse 12s ease-in-out infinite;
    }
    .hero-ring-1 { width: 500px; height: 500px; }
    .hero-ring-2 { width: 800px; height: 800px; animation-delay: 2s; opacity: .6; }
    @keyframes ringPulse {
        0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: .06; }
        50% { transform: translate(-50%, -50%) scale(1.06); opacity: .12; }
    }

    .hero-content {
        position: relative; z-index: 2;
        text-align: center;
        padding: 0 24px;
        max-width: 1000px;
    }
    .hero-tagline {
        font-family: var(--fb);
        font-size: 10px;
        letter-spacing: .6em;
        text-transform: uppercase;
        color: var(--gold);
        font-weight: 400;
        display: block;
        margin-bottom: 28px;
    }
    .hero-ornament {
        display: flex; align-items: center; justify-content: center;
        gap: 20px; margin-bottom: 28px;
    }
    .ornament-line {
        width: 80px; height: 1px;
        background: linear-gradient(90deg, transparent, rgba(197,164,126,.6));
    }
    .ornament-line.right {
        background: linear-gradient(270deg, transparent, rgba(197,164,126,.6));
    }
    .ornament-diamond {
        width: 6px; height: 6px;
        background: var(--gold);
        transform: rotate(45deg);
        box-shadow: 0 0 20px rgba(197,164,126,.3);
    }
    .hero-names {
        font-family: var(--ft);
        font-size: clamp(56px, 12vw, 120px);
        font-weight: 400;
        color: #fff;
        line-height: .95;
        letter-spacing: -.03em;
        margin-bottom: 32px;
    }
    .hero-name { display: inline-block; }
    .ampersand {
        display: block;
        font-style: italic;
        color: var(--gold);
        font-size: .4em;
        line-height: 1.6;
        font-weight: 300;
    }
    .hero-date {
        display: inline-flex; align-items: center; gap: 14px;
        font-family: var(--fb);
        font-size: 11px;
        letter-spacing: .35em;
        text-transform: uppercase;
        color: rgba(255,255,255,.8);
        font-weight: 300;
        margin-bottom: 44px;
    }
    .hero-date i { color: var(--gold); font-size: 14px; }
    .hero-date-sep {
        width: 4px; height: 4px; border-radius: 50%;
        background: rgba(197,164,126,.4);
    }

    .countdown-timer {
        display: flex; gap: 12px; justify-content: center;
        margin-bottom: 48px;
    }
    .countdown-item {
        display: flex; flex-direction: column; align-items: center;
        min-width: 80px; padding: 20px 16px;
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.08);
        backdrop-filter: blur(20px);
        border-radius: 16px;
    }
    .countdown-number {
        font-family: var(--ft);
        font-size: 44px;
        font-weight: 500;
        color: #fff;
        line-height: 1;
    }
    .countdown-label {
        font-family: var(--fb);
        font-size: 8px;
        letter-spacing: .35em;
        text-transform: uppercase;
        color: var(--gold);
        margin-top: 8px;
        font-weight: 400;
    }

    .hero-actions {
        display: flex; align-items: center; justify-content: center;
        gap: 16px; flex-wrap: wrap;
    }
    .hero-cta {
        display: inline-flex; align-items: center; justify-content: center; gap: 10px;
        font-family: var(--fb);
        font-size: 10px;
        letter-spacing: .35em;
        text-transform: uppercase;
        font-weight: 500;
        color: var(--dark);
        background: var(--gold);
        padding: 18px 40px;
        text-decoration: none;
        border-radius: 999px;
        transition: all .5s var(--ease);
        box-shadow: 0 8px 32px rgba(197,164,126,.25);
    }
    .hero-cta:hover {
        transform: translateY(-3px);
        color: var(--dark);
        box-shadow: 0 16px 48px rgba(197,164,126,.35);
        filter: brightness(1.08);
    }
    .hero-cta-secondary {
        background: transparent;
        color: #fff;
        border: 1px solid rgba(255,255,255,.15);
        box-shadow: none;
    }
    .hero-cta-secondary:hover {
        color: #fff;
        background: rgba(255,255,255,.08);
        border-color: rgba(255,255,255,.25);
        box-shadow: 0 8px 32px rgba(0,0,0,.15);
    }
    .hero-scroll-indicator {
        position: absolute; left: 50%; bottom: 32px;
        transform: translateX(-50%); z-index: 2;
        display: inline-flex; flex-direction: column;
        align-items: center; gap: 8px;
        text-decoration: none;
        color: rgba(255,255,255,.5);
        font-size: 9px;
        letter-spacing: .35em;
        text-transform: uppercase;
        animation: scrollFloat 3s ease-in-out infinite;
    }
    .hero-scroll-indicator i { font-size: 16px; color: var(--gold); }
    @keyframes scrollFloat {
        0%, 100% { transform: translateX(-50%) translateY(0); opacity: .5; }
        50% { transform: translateX(-50%) translateY(8px); opacity: .9; }
    }

    /* ═══════════════════════════════════════════════════════
       SECTIONS — Luxe Spacing & Typography
    ═══════════════════════════════════════════════════════ */
    .wedding-section { padding: 160px 0; position: relative; overflow: hidden; }
    .section-inner { max-width: 1100px; margin: 0 auto; padding: 0 40px; }
    .alt-bg { background: var(--ivory); }
    .dark-bg { background: linear-gradient(170deg, #141210 0%, #0c0a08 100%); color: var(--cream); }

    .section-header { text-align: center; margin-bottom: 80px; }
    .section-ornament {
        display: flex; align-items: center; justify-content: center;
        gap: 16px; margin-bottom: 20px;
    }
    .section-ornament-line {
        width: 48px; height: 1px;
        background: linear-gradient(90deg, transparent, rgba(197,164,126,.5));
    }
    .section-ornament-line:last-child {
        background: linear-gradient(270deg, transparent, rgba(197,164,126,.5));
    }
    .section-ornament-icon { color: var(--gold); font-size: 10px; }
    .section-label {
        font-family: var(--fb);
        font-size: 9px;
        letter-spacing: 6px;
        text-transform: uppercase;
        color: var(--gold);
        font-weight: 400;
        display: block;
        margin-bottom: 16px;
    }
    .section-title {
        font-family: var(--ft);
        font-size: clamp(36px, 5.5vw, 62px);
        font-weight: 400;
        color: var(--wt);
        line-height: 1.05;
        margin-bottom: 20px;
        letter-spacing: -.02em;
    }
    .section-subtitle {
        font-size: 15px;
        font-weight: 300;
        color: var(--wm);
        line-height: 1.85;
        max-width: 520px;
        margin-inline: auto;
    }

    /* STORY */
    .story-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 100px;
        align-items: center;
    }
    .story-image { position: relative; }
    .story-image-frame {
        position: relative;
        border-radius: 4px;
        overflow: hidden;
    }
    .story-image-frame::before {
        content: '';
        position: absolute;
        inset: -16px -16px 16px 16px;
        border: 1px solid rgba(197,164,126,.15);
        border-radius: 4px;
        pointer-events: none;
    }
    .story-image-frame img {
        width: 100%;
        aspect-ratio: 3/4;
        object-fit: cover;
        display: block;
    }
    .story-quote {
        font-family: var(--ft);
        font-size: 24px;
        font-style: italic;
        font-weight: 300;
        line-height: 1.55;
        color: var(--dark);
        margin-bottom: 28px;
        padding-left: 28px;
        border-left: 2px solid var(--gold);
    }
    .story-body {
        font-size: 15px;
        font-weight: 300;
        line-height: 2;
        color: var(--wm);
    }

    /* GALLERY */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 8px;
    }
    .gallery-item {
        position: relative;
        overflow: hidden;
        aspect-ratio: 1;
        border-radius: 4px;
    }
    .gallery-item img {
        width: 100%; height: 100%;
        object-fit: cover; display: block;
        transition: transform .8s var(--ease);
    }
    .gallery-item:hover img { transform: scale(1.06); }
    .gallery-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,.6) 0%, transparent 40%);
        display: flex; align-items: flex-end;
        padding: 24px;
        opacity: 0;
        transition: opacity .4s var(--ease);
    }
    .gallery-item:hover .gallery-overlay { opacity: 1; }
    .gallery-caption {
        font-family: var(--ft);
        font-size: 16px;
        color: white;
        font-style: italic;
    }

    /* DETAILS */
    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }
    .detail-card {
        background: white;
        border: 1px solid rgba(197,164,126,.08);
        padding: 48px 28px;
        text-align: center;
        border-radius: 4px;
        transition: all .5s var(--ease);
        position: relative;
    }
    .detail-card::after {
        content: '';
        position: absolute; bottom: 0; left: 20%; right: 20%;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--gold), transparent);
        opacity: 0;
        transition: opacity .5s var(--ease);
    }
    .detail-card:hover { transform: translateY(-4px); }
    .detail-card:hover::after { opacity: 1; }
    .detail-icon {
        font-size: 20px;
        color: var(--gold);
        margin-bottom: 24px;
        display: block;
    }
    .detail-label {
        font-family: var(--fb);
        font-size: 9px;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: var(--wm);
        font-weight: 400;
        display: block;
        margin-bottom: 12px;
    }
    .detail-value {
        font-family: var(--ft);
        font-size: 22px;
        font-weight: 500;
        color: var(--wt);
    }
    .detail-desc {
        font-size: 13px;
        color: var(--wm);
        margin-top: 6px;
        font-weight: 300;
    }

    /* PROGRAMME */
    .program-timeline {
        position: relative;
        max-width: 760px;
        margin: 0 auto;
    }
    .program-timeline::before {
        content: '';
        position: absolute;
        left: 50%; top: 0; bottom: 0;
        width: 1px;
        background: linear-gradient(to bottom, transparent, var(--gold) 8%, var(--gold) 92%, transparent);
        transform: translateX(-50%);
        opacity: .3;
    }
    .program-item {
        display: grid;
        grid-template-columns: 1fr 52px 1fr;
        margin-bottom: 56px;
        align-items: start;
    }
    .program-item:last-child { margin-bottom: 0; }
    .program-item:nth-child(odd) .program-content { text-align: right; padding-right: 36px; }
    .program-item:nth-child(even) .program-content { text-align: left; padding-left: 36px; grid-column: 3; }
    .program-item:nth-child(even) .program-dot { grid-column: 2; grid-row: 1; }
    .program-dot {
        width: 12px; height: 12px; border-radius: 50%;
        background: var(--gold);
        border: 3px solid var(--cream);
        box-shadow: 0 0 0 1px var(--gold);
        margin-top: 6px;
        justify-self: center;
    }
    .alt-bg .program-dot { border-color: var(--ivory); }
    .program-icon { display: none; }
    .program-time {
        font-family: var(--fb);
        font-size: 10px;
        letter-spacing: .25em;
        text-transform: uppercase;
        color: var(--gold);
        margin-bottom: 8px;
        font-weight: 400;
    }
    .program-title {
        font-family: var(--ft);
        font-size: 24px;
        font-weight: 500;
        color: var(--wt);
        margin-bottom: 8px;
        line-height: 1.2;
    }
    .program-desc {
        font-size: 14px;
        font-weight: 300;
        color: var(--wm);
        line-height: 1.7;
    }
    .program-venue {
        font-size: 11px;
        letter-spacing: .1em;
        color: var(--gold);
        margin-top: 10px;
        font-weight: 400;
    }

    /* VENUES */
    .venues-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
    }
    .venue-card {
        position: relative;
        overflow: hidden;
        aspect-ratio: 3/4;
        border-radius: 4px;
        transition: transform .6s var(--ease);
    }
    .venue-card:hover { transform: translateY(-4px); }
    .venue-card img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 1s var(--ease);
    }
    .venue-card:hover img { transform: scale(1.06); }
    .venue-photo { width: 100%; height: 100%; }
    .venue-photo-placeholder {
        width: 100%; height: 100%;
        background: linear-gradient(155deg, #2a2319, #1a1612);
        display: flex; align-items: center; justify-content: center;
        font-size: 36px; color: rgba(197,164,126,.15);
    }
    .venue-info {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,.85) 0%, rgba(0,0,0,.15) 55%, transparent 100%);
        display: flex; flex-direction: column; justify-content: flex-end;
        padding: 24px;
    }
    .venue-name {
        font-family: var(--ft);
        font-size: 20px; font-weight: 500;
        color: white; margin-bottom: 4px;
    }
    .venue-address {
        font-size: 12px; color: rgba(255,255,255,.5);
        font-weight: 300; margin-bottom: 12px;
    }
    .venue-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .btn-venue {
        display: inline-flex; align-items: center; gap: 5px;
        font-family: var(--fb); font-size: 8px; letter-spacing: 2px;
        text-transform: uppercase; font-weight: 500;
        color: var(--dark); background: var(--gold);
        padding: 7px 14px; text-decoration: none;
        border-radius: 999px; transition: all .3s var(--ease);
    }
    .btn-venue:hover { filter: brightness(1.1); transform: translateY(-1px); }
    .btn-venue-maps { background: var(--gold); }
    .btn-venue-waze { background: var(--gold-light); }

    /* DRESS CODE */
    .dresscode-palette-wrap { text-align: center; margin-bottom: 48px; }
    .dresscode-palette-wrap .palette-swatches { justify-content: center; margin-top: 16px; }
    .dresscode-cards-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    .dresscode-card {
        background: rgba(255,255,255,.04);
        border: 1px solid rgba(197,164,126,.12);
        padding: 32px 24px;
        border-radius: 4px;
        transition: all .4s var(--ease);
        min-height: 140px;
    }
    .dresscode-card:hover {
        border-color: rgba(197,164,126,.3);
        transform: translateY(-2px);
    }
    .dresscode-label {
        font-family: var(--fb);
        font-size: 9px; letter-spacing: 3px;
        text-transform: uppercase;
        color: var(--gold); font-weight: 500;
        margin-bottom: 16px;
        display: flex; align-items: center; gap: 8px;
    }
    .palette-swatches { display: flex; gap: 16px; flex-wrap: wrap; }
    .palette-swatch { display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .swatch-color {
        width: 52px; height: 52px; border-radius: 50%;
        box-shadow: 0 4px 24px rgba(0,0,0,.3);
    }
    .swatch-name { font-size: 11px; color: rgba(255,255,255,.45); letter-spacing: 1px; }
    .univers-intro { text-align: center; max-width: 520px; margin: 0 auto 48px; }
    .univers-intro .section-subtitle { color: rgba(255,255,255,.6); }
    .univers-inspiration-title {
        font-family: var(--ft);
        font-size: 24px; font-weight: 500;
        color: #fff; margin-bottom: 28px;
        text-align: center;
    }
    .univers-inspiration-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 16px; margin-top: 32px;
    }
    .univers-inspiration-item {
        position: relative; overflow: hidden;
        border-radius: 4px; aspect-ratio: 3/4;
        background: rgba(255,255,255,.04);
        border: 1px solid rgba(255,255,255,.06);
    }
    .univers-inspiration-item img {
        width: 100%; height: 100%;
        object-fit: cover; display: block;
        transition: transform .6s var(--ease);
    }
    .univers-inspiration-item:hover img { transform: scale(1.06); }
    .univers-inspiration-item .overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,.6) 0%, transparent 50%);
        display: flex; align-items: flex-end; padding: 16px;
        opacity: 0; transition: opacity .4s var(--ease);
    }
    .univers-inspiration-item:hover .overlay { opacity: 1; }
    .univers-inspiration-item .overlay span {
        font-family: var(--fb); font-size: 12px; color: #fff; letter-spacing: .05em;
    }

    /* GIFTS */
    .gifts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }
    .gift-card {
        background: white;
        border: 1px solid rgba(197,164,126,.08);
        overflow: hidden;
        border-radius: 4px;
        transition: all .5s var(--ease);
    }
    .gift-card:hover { transform: translateY(-4px); box-shadow: 0 20px 60px rgba(0,0,0,.06); }
    .gift-image { aspect-ratio: 1; overflow: hidden; background: var(--cream); }
    .gift-image img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform .6s var(--ease);
    }
    .gift-card:hover .gift-image img { transform: scale(1.05); }
    .gift-image-placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        font-size: 40px; color: var(--gold); opacity: .2;
    }
    .gift-info { padding: 28px; }
    .gift-name {
        font-family: var(--ft);
        font-size: 20px; font-weight: 500;
        color: var(--dark); margin-bottom: 8px;
    }
    .gift-price {
        font-family: var(--fb);
        font-size: 11px; letter-spacing: 2px;
        color: var(--gold); font-weight: 500;
        margin-bottom: 8px;
    }
    .gift-desc { font-size: 13px; color: var(--wm); line-height: 1.7; font-weight: 300; }
    .gift-reserved {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 10px; letter-spacing: 2px; font-weight: 500;
        text-transform: uppercase; color: #10b981; margin-top: 12px;
    }

    /* RULES */
    .rules-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .rules-column {
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(197,164,126,.08);
        padding: 40px 32px;
        border-radius: 4px;
    }
    .rules-column-title {
        font-family: var(--fb);
        font-size: 9px; letter-spacing: 3px;
        text-transform: uppercase; font-weight: 500;
        margin-bottom: 28px;
        display: flex; align-items: center; gap: 8px;
    }
    .rules-column.allowed .rules-column-title { color: #10b981; }
    .rules-column.forbidden .rules-column-title { color: #ef4444; }
    .rules-column.recommendation .rules-column-title { color: var(--gold); }
    .rule-item { display: flex; gap: 12px; margin-bottom: 18px; }
    .rule-icon { font-size: 14px; flex-shrink: 0; margin-top: 2px; }
    .rules-column.allowed .rule-icon { color: #10b981; }
    .rules-column.forbidden .rule-icon { color: #ef4444; }
    .rules-column.recommendation .rule-icon { color: var(--gold); }
    .rule-title { font-size: 14px; color: white; font-weight: 400; margin-bottom: 3px; }
    .rule-desc { font-size: 12px; color: rgba(255,255,255,.38); line-height: 1.6; }

    /* RSVP */
    .rsvp-section {
        background: linear-gradient(170deg, #141210 0%, #0c0a08 100%);
        position: relative; overflow: hidden;
    }
    .rsvp-section::before {
        content: '';
        position: absolute; inset: 0;
        background: radial-gradient(ellipse 600px 400px at 50% 0%, rgba(197,164,126,.06), transparent);
    }
    .rsvp-form-card {
        max-width: 600px; margin: 0 auto;
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(197,164,126,.12);
        padding: 60px;
        position: relative;
        border-radius: 4px;
        backdrop-filter: blur(20px);
    }
    .rsvp-form-card::before {
        content: '';
        position: absolute; inset: 8px;
        border: 1px solid rgba(197,164,126,.05);
        pointer-events: none;
        border-radius: 2px;
    }
    .rsvp-form-title {
        font-family: var(--ft);
        font-size: 34px; font-weight: 400;
        color: white; margin-bottom: 8px;
    }
    .rsvp-form-subtitle {
        font-size: 13px;
        color: rgba(255,255,255,.38);
        margin-bottom: 40px;
        font-weight: 300;
    }
    .rsvp-input {
        width: 100%;
        padding: 15px 20px;
        background: rgba(255,255,255,.04);
        border: 1px solid rgba(197,164,126,.15);
        color: white;
        font-family: var(--fb);
        font-size: 14px;
        font-weight: 300;
        outline: none;
        border-radius: 4px;
        transition: all .4s var(--ease);
    }
    .rsvp-input:focus {
        border-color: var(--gold);
        background: rgba(255,255,255,.06);
        box-shadow: 0 0 0 3px rgba(197,164,126,.06);
    }
    .rsvp-input::placeholder { color: rgba(255,255,255,.2); }
    textarea.rsvp-input { resize: vertical; }
    .rsvp-status-group { display: flex; gap: 10px; }
    .rsvp-status-btn {
        flex: 1; padding: 16px;
        cursor: pointer;
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(197,164,126,.1);
        display: flex; flex-direction: column;
        align-items: center; gap: 8px;
        transition: all .3s var(--ease);
        font-family: var(--fb);
        border-radius: 4px;
    }
    .rsvp-status-btn span:last-child {
        font-size: 10px; letter-spacing: 2px;
        text-transform: uppercase;
        color: rgba(255,255,255,.3);
        font-weight: 400;
    }
    .rsvp-status-btn:hover {
        border-color: rgba(197,164,126,.3);
        background: rgba(197,164,126,.04);
        transform: translateY(-2px);
    }
    .rsvp-status-btn.active-accepted { border-color: #10b981; background: rgba(16,185,129,.08); }
    .rsvp-status-btn.active-accepted span:last-child { color: #10b981; }
    .rsvp-status-btn.active-maybe { border-color: #f59e0b; background: rgba(245,158,11,.08); }
    .rsvp-status-btn.active-maybe span:last-child { color: #f59e0b; }
    .rsvp-status-btn.active-declined { border-color: #ef4444; background: rgba(239,68,68,.08); }
    .rsvp-status-btn.active-declined span:last-child { color: #ef4444; }
    .rsvp-btn {
        width: 100%; padding: 18px;
        background: var(--gold);
        border: none; cursor: pointer;
        font-family: var(--fb);
        font-size: 10px; letter-spacing: 5px;
        text-transform: uppercase;
        font-weight: 500; color: var(--dark);
        margin-top: 36px;
        border-radius: 999px;
        transition: all .4s var(--ease);
    }
    .rsvp-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(197,164,126,.25);
        filter: brightness(1.08);
    }
    .alert {
        padding: 14px 18px; border-radius: 4px;
        margin-bottom: 24px; font-size: 13px; font-weight: 300;
    }
    .alert-success { background: rgba(16,185,129,.08); border: 1px solid rgba(16,185,129,.2); color: #10b981; }
    .alert-danger { background: rgba(239,68,68,.08); border: 1px solid rgba(239,68,68,.2); color: #ef4444; }

    /* FOOTER */
    .wedding-footer {
        background: #0a0908;
        text-align: center;
        padding: 100px 40px 60px;
        position: relative;
    }
    .wedding-footer::before {
        content: '';
        position: absolute; top: 0; left: 25%; right: 25%; height: 1px;
        background: linear-gradient(90deg, transparent, rgba(197,164,126,.15), transparent);
    }
    .footer-couple {
        font-family: var(--ft);
        font-size: 42px; font-weight: 400;
        color: white;
        margin-bottom: 12px;
        letter-spacing: .02em;
    }
    .footer-date {
        font-family: var(--fb);
        font-size: 9px; letter-spacing: 5px;
        text-transform: uppercase;
        color: var(--gold);
        font-weight: 300;
        margin-bottom: 36px;
    }
    .footer-ornament {
        display: flex; align-items: center; justify-content: center;
        gap: 18px; margin-bottom: 28px;
    }
    .footer-line { width: 60px; height: 1px; background: rgba(197,164,126,.15); }
    .footer-icon { color: var(--gold); font-size: 10px; }
    .floral-corner {
        position: absolute;
        width: 180px; height: 180px;
        pointer-events: none; opacity: .08; z-index: 0;
        background:
            radial-gradient(circle at 30% 30%, rgba(197,164,126,.5) 0 6%, transparent 7%),
            radial-gradient(circle at 55% 45%, rgba(197,164,126,.25) 0 7%, transparent 8%),
            radial-gradient(circle at 70% 70%, rgba(197,164,126,.15) 0 8%, transparent 9%);
        filter: blur(1px);
    }
    .floral-corner-tl { top: 0; left: 0; }
    .floral-corner-tr { top: 0; right: 0; transform: scaleX(-1); }
    .floral-corner-br { bottom: 0; right: 0; transform: rotate(180deg); }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .dresscode-cards-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 900px) {
        .story-content { grid-template-columns: 1fr; gap: 56px; }
        .story-image-frame::before { display: none; }
        .univers-inspiration-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .rules-grid { grid-template-columns: 1fr; }
        .program-timeline { max-width: 100%; }
        .program-timeline::before { left: 14px; }
        .program-item {
            display: grid;
            grid-template-columns: 30px 1fr;
            gap: 0 20px;
            margin-bottom: 40px;
        }
        .program-item .program-dot {
            grid-column: 1; grid-row: 1;
            width: 12px; height: 12px;
            margin-left: 8px; margin-top: 5px;
            justify-self: start;
        }
        .program-item .program-content {
            grid-column: 2; grid-row: 1;
            text-align: left !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .program-item:nth-child(even) .program-dot { grid-column: 1; }
        .program-item:nth-child(even) .program-content { grid-column: 2; }
        .program-title { font-size: 18px; }
        .rsvp-form-card { padding: 36px 24px; }
    }

    .mobile-nav-toggle {
        display: none;
        flex-direction: column; gap: 5px;
        background: none; border: none; cursor: pointer;
        padding: 8px; z-index: 101;
    }
    .mobile-nav-toggle span {
        display: block; width: 22px; height: 1.5px;
        background: rgba(255,255,255,.6);
        transition: all .3s var(--ease);
    }
    .wedding-nav.scrolled .mobile-nav-toggle span { background: var(--dark); }
    .mobile-nav-toggle.is-open span:nth-child(1) { transform: translateY(6.5px) rotate(45deg); }
    .mobile-nav-toggle.is-open span:nth-child(2) { opacity: 0; }
    .mobile-nav-toggle.is-open span:nth-child(3) { transform: translateY(-6.5px) rotate(-45deg); }

    @media (max-width: 600px) {
        .mobile-nav-toggle { display: flex; }
        .wedding-nav { padding: 0 20px; height: 64px; }
        .nav-links {
            display: none;
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(10,9,8,.97); backdrop-filter: blur(24px);
            flex-direction: column;
            align-items: center; justify-content: center;
            gap: 32px; z-index: 100;
        }
        .nav-links.is-open { display: flex; }
        .nav-links a {
            font-size: 12px !important;
            letter-spacing: 5px !important;
            color: rgba(255,255,255,.6) !important;
        }
        .nav-links a:hover { color: var(--gold) !important; }
        .inv-names { font-size: clamp(36px, 12vw, 52px); }
        .inv-label { font-size: 8px; letter-spacing: 4px; }
        .inv-open-btn { padding: 14px 28px; font-size: 9px; letter-spacing: 3px; }
        .wedding-section { padding: 100px 0; }
        .section-inner { padding: 0 20px; }
        .section-header { margin-bottom: 56px; }
        .hero-section { min-height: 100dvh; padding: 0 16px; }
        .hero-names { font-size: clamp(42px, 14vw, 62px); margin-bottom: 24px; }
        .hero-tagline { font-size: 9px; }
        .hero-date { font-size: 10px; letter-spacing: .2em; margin-bottom: 32px; }
        .countdown-timer { gap: 8px; margin-bottom: 36px; }
        .countdown-item { padding: 14px 12px; min-width: 68px; }
        .countdown-number { font-size: 32px; }
        .countdown-label { font-size: 7px; }
        .hero-actions { flex-direction: column; gap: 10px; }
        .hero-cta, .hero-cta-secondary { width: 100%; max-width: 300px; padding: 16px 28px; }
        .hero-scroll-indicator { bottom: 20px; font-size: 8px; }
        .details-grid, .venues-grid, .gifts-grid { gap: 12px; }
        .rsvp-status-group { flex-direction: column; }
        .dresscode-cards-grid { grid-template-columns: 1fr; }
    }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
    }
    </style>
</head>
<body>

@if($wedding->envelope_animation)
<div id="invOverlay" class="inv-overlay">
    <div class="inv-panel inv-panel--left" id="invPanelLeft"></div>
    <div class="inv-panel inv-panel--right" id="invPanelRight"></div>
    <div class="inv-split-line" id="invSplitLine"></div>
    <div class="inv-split-glow"></div>
    <div class="inv-particles" id="invParticles"></div>

    <div class="inv-content" id="invContent">
        <div class="inv-label">Vous êtes invité(e) au mariage de</div>
        <div class="inv-ornament">
            <span class="inv-orn-line"></span>
            <span class="inv-orn-diamond"></span>
            <span class="inv-orn-line"></span>
        </div>
        <h2 class="inv-names">
            <span class="inv-name">{{ $wedding->bride_name }}</span>
            <span class="inv-amp">&amp;</span>
            <span class="inv-name">{{ $wedding->groom_name }}</span>
        </h2>
        @if($wedding->wedding_date)
        <div class="inv-date">
            <span class="inv-date-line"></span>
            {{ $wedding->wedding_date->translatedFormat('d F Y') }}
            <span class="inv-date-line"></span>
        </div>
        @endif
        <button class="inv-open-btn" id="invOpenBtn" type="button">
            <span>Ouvrir l'invitation</span>
            <span class="inv-btn-icon"><i class="bi bi-arrow-right"></i></span>
        </button>
        <div class="inv-hint">Appuyez pour découvrir</div>
    </div>
</div>
@endif

<nav class="wedding-nav" id="weddingNav">
    <div class="nav-couple">{{ $wedding->bride_name }} &amp; {{ $wedding->groom_name }}</div>
    <button class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>
    <div class="nav-links">
        @if($sectionKeys['story'] ?? true) <a href="#story">Histoire</a> @endif
        @if($sectionKeys['program'] ?? true) <a href="#programme">Programme</a> @endif
        @if($sectionKeys['venues'] ?? true) <a href="#lieux">Lieux</a> @endif
        @if($sectionKeys['rsvp'] ?? true) <a href="#rsvp">RSVP</a> @endif
        @if($sectionKeys['gifts'] ?? true) <a href="#cadeaux">Cadeaux</a> @endif
    </div>
</nav>

<section class="hero-section" id="hero">
    @if($wedding->hero_image)
    <div class="hero-bg" id="heroBg" style="background-image: url('{{ Storage::url($wedding->hero_image) }}')"></div>
    @else
    <div class="hero-bg" id="heroBg" style="background: linear-gradient(160deg, #1c1917 0%, #0a0908 100%)"></div>
    @endif

    <div class="hero-overlay"></div>
    <div class="hero-vignette"></div>
    <div class="hero-light hero-light-1"></div>
    <div class="hero-light hero-light-2"></div>
    <div class="hero-particles" id="heroParticles"></div>
    <div class="hero-ring hero-ring-1"></div>
    <div class="hero-ring hero-ring-2"></div>

    @if($wedding->floral_decor)
    <div class="floral-corner floral-corner-tl"></div>
    <div class="floral-corner floral-corner-tr"></div>
    @endif

    <div class="hero-content" id="heroContent">
        <span class="hero-tagline">Invitation au mariage</span>

        <div class="hero-ornament">
            <span class="ornament-line"></span>
            <span class="ornament-diamond"></span>
            <span class="ornament-line right"></span>
        </div>

        <h1 class="hero-names">
            <span class="hero-name">{{ $wedding->bride_name }}</span>
            <span class="ampersand">&amp;</span>
            <span class="hero-name">{{ $wedding->groom_name }}</span>
        </h1>

        <div class="hero-date" id="weddingDate" data-date="{{ $wedding->wedding_date?->toISOString() }}">
            @if($wedding->wedding_date)
            <i class="bi bi-calendar-heart"></i>
            <span>{{ $wedding->wedding_date->translatedFormat('d F Y') }}</span>
            @else
            <span>Date à venir</span>
            @endif
        </div>

        @if($wedding->wedding_date && $wedding->getDaysUntilWedding() > 0)
        <div class="countdown-timer" id="countdownTimer">
            <div class="countdown-item">
                <span class="countdown-number" id="cd-days">00</span>
                <span class="countdown-label">Jours</span>
            </div>
            <div class="countdown-item">
                <span class="countdown-number" id="cd-hours">00</span>
                <span class="countdown-label">Heures</span>
            </div>
            <div class="countdown-item">
                <span class="countdown-number" id="cd-mins">00</span>
                <span class="countdown-label">Minutes</span>
            </div>
            <div class="countdown-item">
                <span class="countdown-number" id="cd-secs">00</span>
                <span class="countdown-label">Secondes</span>
            </div>
        </div>
        @endif

        <div class="hero-actions">
            <a href="#rsvp" class="hero-cta">
                <i class="bi bi-envelope-heart"></i>
                Confirmer ma présence
            </a>
            @if($sectionKeys['program'] ?? true)
            <a href="#programme" class="hero-cta hero-cta-secondary">
                <i class="bi bi-stars"></i>
                Voir le programme
            </a>
            @endif
        </div>
    </div>

    @if($sectionKeys['story'] ?? true)
    <a href="#story" class="hero-scroll-indicator" aria-label="Descendre">
        <span>Découvrir</span>
        <i class="bi bi-chevron-down"></i>
    </a>
    @endif
</section>

@if($sectionKeys['story'] ?? true)
<section class="wedding-section" id="story">
    @if($wedding->floral_decor)
    <div class="floral-corner floral-corner-br"></div>
    @endif
    <div class="section-inner">
        <div class="section-header" data-aos="fade-up" data-aos-duration="1000">
            <div class="section-ornament">
                <span class="section-ornament-line"></span>
                <i class="bi bi-heart-fill section-ornament-icon"></i>
                <span class="section-ornament-line"></span>
            </div>
            <span class="section-label">Notre histoire</span>
            <h2 class="section-title">Deux cœurs qui ne font qu'un</h2>
            @if($wedding->intro_text)
            <p class="section-subtitle">{{ $wedding->intro_text }}</p>
            @endif
        </div>

        <div class="story-content">
            @if($wedding->couple_photo)
            <div class="story-image" data-aos="fade-right" data-aos-duration="1200">
                <div class="story-image-frame">
                    <img src="{{ Storage::url($wedding->couple_photo) }}" alt="{{ $wedding->getCoupleName() }}">
                </div>
            </div>
            @endif
            <div class="story-text-block" data-aos="fade-left" data-aos-duration="1200">
                @if($wedding->quote)
                <p class="story-quote">« {{ $wedding->quote }} »</p>
                @endif
                @if($wedding->story_text)
                <div class="story-body">{{ nl2br(e($wedding->story_text)) }}</div>
                @elseif($wedding->welcome_message)
                <div class="story-body">{{ nl2br(e($wedding->welcome_message)) }}</div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif

@if($sectionKeys['gallery'] ?? true)
<section class="wedding-section alt-bg" id="galerie">
    <div class="section-inner">
        <div class="section-header" data-aos="fade-up" data-aos-duration="1000">
            <span class="section-label">Galerie</span>
            <h2 class="section-title">Nos plus beaux instants</h2>
        </div>
        <div class="gallery-grid">
            @foreach($wedding->galleryItems as $item)
            <div class="gallery-item" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}" data-aos-duration="900">
                <a href="{{ Storage::url($item->image) }}" data-gallery="wedding-gallery" data-type="image">
                    <img src="{{ Storage::url($item->image) }}" alt="{{ $item->caption ?? 'Photo ' . $loop->iteration }}" loading="lazy">
                    @if($item->caption)
                    <div class="gallery-overlay">
                        <p class="gallery-caption">{{ $item->caption }}</p>
                    </div>
                    @endif
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if(($sectionKeys['details'] ?? true))
<section class="wedding-section" id="details">
    <div class="section-inner">
        <div class="section-header" data-aos="fade-up" data-aos-duration="1000">
            <span class="section-label">Informations</span>
            <h2 class="section-title">Les détails de notre fête</h2>
        </div>
        <div class="details-grid">
            @if($wedding->wedding_date)
            <div class="detail-card" data-aos="fade-up" data-aos-duration="900">
                <div class="detail-icon"><i class="bi bi-calendar-heart-fill"></i></div>
                <span class="detail-label">Date</span>
                <div class="detail-value">{{ $wedding->wedding_date->translatedFormat('d F Y') }}</div>
            </div>
            @endif
            @foreach($wedding->venues->take(3) as $venue)
            <div class="detail-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 120 }}" data-aos-duration="900">
                <div class="detail-icon"><i class="bi bi-geo-alt-fill"></i></div>
                <span class="detail-label">{{ ucfirst($venue->type) }}</span>
                <div class="detail-value">{{ $venue->name }}</div>
                @if($venue->city) <div class="detail-desc">{{ $venue->city }}</div> @endif
            </div>
            @endforeach
            @if($wedding->rsvp_deadline)
            <div class="detail-card" data-aos="fade-up" data-aos-duration="900">
                <div class="detail-icon"><i class="bi bi-clock-fill"></i></div>
                <span class="detail-label">Répondez avant le</span>
                <div class="detail-value">{{ $wedding->rsvp_deadline->translatedFormat('d F Y') }}</div>
            </div>
            @endif
        </div>
    </div>
</section>
@endif

@if($sectionKeys['program'] ?? true)
<section class="wedding-section alt-bg" id="programme">
    <div class="section-inner">
        <div class="section-header" data-aos="fade-up" data-aos-duration="1000">
            <span class="section-label">Programme</span>
            <h2 class="section-title">Le fil de notre journée</h2>
        </div>
        <div class="program-timeline">
            @foreach($wedding->programItems as $item)
            <div class="program-item" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}" data-aos-duration="900">
                <div class="program-dot"></div>
                <div class="program-content">
                    <div class="program-icon"><i class="bi {{ $item->icon ?? 'bi-star-fill' }}"></i></div>
                    <div class="program-time">{{ $item->getDateTimeFormatted() }}</div>
                    <h3 class="program-title">{{ $item->title }}</h3>
                    @if($item->description)
                    <p class="program-desc">{{ $item->description }}</p>
                    @endif
                    @if($item->venue_name)
                    <div class="program-venue">
                        <i class="bi bi-geo-alt"></i>
                        {{ $item->venue_name }}
                        @if($item->address) · {{ $item->address }} @endif
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($sectionKeys['venues'] ?? true)
<section class="wedding-section" id="lieux">
    <div class="section-inner">
        <div class="section-header" data-aos="fade-up" data-aos-duration="1000">
            <span class="section-label">Lieux</span>
            <h2 class="section-title">Où nous vous accueillons</h2>
        </div>
        <div class="venues-grid">
            @foreach($wedding->venues as $venue)
            <div class="venue-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 120 }}" data-aos-duration="900">
                <div class="venue-photo">
                    @if($venue->photo)
                    <img src="{{ Storage::url($venue->photo) }}" alt="{{ $venue->name }}">
                    @else
                    <div class="venue-photo-placeholder"><i class="bi bi-building"></i></div>
                    @endif
                </div>
                <div class="venue-info">
                    <h3 class="venue-name">{{ $venue->name }}</h3>
                    @if($venue->address)
                    <p class="venue-address"><i class="bi bi-geo-alt me-1"></i>{{ $venue->address }}</p>
                    @endif
                    @if($venue->description)
                    <p class="venue-address">{{ $venue->description }}</p>
                    @endif
                    <div class="venue-actions">
                        @if($venue->google_maps_url)
                        <a href="{{ $venue->google_maps_url }}" target="_blank" class="btn-venue btn-venue-maps">
                            <i class="bi bi-map-fill"></i> Google Maps
                        </a>
                        @endif
                        @if($venue->waze_url)
                        <a href="{{ $venue->waze_url }}" target="_blank" class="btn-venue btn-venue-waze">
                            <i class="bi bi-signpost-fill"></i> Waze
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@php
    $hasUniversContent = $theme && ($theme->dress_code_description || $theme->mood_description || $theme->dress_code_style || $theme->dress_code_formality || $theme->dress_code_men || $theme->dress_code_women || $theme->dress_code_accessories || $theme->forbidden_colors);
    $showUniversSection = ($sectionKeys['dresscode'] ?? true) && ($hasUniversContent || $wedding->inspirationItems->count() || $wedding->colorPalette->count());
@endphp
@if($showUniversSection)
<section class="wedding-section dark-bg" id="dresscode">
    @if($wedding->floral_decor)
    <div class="floral-corner floral-corner-tl" style="opacity:.06"></div>
    <div class="floral-corner floral-corner-br" style="opacity:.06"></div>
    @endif
    <div class="section-inner">
        <div class="section-header" data-aos="fade-up" data-aos-duration="1000">
            <div class="section-ornament">
                <span class="section-ornament-line"></span>
                <i class="bi bi-stars section-ornament-icon"></i>
                <span class="section-ornament-line"></span>
            </div>
            <span class="section-label">Ambiance &amp; tenues</span>
            <h2 class="section-title" style="color:#fff">L'univers de notre mariage</h2>
            @if($theme?->mood_description)
            <p class="section-subtitle univers-intro">{{ $theme->mood_description }}</p>
            @else
            <p class="section-subtitle univers-intro">Quelques repères pour s'habiller et se glisser dans notre univers.</p>
            @endif
        </div>

        @if($wedding->colorPalette->count())
        <div class="dresscode-palette-wrap" data-aos="fade-up" data-aos-duration="900">
            <h4 style="font-family:var(--ft);color:#fff;margin:0;font-size:22px;font-weight:400">Notre palette</h4>
            <div class="palette-swatches">
                @foreach($wedding->colorPalette as $color)
                <div class="palette-swatch">
                    <div class="swatch-color" style="background: {{ $color->hex_color }}"></div>
                    <div class="swatch-name" style="color:rgba(255,255,255,.5)">{{ $color->name }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($hasUniversContent)
        <div class="dresscode-cards-grid">
            <div class="dresscode-card" data-aos="fade-up" data-aos-delay="0" data-aos-duration="900">
                <div class="dresscode-label"><i class="bi bi-star-fill"></i> Tenue demandée</div>
                @if($theme->dress_code_style)
                <p style="color:#fff;margin:0;font-size:17px;font-family:var(--ft)">{{ $theme->dress_code_style }}</p>
                @endif
                @if($theme->dress_code_formality)
                <p style="color:rgba(255,255,255,.65);margin:8px 0 0;font-size:14px;line-height:1.6">{{ $theme->dress_code_formality }}</p>
                @endif
                @if(!$theme->dress_code_style && !$theme->dress_code_formality)
                <p style="color:rgba(255,255,255,.3);margin:0;font-size:14px">—</p>
                @endif
            </div>
            <div class="dresscode-card" data-aos="fade-up" data-aos-delay="80" data-aos-duration="900">
                <div class="dresscode-label"><i class="bi bi-info-circle-fill"></i> Description</div>
                @if($theme->dress_code_description)
                <p style="color:rgba(255,255,255,.8);margin:0;line-height:1.7;font-size:14px">{{ $theme->dress_code_description }}</p>
                @else
                <p style="color:rgba(255,255,255,.3);margin:0;font-size:14px">—</p>
                @endif
            </div>
            <div class="dresscode-card" data-aos="fade-up" data-aos-delay="160" data-aos-duration="900">
                <div class="dresscode-label"><i class="bi bi-people-fill"></i> Pour lui & pour elle</div>
                @if($theme->dress_code_men)
                <p style="color:rgba(255,255,255,.8);margin:0;font-size:13px;line-height:1.6"><strong style="color:rgba(255,255,255,.5)">Hommes :</strong> {{ $theme->dress_code_men }}</p>
                @endif
                @if($theme->dress_code_men && $theme->dress_code_women)
                <p style="margin:10px 0 0;color:rgba(255,255,255,.8);font-size:13px;line-height:1.6"><strong style="color:rgba(255,255,255,.5)">Femmes :</strong> {{ $theme->dress_code_women }}</p>
                @elseif($theme->dress_code_women)
                <p style="color:rgba(255,255,255,.8);margin:0;font-size:13px;line-height:1.6">{{ $theme->dress_code_women }}</p>
                @endif
                @if(!$theme->dress_code_men && !$theme->dress_code_women)
                <p style="color:rgba(255,255,255,.3);margin:0;font-size:14px">—</p>
                @endif
            </div>
            <div class="dresscode-card" data-aos="fade-up" data-aos-delay="240" data-aos-duration="900" style="@if($theme->forbidden_colors) border-color:rgba(239,68,68,.25); @endif">
                <div class="dresscode-label" @if($theme->forbidden_colors) style="color:#f87171" @endif><i class="bi bi-x-circle-fill"></i> À éviter & accessoires</div>
                @if($theme->forbidden_colors)
                <p style="color:rgba(255,255,255,.75);margin:0;font-size:13px;line-height:1.6">{{ $theme->forbidden_colors }}</p>
                @endif
                @if($theme->dress_code_accessories)
                <p style="margin:{{ $theme->forbidden_colors ? '10px' : '0' }} 0 0;color:rgba(255,255,255,.75);font-size:13px;line-height:1.6">{{ $theme->dress_code_accessories }}</p>
                @endif
                @if(!$theme->forbidden_colors && !$theme->dress_code_accessories)
                <p style="color:rgba(255,255,255,.3);margin:0;font-size:14px">—</p>
                @endif
            </div>
        </div>
        @endif

        @if($wedding->inspirationItems->count())
        <div class="univers-inspiration-wrap" data-aos="fade-up" data-aos-duration="1000">
            <h3 class="univers-inspiration-title">Inspirations tenues</h3>
            <p style="text-align:center;color:rgba(255,255,255,.4);font-size:13px;margin:-16px 0 0;font-weight:300">Quelques idées pour vous inspirer</p>
            <div class="univers-inspiration-grid">
                @foreach($wedding->inspirationItems as $insp)
                <div class="univers-inspiration-item">
                    <a href="{{ Storage::url($insp->image) }}" data-gallery="inspiration-gallery" data-type="image">
                        <img src="{{ Storage::url($insp->image) }}" alt="{{ $insp->caption ?? 'Inspiration tenue' }}" loading="lazy">
                        <div class="overlay">
                            <span>{{ $insp->caption ?? 'Inspiration' }}</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endif

@if($sectionKeys['gifts'] ?? true)
<section class="wedding-section alt-bg" id="cadeaux">
    <div class="section-inner">
        <div class="section-header" data-aos="fade-up" data-aos-duration="1000">
            <span class="section-label">Liste de souhaits</span>
            <h2 class="section-title">Notre liste de mariage</h2>
            <p class="section-subtitle">Votre présence est le plus beau cadeau. Mais si vous souhaitez nous gâter, voici quelques idées.</p>
        </div>
        @foreach($wedding->giftCategories as $category)
        @if($category->items->count())
        <div class="mb-5" data-aos="fade-up" data-aos-duration="900">
            <h3 style="font-family:var(--ft);font-size:22px;margin-bottom:24px;color:var(--wt);font-weight:400">{{ $category->name }}</h3>
            <div class="gifts-grid">
                @foreach($category->items as $gift)
                <div class="gift-card">
                    <div class="gift-image">
                        @if($gift->image)
                        <img src="{{ Storage::url($gift->image) }}" alt="{{ $gift->name }}" loading="lazy">
                        @else
                        <div class="gift-image-placeholder"><i class="bi bi-gift"></i></div>
                        @endif
                    </div>
                    <div class="gift-info">
                        <h4 class="gift-name">{{ $gift->name }}</h4>
                        @if($gift->price)
                        <div class="gift-price">{{ number_format($gift->price, 0, ',', ' ') }} €</div>
                        @elseif($gift->free_contribution)
                        <div class="gift-price">Participation libre</div>
                        @endif
                        @if($gift->description)
                        <p class="gift-desc">{{ Str::limit($gift->description, 100) }}</p>
                        @endif
                        @if($gift->is_reserved)
                        <span class="gift-reserved"><i class="bi bi-check-circle me-1"></i>Réservé</span>
                        @elseif($gift->external_link)
                        <a href="{{ $gift->external_link }}" target="_blank" class="btn-venue btn-venue-maps mt-2">
                            <i class="bi bi-box-arrow-up-right"></i> Voir le cadeau
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach
    </div>
</section>
@endif

@if($sectionKeys['rules'] ?? true)
<section class="wedding-section dark-bg" id="regles">
    <div class="section-inner">
        <div class="section-header" data-aos="fade-up" data-aos-duration="1000">
            <span class="section-label">Consignes</span>
            <h2 class="section-title" style="color:white">Pour que ce jour soit parfait</h2>
        </div>
        <div class="rules-grid">
            @foreach(['allowed' => ['icon' => 'bi-check-circle-fill', 'label' => 'Bienvenus'], 'forbidden' => ['icon' => 'bi-x-circle-fill', 'label' => 'À éviter'], 'recommendation' => ['icon' => 'bi-star-fill', 'label' => 'Recommandations']] as $type => $config)
            @php $typeRules = $wedding->rules->where('type', $type)->where('is_active', true); @endphp
            @if($typeRules->count())
            <div class="rules-column {{ $type }}" data-aos="fade-up" data-aos-duration="900">
                <div class="rules-column-title">
                    <i class="bi {{ $config['icon'] }}"></i>
                    {{ $config['label'] }}
                </div>
                @foreach($typeRules as $rule)
                <div class="rule-item">
                    <i class="bi {{ $rule->icon ?? $config['icon'] }} rule-icon"></i>
                    <div>
                        <div class="rule-title" style="color:white">{{ $rule->title }}</div>
                        @if($rule->description)
                        <div class="rule-desc">{{ $rule->description }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            @endforeach
        </div>
    </div>
</section>
@endif

@if(($sectionKeys['accommodation'] ?? true) && $wedding->accommodation_details)
<section class="wedding-section" id="hebergement">
    <div class="section-inner">
        <div class="section-header" data-aos="fade-up" data-aos-duration="1000">
            <span class="section-label">Hébergement</span>
            <h2 class="section-title">{{ $wedding->accommodation_info ?? 'Infos pratiques' }}</h2>
        </div>
        <div class="story-body" style="max-width:700px;margin:0 auto;font-size:16px;line-height:2;text-align:center" data-aos="fade-up" data-aos-duration="900">
            {!! nl2br(e($wedding->accommodation_details)) !!}
        </div>
    </div>
</section>
@endif

@if($sectionKeys['rsvp'] ?? true)
<section class="wedding-section rsvp-section" id="rsvp">
    <div class="section-inner">
        <div class="section-header" data-aos="fade-up" data-aos-duration="1000">
            <span class="section-label" style="color:rgba(255,255,255,.5)">Votre réponse</span>
            <h2 class="section-title" style="color:white">Serez-vous des nôtres ?</h2>
            @if($wedding->rsvp_deadline)
            <p style="color:rgba(255,255,255,.4);font-size:14px;font-weight:300">
                <i class="bi bi-clock me-1"></i>Merci de répondre avant le {{ $wedding->rsvp_deadline->translatedFormat('d F Y') }}
            </p>
            @endif
        </div>
        <div class="rsvp-form-card" data-aos="fade-up" data-aos-duration="1000">
            <h3 class="rsvp-form-title">Confirmer ma présence</h3>
            <p class="rsvp-form-subtitle">Entrez votre code d'invitation personnel</p>
            @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
            @endif
            <form method="POST" action="{{ route('rsvp.submit', $wedding->slug) }}" id="rsvpForm">
                @csrf
                <div class="mb-4">
                    <label style="font-weight:500;font-size:12px;margin-bottom:8px;display:block;color:white;letter-spacing:1px">Code d'invitation</label>
                    <input type="text" name="invitation_code" class="rsvp-input" placeholder="Votre code (ex: ABCD1234)" required style="text-transform:uppercase;letter-spacing:3px;font-size:16px">
                    <div style="font-size:12px;color:rgba(255,255,255,.25);margin-top:6px;font-weight:300">Vous trouverez votre code sur votre invitation papier ou dans l'email reçu.</div>
                </div>
                <div class="mb-4">
                    <label style="font-weight:500;font-size:12px;margin-bottom:10px;display:block;color:white;letter-spacing:1px">Votre réponse</label>
                    <div class="rsvp-status-group">
                        <button type="button" class="rsvp-status-btn" data-status="accepted">
                            <i class="bi bi-check-circle-fill" style="color:#10b981;font-size:20px"></i>
                            <span>J'accepte</span>
                        </button>
                        <button type="button" class="rsvp-status-btn" data-status="maybe">
                            <i class="bi bi-question-circle-fill" style="color:#f59e0b;font-size:20px"></i>
                            <span>À confirmer</span>
                        </button>
                        <button type="button" class="rsvp-status-btn" data-status="declined">
                            <i class="bi bi-x-circle-fill" style="color:#ef4444;font-size:20px"></i>
                            <span>Je décline</span>
                        </button>
                    </div>
                    <input type="hidden" name="rsvp_status" id="rsvpStatusInput" value="" required>
                </div>
                <div id="companionsField" style="display:none" class="mb-4">
                    <label style="font-weight:500;font-size:12px;margin-bottom:8px;display:block;color:white;letter-spacing:1px">Accompagnants</label>
                    <input type="number" name="companions_count" class="rsvp-input mb-2" placeholder="Nombre d'accompagnants" min="0" max="10">
                    <div id="companionsContainer"></div>
                    <button type="button" id="addCompanion" class="btn-venue btn-venue-maps mt-2">
                        <i class="bi bi-person-plus"></i> Ajouter un accompagnant
                    </button>
                    <input type="hidden" id="maxCompanions" value="5">
                </div>
                <div class="mb-4">
                    <label style="font-weight:500;font-size:12px;margin-bottom:8px;display:block;color:white;letter-spacing:1px">Restrictions alimentaires</label>
                    <input type="text" name="dietary_restrictions" class="rsvp-input" placeholder="Végétarien, allergies, sans gluten...">
                </div>
                <div class="mb-4">
                    <label style="font-weight:500;font-size:12px;margin-bottom:8px;display:block;color:white;letter-spacing:1px">Un mot pour nous</label>
                    <textarea name="message" class="rsvp-input" rows="3" placeholder="Votre message aux mariés..."></textarea>
                </div>
                <button type="submit" class="rsvp-btn">
                    <i class="bi bi-send me-2"></i>Envoyer ma réponse
                </button>
            </form>
        </div>
    </div>
</section>
@endif

<footer class="wedding-footer">
    <div class="footer-couple">{{ $wedding->bride_name }} &amp; {{ $wedding->groom_name }}</div>
    <div class="footer-date">{{ $wedding->getWeddingDateFormatted() }}</div>
    <div class="footer-ornament">
        <span class="footer-line"></span>
        <i class="bi bi-heart-fill footer-icon"></i>
        <span class="footer-line"></span>
    </div>
    @if($wedding->quote)
    <p style="font-family:var(--ft);color:rgba(255,255,255,.3);font-size:16px;font-style:italic;margin-bottom:24px;font-weight:300">
        « {{ $wedding->quote }} »
    </p>
    @endif
    <div style="font-size:11px;color:rgba(255,255,255,.15);letter-spacing:2px">Invitation créée avec amour</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/js/glightbox.min.js"></script>
<script src="{{ asset('js/wedding.js') }}"></script>
<script>
AOS.init({ once: true, duration: 900, easing: 'ease-out-cubic', offset: 80 });
GLightbox({ selector: '[data-gallery]' });

@if($wedding->envelope_animation)
(function () {
    const container = document.getElementById('invParticles');
    for (let i = 0; i < 35; i++) {
        const p = document.createElement('div');
        p.className = 'inv-particle';
        const size = Math.random() * 3 + 1;
        p.style.cssText = `width:${size}px;height:${size}px;left:${Math.random()*100}%;top:${Math.random()*100}%;--dur:${Math.random()*8+6}s;--delay:${Math.random()*6}s;--op:${Math.random()*.18+.03}`;
        container.appendChild(p);
    }

    const overlay = document.getElementById('invOverlay');
    const content = document.getElementById('invContent');
    const panelL = document.getElementById('invPanelLeft');
    const panelR = document.getElementById('invPanelRight');
    const splitLine = document.getElementById('invSplitLine');
    const btn = document.getElementById('invOpenBtn');
    let opened = false;

    const contentEls = content.children;
    gsap.set(contentEls, { opacity: 0, y: 30 });
    gsap.to(contentEls, { opacity: 1, y: 0, duration: 1, stagger: 0.12, ease: 'power3.out', delay: 0.4 });

    function fireGoldenConfetti() {
        const gold = ['#c5a47e', '#dcc9a8', '#b8975a', '#e8d5b5', '#f5e6d3', '#fff8ee'];
        const count = window.innerWidth < 600 ? 60 : 120;
        confetti({ particleCount: count, spread: 100, startVelocity: 55, origin: { x: 0.5, y: 0.45 }, colors: gold, ticks: 250, gravity: 0.7, scalar: 1.3, shapes: ['circle'], disableForReducedMotion: true });
        setTimeout(() => {
            confetti({ particleCount: Math.floor(count * 0.5), spread: 140, startVelocity: 30, origin: { x: 0.5, y: 0.5 }, colors: gold, ticks: 200, gravity: 0.5, scalar: 0.8, shapes: ['circle'], disableForReducedMotion: true });
        }, 250);
    }

    function openInvitation() {
        if (opened) return;
        opened = true;
        const tl = gsap.timeline();
        tl.to(content, { opacity: 0, y: -50, scale: 0.96, duration: 0.6, ease: 'power3.in' });
        tl.to(splitLine, { opacity: 1, boxShadow: '0 0 50px 10px rgba(197,164,126,.4)', duration: 0.35, ease: 'power2.out' }, '-=0.1');
        tl.call(fireGoldenConfetti);
        tl.to(panelL, { xPercent: -105, duration: 1.1, ease: 'power4.inOut' }, '-=0.1');
        tl.to(panelR, { xPercent: 105, duration: 1.1, ease: 'power4.inOut' }, '<');
        tl.to(splitLine, { opacity: 0, duration: 0.4, ease: 'power2.in' }, '-=0.6');
        tl.set(overlay, { visibility: 'hidden', pointerEvents: 'none' });
    }

    overlay.addEventListener('click', openInvitation);
    if (btn) btn.addEventListener('click', function(e) { e.stopPropagation(); openInvitation(); });
})();
@endif

(function () {
    const heroParticles = document.getElementById('heroParticles');
    const heroBg = document.getElementById('heroBg');

    if (heroParticles) {
        for (let i = 0; i < 22; i++) {
            const p = document.createElement('span');
            p.className = 'hero-particle';
            const size = Math.random() * 3 + 2;
            p.style.width = `${size}px`;
            p.style.height = `${size}px`;
            p.style.left = `${Math.random() * 100}%`;
            p.style.bottom = `${Math.random() * 20 - 10}%`;
            p.style.setProperty('--dur', `${Math.random() * 10 + 10}s`);
            p.style.setProperty('--op', `${Math.random() * 0.12 + 0.03}`);
            p.style.animationDelay = `${Math.random() * 10}s`;
            heroParticles.appendChild(p);
        }
    }

    const heroContent = document.getElementById('heroContent');
    if (heroContent && typeof gsap !== 'undefined') {
        const els = heroContent.children;
        gsap.set(els, { opacity: 0, y: 50 });

        function animateHero() {
            gsap.to(els, { opacity: 1, y: 0, duration: 1.2, stagger: 0.15, ease: 'power3.out', delay: 0.3 });
        }

        const overlay = document.getElementById('invOverlay');
        if (overlay) {
            const obs = new MutationObserver(() => {
                if (overlay.style.visibility === 'hidden') { animateHero(); obs.disconnect(); }
            });
            obs.observe(overlay, { attributes: true, attributeFilter: ['style'] });
        } else {
            animateHero();
        }
    }

    let ticking = false;
    window.addEventListener('scroll', function () {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                const s = window.scrollY;
                if (heroBg && s < window.innerHeight) {
                    heroBg.style.transform = `scale(1.08) translateY(${s * 0.18}px)`;
                }
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });
})();

@if($wedding->wedding_date && $wedding->getDaysUntilWedding() > 0)
(function () {
    const target = new Date('{{ $wedding->wedding_date->toISOString() }}');
    const pad = n => String(n).padStart(2, '0');
    function tick() {
        const diff = target - Date.now();
        if (diff <= 0) { document.getElementById('countdownTimer')?.remove(); return; }
        document.getElementById('cd-days').textContent  = pad(Math.floor(diff / 86400000));
        document.getElementById('cd-hours').textContent = pad(Math.floor((diff % 86400000) / 3600000));
        document.getElementById('cd-mins').textContent  = pad(Math.floor((diff % 3600000) / 60000));
        document.getElementById('cd-secs').textContent  = pad(Math.floor((diff % 60000) / 1000));
    }
    tick();
    setInterval(tick, 1000);
})();
@endif
</script>
</body>
</html>
