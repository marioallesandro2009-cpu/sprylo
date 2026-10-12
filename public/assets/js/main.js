// Sprylo — light interaction (no framework)
(function () {
  // Mobile drawer
  const drawer = document.getElementById('drawer');
  const toggle = document.querySelector('.nav-toggle');
  const close  = document.querySelector('.drawer-close');

  function setDrawer(open) {
    if (!drawer || !toggle) return;
    drawer.classList.toggle('is-open', open);
    drawer.setAttribute('aria-hidden', String(!open));
    toggle.setAttribute('aria-expanded', String(open));
    document.body.style.overflow = open ? 'hidden' : '';
  }
  toggle && toggle.addEventListener('click', () => setDrawer(!drawer.classList.contains('is-open')));
  close  && close.addEventListener('click', () => setDrawer(false));
  drawer && drawer.querySelectorAll('a').forEach((a) => a.addEventListener('click', () => setDrawer(false)));
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && drawer && drawer.classList.contains('is-open')) setDrawer(false);
  });

  // Shop filters toggle (collapsed by default on tablet/mobile)
  const filtersToggle = document.querySelector('.filters-toggle');
  const filtersForm = document.querySelector('.filters');
  if (filtersToggle && filtersForm) {
    filtersToggle.addEventListener('click', () => {
      const isOpen = filtersForm.classList.toggle('is-open');
      filtersToggle.setAttribute('aria-expanded', String(isOpen));
    });
  }

  // Category tabs (just visual switch)
  document.querySelectorAll('.tabs').forEach((tablist) => {
    tablist.querySelectorAll('.tab').forEach((tab) => {
      tab.addEventListener('click', () => {
        tablist.querySelectorAll('.tab').forEach((t) => {
          t.classList.remove('is-active');
          t.setAttribute('aria-selected', 'false');
        });
        tab.classList.add('is-active');
        tab.setAttribute('aria-selected', 'true');
      });
    });
  });

  // Product gallery thumbnails
  const thumbs = document.querySelectorAll('.gallery-thumbs button');
  const mainImg = document.querySelector('.gallery-main img');
  thumbs.forEach((t) => {
    t.addEventListener('click', () => {
      thumbs.forEach((x) => x.classList.remove('is-active'));
      t.classList.add('is-active');
      const src = t.querySelector('img')?.src;
      if (src && mainImg) mainImg.src = src.replace(/w=\d+/, 'w=900');
    });
  });

  // Qty +/- buttons — delegated so it still works after AJAX-swapped cart markup
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.qty [data-act]');
    if (!btn) return;
    const input = btn.closest('.qty').querySelector('input');
    if (!input) return;
    input.value = btn.dataset.act === '-'
      ? Math.max(1, parseInt(input.value || 1) - 1)
      : parseInt(input.value || 1) + 1;
  });

  // Color swatches & option pills (visual switch)
  document.querySelectorAll('.color-swatches, .option-pills').forEach((group) => {
    group.querySelectorAll('button').forEach((b) => {
      b.addEventListener('click', () => {
        group.querySelectorAll('button').forEach((x) => x.classList.remove('is-active'));
        b.classList.add('is-active');
      });
    });
  });
})();
