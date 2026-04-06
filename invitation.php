<?php
require_once __DIR__ . '/config.php';

$pdo = db();
$settings = [];
foreach ($pdo->query("SELECT skey, svalue FROM settings")->fetchAll() as $r) {
    $settings[$r['skey']] = $r['svalue'];
}
$s = fn($k, $d = '') => $settings[$k] ?? $d;

$code = strtoupper(trim($_GET['code'] ?? ''));
$guest = null;
if ($code) {
    $stmt = $pdo->prepare("SELECT * FROM guests WHERE code = :c LIMIT 1");
    $stmt->execute(['c' => $code]);
    $guest = $stmt->fetch();
}

if (!$guest) {
    header('Location: ' . app_url(''));
    exit;
}

$bride = $s('bride_name', 'Lisa');
$groom = $s('groom_name', 'Christ');
$weddingDate = $s('wedding_date', '2026-06-06');
$weddingTime = $s('wedding_time', '15:00');
$themePrimary = $s('theme_primary', '#A8C8E0');
$themeAccent  = $s('theme_accent', '#7B9EC4');
$themeDark    = $s('theme_dark', '#2C3E50');

$lieux = $pdo->query("SELECT * FROM lieux ORDER BY sort_order ASC, id ASC LIMIT 2")->fetchAll();
$ceremony = $lieux[0] ?? null;
$reception = $lieux[1] ?? null;

$dateFormattedFr = format_date_fr($weddingDate);
$guestName = $guest['name'] ?: 'Cher(e) invité(e)';
$brideInitial = mb_strtoupper(mb_substr($bride, 0, 1));
$groomInitial = mb_strtoupper(mb_substr($groom, 0, 1));
$homeWithSkipIntro = rtrim(app_url(''), '/') . '/?skip_intro=1#rsvp';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Invitation — <?= sanitize($bride) ?> & <?= sanitize($groom) ?></title>
    <meta name="description" content="Vous êtes cordialement invité(e)s au mariage de <?= sanitize($bride) ?> et <?= sanitize($groom) ?> — <?= $dateFormattedFr ?>">
    <meta property="og:title" content="Invitation au mariage de <?= sanitize($bride) ?> & <?= sanitize($groom) ?>">
    <meta property="og:description" content="Le <?= $dateFormattedFr ?> — Vous êtes cordialement invité(e)s">
    <meta property="og:type" content="website">
    <meta name="robots" content="noindex,nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Great+Vibes&family=Jost:wght@400;500;600&family=Playfair+Display:ital,wght@0,500;0,600;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --sky: <?= htmlspecialchars($themePrimary, ENT_QUOTES, 'UTF-8') ?>;
            --sky-d: <?= htmlspecialchars($themeAccent, ENT_QUOTES, 'UTF-8') ?>;
            --dark: <?= htmlspecialchars($themeDark, ENT_QUOTES, 'UTF-8') ?>;
            --paper: #faf7f1;
            --paper-shade: #f0ebe3;
            --ink: #2a2420;
            --ink-soft: #4a433c;
            --gold-line: #b8a88a;
            --gold-soft: #d8cfc0;
            --ft: 'Cormorant Garamond', Georgia, serif;
            --fb: 'Jost', sans-serif;
            --logo: 'Playfair Display', Georgia, serif;
            --script: 'Great Vibes', cursive;
            --ease: cubic-bezier(.4,0,.2,1);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { height: 100%; }
        body {
            font-family: var(--fb);
            font-weight: 400;
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px 20px;
            -webkit-font-smoothing: antialiased;
            background:
                radial-gradient(ellipse 120% 80% at 50% -20%, rgba(255,255,255,.14), transparent 55%),
                radial-gradient(ellipse 90% 60% at 100% 100%, rgba(0,0,0,.2), transparent 45%),
                radial-gradient(ellipse 70% 50% at 0% 80%, rgba(0,0,0,.15), transparent 40%),
                linear-gradient(160deg, #1e2a35 0%, var(--dark) 38%, #243445 100%);
        }

        .inv {
            text-align: center;
            max-width: 440px;
            width: 100%;
            position: relative;
        }

        .inv-card {
            position: relative;
            background: linear-gradient(165deg, var(--paper) 0%, var(--paper-shade) 100%);
            color: var(--ink);
            border: 1px solid var(--gold-line);
            border-radius: 4px;
            padding: 48px 36px 44px;
            box-shadow:
                0 2px 0 rgba(255,255,255,.75) inset,
                0 24px 48px rgba(0,0,0,.28),
                0 0 0 3px var(--gold-soft),
                0 0 0 5px var(--paper-shade);
            animation: cardIn .85s var(--ease) both;
            overflow: hidden;
        }
        .inv-card::before {
            content: '';
            position: absolute;
            inset: 14px;
            border: 1px solid rgba(184, 168, 138, .45);
            border-radius: 2px;
            pointer-events: none;
        }
        .inv-card-deco {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            font-family: var(--script);
            font-size: 28px;
            color: var(--sky-d);
            opacity: .85;
            line-height: 1;
            pointer-events: none;
        }
        .inv-card-deco.bot {
            top: auto;
            bottom: 10px;
            transform: translateX(-50%) rotate(180deg);
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .inv-monogram {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: nowrap;
            white-space: nowrap;
            min-height: 56px;
            padding: 8px 22px;
            margin-bottom: 28px;
            border-radius: 999px;
            background: #fff;
            border: 2px solid var(--sky-d);
            box-shadow: 0 6px 18px rgba(44,62,80,.12);
            font-family: var(--logo);
            font-size: 1.35rem;
            font-weight: 600;
            font-style: italic;
            color: var(--sky-d);
            letter-spacing: .02em;
            animation: monoPop .55s var(--ease) .2s both;
        }
        .inv-mono-amp {
            font-family: var(--ft);
            font-size: 1rem;
            font-weight: 500;
            font-style: italic;
            opacity: .9;
            color: var(--ink-soft);
        }
        @keyframes monoPop {
            from { opacity: 0; transform: scale(.92); }
            to   { opacity: 1; transform: scale(1); }
        }

        .inv-cordial {
            position: relative;
            z-index: 1;
            font-family: var(--fb);
            font-size: clamp(0.8rem, 2.8vw, 0.95rem);
            font-weight: 600;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--ink);
            line-height: 1.55;
            margin-bottom: 6px;
        }
        .inv-sub {
            position: relative;
            z-index: 1;
            font-family: var(--ft);
            font-size: 1.05rem;
            font-style: italic;
            color: var(--ink-soft);
            margin-bottom: 14px;
        }
        .inv-for {
            position: relative;
            z-index: 1;
            font-family: var(--logo);
            font-size: 1.5rem;
            font-weight: 600;
            font-style: italic;
            color: var(--ink);
            margin-bottom: 22px;
            line-height: 1.25;
        }

        .inv-orn {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 20px;
        }
        .inv-line { width: 48px; height: 1px; background: linear-gradient(90deg, transparent, var(--gold-line), transparent); }
        .inv-orn i { color: var(--sky-d); font-size: 9px; }

        .inv-names-block {
            position: relative;
            z-index: 1;
            margin-bottom: 6px;
        }
        .inv-names-script {
            font-family: var(--script);
            font-size: clamp(2.5rem, 9vw, 3.25rem);
            font-weight: 400;
            color: var(--ink);
            line-height: 1.05;
            letter-spacing: .02em;
        }
        .inv-names-amp {
            display: block;
            font-family: var(--ft);
            font-size: 1.35rem;
            font-style: italic;
            color: var(--sky-d);
            margin: 2px 0 4px;
        }

        .inv-date {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            font-family: var(--fb);
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--ink);
            margin-top: 14px;
            margin-bottom: 4px;
        }
        .inv-date i { color: var(--sky-d); font-size: 1rem; }

        .inv-time {
            position: relative;
            z-index: 1;
            font-size: 0.88rem;
            color: var(--ink-soft);
            letter-spacing: .06em;
            margin-bottom: 26px;
        }

        .inv-details {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 28px;
            text-align: left;
        }
        .inv-detail {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 8px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(184, 168, 138, .35);
        }
        .inv-detail i {
            font-size: 1.1rem;
            color: var(--sky-d);
            flex-shrink: 0;
            width: 24px;
            text-align: center;
            margin-top: 2px;
        }
        .inv-detail-label {
            font-size: 0.65rem;
            letter-spacing: .2em;
            text-transform: uppercase;
            font-weight: 600;
            color: var(--ink-soft);
            margin-bottom: 4px;
        }
        .inv-detail-value {
            font-size: 0.92rem;
            color: var(--ink);
            line-height: 1.45;
            font-weight: 500;
        }
        .inv-detail-address {
            display: block;
            font-size: 0.8rem;
            font-weight: 400;
            color: var(--ink-soft);
            margin-top: 4px;
        }

        .inv-code-box {
            position: relative;
            z-index: 1;
            padding: 18px 20px;
            border-radius: 8px;
            background: linear-gradient(135deg, rgba(168,200,224,.12), rgba(123,158,196,.08));
            border: 1px solid rgba(123,158,196,.35);
            margin-bottom: 26px;
        }
        .inv-code-label {
            font-size: 0.65rem;
            letter-spacing: .24em;
            text-transform: uppercase;
            font-weight: 600;
            color: var(--ink-soft);
            margin-bottom: 8px;
        }
        .inv-code {
            font-family: var(--fb);
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: .2em;
            color: var(--sky-d);
        }

        .inv-btn {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: var(--fb);
            font-size: 0.72rem;
            letter-spacing: .2em;
            text-transform: uppercase;
            font-weight: 600;
            color: #faf7f1;
            background: var(--dark);
            padding: 16px 32px;
            border-radius: 999px;
            text-decoration: none;
            border: 2px solid rgba(0,0,0,.12);
            transition: transform .3s var(--ease), box-shadow .3s var(--ease), background .3s;
            box-shadow: 0 6px 24px rgba(0,0,0,.22);
        }
        .inv-btn:hover {
            transform: translateY(-2px);
            background: #243747;
            box-shadow: 0 12px 32px rgba(0,0,0,.28);
        }
        .inv-btn:focus-visible {
            outline: 3px solid var(--sky);
            outline-offset: 3px;
        }

        .inv-footer {
            position: relative;
            z-index: 1;
            margin-top: 28px;
            font-family: var(--ft);
            font-size: 0.95rem;
            font-style: italic;
            color: var(--ink-soft);
        }

        @media (max-width: 480px) {
            .inv-card { padding: 40px 22px 36px; }
            .inv-card::before { inset: 10px; }
            .inv-monogram { font-size: 1.15rem; padding: 6px 18px; }
        }
    </style>
</head>
<body>
    <div class="inv">
        <div class="inv-card">
            <span class="inv-card-deco" aria-hidden="true">❧</span>
            <span class="inv-card-deco bot" aria-hidden="true">❧</span>

            <div class="inv-monogram" aria-label="Initiales"><?= sanitize($brideInitial) ?><span class="inv-mono-amp">&</span><?= sanitize($groomInitial) ?></div>

            <p class="inv-cordial">Vous êtes cordialement invité(e)s</p>
            <p class="inv-sub">à célébrer notre mariage</p>
            <p class="inv-for"><?= sanitize($guestName) ?></p>

            <div class="inv-orn">
                <span class="inv-line"></span>
                <i class="bi bi-heart-fill" aria-hidden="true"></i>
                <span class="inv-line"></span>
            </div>

            <div class="inv-names-block">
                <div class="inv-names-script"><?= sanitize($bride) ?></div>
                <span class="inv-names-amp">et</span>
                <div class="inv-names-script"><?= sanitize($groom) ?></div>
            </div>

            <div class="inv-date"><i class="bi bi-calendar-heart" aria-hidden="true"></i> <?= htmlspecialchars($dateFormattedFr, ENT_QUOTES, 'UTF-8') ?></div>
            <p class="inv-time">à <?= sanitize($weddingTime) ?></p>

            <div class="inv-details">
                <?php if ($ceremony): ?>
                <div class="inv-detail">
                    <i class="bi bi-building" aria-hidden="true"></i>
                    <div>
                        <div class="inv-detail-label">Cérémonie</div>
                        <div class="inv-detail-value">
                            <?= sanitize($ceremony['name']) ?>
                            <?php if (!empty($ceremony['address'])): ?>
                            <span class="inv-detail-address"><?= sanitize($ceremony['address']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($reception): ?>
                <div class="inv-detail">
                    <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                    <div>
                        <div class="inv-detail-label">Réception</div>
                        <div class="inv-detail-value">
                            <?= sanitize($reception['name']) ?>
                            <?php if (!empty($reception['address'])): ?>
                            <span class="inv-detail-address"><?= sanitize($reception['address']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="inv-code-box">
                <div class="inv-code-label">Votre code d'invitation</div>
                <div class="inv-code"><?= sanitize($guest['code']) ?></div>
            </div>

            <a href="<?= htmlspecialchars($homeWithSkipIntro, ENT_QUOTES, 'UTF-8') ?>" class="inv-btn"><i class="bi bi-envelope-heart-fill" aria-hidden="true"></i> Entrer sur le site &amp; répondre</a>

            <p class="inv-footer">Avec toute notre affection</p>
        </div>
    </div>
</body>
</html>
