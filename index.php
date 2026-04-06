<?php
require_once __DIR__ . '/config.php';

$pdo = db();
$settings = [];
foreach ($pdo->query("SELECT skey, svalue FROM settings")->fetchAll() as $r) {
    $settings[$r['skey']] = $r['svalue'];
}
$s = fn($k, $d = '') => $settings[$k] ?? $d;

$gallery = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC, id DESC")->fetchAll();

$weddingDate = $s('wedding_date', '2026-06-06');
$bride = $s('bride_name', 'Lisa');
$groom = $s('groom_name', 'Christ');

$programme = $pdo->query("SELECT * FROM programme ORDER BY sort_order ASC, id ASC")->fetchAll();
$lieux = $pdo->query("SELECT * FROM lieux ORDER BY sort_order ASC, id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($bride) ?> &amp; <?= sanitize($groom) ?> — Notre Mariage</title>
    <meta name="description" content="Invitation au mariage de <?= sanitize($bride) ?> et <?= sanitize($groom) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&family=Jost:wght@200;300;400;500&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- LOADER -->
<div id="loader" class="loader">
    <div class="loader-content">
        <div class="loader-names"><?= sanitize($bride) ?> <span>&amp;</span> <?= sanitize($groom) ?></div>
        <div class="loader-bar"><div class="loader-progress"></div></div>
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
        <p class="hero-label"><?= sanitize($s('hero_subtitle', 'Invitation au mariage')) ?></p>
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
            <a href="#programme" class="btn btn-outline"><i class="bi bi-stars"></i> Voir le programme</a>
        </div>
    </div>
    <a href="#histoire" class="scroll-down"><span>Découvrir</span><i class="bi bi-chevron-down"></i></a>
</section>

<!-- HISTOIRE -->
<section class="section" id="histoire">
    <div class="container">
        <div class="section-head" data-anim="fade-up">
            <div class="section-orn"><span class="s-line"></span><i class="bi bi-heart-fill"></i><span class="s-line"></span></div>
            <span class="section-label">Notre histoire</span>
            <h2 class="section-title">Deux cœurs qui ne font qu'un</h2>
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
        <div class="gallery" data-anim="fade-up">
            <?php if (empty($gallery)): ?>
                <?php for ($i = 1; $i <= 4; $i++): ?>
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
            <h2 class="section-title">Les détails de notre fête</h2>
        </div>
        <div class="info-grid" data-anim="fade-up">
            <div class="info-card">
                <i class="bi bi-calendar-heart-fill"></i>
                <span class="info-label">Date</span>
                <strong>06 Juin 2026</strong>
            </div>
            <div class="info-card">
                <i class="bi bi-building"></i>
                <span class="info-label">Mairie</span>
                <strong>Mairie de Chevigny-Saint-Sauveur</strong>
            </div>
            <div class="info-card">
                <i class="bi bi-geo-alt-fill"></i>
                <span class="info-label">Réception</span>
                <strong>Le Lieu Dit — Dijon</strong>
            </div>
            <div class="info-card">
                <i class="bi bi-clock-fill"></i>
                <span class="info-label">Répondez avant le</span>
                <strong>15 Juillet 2025</strong>
            </div>
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
                <?php foreach ($lieux as $lieu): ?>
                <div class="venue-card">
                    <?php if (!empty($lieu['photo'])): ?>
                    <div class="venue-photo">
                        <img src="<?= sanitize(UPLOAD_URL_LIEUX . $lieu['photo']) ?>" alt="<?= sanitize($lieu['name']) ?>" loading="lazy">
                    </div>
                    <?php elseif ($lieu['maps_embed']): ?>
                    <div class="venue-map">
                        <iframe src="<?= sanitize($lieu['maps_embed']) ?>" allowfullscreen loading="lazy"></iframe>
                    </div>
                    <?php endif; ?>
                    <div class="venue-info">
                        <h3><i class="bi <?= sanitize($lieu['icon'] ?: 'bi-geo-alt-fill') ?>"></i> <?= sanitize($lieu['name']) ?></h3>
                        <?php if ($lieu['address']): ?>
                        <p><?= sanitize($lieu['address']) ?></p>
                        <?php endif; ?>
                        <?php if ($lieu['maps_url']): ?>
                        <a href="<?= sanitize($lieu['maps_url']) ?>" target="_blank" class="btn btn-sm">
                            <i class="bi bi-map-fill"></i> Itinéraire
                        </a>
                        <?php endif; ?>
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
            <span class="section-label">Ambiance &amp; Tenues</span>
            <h2 class="section-title">L'univers de notre mariage</h2>
            <p class="section-sub">Mariage romantique et champêtre — lumière douce, nature et élégance.</p>
        </div>
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
            <h2 class="section-title">Pour que ce jour soit parfait</h2>
        </div>
        <div class="rules-list" data-anim="fade-up">
            <div class="rule-item rule-ok"><i class="bi bi-check-circle-fill"></i><span>Enfants bienvenus</span></div>
            <div class="rule-item rule-warn"><i class="bi bi-phone-fill"></i><span>Téléphones interdits pendant la cérémonie</span></div>
            <div class="rule-item rule-info"><i class="bi bi-clock-fill"></i><span>Arrivez 15 minutes avant</span></div>
            <div class="rule-item rule-info"><i class="bi bi-car-front-fill"></i><span>Covoiturage encouragé</span></div>
            <div class="rule-item rule-info"><i class="bi bi-house-heart-fill"></i><span>Hébergement recommandé à proximité</span></div>
        </div>
    </div>
</section>

<!-- HÉBERGEMENT -->
<section class="section section-alt" id="hebergement">
    <div class="container">
        <div class="section-head" data-anim="fade-up">
            <span class="section-label">Hébergement</span>
            <h2 class="section-title">Où dormir</h2>
        </div>
        <div class="hotels" data-anim="fade-up">
            <div class="hotel-card">
                <i class="bi bi-building"></i>
                <h3>Château de la Forêt</h3>
                <p>À 5 minutes du lieu de réception</p>
            </div>
            <div class="hotel-card">
                <i class="bi bi-building"></i>
                <h3>Hôtel des Arts</h3>
                <p>À 10 minutes du lieu de réception</p>
            </div>
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
    <p class="footer-credit">Créé avec amour</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
