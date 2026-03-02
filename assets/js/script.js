// assets/js/script.js
document.addEventListener('DOMContentLoaded', () => {
  // Mobile navbar toggle
  const btn = document.getElementById('mobileBtn');
  const menu = document.getElementById('mobileMenu');
  if (btn && menu) {
    btn.addEventListener('click', () => {
      menu.classList.toggle('hidden');
    });

    // Close the menu after navigation (mobile UX)
    menu.addEventListener('click', (ev) => {
      const a = ev.target.closest('a');
      if (a) menu.classList.add('hidden');
    });
  }

  // Reveal-on-scroll animations
  const els = Array.from(document.querySelectorAll('.reveal'));
  if (!('IntersectionObserver' in window) || els.length === 0) {
    els.forEach(el => el.classList.add('reveal-show'));
    return;
  }
  const io = new IntersectionObserver((entries) => {
    for (const e of entries) {
      if (e.isIntersecting) {
        e.target.classList.add('reveal-show');
        io.unobserve(e.target);
      }
    }
  }, { threshold: 0.12, rootMargin: '0px 0px -10% 0px' });

  els.forEach(el => io.observe(el));
});
