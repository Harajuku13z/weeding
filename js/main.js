document.addEventListener('DOMContentLoaded', () => {

    const EP = window.__ENDPOINTS || {};
    const rsvpUrl = EP.rsvp || '/api/rsvp.php';
    const succesUrl = EP.succes || '/succes.php';

    /* ─── INTRO MODERNE (plein écran) ───────────────────── */
    const introOverlay = document.getElementById('introOverlay');
    const introBtn     = document.getElementById('introBtn');
    const introContent = document.getElementById('introContent');

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (introOverlay && introContent) {
        if (typeof gsap !== 'undefined' && !prefersReduced) {
            gsap.set(introContent, { opacity: 0, y: 20 });
            gsap.to(introContent, {
                opacity: 1,
                y: 0,
                duration: 1.05,
                ease: 'sine.out',
                delay: 0.08,
            });
        } else if (typeof gsap !== 'undefined' && prefersReduced) {
            gsap.set(introContent, { opacity: 1 });
        } else {
            introOverlay.classList.add('intro-fallback');
        }
    }

    function closeIntro() {
        if (!introOverlay || introOverlay._opened) return;
        introOverlay._opened = true;
        introOverlay.classList.add('is-closing');

        const done = () => {
            if (introContent && typeof gsap !== 'undefined') {
                gsap.set(introContent, { clearProps: 'opacity,transform' });
            }
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
            opacity: 0,
            y: -14,
            duration: 0.42,
            ease: 'power2.in',
        }).to(
            introOverlay,
            { opacity: 0, duration: 0.52, ease: 'power2.inOut' },
            '-=0.2'
        );
    }

    if (introBtn) introBtn.addEventListener('click', closeIntro);

    /* ─── Code invitation dans le RSVP (?invite=CODE) ───── */
    const rsvpCodeInput = document.getElementById('rsvpCode');
    if (rsvpCodeInput && window.__INVITE_CODE__) {
        rsvpCodeInput.value = window.__INVITE_CODE__;
    }

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
        const root = document.getElementById('heroContent');
        if (!root || typeof gsap === 'undefined') return;

        const sd = document.getElementById('scrollDown');
        if (prefersReduced) {
            if (sd) gsap.set(sd, { opacity: 1, clearProps: 'opacity' });
            return;
        }

        const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
        tl.from('.hero-label', { opacity: 0, y: 38, duration: 0.88 }, 0)
            .from('.hero-ornament', { opacity: 0, scale: 0.86, duration: 0.72 }, 0.1)
            .from('.hero-names .hero-name', {
                opacity: 0,
                y: 54,
                duration: 1.05,
                stagger: 0.22,
                ease: 'power4.out',
            }, 0.2)
            .from(
                '.hero-names .hero-amp',
                { opacity: 0, scale: 0.35, y: 22, duration: 0.92, ease: 'back.out(1.75)' },
                0.48
            )
            .from('.hero-date', { opacity: 0, y: 30, duration: 0.78 }, 0.52)
            .from(
                '.countdown .cd-item',
                { opacity: 0, y: 34, duration: 0.72, stagger: 0.1, ease: 'power3.out' },
                0.62
            )
            .from('.hero-actions', { opacity: 0, y: 28, duration: 0.88 }, 0.78);

        if (sd) {
            tl.fromTo(
                sd,
                { opacity: 0, y: 12 },
                { opacity: 1, y: 0, duration: 0.8, ease: 'power2.out' },
                '-=0.45'
            );
        }
    }

    /* Pas d’écran intro (ex. lien invitation ?skip_intro=1) → hero animé au chargement */
    if (!introOverlay) {
        animateHero();
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
        const reduceScroll = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        document.querySelectorAll('[data-anim="fade-up"]').forEach((el) => {
            const staggerKids = el.dataset.staggerChildren === 'true';
            if (staggerKids) {
                const children = Array.from(el.children).filter((c) => c.nodeType === 1);
                if (!children.length) return;
                // Cartes tenues : pas de fondu (opacity 0 restait si ScrollTrigger tardif → texte « invisible » sur mobile).
                const skipOpacityFade = el.classList.contains('dress-grid');
                gsap.from(children, {
                    y: reduceScroll ? 0 : 48,
                    opacity: skipOpacityFade || reduceScroll ? 1 : 0,
                    duration: reduceScroll ? 0.01 : 1.02,
                    stagger: reduceScroll ? 0 : parseFloat(el.dataset.stagger || '0.1'),
                    ease: reduceScroll ? 'none' : 'power4.out',
                    scrollTrigger: {
                        trigger: el,
                        start: skipOpacityFade ? 'top 92%' : 'top 88%',
                        once: true,
                    },
                });
            } else {
                gsap.from(el, {
                    y: reduceScroll ? 0 : 58,
                    opacity: 0,
                    duration: reduceScroll ? 0.01 : 1.1,
                    ease: reduceScroll ? 'none' : 'power4.out',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 85%',
                        once: true,
                    },
                });
            }
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
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;
            try {
                const res = await fetch(rsvpUrl, { method: 'POST', body: data });
                let json = null;
                try {
                    json = await res.json();
                } catch (_) {
                    json = null;
                }

                const msg = json && json.message ? String(json.message) : '';
                const ok =
                    res.ok &&
                    json &&
                    (
                        json.success === true ||
                        json.success === 1 ||
                        json.success === 'true' ||
                        (Number(json.guest_id) > 0 && /a bien été enregistrée/i.test(msg))
                    );

                if (ok) {
                    const params = new URLSearchParams({
                        status: statusInput.value,
                        code: (data.get('code') || '').toString().trim(),
                        gid: String(json.guest_id != null ? json.guest_id : 0),
                    });
                    const hasEmail = (data.get('email') || '').toString().trim() !== '';
                    if (hasEmail && json.mail_tried && json.mail_sent === false) {
                        params.set('mail_warn', '1');
                    }
                    const dest = new URL(succesUrl, window.location.origin);
                    dest.search = params.toString();
                    window.location.replace(dest.href);
                    return;
                }

                if (submitBtn) submitBtn.disabled = false;
                showAlert(json && json.message ? json.message : 'Réponse impossible. Réessayez.', 'err');
            } catch {
                if (submitBtn) submitBtn.disabled = false;
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
