<?php
/** Modal « faire-part » — $inviteGuest, $inviteGuestName, lieux, etc. fournis par index.php */
?>
<button type="button" class="invite-fab" id="inviteFab" aria-haspopup="dialog" aria-controls="inviteModal">
    <i class="bi bi-postcard-heart" aria-hidden="true"></i>
    <span>Voir l’invitation</span>
</button>

<div class="invite-modal" id="inviteModal" role="dialog" aria-modal="true" aria-labelledby="inviteModalTitle" hidden>
    <div class="invite-modal__backdrop" data-close-invite tabindex="-1"></div>
    <div class="invite-modal__panel">
        <button type="button" class="invite-modal__close" data-close-invite aria-label="Fermer l’invitation">&times;</button>
        <div class="invite-modal__inner">
            <h2 id="inviteModalTitle" class="invite-modal__sr-only">Votre invitation</h2>
            <span class="imc-deco imc-deco--top" aria-hidden="true">❧</span>
            <span class="imc-deco imc-deco--bot" aria-hidden="true">❧</span>

            <div class="imc-mono" aria-hidden="true">
                <?= sanitize($brideInitial) ?><span class="imc-mono-amp">&amp;</span><?= sanitize($groomInitial) ?>
            </div>

            <p class="imc-cordial">Vous êtes cordialement invité(e)s</p>
            <p class="imc-sub">à célébrer notre mariage</p>
            <p class="imc-for"><?= sanitize($inviteGuestName) ?></p>

            <div class="imc-orn">
                <span class="imc-line"></span>
                <i class="bi bi-heart-fill" aria-hidden="true"></i>
                <span class="imc-line"></span>
            </div>

            <div class="imc-names">
                <div class="imc-name-script"><?= sanitize($bride) ?></div>
                <span class="imc-names-et">et</span>
                <div class="imc-name-script"><?= sanitize($groom) ?></div>
            </div>

            <div class="imc-dateline"><i class="bi bi-calendar-heart" aria-hidden="true"></i> <?= htmlspecialchars($introDateFr, ENT_QUOTES, 'UTF-8') ?></div>
            <p class="imc-time">à <?= sanitize($weddingTime) ?></p>

            <div class="imc-details">
                <?php if (!empty($inviteCeremony)): ?>
                <div class="imc-detail">
                    <i class="bi bi-building" aria-hidden="true"></i>
                    <div>
                        <div class="imc-detail-label">Cérémonie</div>
                        <div class="imc-detail-val">
                            <?= sanitize($inviteCeremony['name']) ?>
                            <?php if (!empty($inviteCeremony['address'])): ?>
                            <span class="imc-detail-addr"><?= sanitize($inviteCeremony['address']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($inviteReception)): ?>
                <div class="imc-detail">
                    <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                    <div>
                        <div class="imc-detail-label">Réception</div>
                        <div class="imc-detail-val">
                            <?= sanitize($inviteReception['name']) ?>
                            <?php if (!empty($inviteReception['address'])): ?>
                            <span class="imc-detail-addr"><?= sanitize($inviteReception['address']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="imc-codebox">
                <div class="imc-code-lbl">Votre code d’invitation</div>
                <div class="imc-code"><?= sanitize($inviteGuest['code']) ?></div>
            </div>

            <p class="imc-foot">Avec toute notre affection</p>
        </div>
    </div>
</div>
