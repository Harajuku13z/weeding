/* ═══════════════════════════════════════════════════════
   INVITATION MARIAGE — Wedding JS (animations & interactions)
   ═══════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', function () {

  // ─── Compte à rebours ──────────────────────────────────
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
      const days  = Math.floor(diff / 86400000);
      const hours = Math.floor((diff % 86400000) / 3600000);
      const mins  = Math.floor((diff % 3600000) / 60000);
      const secs  = Math.floor((diff % 60000) / 1000);

      const pad = n => String(n).padStart(2, '0');
      const d = document.getElementById('cd-days');
      const h = document.getElementById('cd-hours');
      const m = document.getElementById('cd-mins');
      const s = document.getElementById('cd-secs');
      if (d) d.textContent = pad(days);
      if (h) h.textContent = pad(hours);
      if (m) m.textContent = pad(mins);
      if (s) s.textContent = pad(secs);
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
  }

  // ─── Navigation au scroll ──────────────────────────────
  const nav = document.querySelector('.wedding-nav');
  if (nav) {
    window.addEventListener('scroll', () => {
      nav.classList.toggle('scrolled', window.scrollY > 60);
    }, { passive: true });
  }

  // ─── RSVP boutons de statut ────────────────────────────
  document.querySelectorAll('.rsvp-status-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const status = this.dataset.status;
      document.querySelectorAll('.rsvp-status-btn').forEach(b => {
        b.className = 'rsvp-status-btn';
      });
      this.classList.add('active-' + status);

      const input = document.getElementById('rsvpStatusInput');
      if (input) input.value = status;

      const companionsField = document.getElementById('companionsField');
      if (companionsField) {
        companionsField.style.display = status === 'accepted' ? 'block' : 'none';
      }
    });
  });

  // ─── Ajout accompagnants ───────────────────────────────
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

  // ─── Scroll doux (liens de navigation) ─────────────────
  document.querySelectorAll('.nav-links a[href^="#"], a.hero-cta[href^="#"], a.hero-scroll-indicator[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ─── Menu mobile toggle ────────────────────────────────
  const mobileToggle = document.getElementById('mobileNavToggle');
  const navLinks = document.querySelector('.nav-links');
  if (mobileToggle && navLinks) {
    mobileToggle.addEventListener('click', () => {
      navLinks.classList.toggle('is-open');
      mobileToggle.classList.toggle('is-open');
    });
    navLinks.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        navLinks.classList.remove('is-open');
        mobileToggle.classList.remove('is-open');
      });
    });
  }

});
