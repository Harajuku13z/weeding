/* Version avec enveloppe d'ouverture + JS simple */

document.addEventListener('DOMContentLoaded', function () {
  // ─── Animation enveloppe (CSS + petite dose de JS) ─────
  const overlay = document.getElementById('envelopeOverlay');
  const wrapper = document.getElementById('envelopeWrapper');

  if (overlay && wrapper) {
    wrapper.addEventListener('click', () => {
      // Lance l'ouverture (géré par CSS)
      overlay.classList.add('is-open');
      // Puis on masque l'overlay après l'animation
      setTimeout(() => {
        overlay.classList.add('is-hidden');
      }, 1400); // durée cohérente avec les transitions CSS
    });
  }

  // ─── Compte à rebours simple ───────────────────────────
  const weddingDateEl = document.getElementById('weddingDate');
  if (weddingDateEl && weddingDateEl.dataset.date) {
    const weddingDate = new Date(weddingDateEl.dataset.date);

    function updateCountdown() {
      const now = new Date();
      const diff = weddingDate - now;
      if (diff <= 0) {
        const timer = document.getElementById('countdownTimer');
        if (timer) timer.style.display = 'none';
        return;
      }
      const days = Math.floor(diff / (1000 * 60 * 60 * 24));
      const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      const secs = Math.floor((diff % (1000 * 60)) / 1000);

      const d = document.getElementById('cd-days');
      const h = document.getElementById('cd-hours');
      const m = document.getElementById('cd-mins');
      const s = document.getElementById('cd-secs');
      if (d) d.textContent = String(days).padStart(2, '0');
      if (h) h.textContent = String(hours).padStart(2, '0');
      if (m) m.textContent = String(mins).padStart(2, '0');
      if (s) s.textContent = String(secs).padStart(2, '0');
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
  }

  // ─── Barre de navigation simple (fond blanc au scroll) ─
  const nav = document.querySelector('.wedding-nav');
  if (nav) {
    window.addEventListener('scroll', () => {
      nav.classList.toggle('scrolled', window.scrollY > 40);
    }, { passive: true });
  }

  // ─── Gestion du choix RSVP (page publique) ─────────────
  document.querySelectorAll('.rsvp-status-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.rsvp-status-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      const input = document.getElementById('rsvpStatusInput');
      if (input) input.value = this.dataset.status;

      const companionsField = document.getElementById('companionsField');
      if (companionsField) {
        companionsField.style.display = this.dataset.status === 'accepted' ? 'block' : 'none';
      }
    });
  });

  // ─── Ajout accompagnants (page publique) ───────────────
  const addCompanionBtn = document.getElementById('addCompanion');
  if (addCompanionBtn) {
    let companionCount = 0;
    addCompanionBtn.addEventListener('click', () => {
      companionCount++;
      const maxComp = parseInt(document.getElementById('maxCompanions')?.value || '5');
      if (companionCount > maxComp) {
        alert('Nombre maximum d\'accompagnants atteint.');
        return;
      }
      const container = document.getElementById('companionsContainer');
      const div = document.createElement('div');
      div.className = 'companion-row mb-2';
      div.innerHTML = `
        <div class="row g-2">
          <div class="col-5">
            <input type="text" name="companions[${companionCount}][first_name]" class="rsvp-input" placeholder="Prénom *" required>
          </div>
          <div class="col-5">
            <input type="text" name="companions[${companionCount}][last_name]" class="rsvp-input" placeholder="Nom">
          </div>
          <div class="col-2">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.companion-row').remove()">×</button>
          </div>
        </div>
      `;
      container.appendChild(div);
    });
  }

  // ─── Scroll doux sur les liens du menu ─────────────────
  document.querySelectorAll('.nav-links a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
});
