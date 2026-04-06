<?php
require_once __DIR__ . '/config.php';

$pdo = db();
$settings = [];
foreach ($pdo->query("SELECT skey, svalue FROM settings")->fetchAll() as $r) {
    $settings[$r['skey']] = $r['svalue'];
}
$s = fn($k, $d = '') => $settings[$k] ?? $d;

$gallery = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC, id DESC LIMIT 3")->fetchAll();

$weddingDate = $s('wedding_date', '2026-06-06');
$bride = $s('bride_name', 'Lisa');
$groom = $s('groom_name', 'Christ');

$programme = $pdo->query("SELECT * FROM programme ORDER BY sort_order ASC, id ASC")->fetchAll();
$lieux = $pdo->query("SELECT * FROM lieux ORDER BY sort_order ASC, id ASC")->fetchAll();
$hebergements = $pdo->query("SELECT * FROM hebergements ORDER BY sort_order ASC, id ASC")->fetchAll();
$ambiancePhotos = $pdo->query("SELECT * FROM ambiance_photos ORDER BY sort_order ASC, id DESC")->fetchAll();
$ambianceColors = $pdo->query("SELECT * FROM ambiance_colors ORDER BY sort_order ASC, id ASC")->fetchAll();

$themePrimary = $s('theme_primary', '#A8C8E0');
$themeAccent  = $s('theme_accent', '#7B9EC4');
$themeDark    = $s('theme_dark', '#2C3E50');

$brideInitial = mb_strtoupper(mb_substr($bride, 0, 1));
$groomInitial = mb_strtoupper(mb_substr($groom, 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($bride) ?> &amp; <?= sanitize($groom) ?> — Notre Mariage</title>
    <meta name="description" content="Invitation au mariage de <?= sanitize($bride) ?> et <?= sanitize($groom) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&family=Jost:wght@200;300;400;500;600&family=Great+Vibes&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>:root{--sky:<?= sanitize($themePrimary) ?>;--sky-d:<?= sanitize($themeAccent) ?>;--dark:<?= sanitize($themeDark) ?>}</style>
</head>
<body class="intro-locked">

<!-- INTRO MODERNE -->
<div id="introOverlay" class="intro-overlay" role="dialog" aria-modal="true" aria-labelledby="introTitle">
    <div class="intro-bg" aria-hidden="true"></div>
    <div class="intro-grain" aria-hidden="true"></div>
    <div class="intro-content" id="introContent">
        <p class="intro-kicker">Vous êtes cordialement invité<?= mb_strtolower(mb_substr($bride, -1)) === 'a' ? 'e' : '(e)' ?>s</p>
        <div class="intro-monogram" aria-hidden="true"><?= sanitize($brideInitial) ?><span class="intro-monogram-amp">&</span><?= sanitize($groomInitial) ?></div>
        <h1 class="intro-title" id="introTitle"><?= sanitize($bride) ?> <span class="intro-title-amp">&</span> <?= sanitize($groom) ?></h1>
        <p class="intro-date"><i class="bi bi-calendar-heart"></i> <?= date('d F Y', strtotime($weddingDate)) ?></p>
        <button type="button" class="intro-cta" id="introBtn">
            <span>Entrer sur le site</span>
            <i class="bi bi-arrow-down-circle"></i>
        </button>
    </div>
</div>

<!-- NAV -->
<nav class="nav" id="nav">
    <div class="nav-brand"><?= sanitize($bride) ?> &amp; <?= sanitize($groom) ?></div>
    <button class="nav-toggle" id="navToggle" aria-label="Menu"><span></span><span></span><span></span></button>
    <div class="nav-links" id="navLinks">
        <a href="#histoire">Histoire</a>
        <a href="#galerie">Galerie</a>
        <a href="#programme">Programme</a>
        <a href="#lieux">Lieux</a>
        <a href="#rsvp">RSVP</a>
    </div>
</nav>

<!-- HERO -->
<section class="hero" id="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-particles" id="heroParticles"></div>
    <div class="hero-content" id="heroContent">
        <p class="hero-label"><?= sanitize($s('hero_subtitle', 'Nous nous disons oui')) ?></p>
        <div class="hero-ornament"><span class="orn-line"></span><span class="orn-diamond"></span><span class="orn-line right"></span></div>
        <h1 class="hero-names">
            <span class="hero-name"><?= sanitize($bride) ?></span>
            <span class="hero-amp">&amp;</span>
            <span class="hero-name"><?= sanitize($groom) ?></span>
        </h1>
        <div class="hero-date" id="heroDate" data-date="<?= sanitize($weddingDate) ?>">
            <i class="bi bi-calendar-heart"></i>
            <span><?= date('d F Y', strtotime($weddingDate)) ?></span>
        </div>
        <div class="countdown" id="countdown">
            <div class="cd-item"><span class="cd-num" id="cd-d">00</span><span class="cd-lbl">Jours</span></div>
            <div class="cd-item"><span class="cd-num" id="cd-h">00</span><span class="cd-lbl">Heures</span></div>
            <div class="cd-item"><span class="cd-num" id="cd-m">00</span><span class="cd-lbl">Minutes</span></div>
            <div class="cd-item"><span class="cd-num" id="cd-s">00</span><span class="cd-lbl">Secondes</span></div>
        </div>
        <div class="hero-actions">
            <a href="#rsvp" class="btn btn-primary"><i class="bi bi-envelope-heart"></i> Confirmer ma présence</a>
            <a href="#programme" class="btn btn-outline"><i class="bi bi-stars"></i> Découvrir la journée</a>
        </div>
    </div>
    <a href="#histoire" class="scroll-down" id="scrollDown"><span>Explorer</span><i class="bi bi-chevron-down"></i></a>
</section>

<!-- HISTOIRE -->
<section class="section" id="histoire">
    <div class="container">
        <div class="section-head" data-anim="fade-up">
            <div class="section-orn"><span class="s-line"></span><i class="bi bi-heart-fill"></i><span class="s-line"></span></div>
            <span class="section-label">Notre histoire</span>
            <h2 class="section-title">Comment tout a commencé</h2>
        </div>
        <div class="story" data-anim="fade-up">
            <blockquote class="story-quote">« <?= sanitize($s('quote', "L'amour est notre seul vrai trésor")) ?> »</blockquote>
            <p class="story-text"><?= nl2br(sanitize($s('story_text', ''))) ?></p>
        </div>
    </div>
</section>

<!-- GALERIE -->
<section class="section section-alt" id="galerie">
    <div class="container">
        <div class="section-head" data-anim="fade-up">
            <span class="section-label">Galerie</span>
            <h2 class="section-title">Un peu de nous</h2>
        </div>
        <div class="gallery gallery-three" data-anim="fade-up">
            <?php if (empty($gallery)): ?>
                <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="gallery-item">
                    <div class="gallery-placeholder"><i class="bi bi-image"></i><span>Photo <?= $i ?></span></div>
                </div>
                <?php endfor; ?>
            <?php else: ?>
                <?php foreach ($gallery as $img): ?>
                <div class="gallery-item">
                    <img src="<?= sanitize(UPLOAD_URL . $img['filename']) ?>" alt="<?= sanitize($img['caption'] ?: 'Photo mariage') ?>" loading="lazy">
                    <?php if ($img['caption']): ?>
                    <div class="gallery-caption"><?= sanitize($img['caption']) ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- INFORMATIONS -->
<section class="section" id="infos">
    <div class="container">
        <div class="section-head" data-anim="fade-up">
            <span class="section-label">Informations</span>
            <h2 class="section-title">En un coup d'œil</h2>
        </div>
        <div class="info-grid" data-anim="fade-up">
            <div class="info-card">
                <i class="bi bi-calendar-heart-fill"></i>
                <span class="info-label">Date</span>
                <strong><?= date('d F Y', strtotime($weddingDate)) ?></strong>
            </div>
            <div class="info-card">
                <i class="bi bi-clock-fill"></i>
                <span class="info-label">Heure</span>
                <strong><?= sanitize($s('wedding_time', '15:00')) ?></strong>
            </div>
            <?php foreach ($lieux as $i => $lieu): if ($i >= 2) break; ?>
            <div class="info-card">
                <i class="bi <?= sanitize($lieu['icon'] ?: 'bi-geo-alt-fill') ?>"></i>
                <span class="info-label"><?= $i === 0 ? 'Cérémonie' : 'Réception' ?></span>
                <strong><?= sanitize($lieu['name']) ?></strong>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- PROGRAMME -->
<section class="section section-alt" id="programme">
    <div class="container">
        <div class="section-head" data-anim="fade-up">
            <span class="section-label">Programme</span>
            <h2 class="section-title">Le fil de notre journée</h2>
        </div>
        <div class="timeline" data-anim="fade-up">
            <?php if (empty($programme)): ?>
            <p style="text-align:center;color:var(--muted)">Programme à venir.</p>
            <?php else: ?>
                <?php foreach ($programme as $item): ?>
                <div class="tl-item">
                    <div class="tl-dot"></div>
                    <div class="tl-content">
                        <span class="tl-time"><?= sanitize($item['time_label']) ?></span>
                        <h3><?= sanitize($item['title']) ?></h3>
                        <?php if ($item['description']): ?>
                        <p><?= sanitize($item['description']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- LIEUX -->
<section class="section" id="lieux">
    <div class="container">
        <div class="section-head" data-anim="fade-up">
            <span class="section-label">Lieux</span>
            <h2 class="section-title">Où nous vous accueillons</h2>
        </div>
        <div class="venues" data-anim="fade-up">
            <?php if (empty($lieux)): ?>
            <p style="text-align:center;color:var(--muted)">Lieux à venir.</p>
            <?php else: ?>
                <?php $labels = ['Cérémonie', 'Réception', 'Brunch']; ?>
                <?php foreach ($lieux as $idx => $lieu):
                    $addr = $lieu['address'] ?: '';
                    $addrEncoded = urlencode($lieu['name'] . ' ' . $addr);
                    $gmapsLink = $lieu['maps_url'] ?: 'https://www.google.com/maps/search/?api=1&query=' . $addrEncoded;
                    $wazeLink = 'https://waze.com/ul?q=' . $addrEncoded . '&navigate=yes';
                    $appleLink = 'https://maps.apple.com/?q=' . $addrEncoded;
                ?>
                <div class="venue-card">
                    <?php if (!empty($lieu['photo'])): ?>
                    <div class="venue-visual">
                        <img src="<?= sanitize(UPLOAD_URL_LIEUX . $lieu['photo']) ?>" alt="<?= sanitize($lieu['name']) ?>" loading="lazy">
                        <div class="venue-badge"><?= $labels[$idx] ?? 'Lieu' ?></div>
                    </div>
                    <?php else: ?>
                    <div class="venue-visual venue-visual-empty">
                        <i class="bi <?= sanitize($lieu['icon'] ?: 'bi-geo-alt-fill') ?>"></i>
                        <div class="venue-badge"><?= $labels[$idx] ?? 'Lieu' ?></div>
                    </div>
                    <?php endif; ?>
                    <div class="venue-body">
                        <h3><?= sanitize($lieu['name']) ?></h3>
                        <?php if ($addr): ?>
                        <p class="venue-address"><i class="bi bi-geo-alt"></i> <?= sanitize($addr) ?></p>
                        <?php endif; ?>
                        <div class="venue-links">
                            <a href="<?= sanitize($gmapsLink) ?>" target="_blank" class="venue-link venue-link--gmaps">
                                <i class="bi bi-google"></i> Google Maps
                            </a>
                            <a href="<?= sanitize($wazeLink) ?>" target="_blank" class="venue-link venue-link--waze">
                                <i class="bi bi-cursor-fill"></i> Waze
                            </a>
                            <a href="<?= sanitize($appleLink) ?>" target="_blank" class="venue-link venue-link--apple">
                                <i class="bi bi-apple"></i> Plans
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- AMBIANCE & TENUES -->
<section class="section section-dark" id="dresscode">
    <div class="container">
        <div class="section-head" data-anim="fade-up">
            <span class="section-label">Dress code</span>
            <h2 class="section-title">L'univers de notre mariage</h2>
            <p class="section-sub">Une atmosphère romantique et champêtre — pensez lumière douce, nature et raffinement.</p>
        </div>

        <?php if (!empty($ambiancePhotos)): ?>
        <div class="ambiance-photos" data-anim="fade-up">
            <?php foreach ($ambiancePhotos as $ap): ?>
            <div class="ambiance-photo">
                <img src="<?= sanitize(UPLOAD_URL_AMBIANCE . $ap['filename']) ?>" alt="<?= sanitize($ap['caption'] ?: 'Ambiance mariage') ?>" loading="lazy">
                <?php if ($ap['caption']): ?>
                <div class="ambiance-caption"><?= sanitize($ap['caption']) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($ambianceColors)): ?>
        <div class="palette-section" data-anim="fade-up">
            <p class="palette-label">Notre palette de couleurs</p>
            <div class="palette-row">
                <?php foreach ($ambianceColors as $ac): ?>
                <div class="palette-swatch">
                    <div class="palette-color" style="background:<?= sanitize($ac['color_hex']) ?>"></div>
                    <?php if ($ac['color_name']): ?>
                    <span class="palette-name"><?= sanitize($ac['color_name']) ?></span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="dress-grid" data-anim="fade-up">
            <div class="dress-card">
                <div class="dress-icon"><i class="bi bi-suit-heart-fill"></i></div>
                <h3>Hommes</h3>
                <p>Costume clair, lin, tons naturels</p>
            </div>
            <div class="dress-card">
                <div class="dress-icon"><i class="bi bi-flower1"></i></div>
                <h3>Femmes</h3>
                <p>Robe élégante, couleurs pastel ou champêtre</p>
            </div>
            <div class="dress-card dress-avoid">
                <div class="dress-icon"><i class="bi bi-x-circle-fill"></i></div>
                <h3>À éviter</h3>
                <p>Blanc, ivoire, noir strict</p>
            </div>
        </div>
    </div>
</section>

<!-- CONSIGNES -->
<section class="section" id="consignes">
    <div class="container">
        <div class="section-head" data-anim="fade-up">
            <span class="section-label">Consignes</span>
            <h2 class="section-title">Quelques mots avant le grand jour</h2>
        </div>
        <div class="rules-list" data-anim="fade-up">
            <div class="rule-item rule-ok"><i class="bi bi-check-circle-fill"></i><span>Enfants bienvenus</span></div>
            <div class="rule-item rule-warn"><i class="bi bi-phone-fill"></i><span>Téléphones en mode silencieux pendant la cérémonie</span></div>
            <div class="rule-item rule-info"><i class="bi bi-clock-fill"></i><span>Arrivez 15 minutes avant le début</span></div>
            <div class="rule-item rule-info"><i class="bi bi-car-front-fill"></i><span>Covoiturage encouragé</span></div>
            <div class="rule-item rule-info"><i class="bi bi-house-heart-fill"></i><span>Pensez à réserver votre hébergement</span></div>
        </div>
    </div>
</section>

<!-- HÉBERGEMENT -->
<section class="section section-alt" id="hebergement">
    <div class="container">
        <div class="section-head" data-anim="fade-up">
            <span class="section-label">Hébergement</span>
            <h2 class="section-title">Où poser vos valises</h2>
        </div>
        <div class="venues" data-anim="fade-up">
            <?php if (empty($hebergements)): ?>
            <p style="text-align:center;color:var(--muted);grid-column:1/-1">Hébergements à venir.</p>
            <?php else: ?>
                <?php foreach ($hebergements as $hotel):
                    $mapsQuery = urlencode(trim($hotel['name'] . ' France'));
                    $gmapsLink = 'https://www.google.com/maps/search/?api=1&query=' . $mapsQuery;
                    $wazeLink  = 'https://waze.com/ul?q=' . $mapsQuery . '&navigate=yes';
                    $appleLink = 'https://maps.apple.com/?q=' . $mapsQuery;
                ?>
                <div class="venue-card">
                    <?php if (!empty($hotel['photo'])): ?>
                    <div class="venue-visual">
                        <img src="<?= sanitize(UPLOAD_URL_HOTEL . $hotel['photo']) ?>" alt="<?= sanitize($hotel['name']) ?>" loading="lazy">
                        <div class="venue-badge">Hébergement</div>
                    </div>
                    <?php else: ?>
                    <div class="venue-visual venue-visual-empty">
                        <i class="bi bi-building"></i>
                        <div class="venue-badge">Hébergement</div>
                    </div>
                    <?php endif; ?>
                    <div class="venue-body">
                        <h3><?= sanitize($hotel['name']) ?></h3>
                        <?php if ($hotel['distance']): ?>
                        <p class="venue-address"><i class="bi bi-signpost-2"></i> <?= sanitize($hotel['distance']) ?></p>
                        <?php endif; ?>
                        <?php if ($hotel['description']): ?>
                        <p class="venue-address"><i class="bi bi-info-circle"></i> <?= sanitize($hotel['description']) ?></p>
                        <?php endif; ?>
                        <div class="venue-links">
                            <a href="<?= sanitize($gmapsLink) ?>" target="_blank" rel="noopener" class="venue-link venue-link--gmaps">
                                <i class="bi bi-google"></i> Google Maps
                            </a>
                            <a href="<?= sanitize($wazeLink) ?>" target="_blank" rel="noopener" class="venue-link venue-link--waze">
                                <i class="bi bi-cursor-fill"></i> Waze
                            </a>
                            <a href="<?= sanitize($appleLink) ?>" target="_blank" rel="noopener" class="venue-link venue-link--apple">
                                <i class="bi bi-apple"></i> Plans
                            </a>
                            <?php if (!empty($hotel['link'])): ?>
                            <a href="<?= sanitize($hotel['link']) ?>" target="_blank" rel="noopener" class="venue-link venue-link--site">
                                <i class="bi bi-box-arrow-up-right"></i> Site / Réserver
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- RSVP -->
<section class="section section-dark" id="rsvp">
    <div class="container">
        <div class="section-head" data-anim="fade-up">
            <span class="section-label">Votre réponse</span>
            <h2 class="section-title">Serez-vous des nôtres ?</h2>
        </div>
        <form class="rsvp-form" id="rsvpForm" data-anim="fade-up">
            <div id="rsvpAlert" class="alert" style="display:none"></div>
            <div class="form-group">
                <label>Code d'invitation</label>
                <input type="text" name="code" id="rsvpCode" placeholder="Ex: LISA2026" required>
            </div>
            <div class="form-group">
                <label>Votre réponse</label>
                <div class="status-group">
                    <button type="button" class="status-btn" data-val="accepted"><i class="bi bi-check-circle-fill"></i> J'accepte</button>
                    <button type="button" class="status-btn" data-val="maybe"><i class="bi bi-question-circle-fill"></i> À confirmer</button>
                    <button type="button" class="status-btn" data-val="declined"><i class="bi bi-x-circle-fill"></i> Je décline</button>
                </div>
                <input type="hidden" name="status" id="rsvpStatus">
            </div>
            <div class="form-group">
                <label>Nombre d'accompagnants</label>
                <input type="number" name="companions" min="0" max="10" value="0">
            </div>
            <div class="form-group">
                <label>Restrictions alimentaires</label>
                <input type="text" name="dietary" placeholder="Végétarien, allergies...">
            </div>
            <div class="form-group">
                <label>Un mot pour nous</label>
                <textarea name="message" rows="3" placeholder="Votre message..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-full"><i class="bi bi-send-fill"></i> Envoyer ma réponse</button>
        </form>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-names"><?= sanitize($bride) ?> &amp; <?= sanitize($groom) ?></div>
    <div class="footer-date"><?= date('d F Y', strtotime($weddingDate)) ?></div>
    <div class="footer-orn"><span class="f-line"></span><i class="bi bi-heart-fill"></i><span class="f-line"></span></div>
    <p class="footer-quote">« <?= sanitize($s('quote', '')) ?> »</p>
    <p class="footer-credit">Fait avec amour</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
