// Homepage canvas + reveal: progressive enhancement, no deps.
// ponytail: canvas 2D only; upgrade to WebGL shader when motion budget allows.
(() => {
  'use strict';
  document.documentElement.classList.add('js');
  const canvas = document.querySelector('[data-hero-canvas]');
  if (canvas) {
    const ctx = canvas.getContext('2d');
    const DPR = Math.min(window.devicePixelRatio || 1, 2);
    let w = 0, h = 0, t = 0, raf = 0;
    const dots = Array.from({ length: 70 }, () => ({
      x: Math.random(), y: Math.random(),
      r: 0.6 + Math.random() * 1.8,
      s: 0.2 + Math.random() * 0.8,
      o: 0.15 + Math.random() * 0.5,
    }));
    const size = () => {
      const rect = canvas.getBoundingClientRect();
      w = Math.max(1, rect.width); h = Math.max(1, rect.height);
      canvas.width = w * DPR; canvas.height = h * DPR;
      ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
    };
    const frame = () => {
      t += 0.008;
      ctx.clearRect(0, 0, w, h);
      // drifting glow blobs
      const blobs = [
        [0.22 + 0.06 * Math.sin(t * 1.3), 0.35 + 0.05 * Math.cos(t), 0.32, '16,185,129'],
        [0.78 + 0.05 * Math.cos(t * 0.9), 0.62 + 0.06 * Math.sin(t * 1.1), 0.36, '52,211,153'],
      ];
      for (const [bx, by, br, col] of blobs) {
        const g = ctx.createRadialGradient(w * bx, h * by, 0, w * bx, h * by, Math.max(w, h) * br);
        g.addColorStop(0, `rgba(${col},0.35)`);
        g.addColorStop(1, 'rgba(16,185,129,0)');
        ctx.fillStyle = g;
        ctx.fillRect(0, 0, w, h);
      }
      // starfield drift
      ctx.fillStyle = '#fff';
      for (const d of dots) {
        d.y -= 0.0006 * d.s;
        if (d.y < -0.02) { d.y = 1.02; d.x = Math.random(); }
        ctx.globalAlpha = d.o * (0.7 + 0.3 * Math.sin(t * 3 + d.x * 20));
        ctx.beginPath();
        ctx.arc(d.x * w, d.y * h, d.r, 0, Math.PI * 2);
        ctx.fill();
      }
      ctx.globalAlpha = 1;
      raf = requestAnimationFrame(frame);
    };
    size();
    frame();
    window.addEventListener('resize', size, { passive: true });
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) cancelAnimationFrame(raf);
      else { t += 0.01; frame(); }
    });
  }

  // scroll reveal: one IntersectionObserver, [data-reveal] fades up once
  const items = document.querySelectorAll('[data-reveal]');
  if (items.length && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      for (const e of entries) {
        if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
      }
    }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
    items.forEach((el) => io.observe(el));
  } else {
    items.forEach((el) => el.classList.add('is-visible'));
  }
})();
