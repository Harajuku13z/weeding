<?php
require_once __DIR__ . '/config.php';

$pdo = db();
$settings = [];
foreach ($pdo->query("SELECT skey, svalue FROM settings")->fetchAll() as $r) {
    $settings[$r['skey']] = $r['svalue'];
}
$s = fn($k, $d = '') => $settings[$k] ?? $d;

$bride = $s('bride_name', 'Lisa');
$groom = $s('groom_name', 'Christ');
$weddingDate = $s('wedding_date', '2026-06-06');
$themePrimary = $s('theme_primary', '#A8C8E0');
$themeAccent  = $s('theme_accent', '#7B9EC4');
$themeDark    = $s('theme_dark', '#2C3E50');

$statusRaw = $_GET['status'] ?? 'accepted';
$status = in_array($statusRaw, ['accepted', 'maybe', 'declined'], true) ? $statusRaw : 'accepted';
$guestId = (int) ($_GET['gid'] ?? 0);
$reminderSaved = isset($_GET['reminder']);
$mailWarn = isset($_GET['mail_warn']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Merci — <?= sanitize($bride) ?> & <?= sanitize($groom) ?></title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,400&family=Great+Vibes&family=Jost:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>window.__ENDPOINTS = <?= json_encode([
        'reminder' => app_url('api/reminder.php'),
        'home'     => app_url(''),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;</script>
    <style>
        :root {
            --sky: <?= $themePrimary ?>;
            --sky-d: <?= $themeAccent ?>;
            --dark: <?= $themeDark ?>;
            --ft: 'Cormorant Garamond', Georgia, serif;
            --fb: 'Jost', sans-serif;
            --script: 'Great Vibes', cursive;
            --logo: 'Playfair Display', Georgia, serif;
            --ease: cubic-bezier(.4,0,.2,1);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { height: 100%; }
        body {
            font-family: var(--fb); font-weight: 400; color: #fff;
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--dark) 0%, #1a252f 40%, #162029 100%);
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }

        .success-card {
            text-align: center; max-width: 520px; width: 100%;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 24px; padding: 56px 40px;
            backdrop-filter: blur(20px);
            animation: cardIn .8s var(--ease) both;
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(30px) scale(.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .success-icon {
            width: 80px; height: 80px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px; font-size: 36px;
            animation: iconPop .5s var(--ease) .3s both;
        }
        @keyframes iconPop {
            from { opacity: 0; transform: scale(.5); }
            60%  { transform: scale(1.1); }
            to   { opacity: 1; transform: scale(1); }
        }

        .icon-accepted { background: rgba(106,175,123,.15); color: #6AAF7B; border: 2px solid rgba(106,175,123,.25); }
        .icon-declined { background: rgba(217,107,107,.12); color: #D96B6B; border: 2px solid rgba(217,107,107,.2); }
        .icon-maybe    { background: rgba(212,168,83,.12); color: #D4A853; border: 2px solid rgba(212,168,83,.2); }

        .success-title {
            font-family: var(--ft); font-size: 32px; font-weight: 500;
            margin-bottom: 16px; line-height: 1.2;
        }
        .success-msg {
            font-size: 16px; color: rgba(255,255,255,.55);
            line-height: 1.8; margin-bottom: 32px; font-weight: 400;
        }

        .success-orn {
            display: flex; align-items: center; justify-content: center; gap: 14px;
            margin-bottom: 28px;
        }
        .s-orn-line { width: 40px; height: 1px; background: rgba(168,200,224,.2); }
        .s-orn-heart { color: var(--sky); font-size: 10px; }

        .success-names {
            font-family: var(--logo); font-size: 28px; font-weight: 500; font-style: italic;
            color: var(--sky); margin-bottom: 8px; letter-spacing: .02em;
        }
        .success-date {
            font-size: 11px; letter-spacing: 4px; text-transform: uppercase;
            color: rgba(255,255,255,.35); margin-bottom: 32px; font-weight: 500;
        }

        .reminder-section {
            margin-top: 12px; margin-bottom: 32px;
            padding: 24px; border-radius: 14px;
            background: rgba(212,168,83,.06);
            border: 1px solid rgba(212,168,83,.15);
            animation: fadeIn .6s var(--ease) .5s both;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .reminder-title {
            font-size: 14px; font-weight: 500; color: #D4A853;
            margin-bottom: 14px; letter-spacing: .05em;
        }
        .reminder-options {
            display: flex; gap: 8px; flex-wrap: wrap; justify-content: center;
        }
        .reminder-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 11px 20px; border-radius: 999px;
            font-family: var(--fb); font-size: 13px; font-weight: 500;
            background: rgba(212,168,83,.08);
            border: 1px solid rgba(212,168,83,.2);
            color: #D4A853; cursor: pointer;
            transition: all .3s var(--ease);
        }
        .reminder-btn:hover {
            background: rgba(212,168,83,.18);
            border-color: rgba(212,168,83,.4);
            transform: translateY(-1px);
        }
        .reminder-btn.selected {
            background: rgba(212,168,83,.2);
            border-color: #D4A853;
            box-shadow: 0 4px 16px rgba(212,168,83,.2);
        }
        .reminder-btn i { font-size: 14px; }
        .reminder-saved {
            font-size: 13px; color: #6AAF7B; margin-top: 12px;
            display: none; animation: fadeIn .3s var(--ease) both;
        }
        .reminder-saved.show { display: block; }

        .mail-warn-banner {
            margin-bottom: 24px; padding: 16px 18px; border-radius: 12px;
            font-size: 13px; line-height: 1.55; text-align: left;
            background: rgba(217,107,107,.1);
            border: 1px solid rgba(217,107,107,.25);
            color: rgba(255,255,255,.75);
        }
        .mail-warn-banner strong { color: #e8a0a0; }

        .back-link {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 12px; letter-spacing: .15em; text-transform: uppercase;
            font-weight: 500; color: var(--sky);
            text-decoration: none; padding: 14px 32px;
            border: 1px solid rgba(168,200,224,.2);
            border-radius: 999px;
            transition: all .3s var(--ease);
        }
        .back-link:hover {
            background: rgba(168,200,224,.08);
            border-color: rgba(168,200,224,.35);
        }

        @media (max-width: 480px) {
            .success-card { padding: 40px 24px; }
            .success-title { font-size: 26px; }
            .reminder-options { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="success-card">
        <?php if ($mailWarn): ?>
            <div class="mail-warn-banner">
                <strong>E-mail non reçu ?</strong> La réponse est bien enregistrée, mais l’envoi automatique de la confirmation a échoué (configuration serveur). Vérifiez vos courriers indésirables. Les mariés peuvent renvoyer le message depuis l’admin (invités → confirmation), ou contactez-les directement.
            </div>
        <?php endif; ?>
        <?php if ($status === 'accepted'): ?>
            <div class="success-icon icon-accepted"><i class="bi bi-heart-fill"></i></div>
            <h1 class="success-title">Merci de votre réponse !</h1>
            <p class="success-msg">
                Nous sommes ravis de vous compter parmi nous pour ce jour si spécial.<br>
                Votre présence rendra cette journée encore plus belle et inoubliable.
            </p>

        <?php elseif ($status === 'declined'): ?>
            <div class="success-icon icon-declined"><i class="bi bi-emoji-frown"></i></div>
            <h1 class="success-title">Nous comprenons</h1>
            <p class="success-msg">
                Nous sommes désolés de ne pas pouvoir vous compter parmi nous ce jour-là.<br>
                Vous serez dans nos pensées et nous espérons vous revoir très vite.
            </p>

        <?php elseif ($status === 'maybe'): ?>
            <div class="success-icon icon-maybe"><i class="bi bi-clock-fill"></i></div>
            <h1 class="success-title">Réponse en attente</h1>
            <p class="success-msg">
                Merci d'avoir pris le temps de nous répondre.<br>
                Nous comprenons que tout n'est pas encore certain — prenez votre temps !
            </p>

            <?php if ($reminderSaved): ?>
                <div class="reminder-section">
                    <div class="reminder-title"><i class="bi bi-check-circle-fill"></i> Rappel enregistré !</div>
                    <p style="font-size:13px;color:rgba(255,255,255,.45)">Si vous avez indiqué votre e-mail sur le formulaire, vous recevrez une confirmation et un rappel à la date prévue (selon l’envoi par le serveur du site).</p>
                </div>
            <?php else: ?>
                <div class="reminder-section" id="reminderSection">
                    <div class="reminder-title"><i class="bi bi-bell"></i> Souhaitez-vous un rappel ?</div>
                    <p style="font-size:12px;color:rgba(255,255,255,.42);margin-bottom:14px">Indiquez votre e-mail sur la page du site si ce n’est pas déjà fait : sans e-mail, le rappel automatique ne pourra pas vous joindre par mail.</p>
                    <div class="reminder-options" id="reminderOptions">
                        <button type="button" class="reminder-btn" data-delay="7"><i class="bi bi-calendar-week"></i> Dans 1 semaine</button>
                        <button type="button" class="reminder-btn" data-delay="14"><i class="bi bi-calendar-week"></i> Dans 2 semaines</button>
                        <button type="button" class="reminder-btn" data-delay="30"><i class="bi bi-calendar-month"></i> Dans 1 mois</button>
                    </div>
                    <div class="reminder-saved" id="reminderSaved"><i class="bi bi-check-circle-fill"></i> Rappel enregistré avec succès !</div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="success-icon icon-accepted"><i class="bi bi-heart-fill"></i></div>
            <h1 class="success-title">Merci !</h1>
            <p class="success-msg">Votre réponse a bien été enregistrée.</p>
        <?php endif; ?>

        <div class="success-orn">
            <span class="s-orn-line"></span>
            <i class="bi bi-heart-fill s-orn-heart"></i>
            <span class="s-orn-line"></span>
        </div>

        <div class="success-names"><?= sanitize($bride) ?> & <?= sanitize($groom) ?></div>
        <div class="success-date"><?= date('d F Y', strtotime($weddingDate)) ?></div>

        <a href="<?= htmlspecialchars(app_url(''), ENT_QUOTES, 'UTF-8') ?>" class="back-link"><i class="bi bi-arrow-left"></i> Retour au site</a>
    </div>

    <script>
    (function(){
    const reminderUrl = (window.__ENDPOINTS && window.__ENDPOINTS.reminder) || '/api/reminder.php';
    document.querySelectorAll('.reminder-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const delay = btn.dataset.delay;
            const gid = <?= $guestId ?>;

            document.querySelectorAll('.reminder-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');

            try {
                const fd = new FormData();
                fd.append('guest_id', gid);
                fd.append('delay_days', delay);

                const res = await fetch(reminderUrl, { method: 'POST', body: fd });
                const json = await res.json();

                if (json.success) {
                    document.getElementById('reminderSaved').classList.add('show');
                    setTimeout(() => {
                        document.querySelectorAll('.reminder-btn').forEach(b => {
                            b.style.pointerEvents = 'none';
                            b.style.opacity = b === btn ? '1' : '.4';
                        });
                    }, 300);
                }
            } catch(e) {}
        });
    });
    })();
    </script>
</body>
</html>
