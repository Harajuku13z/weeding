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
    header('Location: /');
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

$dateFormatted = date('d F Y', strtotime($weddingDate));
$guestName = $guest['name'] ?: 'Cher(e) invité(e)';
$brideInitial = mb_strtoupper(mb_substr($bride, 0, 1));
$groomInitial = mb_strtoupper(mb_substr($groom, 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Invitation — <?= sanitize($bride) ?> & <?= sanitize($groom) ?></title>
    <meta name="description" content="Vous êtes invité(e) au mariage de <?= sanitize($bride) ?> et <?= sanitize($groom) ?> le <?= $dateFormatted ?>">
    <meta property="og:title" content="Invitation au mariage de <?= sanitize($bride) ?> & <?= sanitize($groom) ?>">
    <meta property="og:description" content="Le <?= $dateFormatted ?> — Vous êtes cordialement invité(e)">
    <meta property="og:type" content="website">
    <meta name="robots" content="noindex,nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,400&family=Jost:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --sky: <?= $themePrimary ?>;
            --sky-d: <?= $themeAccent ?>;
            --dark: <?= $themeDark ?>;
            --ft: 'Cormorant Garamond', Georgia, serif;
            --fb: 'Jost', sans-serif;
            --logo: 'Playfair Display', Georgia, serif;
            --ease: cubic-bezier(.4,0,.2,1);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { height: 100%; }
        body {
            font-family: var(--fb); font-weight: 400; color: #fff;
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--dark) 0%, #1a252f 40%, #162029 100%);
            padding: 24px;
            -webkit-font-smoothing: antialiased;
        }

        .inv {
            text-align: center; max-width: 480px; width: 100%;
            position: relative;
        }

        .inv-card {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 24px; padding: 56px 40px;
            backdrop-filter: blur(20px);
            animation: cardIn .8s var(--ease) both;
            position: relative;
            overflow: hidden;
        }
        .inv-card::before {
            content: ''; position: absolute; top: -1px; left: 20%; right: 20%;
            height: 2px; background: linear-gradient(90deg, transparent, var(--sky), transparent);
        }
        .inv-card::after {
            content: ''; position: absolute; bottom: -1px; left: 20%; right: 20%;
            height: 2px; background: linear-gradient(90deg, transparent, var(--sky), transparent);
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(30px) scale(.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .inv-monogram {
            width: 72px; height: 72px; border-radius: 50%;
            margin: 0 auto 28px;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.1);
            display: flex; align-items: center; justify-content: center;
            font-family: var(--logo); font-size: 18px; font-weight: 500; font-style: italic;
            color: var(--sky); letter-spacing: .05em;
            animation: monoPop .5s var(--ease) .3s both;
        }
        @keyframes monoPop {
            from { opacity: 0; transform: scale(.6); }
            60%  { transform: scale(1.05); }
            to   { opacity: 1; transform: scale(1); }
        }

        .inv-label {
            font-size: 11px; letter-spacing: .4em; text-transform: uppercase;
            color: var(--sky); font-weight: 500; margin-bottom: 8px;
        }

        .inv-for {
            font-family: var(--ft); font-size: 20px; font-weight: 400; font-style: italic;
            color: rgba(255,255,255,.45); margin-bottom: 28px;
        }

        .inv-orn {
            display: flex; align-items: center; justify-content: center; gap: 14px;
            margin-bottom: 24px;
        }
        .inv-line { width: 40px; height: 1px; background: rgba(168,200,224,.2); }
        .inv-orn i { color: var(--sky); font-size: 8px; }

        .inv-names {
            font-family: var(--logo); font-size: 42px; font-weight: 500; font-style: italic;
            color: #fff; line-height: 1.1; margin-bottom: 8px; letter-spacing: .01em;
        }
        .inv-amp {
            display: block; font-family: var(--ft); font-size: 20px;
            color: var(--sky); line-height: 1.8; font-style: italic; font-weight: 300;
        }

        .inv-date {
            display: inline-flex; align-items: center; gap: 10px;
            font-size: 14px; letter-spacing: .2em; text-transform: uppercase;
            color: rgba(255,255,255,.7); font-weight: 400;
            margin: 20px 0 8px;
        }
        .inv-date i { color: var(--sky); font-size: 14px; }

        .inv-time {
            font-size: 13px; color: rgba(255,255,255,.4); letter-spacing: .1em;
            margin-bottom: 28px;
        }

        .inv-details {
            display: flex; flex-direction: column; gap: 14px;
            margin-bottom: 32px;
        }
        .inv-detail {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 20px; border-radius: 12px;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.06);
        }
        .inv-detail i {
            font-size: 18px; color: var(--sky); flex-shrink: 0; width: 24px; text-align: center;
        }
        .inv-detail-text { text-align: left; }
        .inv-detail-label {
            font-size: 10px; letter-spacing: .2em; text-transform: uppercase;
            color: rgba(255,255,255,.35); font-weight: 500; margin-bottom: 2px;
        }
        .inv-detail-value {
            font-size: 14px; color: rgba(255,255,255,.75); font-weight: 400;
        }

        .inv-code-box {
            padding: 16px 20px; border-radius: 12px;
            background: rgba(168,200,224,.06);
            border: 1px solid rgba(168,200,224,.12);
            margin-bottom: 28px;
        }
        .inv-code-label {
            font-size: 10px; letter-spacing: .3em; text-transform: uppercase;
            color: rgba(255,255,255,.35); font-weight: 500; margin-bottom: 6px;
        }
        .inv-code {
            font-size: 22px; font-weight: 600; letter-spacing: .15em;
            color: var(--sky);
        }

        .inv-btn {
            display: inline-flex; align-items: center; gap: 8px;
            font-family: var(--fb); font-size: 12px; letter-spacing: .15em; text-transform: uppercase;
            font-weight: 500; color: var(--dark);
            background: var(--sky); padding: 16px 36px;
            border-radius: 999px; text-decoration: none;
            transition: all .4s var(--ease);
            box-shadow: 0 8px 28px rgba(168,200,224,.25);
        }
        .inv-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 40px rgba(168,200,224,.35);
            filter: brightness(1.08);
        }

        .inv-footer {
            margin-top: 32px; font-size: 12px;
            color: rgba(255,255,255,.2); font-style: italic;
        }

        @media (max-width: 480px) {
            .inv-card { padding: 40px 24px; }
            .inv-names { font-size: 32px; }
        }
    </style>
</head>
<body>
    <div class="inv">
        <div class="inv-card">
            <div class="inv-monogram"><?= sanitize($brideInitial) ?>&<?= sanitize($groomInitial) ?></div>

            <div class="inv-label">Invitation au mariage</div>
            <div class="inv-for"><?= sanitize($guestName) ?></div>

            <div class="inv-orn">
                <span class="inv-line"></span>
                <i class="bi bi-diamond-fill"></i>
                <span class="inv-line"></span>
            </div>

            <div class="inv-names">
                <?= sanitize($bride) ?>
                <span class="inv-amp">&</span>
                <?= sanitize($groom) ?>
            </div>

            <div class="inv-date"><i class="bi bi-calendar-heart"></i> <?= $dateFormatted ?></div>
            <div class="inv-time">à <?= sanitize($weddingTime) ?></div>

            <div class="inv-details">
                <?php if ($ceremony): ?>
                <div class="inv-detail">
                    <i class="bi bi-building"></i>
                    <div class="inv-detail-text">
                        <div class="inv-detail-label">Cérémonie</div>
                        <div class="inv-detail-value"><?= sanitize($ceremony['name']) ?><br><span style="font-size:12px;color:rgba(255,255,255,.4)"><?= sanitize($ceremony['address']) ?></span></div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($reception): ?>
                <div class="inv-detail">
                    <i class="bi bi-geo-alt-fill"></i>
                    <div class="inv-detail-text">
                        <div class="inv-detail-label">Réception</div>
                        <div class="inv-detail-value"><?= sanitize($reception['name']) ?><br><span style="font-size:12px;color:rgba(255,255,255,.4)"><?= sanitize($reception['address']) ?></span></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="inv-code-box">
                <div class="inv-code-label">Votre code d'invitation</div>
                <div class="inv-code"><?= sanitize($guest['code']) ?></div>
            </div>

            <a href="/#rsvp" class="inv-btn"><i class="bi bi-envelope-heart-fill"></i> Confirmer ma présence</a>

            <div class="inv-footer">Avec tout notre amour</div>
        </div>
    </div>
</body>
</html>
