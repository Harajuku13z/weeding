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
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&family=Jost:wght@200;300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/css/glightbox.min.css">
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
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
        --cream:      var(--color-background, #f8f4ef);
        --ivory:      var(--color-secondary, #fdf9f5);
        --dark:       var(--color-text, #1a1612);
        --gold:       var(--color-primary, #b8975a);
        --gold-light: var(--color-accent, #d4b47a);
        --wt:         var(--color-text, #3d342c);
        --wm:         var(--color-accent, #8c7d70);
        --ft:         var(--font-title, 'Cormorant Garamond', Georgia, serif);
        --fb:         var(--font-body, 'Jost', sans-serif);
        --radius:     var(--border-radius, 8px);
    }

    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body {
        margin: 0;
        background: var(--cream);
        color: var(--wt);
        font-family: var(--fb);
        overflow-x: hidden;
    }

    /* ═══════════════════════════════════════════════════════
       ENVELOPE OVERLAY
    ═══════════════════════════════════════════════════════ */
    .envelope-overlay {
        position: fixed; inset: 0; z-index: 9999;
        background: radial-gradient(ellipse at 50% 40%, #1a150f 0%, #0a0806 100%);
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        cursor: pointer;
    }
    .envelope-overlay.is-dismissed {
        opacity: 0; visibility: hidden; pointer-events: none;
    }

    .env-particles { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .env-particle {
        position: absolute; border-radius: 50%;
        background: var(--gold); opacity: 0;
        animation: envFloat var(--dur,7s) var(--delay,0s) infinite ease-in-out;
    }
    @keyframes envFloat {
        0%   { transform: translateY(0) scale(1); opacity: 0; }
        15%  { opacity: var(--op,.2); }
        85%  { opacity: var(--op,.2); }
        100% { transform: translateY(-100vh) scale(.3); opacity: 0; }
    }

    .envelope-wrapper {
        position: relative; z-index: 1;
        display: flex; flex-direction: column;
        align-items: center; gap: 40px;
    }

    .env-scene-label {
        font-family: var(--ft);
        font-size: clamp(12px,1.8vw,15px);
        font-weight: 300;
        letter-spacing: 6px;
        text-transform: uppercase;
        color: rgba(255,255,255,.3);
        text-align: center;
        padding: 0 20px;
    }

    .envelope-body {
        position: relative; width: 380px; height: 250px;
        filter: drop-shadow(0 40px 80px rgba(0,0,0,.65));
        transition: transform .4s ease;
        perspective: 1200px;
    }
    .envelope-body:hover { transform: translateY(-6px) scale(1.02); }

    .env-clip {
        position: absolute; inset: 0;
        overflow: hidden; border-radius: var(--radius);
        perspective: 1200px;
    }

    .env-back {
        position: absolute; inset: 0;
        background: linear-gradient(155deg, #2c2318 0%, #1b1710 100%);
        border: 1px solid rgba(184,151,90,.18);
        border-radius: var(--radius);
    }

    .env-deco-line {
        position: absolute; height: 1px; left: 14%; right: 14%; z-index: 2;
        background: linear-gradient(90deg, transparent, var(--gold), transparent);
    }
    .env-deco-line.top { top: 26px; }
    .env-deco-line.bottom { bottom: 22px; }

    .envelope-left {
        position: absolute; top: 0; left: 0;
        width: 50%; height: 100%; z-index: 3;
        clip-path: polygon(0 0, 0 100%, 100% 50%);
        background: linear-gradient(135deg, #261f14, #1e1911);
    }
    .envelope-right {
        position: absolute; top: 0; right: 0;
        width: 50%; height: 100%; z-index: 3;
        clip-path: polygon(100% 0, 0 50%, 100% 100%);
        background: linear-gradient(225deg, #261f14, #1e1911);
    }
    .envelope-flap-bottom {
        position: absolute; bottom: 0; left: 0;
        width: 100%; height: 55%; z-index: 4;
        clip-path: polygon(0 100%, 50% 0%, 100% 100%);
        background: linear-gradient(175deg, #2b2217, #1a1610);
    }
    .envelope-flap {
        position: absolute; top: 0; left: 0;
        width: 100%; height: 55%; z-index: 10;
        clip-path: polygon(0 0, 100% 0, 50% 100%);
        background: linear-gradient(170deg, #372d1e 0%, #261f14 100%);
        transform-origin: top center;
        transition: transform 1s cubic-bezier(.4,0,.2,1);
        backface-visibility: hidden;
        will-change: transform;
    }
    .envelope-body.is-open .envelope-flap { transform: rotateX(-180deg); }

    .envelope-seal {
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%,-50%);
        width: 54px; height: 54px; border-radius: 50%; z-index: 11;
        background: radial-gradient(circle at 38% 33%, #d6b87c, #8a6a30);
        border: 2px solid rgba(255,255,255,.12);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; color: rgba(255,255,255,.85);
        box-shadow: 0 4px 24px rgba(0,0,0,.55), inset 0 1px 0 rgba(255,255,255,.18);
        transition: transform .4s ease .1s, opacity .4s ease .1s;
    }
    .envelope-body.is-open .envelope-seal {
        transform: translate(-50%,-50%) scale(0); opacity: 0;
    }

    .envelope-card {
        position: absolute;
        left: 8%; right: 8%; top: 8%; height: 84%;
        background: linear-gradient(155deg, #fdf9f5 0%, #f3e9df 100%);
        border-radius: 3px; z-index: 2;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center; gap: 12px;
        overflow: hidden;
        box-shadow: 0 -16px 48px rgba(0,0,0,.45);
        text-align: center;
        padding: 10px 16px;
        will-change: transform;
    }
    .envelope-card::before {
        content: '';
        position: absolute; inset: 9px;
        border: 1px solid rgba(184,151,90,.28);
        border-radius: 2px;
        pointer-events: none;
    }
    .envelope-card::after {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(to bottom, rgba(184,151,90,.06) 0%, transparent 30%);
        pointer-events: none;
    }

    .envelope-hint {
        display: flex; flex-direction: column; align-items: center; gap: 12px;
        font-family: var(--fb); font-size: 10px; letter-spacing: 4px;
        text-transform: uppercase; color: rgba(255,255,255,.28); font-weight: 300;
        text-align: center;
        padding: 0 20px;
    }
    .env-hint-arrow {
        display: flex; flex-direction: column; align-items: center; gap: 4px;
        animation: envBounce 2s ease-in-out infinite;
    }
    .env-hint-arrow span {
        display: block; width: 1px; height: 12px;
        background: linear-gradient(to bottom, var(--gold), transparent);
    }
    .env-hint-arrow span:nth-child(2) { opacity: .6; }
    .env-hint-arrow span:nth-child(3) { opacity: .3; }
    @keyframes envBounce {
        0%,100% { transform: translateY(0); }
        50% { transform: translateY(6px); }
    }

    /* ═══════════════════════════════════════════════════════
       NAVIGATION
    ═══════════════════════════════════════════════════════ */
    .wedding-nav {
        position: fixed; top: 0; left: 0; right: 0; z-index: 100;
        padding: 0 48px; height: 72px;
        display: flex; align-items: center; justify-content: space-between;
        transition: background .35s ease, backdrop-filter .35s ease, box-shadow .35s ease;
    }
    .wedding-nav.scrolled {
        background: rgba(248,244,239,.88);
        backdrop-filter: blur(18px);
        box-shadow: 0 10px 30px rgba(20,18,16,.06);
        border-bottom: 1px solid rgba(184,151,90,.10);
    }
    .nav-couple {
        font-family: var(--ft);
        font-size: 22px;
        font-weight: 500;
        color: white;
        transition: color .35s ease;
        letter-spacing: .01em;
    }
    .wedding-nav.scrolled .nav-couple { color: var(--dark); }
    .nav-links { display: flex; gap: 34px; }
    .nav-links a {
        font-family: var(--fb);
        font-size: 10px;
        letter-spacing: 3px;
        text-transform: uppercase;
        font-weight: 500;
        color: rgba(255,255,255,.56);
        text-decoration: none;
        transition: color .25s ease;
    }
    .wedding-nav.scrolled .nav-links a { color: var(--wm); }
    .nav-links a:hover { color: var(--gold) !important; }

    /* ═══════════════════════════════════════════════════════
       HERO
    ═══════════════════════════════════════════════════════ */
    .hero-section {
        position: relative;
        height: 100vh;
        min-height: 760px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        isolation: isolate;
    }

    .hero-bg {
        position: absolute;
        inset: -30px;
        background-size: cover;
        background-position: center;
        transform: scale(1.06);
        will-change: transform;
        filter: brightness(0.85) saturate(1.1);
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        z-index: 1;
        background:
            radial-gradient(circle at 50% 30%, rgba(184,151,90,.12), transparent 40%),
            linear-gradient(to bottom, rgba(0,0,0,.18) 0%, rgba(0,0,0,.42) 50%, rgba(0,0,0,.72) 100%);
    }

    .hero-grid {
        position: absolute; inset: 0; z-index: 1;
        background-image:
            linear-gradient(rgba(184,151,90,.18) 1px, transparent 1px),
            linear-gradient(90deg, rgba(184,151,90,.18) 1px, transparent 1px);
        background-size: 80px 80px;
        opacity: .045;
    }

    .hero-light {
        position: absolute;
        border-radius: 50%;
        filter: blur(90px);
        pointer-events: none;
        z-index: 1;
        mix-blend-mode: screen;
    }
    .hero-light-1 {
        width: 340px;
        height: 340px;
        background: rgba(184,151,90,.18);
        top: 10%;
        left: 8%;
        animation: floatLight 10s ease-in-out infinite;
    }
    .hero-light-2 {
        width: 280px;
        height: 280px;
        background: rgba(255,255,255,.08);
        bottom: 10%;
        right: 8%;
        animation: floatLight 12s ease-in-out infinite reverse;
    }
    @keyframes floatLight {
        0%,100% { transform: translate3d(0,0,0); }
        50% { transform: translate3d(0,-18px,0); }
    }

    .hero-particles {
        position: absolute;
        inset: 0;
        z-index: 1;
        pointer-events: none;
        overflow: hidden;
    }
    .hero-particle {
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,.85), rgba(184,151,90,.18));
        opacity: 0;
        animation: heroParticleFloat var(--dur, 10s) linear infinite;
    }
    @keyframes heroParticleFloat {
        0% {
            transform: translateY(30px) scale(.6);
            opacity: 0;
        }
        15% { opacity: var(--op, .18); }
        85% { opacity: var(--op, .18); }
        100% {
            transform: translateY(-120vh) scale(1.15);
            opacity: 0;
        }
    }

    .hero-circle {
        position: absolute;
        border-radius: 50%;
        border: 1px solid rgba(184,151,90,.24);
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        opacity: .10;
        z-index: 1;
        animation: pulseCircle 10s ease-in-out infinite;
    }
    .hero-circle-1 { width: 560px; height: 560px; animation-delay: 0s; }
    .hero-circle-2 { width: 860px; height: 860px; animation-delay: 1.5s; }
    .hero-circle-3 { width: 1160px; height: 1160px; animation-delay: 3s; }

    @keyframes pulseCircle {
        0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: .08; }
        50% { transform: translate(-50%, -50%) scale(1.04); opacity: .14; }
    }

    .hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
        padding: 0 24px;
        max-width: 1020px;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        margin-bottom: 26px;
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        backdrop-filter: blur(12px);
        border-radius: 999px;
        color: rgba(255,255,255,.88);
        font-size: 10px;
        letter-spacing: .28em;
        text-transform: uppercase;
        font-weight: 500;
    }

    .hero-ornament {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        margin-bottom: 22px;
    }
    .ornament-line {
        width: 72px;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--gold));
        opacity: .95;
    }
    .ornament-line.right {
        background: linear-gradient(270deg, transparent, var(--gold));
    }
    .ornament-diamond {
        width: 8px;
        height: 8px;
        background: var(--gold);
        transform: rotate(45deg);
        box-shadow: 0 0 20px rgba(184,151,90,.25);
    }

    .hero-tagline {
        font-family: var(--fb);
        font-size: 11px;
        letter-spacing: .42em;
        text-transform: uppercase;
        color: var(--gold);
        font-weight: 500;
        display: block;
        margin-bottom: 22px;
        text-shadow: 0 1px 8px rgba(0,0,0,.4);
    }

    .hero-names {
        font-family: var(--ft);
        font-size: clamp(52px,10vw,108px);
        font-weight: 500;
        color: #fff;
        line-height: 1.02;
        letter-spacing: -.03em;
        margin-bottom: 26px;
        text-shadow: 0 2px 16px rgba(0,0,0,.38), 0 8px 40px rgba(0,0,0,.28);
    }
    .hero-name {
        display: inline-block;
    }

    .ampersand {
        display: block;
        font-style: italic;
        color: var(--gold);
        font-size: .48em;
        line-height: 1.25;
        font-weight: 400;
        text-shadow: 0 1px 8px rgba(0,0,0,.3);
    }

    .hero-date {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        font-family: var(--fb);
        font-size: 12px;
        letter-spacing: .28em;
        text-transform: uppercase;
        color: rgba(255,255,255,.94);
        font-weight: 500;
        margin-bottom: 38px;
        text-shadow: 0 1px 10px rgba(0,0,0,.4);
        padding: 12px 18px;
        border-radius: 999px;
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.10);
        backdrop-filter: blur(10px);
    }
    .hero-date i { color: var(--gold); }

    .countdown-timer {
        display: flex;
        gap: 14px;
        justify-content: center;
        margin-bottom: 38px;
        flex-wrap: wrap;
    }
    .countdown-item {
        display: flex; flex-direction: column; align-items: center;
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.14);
        backdrop-filter: blur(14px);
        border-radius: calc(var(--radius) + 6px);
        padding: 18px 22px;
        min-width: 86px;
        box-shadow: 0 12px 40px rgba(0,0,0,.18);
    }
    .countdown-number {
        font-family: var(--ft);
        font-size: 40px;
        font-weight: 500;
        color: #fff;
        line-height: 1;
        text-shadow: 0 2px 12px rgba(0,0,0,.3);
    }
    .countdown-label {
        font-family: var(--fb);
        font-size: 9px;
        letter-spacing: .28em;
        text-transform: uppercase;
        color: var(--gold);
        margin-top: 8px;
        font-weight: 500;
    }

    .hero-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .hero-cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        font-family: var(--fb);
        font-size: 11px;
        letter-spacing: .28em;
        text-transform: uppercase;
        font-weight: 600;
        color: var(--dark);
        background: linear-gradient(135deg, var(--gold), var(--gold-light));
        padding: 18px 38px;
        text-decoration: none;
        border-radius: 999px;
        transition: transform .35s cubic-bezier(.4,0,.2,1), box-shadow .35s ease, filter .35s ease;
        box-shadow: 0 12px 30px rgba(184,151,90,.25), 0 4px 12px rgba(0,0,0,.15);
        position: relative;
        overflow: hidden;
    }
    .hero-cta::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,.2), transparent 50%);
        pointer-events: none;
    }
    .hero-cta:hover {
        transform: translateY(-4px) scale(1.02);
        color: var(--dark);
        box-shadow: 0 20px 44px rgba(184,151,90,.35), 0 6px 16px rgba(0,0,0,.2);
        filter: brightness(1.05);
    }

    .hero-cta-secondary {
        background: rgba(255,255,255,.08);
        color: #fff;
        border: 1px solid rgba(255,255,255,.14);
        backdrop-filter: blur(12px);
    }
    .hero-cta-secondary:hover {
        color: #fff;
        background: rgba(255,255,255,.12);
    }

    .hero-scroll-indicator {
        position: absolute;
        left: 50%;
        bottom: 24px;
        transform: translateX(-50%);
        z-index: 2;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: rgba(255,255,255,.72);
        font-size: 10px;
        letter-spacing: .28em;
        text-transform: uppercase;
        animation: scrollIndicator 2.4s ease-in-out infinite;
    }
    .hero-scroll-indicator i {
        font-size: 18px;
        color: var(--gold);
    }
    @keyframes scrollIndicator {
        0%,100% { transform: translateX(-50%) translateY(0); opacity: .7; }
        50% { transform: translateX(-50%) translateY(8px); opacity: 1; }
    }

    /* ═══════════════════════════════════════════════════════
       SECTIONS
    ═══════════════════════════════════════════════════════ */
    .wedding-section { padding: 130px 0; position: relative; overflow: hidden; }
    .section-inner { max-width: 1100px; margin: 0 auto; padding: 0 40px; }
    .alt-bg { background: var(--ivory); }
    .dark-bg { background: linear-gradient(160deg, var(--dark) 0%, #0f0c09 100%); color: var(--cream); }

    .section-header { text-align: center; margin-bottom: 72px; }
    .section-ornament {
        display: flex; align-items: center; justify-content: center;
        gap: 16px; margin-bottom: 16px;
    }
    .section-ornament-line {
        width: 40px; height: 1px;
        background: linear-gradient(90deg, transparent, var(--gold));
    }
    .section-ornament-line:last-child {
        background: linear-gradient(270deg, transparent, var(--gold));
    }
    .section-ornament-icon { color: var(--gold); font-size: 12px; }
    .section-label {
        font-family: var(--fb);
        font-size: 9px;
        letter-spacing: 5px;
        text-transform: uppercase;
        color: var(--gold);
        font-weight: 400;
        display: block;
        margin-bottom: 12px;
    }
    .section-title {
        font-family: var(--ft);
        font-size: clamp(32px,5vw,56px);
        font-weight: 400;
        color: var(--wt);
        line-height: 1.1;
        margin-bottom: 20px;
    }
    .section-subtitle {
        font-size: 15px;
        font-weight: 300;
        color: var(--wm);
        line-height: 1.8;
        max-width: 560px;
        margin-inline: auto;
    }

    /* HISTOIRE */
    .story-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 80px;
        align-items: center;
    }
    .story-image { position: relative; }
    .story-image-frame { position: relative; }
    .story-image-frame::before {
        content: '';
        position: absolute;
        inset: -16px -16px 16px 16px;
        border: 1px solid rgba(184,151,90,.18);
        border-radius: 2px;
        pointer-events: none;
    }
    .story-image-frame img {
        width: 100%;
        aspect-ratio: 3/4;
        object-fit: cover;
        display: block;
        border-radius: 2px;
        box-shadow: 0 20px 60px rgba(20,18,16,.08);
    }
    .story-quote {
        font-family: var(--ft);
        font-size: 22px;
        font-style: italic;
        font-weight: 300;
        line-height: 1.6;
        color: var(--dark);
        margin-bottom: 24px;
        padding-left: 24px;
        border-left: 2px solid var(--gold);
    }
    .story-body {
        font-size: 15px;
        font-weight: 300;
        line-height: 1.9;
        color: var(--wm);
    }

    /* GALERIE */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill,minmax(260px,1fr));
        gap: 10px;
    }
    .gallery-item {
        position: relative;
        overflow: hidden;
        aspect-ratio: 1;
        border-radius: calc(var(--radius) + 2px);
    }
    .gallery-item img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .6s ease;
    }
    .gallery-item:hover img { transform: scale(1.06); }
    .gallery-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,.58), rgba(0,0,0,.08));
        display: flex; align-items: flex-end;
        padding: 20px;
        opacity: 0;
        transition: opacity .3s;
    }
    .gallery-item:hover .gallery-overlay { opacity: 1; }
    .gallery-caption {
        font-family: var(--ft);
        font-size: 15px;
        color: white;
        font-style: italic;
    }

    /* DÉTAILS */
    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit,minmax(200px,1fr));
        gap: 18px;
    }
    .detail-card {
        background: linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.85));
        border: 1px solid rgba(184,151,90,.12);
        padding: 42px 28px;
        text-align: center;
        border-radius: calc(var(--radius) + 6px);
        box-shadow: 0 18px 50px rgba(20,18,16,.04);
        transition: transform .35s cubic-bezier(.4,0,.2,1), box-shadow .35s ease, border-color .35s ease;
        backdrop-filter: blur(8px);
    }
    .detail-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 28px 70px rgba(20,18,16,.10);
        border-color: rgba(184,151,90,.22);
    }
    .detail-icon { font-size: 22px; color: var(--gold); margin-bottom: 20px; }
    .detail-label {
        font-family: var(--fb);
        font-size: 9px;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: var(--wm);
        font-weight: 400;
        display: block;
        margin-bottom: 10px;
    }
    .detail-value {
        font-family: var(--ft);
        font-size: 22px;
        font-weight: 500;
        color: var(--wt);
    }
    .detail-desc {
        font-size: 12px;
        color: var(--wm);
        margin-top: 4px;
        font-weight: 300;
    }

    /* PROGRAMME */
    .program-timeline {
        position: relative;
        max-width: 760px;
        margin: 0 auto;
        padding: 0 0 24px;
    }
    .program-timeline::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 12px;
        bottom: 12px;
        width: 2px;
        background: linear-gradient(to bottom, transparent, var(--gold) 5%, var(--gold) 95%, transparent);
        transform: translateX(-50%);
        opacity: .5;
    }
    .program-item {
        display: grid;
        grid-template-columns: 1fr 52px 1fr;
        margin-bottom: 48px;
        align-items: start;
        gap: 0;
    }
    .program-item:last-child { margin-bottom: 0; }

    .program-item:nth-child(odd) .program-content {
        text-align: right;
        padding-right: 32px;
    }
    .program-item:nth-child(even) .program-content {
        text-align: left;
        padding-left: 32px;
        grid-column: 3;
    }
    .program-item:nth-child(even) .program-dot {
        grid-column: 2; grid-row: 1;
    }

    .program-dot {
        width: 14px; height: 14px; border-radius: 50%;
        background: var(--gold);
        border: 3px solid var(--cream);
        box-shadow: 0 0 0 1px var(--gold), 0 0 20px rgba(184,151,90,.18);
        flex-shrink: 0;
        margin-top: 5px;
        justify-self: center;
    }
    .program-icon { display: none; }
    .program-time {
        font-family: var(--fb);
        font-size: 10px;
        letter-spacing: .22em;
        text-transform: uppercase;
        color: var(--gold);
        margin-bottom: 6px;
        font-weight: 500;
    }
    .program-title {
        font-family: var(--ft);
        font-size: 22px;
        font-weight: 500;
        color: var(--wt);
        margin-bottom: 8px;
        line-height: 1.25;
    }
    .program-desc {
        font-size: 14px;
        font-weight: 300;
        color: var(--wm);
        line-height: 1.65;
    }
    .program-venue {
        font-size: 11px;
        letter-spacing: .12em;
        color: var(--gold);
        margin-top: 8px;
        font-weight: 400;
    }

    /* LIEUX — cartes compactes */
    .venues-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
    }
    .venue-card {
        position: relative;
        overflow: hidden;
        aspect-ratio: 3/4;
        border-radius: calc(var(--radius) + 4px);
        box-shadow: 0 12px 36px rgba(20,18,16,.08);
        transition: transform .4s cubic-bezier(.4,0,.2,1), box-shadow .4s ease;
    }
    .venue-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 50px rgba(20,18,16,.14);
    }
    .venue-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .8s cubic-bezier(.4,0,.2,1);
    }
    .venue-card:hover img { transform: scale(1.08); }
    .venue-photo { width: 100%; height: 100%; }
    .venue-photo-placeholder {
        width: 100%; height: 100%;
        background: linear-gradient(155deg, #2a2319, #1a1612);
        display: flex; align-items: center; justify-content: center;
        font-size: 36px; color: rgba(184,151,90,.18);
    }
    .venue-info {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,.88) 0%, rgba(0,0,0,.24) 50%, transparent 100%);
        display: flex; flex-direction: column; justify-content: flex-end;
        padding: 18px;
    }
    .venue-name {
        font-family: var(--ft);
        font-size: 18px;
        font-weight: 500;
        color: white;
        margin-bottom: 4px;
    }
    .venue-address {
        font-size: 11px;
        color: rgba(255,255,255,.56);
        font-weight: 300;
        margin-bottom: 10px;
    }
    .venue-actions { display: flex; gap: 6px; flex-wrap: wrap; }
    .btn-venue {
        display: inline-flex; align-items: center; gap: 5px;
        font-family: var(--fb); font-size: 8px; letter-spacing: 1.5px;
        text-transform: uppercase; font-weight: 500;
        color: var(--dark); background: var(--gold);
        padding: 6px 12px; text-decoration: none;
        border-radius: 999px; transition: background .2s, transform .2s ease;
    }
    .btn-venue:hover {
        background: var(--gold-light);
        transform: translateY(-1px);
    }
    .btn-venue-maps { background: var(--gold); }
    .btn-venue-waze { background: var(--gold-light); }

    /* DRESS CODE : palette en haut, 4 cartes en dessous */
    .dresscode-palette-wrap {
        text-align: center;
        margin-bottom: 40px;
    }
    .dresscode-palette-wrap .palette-swatches {
        justify-content: center;
        margin-top: 12px;
    }
    .dresscode-cards-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    .dresscode-card {
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(184,151,90,.15);
        padding: 28px 24px;
        border-radius: calc(var(--radius) + 6px);
        transition: border-color .3s, transform .25s ease;
        min-height: 140px;
    }
    .dresscode-card:hover {
        border-color: rgba(184,151,90,.35);
        transform: translateY(-2px);
    }
    .dresscode-label {
        font-family: var(--fb);
        font-size: 9px;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: var(--gold);
        font-weight: 500;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .palette-swatches { display: flex; gap: 14px; flex-wrap: wrap; }
    .palette-swatch { display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .swatch-color {
        width: 48px; height: 48px; border-radius: 50%;
        box-shadow: 0 4px 20px rgba(0,0,0,.35);
    }
    .swatch-name { font-size: 11px; color: rgba(255,255,255,.5); letter-spacing: 1px; }
    @media (max-width: 900px) {
        .dresscode-cards-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 500px) {
        .dresscode-cards-grid { grid-template-columns: 1fr; }
    }

    .univers-intro { text-align: center; max-width: 560px; margin: 0 auto 40px; }
    .univers-intro .section-subtitle { color: rgba(255,255,255,.75); }
    .univers-inspiration-title {
        font-family: var(--ft);
        font-size: 22px;
        font-weight: 500;
        color: #fff;
        margin-bottom: 28px;
        text-align: center;
        letter-spacing: .02em;
    }
    .univers-inspiration-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 20px;
        margin-top: 32px;
    }
    .univers-inspiration-item {
        position: relative;
        overflow: hidden;
        border-radius: calc(var(--radius) + 4px);
        aspect-ratio: 3/4;
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.08);
        box-shadow: 0 8px 32px rgba(0,0,0,.2);
    }
    .univers-inspiration-item img {
        width: 100%; height: 100%;
        object-fit: cover; display: block;
        transition: transform .4s ease;
    }
    .univers-inspiration-item:hover img { transform: scale(1.05); }
    .univers-inspiration-item .overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,.6) 0%, transparent 50%);
        display: flex; align-items: flex-end; padding: 16px;
        opacity: 0; transition: opacity .3s;
    }
    .univers-inspiration-item:hover .overlay { opacity: 1; }
    .univers-inspiration-item .overlay span {
        font-family: var(--fb); font-size: 12px; color: #fff; letter-spacing: .05em;
    }

    /* CADEAUX */
    .gifts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill,minmax(260px,1fr));
        gap: 18px;
    }
    .gift-card {
        background: white;
        border: 1px solid rgba(184,151,90,.1);
        overflow: hidden;
        border-radius: calc(var(--radius) + 6px);
        transition: transform .3s, box-shadow .3s;
        box-shadow: 0 14px 40px rgba(20,18,16,.04);
    }
    .gift-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 60px rgba(0,0,0,.08);
    }
    .gift-image { aspect-ratio: 1; overflow: hidden; background: var(--cream); }
    .gift-image img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform .5s;
    }
    .gift-card:hover .gift-image img { transform: scale(1.05); }
    .gift-image-placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        font-size: 40px; color: var(--gold); opacity: .3;
    }
    .gift-info { padding: 24px; }
    .gift-name {
        font-family: var(--ft);
        font-size: 18px;
        font-weight: 500;
        color: var(--dark);
        margin-bottom: 8px;
    }
    .gift-price {
        font-family: var(--fb);
        font-size: 11px;
        letter-spacing: 2px;
        color: var(--gold);
        font-weight: 500;
        margin-bottom: 8px;
    }
    .gift-desc {
        font-size: 12px;
        color: var(--wm);
        line-height: 1.7;
        font-weight: 300;
    }
    .gift-reserved {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 10px; letter-spacing: 2px; font-weight: 500;
        text-transform: uppercase; color: #10b981; margin-top: 12px;
    }

    /* RÈGLES */
    .rules-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; }
    .rules-column {
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(184,151,90,.1);
        padding: 40px 32px;
        border-radius: calc(var(--radius) + 4px);
    }
    .rules-column-title {
        font-family: var(--fb);
        font-size: 9px;
        letter-spacing: 3px;
        text-transform: uppercase;
        font-weight: 500;
        margin-bottom: 24px;
        display: flex; align-items: center; gap: 8px;
    }
    .rules-column.allowed .rules-column-title { color: #10b981; }
    .rules-column.forbidden .rules-column-title { color: #ef4444; }
    .rules-column.recommendation .rules-column-title { color: var(--gold); }
    .rule-item { display: flex; gap: 12px; margin-bottom: 16px; }
    .rule-icon { font-size: 14px; flex-shrink: 0; margin-top: 2px; }
    .rules-column.allowed .rule-icon { color: #10b981; }
    .rules-column.forbidden .rule-icon { color: #ef4444; }
    .rules-column.recommendation .rule-icon { color: var(--gold); }
    .rule-title { font-size: 14px; color: white; font-weight: 400; margin-bottom: 2px; }
    .rule-desc { font-size: 12px; color: rgba(255,255,255,.42); line-height: 1.6; }

    /* RSVP */
    .rsvp-section {
        background: linear-gradient(160deg, var(--dark) 0%, #0f0c09 100%);
        position: relative;
        overflow: hidden;
    }
    .rsvp-section::before {
        content: '';
        position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 700px 500px at 50% 0%, rgba(184,151,90,.08), transparent),
            radial-gradient(ellipse 400px 400px at 20% 80%, rgba(184,151,90,.04), transparent);
    }
    .rsvp-form-card {
        max-width: 620px; margin: 0 auto;
        background: rgba(255,255,255,.04);
        border: 1px solid rgba(184,151,90,.18);
        padding: 56px;
        position: relative;
        border-radius: calc(var(--radius) + 8px);
        backdrop-filter: blur(16px);
        box-shadow: 0 32px 80px rgba(0,0,0,.25), inset 0 1px 0 rgba(255,255,255,.05);
    }
    .rsvp-form-card::before {
        content: '';
        position: absolute; inset: 6px;
        border: 1px solid rgba(184,151,90,.08);
        pointer-events: none;
        border-radius: calc(var(--radius) + 4px);
    }
    .rsvp-form-title {
        font-family: var(--ft);
        font-size: 32px;
        font-weight: 400;
        color: white;
        margin-bottom: 6px;
    }
    .rsvp-form-subtitle {
        font-size: 13px;
        color: rgba(255,255,255,.42);
        margin-bottom: 36px;
        font-weight: 300;
    }

    .rsvp-input {
        width: 100%;
        padding: 14px 18px;
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(184,151,90,.2);
        color: white;
        font-family: var(--fb);
        font-size: 14px;
        font-weight: 300;
        outline: none;
        border-radius: 10px;
        transition: border-color .3s, background .3s, box-shadow .3s;
    }
    .rsvp-input:focus {
        border-color: var(--gold);
        background: rgba(255,255,255,.08);
        box-shadow: 0 0 0 4px rgba(184,151,90,.08);
    }
    .rsvp-input::placeholder { color: rgba(255,255,255,.24); }
    textarea.rsvp-input { resize: vertical; }

    .rsvp-status-group { display: flex; gap: 10px; }
    .rsvp-status-btn {
        flex: 1;
        padding: 14px;
        cursor: pointer;
        background: rgba(255,255,255,.04);
        border: 1px solid rgba(184,151,90,.15);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        transition: all .2s;
        font-family: var(--fb);
        border-radius: 12px;
    }
    .rsvp-status-btn span:last-child {
        font-size: 10px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: rgba(255,255,255,.35);
        font-weight: 400;
    }
    .rsvp-status-btn:hover {
        border-color: rgba(184,151,90,.4);
        background: rgba(184,151,90,.05);
        transform: translateY(-2px);
    }
    .rsvp-status-btn.active-accepted {
        border-color: #10b981;
        background: rgba(16,185,129,.1);
    }
    .rsvp-status-btn.active-accepted span:last-child { color: #10b981; }
    .rsvp-status-btn.active-maybe {
        border-color: #f59e0b;
        background: rgba(245,158,11,.1);
    }
    .rsvp-status-btn.active-maybe span:last-child { color: #f59e0b; }
    .rsvp-status-btn.active-declined {
        border-color: #ef4444;
        background: rgba(239,68,68,.1);
    }
    .rsvp-status-btn.active-declined span:last-child { color: #ef4444; }

    .rsvp-btn {
        width: 100%;
        padding: 18px;
        background: linear-gradient(135deg, var(--gold), var(--gold-light));
        border: none;
        cursor: pointer;
        font-family: var(--fb);
        font-size: 10px;
        letter-spacing: 4px;
        text-transform: uppercase;
        font-weight: 600;
        color: var(--dark);
        margin-top: 32px;
        border-radius: 999px;
        transition: transform .2s ease, box-shadow .2s ease;
        box-shadow: 0 12px 30px rgba(0,0,0,.18);
    }
    .rsvp-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 40px rgba(0,0,0,.24);
    }

    .alert {
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 24px;
        font-size: 13px;
        font-weight: 300;
    }
    .alert-success {
        background: rgba(16,185,129,.1);
        border: 1px solid rgba(16,185,129,.3);
        color: #10b981;
    }
    .alert-danger {
        background: rgba(239,68,68,.1);
        border: 1px solid rgba(239,68,68,.3);
        color: #ef4444;
    }

    /* FOOTER */
    .wedding-footer {
        background: linear-gradient(180deg, #0e0b08 0%, #080604 100%);
        text-align: center;
        padding: 80px 40px;
        position: relative;
    }
    .wedding-footer::before {
        content: '';
        position: absolute; top: 0; left: 20%; right: 20%; height: 1px;
        background: linear-gradient(90deg, transparent, rgba(184,151,90,.2), transparent);
    }
    .footer-couple {
        font-family: var(--ft);
        font-size: 38px;
        font-weight: 400;
        color: white;
        margin-bottom: 12px;
        letter-spacing: .02em;
    }
    .footer-date {
        font-family: var(--fb);
        font-size: 9px;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: var(--gold);
        font-weight: 400;
        margin-bottom: 32px;
    }
    .footer-ornament {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        margin-bottom: 24px;
    }
    .footer-line { width: 80px; height: 1px; background: rgba(184,151,90,.2); }
    .footer-icon { color: var(--gold); font-size: 12px; }

    .floral-corner {
        position: absolute;
        width: 180px;
        height: 180px;
        pointer-events: none;
        opacity: .10;
        z-index: 0;
        background:
            radial-gradient(circle at 30% 30%, rgba(184,151,90,.55) 0 6%, transparent 7%),
            radial-gradient(circle at 55% 45%, rgba(184,151,90,.28) 0 7%, transparent 8%),
            radial-gradient(circle at 70% 70%, rgba(184,151,90,.18) 0 8%, transparent 9%);
        filter: blur(1px);
    }
    .floral-corner-tl { top: 0; left: 0; }
    .floral-corner-tr { top: 0; right: 0; transform: scaleX(-1); }
    .floral-corner-br { bottom: 0; right: 0; transform: rotate(180deg); }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .story-content { grid-template-columns: 1fr; gap: 48px; }
        .story-image-frame::before { display: none; }
        .dresscode-cards-grid { grid-template-columns: repeat(2, 1fr); }
        .univers-inspiration-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .rules-grid { grid-template-columns: 1fr; }

        .program-timeline { max-width: 100%; padding-left: 0; padding-right: 0; }
        .program-timeline::before {
            left: 13px;
            top: 10px;
            bottom: 10px;
            width: 2px;
            opacity: .55;
        }
        .program-item {
            display: grid;
            grid-template-columns: 28px 1fr;
            gap: 0 18px;
            margin-bottom: 32px;
            align-items: start;
        }
        .program-item:last-child { margin-bottom: 0; }
        .program-item .program-dot {
            grid-column: 1;
            grid-row: 1;
            width: 14px;
            height: 14px;
            margin-left: 6px;
            margin-top: 4px;
            justify-self: start;
        }
        .program-item .program-content {
            grid-column: 2;
            grid-row: 1;
            text-align: left !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            min-width: 0;
        }
        .program-item:nth-child(even) .program-dot { grid-column: 1; }
        .program-item:nth-child(even) .program-content { grid-column: 2; }

        .program-title { font-size: 17px; line-height: 1.3; }
        .program-desc { font-size: 13px; margin-top: 4px; }
        .program-time { font-size: 9px; letter-spacing: .18em; }
        .program-venue { font-size: 10px; margin-top: 6px; }

        .rsvp-form-card { padding: 32px 24px; }
    }

    /* ═══════ MOBILE NAV TOGGLE ═══════ */
    .mobile-nav-toggle {
        display: none;
        flex-direction: column; gap: 5px;
        background: none; border: none; cursor: pointer;
        padding: 8px; z-index: 101;
    }
    .mobile-nav-toggle span {
        display: block; width: 22px; height: 1.5px;
        background: rgba(255,255,255,.7);
        transition: transform .3s ease, opacity .3s ease, background .3s ease;
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
            background: rgba(10,8,6,.96); backdrop-filter: blur(20px);
            flex-direction: column;
            align-items: center; justify-content: center;
            gap: 28px; z-index: 100;
        }
        .nav-links.is-open {
            display: flex;
        }
        .nav-links a {
            font-size: 13px !important;
            letter-spacing: 4px !important;
            color: rgba(255,255,255,.7) !important;
        }
        .nav-links a:hover { color: var(--gold) !important; }

        .envelope-body { width: 300px; height: 200px; }

        .wedding-section { padding: 80px 0; }
        .section-inner { padding: 0 20px; }

        .hero-section {
            min-height: 100vh;
            min-height: 100dvh;
            padding: 90px 16px 40px;
        }
        .hero-content { padding: 0 10px; }
        .hero-badge {
            font-size: 9px;
            letter-spacing: .18em;
            padding: 9px 14px;
            margin-bottom: 20px;
        }
        .hero-names {
            font-size: clamp(40px, 12vw, 58px);
            margin-bottom: 18px;
            font-weight: 600;
        }
        .hero-tagline {
            font-size: 10px;
            letter-spacing: .28em;
            margin-bottom: 18px;
        }
        .hero-date {
            font-size: 10px;
            letter-spacing: .18em;
            margin-bottom: 24px;
            padding: 10px 14px;
        }
        .countdown-timer { gap: 8px; margin-bottom: 26px; }
        .countdown-item { padding: 12px 14px; min-width: 72px; }
        .countdown-number { font-size: 30px; }
        .countdown-label { font-size: 8px; }

        .hero-actions {
            flex-direction: column;
            gap: 10px;
        }
        .hero-cta,
        .hero-cta-secondary {
            width: 100%;
            max-width: 320px;
            padding: 15px 22px;
            font-size: 10px;
        }

        .hero-scroll-indicator {
            bottom: 18px;
            font-size: 9px;
        }

        .hero-light-1,
        .hero-light-2 {
            filter: blur(70px);
            opacity: .8;
        }

        .hero-content::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            max-width: 360px;
            height: 120%;
            min-height: 420px;
            background: radial-gradient(ellipse at center, rgba(0,0,0,.30) 0%, transparent 70%);
            pointer-events: none;
            z-index: -1;
        }

        .details-grid,
        .venues-grid,
        .gifts-grid {
            gap: 14px;
        }

        .rsvp-status-group {
            flex-direction: column;
        }
    }
    </style>
</head>
<body>

@if($wedding->envelope_animation)
<div id="envelopeOverlay" class="envelope-overlay">
    <div class="env-particles" id="envParticles"></div>

    <div id="envelopeWrapper" class="envelope-wrapper">
        <span class="env-scene-label">
            {{ $wedding->bride_name }} &amp; {{ $wedding->groom_name }}
            @if($wedding->wedding_date) · {{ $wedding->wedding_date->translatedFormat('d F Y') }} @endif
        </span>

        <div class="envelope-body" id="envelopeBody">
            <div class="env-clip" id="envClip">
                <div class="env-back"></div>
                <div class="env-deco-line top"></div>
                <div class="env-deco-line bottom"></div>
                <div class="envelope-left"></div>
                <div class="envelope-right"></div>
                <div class="envelope-flap-bottom"></div>

                <div class="envelope-card">
                    <span style="font-family:var(--fb);font-size:8px;letter-spacing:4px;text-transform:uppercase;color:var(--gold);font-weight:500">
                        Invitation au mariage
                    </span>
                    <span style="font-family:var(--ft);font-size:22px;font-weight:500;color:var(--dark);letter-spacing:1px">
                        {{ $wedding->getCoupleName() }}
                    </span>
                    <div style="width:32px;height:1px;background:var(--gold)"></div>
                    @if($wedding->wedding_date)
                    <span style="font-family:var(--fb);font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#8c7d70;font-weight:300">
                        {{ $wedding->wedding_date->translatedFormat('d F Y') }}
                    </span>
                    @endif
                </div>

                <div class="envelope-seal"><i class="bi bi-heart-fill"></i></div>
                <div class="envelope-flap"></div>
            </div>
        </div>

        <div class="envelope-hint">
            Cliquez pour ouvrir l'invitation
            <div class="env-hint-arrow"><span></span><span></span><span></span></div>
        </div>
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
    <div class="hero-bg" id="heroBg"></div>
    @endif

    <div class="hero-overlay"></div>
    <div class="hero-light hero-light-1"></div>
    <div class="hero-light hero-light-2"></div>
    <div class="hero-particles" id="heroParticles"></div>

    <div class="hero-grid"></div>
    <div class="hero-circle hero-circle-1"></div>
    <div class="hero-circle hero-circle-2"></div>
    <div class="hero-circle hero-circle-3"></div>

    @if($wedding->floral_decor)
    <div class="floral-corner floral-corner-tl"></div>
    <div class="floral-corner floral-corner-tr"></div>
    @endif

    <div class="hero-content">
        <div class="hero-badge">Une journée inoubliable</div>

        <div class="hero-ornament">
            <span class="ornament-line"></span>
            <span class="ornament-diamond"></span>
            <span class="ornament-line right"></span>
        </div>

        <span class="hero-tagline">Invitation au mariage</span>

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
    <a href="#story" class="hero-scroll-indicator" aria-label="Descendre vers la suite">
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
        <div class="section-header" data-aos="fade-up">
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
            <div class="story-image" data-aos="fade-right">
                <div class="story-image-frame">
                    <img src="{{ Storage::url($wedding->couple_photo) }}" alt="{{ $wedding->getCoupleName() }}">
                </div>
            </div>
            @endif

            <div class="story-text-block" data-aos="fade-left">
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
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">Galerie</span>
            <h2 class="section-title">Nos plus beaux instants</h2>
        </div>

        <div class="gallery-grid">
            @foreach($wedding->galleryItems as $item)
            <div class="gallery-item" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 50 }}">
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
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">Informations</span>
            <h2 class="section-title">Les détails de notre fête</h2>
        </div>

        <div class="details-grid">
            @if($wedding->wedding_date)
            <div class="detail-card" data-aos="fade-up">
                <div class="detail-icon"><i class="bi bi-calendar-heart-fill"></i></div>
                <span class="detail-label">Date</span>
                <div class="detail-value">{{ $wedding->wedding_date->translatedFormat('d F Y') }}</div>
            </div>
            @endif

            @foreach($wedding->venues->take(3) as $venue)
            <div class="detail-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="detail-icon"><i class="bi bi-geo-alt-fill"></i></div>
                <span class="detail-label">{{ ucfirst($venue->type) }}</span>
                <div class="detail-value">{{ $venue->name }}</div>
                @if($venue->city) <div class="detail-desc">{{ $venue->city }}</div> @endif
            </div>
            @endforeach

            @if($wedding->rsvp_deadline)
            <div class="detail-card" data-aos="fade-up">
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
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">Programme</span>
            <h2 class="section-title">Le fil de notre journée</h2>
        </div>

        <div class="program-timeline">
            @foreach($wedding->programItems as $item)
            <div class="program-item">
                <div class="program-dot"></div>
                <div class="program-content">
                    <div class="program-icon">
                        <i class="bi {{ $item->icon ?? 'bi-star-fill' }}"></i>
                    </div>
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
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">Lieux</span>
            <h2 class="section-title">Où nous vous accueillons</h2>
        </div>

        <div class="venues-grid">
            @foreach($wedding->venues as $venue)
            <div class="venue-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
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
    <div class="floral-corner floral-corner-tl" style="opacity:.08"></div>
    <div class="floral-corner floral-corner-br" style="opacity:.08"></div>
    @endif
    <div class="section-inner">
        <div class="section-header" data-aos="fade-up">
            <div class="section-ornament">
                <span class="section-ornament-line"></span>
                <i class="bi bi-stars section-ornament-icon"></i>
                <span class="section-ornament-line"></span>
            </div>
            <span class="section-label">Ambiance &amp; tenues</span>
            <h2 class="section-title" style="color:#fff;font-size:clamp(28px,4vw,42px)">L'univers de notre mariage</h2>
            @if($theme?->mood_description)
            <p class="section-subtitle univers-intro">{{ $theme->mood_description }}</p>
            @else
            <p class="section-subtitle univers-intro">Quelques repères pour s'habiller et se glisser dans notre univers.</p>
            @endif
        </div>

        {{-- 1. Palette en haut --}}
        @if($wedding->colorPalette->count())
        <div class="dresscode-palette-wrap" data-aos="fade-up">
            <h4 style="font-family:var(--ft);color:#fff;margin:0;font-size:20px">Notre palette</h4>
            <div class="palette-swatches">
                @foreach($wedding->colorPalette as $color)
                <div class="palette-swatch">
                    <div class="swatch-color" style="background: {{ $color->hex_color }}"></div>
                    <div class="swatch-name" style="color:rgba(255,255,255,.6)">{{ $color->name }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- 2. Quatre cartes en dessous --}}
        @if($hasUniversContent)
        <div class="dresscode-cards-grid">
            {{-- Carte 1 : Tenue demandée --}}
            <div class="dresscode-card" data-aos="fade-up" data-aos-delay="0">
                <div class="dresscode-label"><i class="bi bi-star-fill"></i> Tenue demandée</div>
                @if($theme->dress_code_style)
                <p style="color:#fff;margin:0;font-size:17px;font-family:var(--ft)">{{ $theme->dress_code_style }}</p>
                @endif
                @if($theme->dress_code_formality)
                <p style="color:rgba(255,255,255,.7);margin:8px 0 0;font-size:14px;line-height:1.5">{{ $theme->dress_code_formality }}</p>
                @endif
                @if(!$theme->dress_code_style && !$theme->dress_code_formality)
                <p style="color:rgba(255,255,255,.45);margin:0;font-size:14px">—</p>
                @endif
            </div>

            {{-- Carte 2 : Description / Ambiance --}}
            <div class="dresscode-card" data-aos="fade-up" data-aos-delay="50">
                <div class="dresscode-label"><i class="bi bi-info-circle-fill"></i> Description</div>
                @if($theme->dress_code_description)
                <p style="color:rgba(255,255,255,.88);margin:0;line-height:1.65;font-size:14px">{{ $theme->dress_code_description }}</p>
                @else
                <p style="color:rgba(255,255,255,.45);margin:0;font-size:14px">—</p>
                @endif
            </div>

            {{-- Carte 3 : Pour lui & pour elle --}}
            <div class="dresscode-card" data-aos="fade-up" data-aos-delay="100">
                <div class="dresscode-label"><i class="bi bi-people-fill"></i> Pour lui & pour elle</div>
                @if($theme->dress_code_men)
                <p style="color:rgba(255,255,255,.88);margin:0;font-size:13px;line-height:1.5"><strong style="color:rgba(255,255,255,.6)">Hommes :</strong> {{ $theme->dress_code_men }}</p>
                @endif
                @if($theme->dress_code_men && $theme->dress_code_women)
                <p style="margin:10px 0 0;color:rgba(255,255,255,.88);font-size:13px;line-height:1.5"><strong style="color:rgba(255,255,255,.6)">Femmes :</strong> {{ $theme->dress_code_women }}</p>
                @elseif($theme->dress_code_women)
                <p style="color:rgba(255,255,255,.88);margin:0;font-size:13px;line-height:1.5">{{ $theme->dress_code_women }}</p>
                @endif
                @if(!$theme->dress_code_men && !$theme->dress_code_women)
                <p style="color:rgba(255,255,255,.45);margin:0;font-size:14px">—</p>
                @endif
            </div>

            {{-- Carte 4 : À éviter & accessoires --}}
            <div class="dresscode-card" data-aos="fade-up" data-aos-delay="150" style="@if($theme->forbidden_colors) border-color:rgba(239,68,68,.3); @endif">
                <div class="dresscode-label" @if($theme->forbidden_colors) style="color:#f87171" @endif><i class="bi bi-x-circle-fill"></i> À éviter & accessoires</div>
                @if($theme->forbidden_colors)
                <p style="color:rgba(255,255,255,.8);margin:0;font-size:13px;line-height:1.5">{{ $theme->forbidden_colors }}</p>
                @endif
                @if($theme->dress_code_accessories)
                <p style="margin:{{ $theme->forbidden_colors ? '10px' : '0' }} 0 0;color:rgba(255,255,255,.8);font-size:13px;line-height:1.5">{{ $theme->dress_code_accessories }}</p>
                @endif
                @if(!$theme->forbidden_colors && !$theme->dress_code_accessories)
                <p style="color:rgba(255,255,255,.45);margin:0;font-size:14px">—</p>
                @endif
            </div>
        </div>
        @endif

        @if($wedding->inspirationItems->count())
        <div class="univers-inspiration-wrap" data-aos="fade-up">
            <h3 class="univers-inspiration-title">Inspirations tenues</h3>
            <p style="text-align:center;color:rgba(255,255,255,.5);font-size:13px;margin:-16px 0 0;font-weight:300">Quelques idées pour vous inspirer</p>
            <div class="univers-inspiration-grid">
                @foreach($wedding->inspirationItems as $insp)
                <div class="univers-inspiration-item">
                    <a href="{{ Storage::url($insp->image) }}" data-gallery="inspiration-gallery" data-type="image">
                        <img src="{{ Storage::url($insp->image) }}" alt="{{ $insp->caption ?? 'Inspiration tenue' }}" loading="lazy">
                        <div class="overlay">
                            @if($insp->caption)
                            <span>{{ $insp->caption }}</span>
                            @else
                            <span>Inspiration</span>
                            @endif
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
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">Liste de souhaits</span>
            <h2 class="section-title">Notre liste de mariage</h2>
            <p class="section-subtitle">Votre présence est le plus beau cadeau. Mais si vous souhaitez nous gâter, voici quelques idées.</p>
        </div>

        @foreach($wedding->giftCategories as $category)
        @if($category->items->count())
        <div class="mb-5" data-aos="fade-up">
            <h3 style="font-family:var(--ft);font-size:20px;margin-bottom:20px;color:var(--wt)">{{ $category->name }}</h3>
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
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">Consignes</span>
            <h2 class="section-title" style="color:white">Pour que ce jour soit parfait</h2>
        </div>

        <div class="rules-grid">
            @foreach(['allowed' => ['icon' => 'bi-check-circle-fill', 'label' => 'Bienvenus'], 'forbidden' => ['icon' => 'bi-x-circle-fill', 'label' => 'À éviter'], 'recommendation' => ['icon' => 'bi-star-fill', 'label' => 'Recommandations']] as $type => $config)
            @php $typeRules = $wedding->rules->where('type', $type)->where('is_active', true); @endphp
            @if($typeRules->count())
            <div class="rules-column {{ $type }}" data-aos="fade-up">
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
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">Hébergement</span>
            <h2 class="section-title">{{ $wedding->accommodation_info ?? 'Infos pratiques' }}</h2>
        </div>
        <div class="story-body" style="max-width:700px;margin:0 auto;font-size:16px;line-height:1.9;text-align:center" data-aos="fade-up">
            {!! nl2br(e($wedding->accommodation_details)) !!}
        </div>
    </div>
</section>
@endif

@if($sectionKeys['rsvp'] ?? true)
<section class="wedding-section rsvp-section" id="rsvp">
    <div class="section-inner">
        <div class="section-header" data-aos="fade-up">
            <span class="section-label" style="color:rgba(255,255,255,.6)">Votre réponse</span>
            <h2 class="section-title" style="color:white">Serez-vous des nôtres ?</h2>
            @if($wedding->rsvp_deadline)
            <p style="color:rgba(255,255,255,.6)">
                <i class="bi bi-clock me-1"></i>Merci de répondre avant le {{ $wedding->rsvp_deadline->translatedFormat('d F Y') }}
            </p>
            @endif
        </div>

        <div class="rsvp-form-card" data-aos="zoom-in">
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
                    <label style="font-weight:600;font-size:13px;margin-bottom:6px;display:block;color:white">Code d'invitation</label>
                    <input type="text" name="invitation_code" class="rsvp-input"
                           placeholder="Votre code (ex: ABCD1234)" required
                           style="text-transform:uppercase;letter-spacing:2px;font-size:16px">
                    <div style="font-size:12px;color:#9e8e82;margin-top:4px">
                        Vous trouverez votre code sur votre invitation papier ou dans l'email reçu.
                    </div>
                </div>

                <div class="mb-4">
                    <label style="font-weight:600;font-size:13px;margin-bottom:10px;display:block;color:white">Votre réponse</label>
                    <div class="rsvp-status-group">
                        <button type="button" class="rsvp-status-btn" data-status="accepted">
                            <i class="bi bi-check-circle-fill" style="color:#10b981"></i>
                            <span>J'accepte</span>
                        </button>
                        <button type="button" class="rsvp-status-btn" data-status="maybe">
                            <i class="bi bi-question-circle-fill" style="color:#f59e0b"></i>
                            <span>À confirmer</span>
                        </button>
                        <button type="button" class="rsvp-status-btn" data-status="declined">
                            <i class="bi bi-x-circle-fill" style="color:#ef4444"></i>
                            <span>Je décline</span>
                        </button>
                    </div>
                    <input type="hidden" name="rsvp_status" id="rsvpStatusInput" value="" required>
                </div>

                <div id="companionsField" style="display:none" class="mb-4">
                    <label style="font-weight:600;font-size:13px;margin-bottom:6px;display:block;color:white">Accompagnants</label>
                    <input type="number" name="companions_count" class="rsvp-input mb-2"
                           placeholder="Nombre d'accompagnants" min="0" max="10">
                    <div id="companionsContainer"></div>
                    <button type="button" id="addCompanion" class="btn-venue btn-venue-maps mt-2">
                        <i class="bi bi-person-plus"></i> Ajouter un accompagnant
                    </button>
                    <input type="hidden" id="maxCompanions" value="5">
                </div>

                <div class="mb-4">
                    <label style="font-weight:600;font-size:13px;margin-bottom:6px;display:block;color:white">Restrictions alimentaires</label>
                    <input type="text" name="dietary_restrictions" class="rsvp-input"
                           placeholder="Végétarien, allergies, sans gluten...">
                </div>

                <div class="mb-4">
                    <label style="font-weight:600;font-size:13px;margin-bottom:6px;display:block;color:white">Un mot pour nous</label>
                    <textarea name="message" class="rsvp-input" rows="3"
                              placeholder="Votre message aux mariés..."></textarea>
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
    <p class="fst-italic" style="font-family:var(--ft);color:rgba(255,255,255,.4);font-size:15px;margin-bottom:20px">
        « {{ $wedding->quote }} »
    </p>
    @endif
    <div style="font-size:12px;color:rgba(255,255,255,.25)">Invitation créée avec amour par Osmose</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/js/glightbox.min.js"></script>
<script src="{{ asset('js/wedding.js') }}"></script>
<script>
AOS.init({ once: true, duration: 800, easing: 'ease-out' });
GLightbox({ selector: '[data-gallery]' });

@if($wedding->envelope_animation)
(function () {
    const container = document.getElementById('envParticles');
    for (let i = 0; i < 40; i++) {
        const p = document.createElement('div');
        p.className = 'env-particle';
        const size = Math.random() * 3 + 1;
        p.style.cssText = `
            width:${size}px; height:${size}px;
            left:${Math.random() * 100}%;
            top:${Math.random() * 100}%;
            --dur:${Math.random() * 8 + 5}s;
            --delay:${Math.random() * 6}s;
            --op:${Math.random() * 0.25 + 0.05};
        `;
        container.appendChild(p);
    }

    const overlay = document.getElementById('envelopeOverlay');
    const body = document.getElementById('envelopeBody');
    const clip = document.getElementById('envClip');
    const seal = body.querySelector('.envelope-seal');
    const flap = body.querySelector('.envelope-flap');
    const card = body.querySelector('.envelope-card');
    const hint = document.querySelector('.envelope-hint');
    let opened = false;

    gsap.set(flap, { rotateX: 0 });
    gsap.set(card, { y: 0 });

    overlay.addEventListener('click', function () {
        if (opened) return;
        opened = true;

        if (hint) gsap.to(hint, { opacity: 0, y: 10, duration: 0.3, ease: 'power2.in' });

        const tl = gsap.timeline();

        tl.to(seal, {
            scale: 0, opacity: 0, duration: 0.5,
            ease: 'back.in(2.5)',
        });

        tl.to(flap, {
            rotateX: -180, duration: 1,
            ease: 'power3.inOut',
        }, '-=0.15');

        tl.call(() => { clip.style.overflow = 'visible'; }, null, '-=0.3');

        tl.to(card, {
            y: '-110%', duration: 1.3,
            ease: 'power3.out',
        }, '-=0.5');

        tl.to(overlay, {
            opacity: 0, duration: 1,
            ease: 'power2.inOut',
            onComplete: () => {
                overlay.style.visibility = 'hidden';
                overlay.style.pointerEvents = 'none';
            }
        }, '-=0.3');
    });
})();
@endif

(function () {
    const heroParticles = document.getElementById('heroParticles');
    const heroBg = document.getElementById('heroBg');

    if (heroParticles) {
        for (let i = 0; i < 28; i++) {
            const p = document.createElement('span');
            p.className = 'hero-particle';
            const size = Math.random() * 4 + 2;
            p.style.width = `${size}px`;
            p.style.height = `${size}px`;
            p.style.left = `${Math.random() * 100}%`;
            p.style.bottom = `${Math.random() * 20 - 10}%`;
            p.style.setProperty('--dur', `${Math.random() * 8 + 8}s`);
            p.style.setProperty('--op', `${Math.random() * 0.18 + 0.05}`);
            p.style.animationDelay = `${Math.random() * 8}s`;
            heroParticles.appendChild(p);
        }
    }

    const heroContent = document.querySelector('.hero-content');
    if (heroContent && typeof gsap !== 'undefined') {
        const heroElements = heroContent.children;
        gsap.set(heroElements, { opacity: 0, y: 40 });

        function animateHero() {
            gsap.to(heroElements, {
                opacity: 1, y: 0, duration: 1,
                stagger: 0.12, ease: 'power3.out', delay: 0.2
            });
        }

        const overlay = document.getElementById('envelopeOverlay');
        if (overlay) {
            const observer = new MutationObserver(() => {
                if (overlay.style.visibility === 'hidden' || overlay.classList.contains('is-dismissed')) {
                    animateHero();
                    observer.disconnect();
                }
            });
            observer.observe(overlay, { attributes: true, attributeFilter: ['style', 'class'] });
        } else {
            animateHero();
        }
    }

    let ticking = false;
    function updateHeroParallax() {
        const scrolled = window.scrollY;
        if (heroBg && scrolled < window.innerHeight) {
            heroBg.style.transform = `scale(1.06) translateY(${scrolled * 0.15}px)`;
        }
        ticking = false;
    }

    window.addEventListener('scroll', function () {
        if (!ticking) {
            window.requestAnimationFrame(updateHeroParallax);
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
        if (diff <= 0) {
            document.getElementById('countdownTimer')?.remove();
            return;
        }
        document.getElementById('cd-days').textContent  = pad(Math.floor(diff / 86400000));
        document.getElementById('cd-hours').textContent = pad(Math.floor((diff % 86400000) / 3600000));
        document.getElementById('cd-mins').textContent  = pad(Math.floor((diff % 3600000) / 60000));
        document.getElementById('cd-secs').textContent  = pad(Math.floor((diff % 60000) / 1000));
    }

    tick();
    setInterval(tick, 1000);
})();
@endif

/* Nav scroll & RSVP buttons handled by wedding.js */
</script>
</body>
</html>