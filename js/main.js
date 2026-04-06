document.addEventListener('DOMContentLoaded', () => {

    const EP = window.__ENDPOINTS || {};
    const rsvpUrl = EP.rsvp || '/api/rsvp.php';
    const succesUrl = EP.succes || '/succes.php';

    /* ─── INTRO MODERNE (plein écran) ───────────────────── */
    const introOverlay = document.getElementById('introOverlay');
    const introBtn     = document.getElementById('introBtn');
    const introContent = document.getElementById('introContent');

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (introOverlay && introContent && typeof gsap !== 'undefined') {
        gsap.set(introContent.children, { opacity: 0, y: 24 });
        gsap.to(introContent.children, {
            opacity: 1, y: 0, duration: prefersReduced ? 0.35 : 0.85,
            stagger: prefersReduced ? 0 : 0.12, ease: 'power3.out', delay: 0.15
        });
    }

    function closeIntro() {
        if (!introOverlay || introOverlay._opened) return;
        introOverlay._opened = true;
        introOverlay.classList.add('is-closing');

        const done = () => {
            introOverlay.style.display = 'none';
            document.body.classList.remove('intro-locked');
            animateHero();
        };

        if (typeof gsap === 'undefined' || prefersReduced) {
            introOverlay.style.opacity = '0';
            done();
            return;
        }

        const tl = gsap.timeline({ onComplete: done });
        tl.to(introContent, {
            opacity: 0, y: -28, scale: 0.97, duration: 0.45, ease: 'power2.in'
        })
        .to(introOverlay.querySelector('.intro-bg'),
            { scale: 1.05, opacity: 0.4, duration: 0.5, ease: 'power2.in' },
            '-=0.25'
        )
        .to(introOverlay, { opacity: 0, duration: 0.55, ease: 'power3.inOut' }, '-=0.35');
    }

    if (introBtn) introBtn.addEventListener('click', closeIntro);

    /* ─── PARTICLES ──────────────────────────────────────── */
    const pc = document.getElementById('heroParticles');
    if (pc) {
        for (let i = 0; i < 20; i++) {
            const p = document.createElement('span');
            p.className = 'hero-particle';
            const s = Math.random() * 3 + 2;
            p.style.width = s + 'px';
            p.style.height = s + 'px';
            p.style.left = Math.random() * 100 + '%';
            p.style.bottom = (Math.random() * 20 - 10) + '%';
            p.style.setProperty('--dur', (Math.random() * 10 + 10) + 's');
            p.style.setProperty('--op', (Math.random() * .1 + .03));
            p.style.animationDelay = (Math.random() * 10) + 's';
            pc.appendChild(p);
        }
    }

    /* ─── HERO GSAP ANIMATION ────────────────────────────── */
    function animateHero() {
        const el = document.getElementById('heroContent');
        if (!el || typeof gsap === 'undefined') return;
        const children = el.children;
        gsap.set(children, { opacity: 0, y: 50 });
        gsap.to(children, {
            opacity: 1, y: 0, duration: 1.2,
            stagger: 0.15, ease: 'power3.out', delay: 0.2
        });

        const sd = document.getElementById('scrollDown');
        if (sd) {
            gsap.set(sd, { opacity: 0 });
            gsap.to(sd, { opacity: 1, duration: .8, delay: 1.5, ease: 'power2.out' });
        }
    }

    /* ─── PARALLAX ───────────────────────────────────────── */
    const heroBg = document.querySelector('.hero-bg');
    let ticking = false;
    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(() => {
                const s = window.scrollY;
                if (heroBg && s < window.innerHeight) {
                    heroBg.style.transform = 'translateY(' + (s * 0.2) + 'px)';
                }
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });

    /* ─── NAV SCROLL ─────────────────────────────────────── */
    const nav = document.getElementById('nav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 60);
    }, { passive: true });

    /* ─── NAV MOBILE TOGGLE ──────────────────────────────── */
    const toggle = document.getElementById('navToggle');
    const links = document.getElementById('navLinks');
    if (toggle && links) {
        toggle.addEventListener('click', () => {
            toggle.classList.toggle('open');
            links.classList.toggle('open');
        });
        links.querySelectorAll('a').forEach(a => {
            a.addEventListener('click', () => {
                toggle.classList.remove('open');
                links.classList.remove('open');
            });
        });
    }

    /* ─── COUNTDOWN ──────────────────────────────────────── */
    const dateEl = document.getElementById('heroDate');
    if (dateEl) {
        const target = new Date(dateEl.dataset.date + 'T15:00:00');
        const pad = n => String(n).padStart(2, '0');
        function tick() {
            const diff = target - Date.now();
            if (diff <= 0) {
                const cd = document.getElementById('countdown');
                if (cd) cd.style.display = 'none';
                return;
            }
            const d = Math.floor(diff / 86400000);
            const h = Math.floor((diff % 86400000) / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            document.getElementById('cd-d').textContent = pad(d);
            document.getElementById('cd-h').textContent = pad(h);
            document.getElementById('cd-m').textContent = pad(m);
            document.getElementById('cd-s').textContent = pad(s);
        }
        tick();
        setInterval(tick, 1000);
    }

    /* ─── GSAP SCROLL ANIMATIONS ─────────────────────────── */
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
        document.querySelectorAll('[data-anim="fade-up"]').forEach(el => {
            gsap.from(el, {
                y: 60, opacity: 0, duration: 1,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: el,
                    start: 'top 85%',
                    once: true,
                }
            });
        });
    }

    /* ─── RSVP FORM ──────────────────────────────────────── */
    const form = document.getElementById('rsvpForm');
    const alertBox = document.getElementById('rsvpAlert');
    const statusInput = document.getElementById('rsvpStatus');

    document.querySelectorAll('.status-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.status-btn').forEach(b => {
                b.className = 'status-btn';
            });
            const val = btn.dataset.val;
            btn.classList.add('active-' + val);
            statusInput.value = val;
        });
    });

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            alertBox.style.display = 'none';

            if (!statusInput.value) {
                showAlert('Veuillez sélectionner votre réponse.', 'err');
                return;
            }

            const data = new FormData(form);
            try {
                const res = await fetch(rsvpUrl, { method: 'POST', body: data });
                const json = await res.json();
                if (json.success) {
                    const params = new URLSearchParams({
                        status: statusInput.value,
                        code: data.get('code') || '',
                        gid: String(json.guest_id || 0)
                    });
                    window.location.href = succesUrl + '?' + params.toString();
                } else {
                    showAlert(json.message, 'err');
                }
            } catch {
                showAlert('Erreur de connexion. Réessayez.', 'err');
            }
        });
    }

    function showAlert(msg, type) {
        alertBox.textContent = msg;
        alertBox.className = 'alert alert-' + type;
        alertBox.style.display = 'block';
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

});
