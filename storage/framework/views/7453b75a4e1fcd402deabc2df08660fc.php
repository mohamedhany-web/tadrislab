<script>
(() => {
  const nav = document.getElementById('lasles-nav');
  const burger = document.getElementById('lasles-burger');
  const mobile = document.getElementById('lasles-mobile');
  const backdrop = document.getElementById('lasles-drawer-backdrop');
  const closeBtn = document.getElementById('lasles-drawer-close');

  // Keep drawer on <body> so sticky/filter ancestors never trap position:fixed
  if (mobile && mobile.parentElement !== document.body) {
    document.body.appendChild(mobile);
  }

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const onScroll = () => nav && nav.classList.toggle('is-scrolled', window.scrollY > 8);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  const setDrawerOpen = (open) => {
    if (!mobile || !burger) return;
    if (open) {
      mobile.classList.add('is-open');
      mobile.setAttribute('aria-hidden', 'false');
      burger.setAttribute('aria-expanded', 'true');
      document.body.classList.add('lasles-drawer-open');
      closeBtn?.focus({ preventScroll: true });
    } else {
      mobile.classList.remove('is-open');
      mobile.setAttribute('aria-hidden', 'true');
      burger.setAttribute('aria-expanded', 'false');
      document.body.classList.remove('lasles-drawer-open');
    }
  };

  burger?.addEventListener('click', () => {
    const open = burger.getAttribute('aria-expanded') === 'true';
    setDrawerOpen(!open);
  });
  backdrop?.addEventListener('click', () => setDrawerOpen(false));
  closeBtn?.addEventListener('click', () => setDrawerOpen(false));
  mobile?.querySelectorAll('a').forEach((a) => a.addEventListener('click', () => setDrawerOpen(false)));
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && burger?.getAttribute('aria-expanded') === 'true') {
      setDrawerOpen(false);
      burger?.focus({ preventScroll: true });
    }
  });

  document.querySelectorAll('[data-plan-more]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('aria-controls');
      const panel = id ? document.getElementById(id) : null;
      if (!panel) return;
      const open = panel.hasAttribute('hidden');
      if (open) {
        panel.removeAttribute('hidden');
        btn.setAttribute('aria-expanded', 'true');
        btn.textContent = btn.dataset.hideLabel || btn.textContent;
      } else {
        panel.setAttribute('hidden', '');
        btn.setAttribute('aria-expanded', 'false');
        btn.textContent = btn.dataset.showLabel || btn.textContent;
      }
    });
  });

  if (reduceMotion) return;

  const isHeroSection = (el) => [...el.classList].some(
    (c) => c === 'lasles-hero' || c.endsWith('-hero')
  );

  const revealNodes = [];
  document.querySelectorAll('main > section, .lasles-stats-wrap').forEach((el, i) => {
    if (isHeroSection(el)) return;
    el.classList.add('lasles-reveal');
    el.style.setProperty('--lasles-delay', `${Math.min(i * 35, 140)}ms`);
    revealNodes.push(el);
  });

  if (!revealNodes.length) return;

  const settle = (el) => el.classList.add('is-in');

  // Settle anything already on / near screen so first paint isn’t stuck mid-transform.
  const markNear = () => {
    const height = window.innerHeight || document.documentElement.clientHeight;
    revealNodes.forEach((el) => {
      if (el.classList.contains('is-in')) return;
      const rect = el.getBoundingClientRect();
      if (rect.top < height * 1.15 && rect.bottom > 0) settle(el);
    });
  };
  markNear();
  requestAnimationFrame(markNear);

  if (!('IntersectionObserver' in window)) {
    revealNodes.forEach(settle);
    return;
  }

  const io = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      settle(entry.target);
      io.unobserve(entry.target);
    });
  }, { rootMargin: '12% 0px 18% 0px', threshold: 0.01 });

  revealNodes.forEach((el) => {
    if (!el.classList.contains('is-in')) io.observe(el);
  });
})();
</script>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/scripts.blade.php ENDPATH**/ ?>