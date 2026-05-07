// ===== Aromas da Serra — app.js v2 (premium motion + carousels + lightbox) =====
(function () {
  'use strict';

  const $  = (s, c=document) => c.querySelector(s);
  const $$ = (s, c=document) => Array.from(c.querySelectorAll(s));
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const isFinePointer = window.matchMedia('(hover:hover) and (pointer:fine)').matches;

  const renderIcons = () => { if (window.lucide) window.lucide.createIcons({attrs:{'stroke-width':1.6}}); };
  renderIcons();

  /* ---------- Mobile menu fallback (keeps menu working even if Alpine is late) ---------- */
  const menuRoot = $('[data-mobile-menu-root]');
  if (menuRoot) {
    const openBtn = $('[data-mobile-menu-open]', menuRoot);
    const closeBtn = $('[data-mobile-menu-close]', menuRoot);
    const panel = $('[data-mobile-menu-panel]', menuRoot);
    const setMenu = (open) => {
      if (!panel) return;
      const alpine = window.Alpine && window.Alpine.$data ? window.Alpine.$data(menuRoot) : null;
      if (alpine && Object.prototype.hasOwnProperty.call(alpine, 'open')) alpine.open = open;
      if (open) panel.removeAttribute('x-cloak');
      panel.style.display = open ? 'block' : 'none';
      panel.setAttribute('aria-hidden', open ? 'false' : 'true');
      if (openBtn) openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
      document.body.classList.toggle('mm-open', open);
    };
    if (openBtn) openBtn.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); setMenu(true); });
    if (closeBtn) closeBtn.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); setMenu(false); });
    panel && panel.addEventListener('click', (e) => { if (e.target.closest('.mm-item, .mm-sub a, .mm-foot a')) setMenu(false); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setMenu(false); });
  }

  /* ---------- Lenis smooth scroll ---------- */
  let lenis = null;
  if (window.Lenis && !reduced) {
    lenis = new Lenis({ duration: 1.05, smoothWheel: true, easing: (t)=>1 - Math.pow(1-t, 4) });
    const raf = (t) => { lenis.raf(t); requestAnimationFrame(raf); };
    requestAnimationFrame(raf);
    document.addEventListener('click', e => {
      const a = e.target.closest('a[href^="#"]');
      if (!a) return;
      const id = a.getAttribute('href');
      if (id.length > 1) {
        const target = document.querySelector(id);
        if (target) { e.preventDefault(); lenis.scrollTo(target, { offset: -90 }); }
      }
    });
  }

  /* ---------- Scroll progress bar ---------- */
  const progress = document.createElement('div');
  progress.className = 'scroll-progress';
  document.body.appendChild(progress);
  const onScrollProgress = () => {
    const h = document.documentElement;
    const max = h.scrollHeight - h.clientHeight;
    progress.style.width = Math.min(100, (h.scrollTop / max) * 100) + '%';
  };
  window.addEventListener('scroll', onScrollProgress, { passive: true });

  /* ---------- Reveal observer ---------- */
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('in');
        io.unobserve(e.target);
      }
    });
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0.06 });
  $$('.reveal, .reveal-stagger').forEach(el => io.observe(el));

  /* ---------- Preloader ---------- */
  window.addEventListener('load', () => {
    const pre = $('#preloader');
    if (pre) { pre.style.opacity = '0'; setTimeout(() => pre.remove(), 700); }
    renderIcons();
  });

  /* ---------- Back to top ---------- */
  const top = $('#to-top');
  window.addEventListener('scroll', () => {
    if (!top) return;
    if (window.scrollY > 600) top.classList.add('visible');
    else top.classList.remove('visible');
  }, { passive: true });
  if (top) top.addEventListener('click', () => {
    if (lenis) lenis.scrollTo(0); else window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  /* ---------- Lucide MutationObserver (debounced) ---------- */
  let t;
  new MutationObserver(() => { clearTimeout(t); t = setTimeout(renderIcons, 80); })
    .observe(document.body, { childList: true, subtree: true });

  /* ---------- Magnetic buttons ---------- */
  if (isFinePointer && !reduced) {
    $$('.magnetic').forEach(btn => {
      let raf;
      btn.addEventListener('mousemove', (e) => {
        const r = btn.getBoundingClientRect();
        const x = (e.clientX - r.left - r.width / 2) * 0.18;
        const y = (e.clientY - r.top - r.height / 2) * 0.22;
        cancelAnimationFrame(raf);
        raf = requestAnimationFrame(() => { btn.style.transform = `translate(${x}px, ${y}px)`; });
      });
      btn.addEventListener('mouseleave', () => { btn.style.transform = ''; });
    });
  }

  /* ---------- Custom cursor pill on gallery hover ---------- */
  if (isFinePointer) {
    const pill = document.createElement('div');
    pill.id = 'cursor-pill';
    pill.textContent = 'Ver +';
    document.body.appendChild(pill);
    let pillX = 0, pillY = 0, tx = 0, ty = 0;
    const animate = () => {
      pillX += (tx - pillX) * 0.18;
      pillY += (ty - pillY) * 0.18;
      pill.style.left = pillX + 'px';
      pill.style.top  = pillY + 'px';
      requestAnimationFrame(animate);
    };
    requestAnimationFrame(animate);
    document.addEventListener('mousemove', (e) => { tx = e.clientX; ty = e.clientY; });
    const sel = '.gallery-tile, [data-cursor="ver"]';
    document.addEventListener('mouseover', (e) => { if (e.target.closest(sel)) pill.classList.add('show'); });
    document.addEventListener('mouseout',  (e) => { if (e.target.closest(sel)) pill.classList.remove('show'); });
  }

  /* ---------- Embla carousels ---------- */
  function initEmbla() {
    if (!window.EmblaCarousel) return;
    $$('.embla').forEach(root => {
      if (root.dataset.emblaInited) return;
      root.dataset.emblaInited = '1';
      const viewport = root.querySelector('.embla__viewport');
      const opts = { loop: true, align: 'start', duration: 28 };
      const autoplay = root.dataset.autoplay === 'true' && window.EmblaCarouselAutoplay
        ? [window.EmblaCarouselAutoplay({ delay: 4500, stopOnInteraction: false })] : [];
      const embla = window.EmblaCarousel(viewport, opts, autoplay);
      const prev = root.querySelector('.embla__btn--prev');
      const next = root.querySelector('.embla__btn--next');
      const dotsBox = root.querySelector('.embla__dots');
      if (prev) prev.addEventListener('click', (ev) => { ev.preventDefault(); ev.stopPropagation(); embla.scrollPrev(); });
      if (next) next.addEventListener('click', (ev) => { ev.preventDefault(); ev.stopPropagation(); embla.scrollNext(); });

      const slides = embla.scrollSnapList();
      if (dotsBox) {
        dotsBox.innerHTML = '';
        slides.forEach((_, i) => {
          const d = document.createElement('button');
          d.className = 'embla__dot';
          d.setAttribute('aria-label', 'Ir para slide ' + (i + 1));
          d.addEventListener('click', (ev) => { ev.preventDefault(); ev.stopPropagation(); embla.scrollTo(i); });
          dotsBox.appendChild(d);
        });
      }
      const updateDots = () => {
        if (!dotsBox) return;
        const sel = embla.selectedScrollSnap();
        $$('.embla__dot', dotsBox).forEach((d, i) => d.classList.toggle('embla__dot--selected', i === sel));
      };
      const updateBtns = () => {
        if (prev) prev.toggleAttribute('disabled', !embla.canScrollPrev());
        if (next) next.toggleAttribute('disabled', !embla.canScrollNext());
      };
      embla.on('select', () => { updateDots(); updateBtns(); });
      embla.on('reInit', () => { updateDots(); updateBtns(); });
      updateDots(); updateBtns();
    });
  }
  let emblaTries = 0;
  const emblaTry = () => {
    if (window.EmblaCarousel) initEmbla();
    else if (emblaTries++ < 40) setTimeout(emblaTry, 100);
  };
  emblaTry();

  /* ---------- GLightbox ---------- */
  const lightboxTry = (n=0) => {
    if (window.GLightbox) {
      window.GLightbox({ selector: '.glightbox', loop: true, touchNavigation: true });
    } else if (n < 40) setTimeout(() => lightboxTry(n+1), 100);
  };
  lightboxTry();

  /* ---------- Subtle parallax on page-hero img ---------- */
  if (!reduced) {
    const heroImg = $('.page-hero-img');
    if (heroImg) {
      window.addEventListener('scroll', () => {
        const y = window.scrollY;
        if (y < 800) heroImg.style.transform = `scale(1.06) translateY(${y * 0.18}px)`;
      }, { passive: true });
    }
  }

  /* ---------- Form helpers (admin/contact) ---------- */
  window.aromasToast = function(msg, type='success'){
    const wrap = document.getElementById('toasts') || (() => {
      const w = document.createElement('div'); w.id='toasts';
      w.className='fixed bottom-6 right-6 z-[200] flex flex-col gap-2'; document.body.appendChild(w); return w;
    })();
    const t = document.createElement('div');
    const colors = {success:'#3a5b30', error:'#9a3a3a', info:'#a98955'};
    t.style.cssText = `background:${colors[type]||colors.success};color:#faf6ef;padding:12px 18px;border-radius:8px;box-shadow:0 18px 40px -10px rgba(60,50,40,.4);font-size:13px;letter-spacing:.04em;opacity:0;transform:translateY(8px);transition:all .35s cubic-bezier(.16,1,.3,1);`;
    t.textContent = msg;
    wrap.appendChild(t);
    requestAnimationFrame(() => { t.style.opacity='1'; t.style.transform='none'; });
    setTimeout(() => { t.style.opacity='0'; t.style.transform='translateY(8px)'; setTimeout(()=>t.remove(), 400); }, 3500);
  };
})();
