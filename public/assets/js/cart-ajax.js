// Sprylo — cart qty update & remove without a full page reload
(function () {
  const content = document.getElementById('cart-content');
  if (!content) return;

  function updateCartBadge(count) {
    const cartIcon = document.querySelector('.icon-btn--cart');
    if (!cartIcon) return;

    let badge = cartIcon.querySelector('.count');

    if (count > 0) {
      if (!badge) {
        badge = document.createElement('span');
        badge.className = 'count';
        cartIcon.appendChild(badge);
      }
      badge.textContent = count;
    } else if (badge) {
      badge.remove();
    }
  }

  // Quantity changes save themselves shortly after the last tap, so there's no "Update cart" button to hunt for.
  let autoUpdateTimer = null;

  function scheduleAutoUpdate() {
    const form = document.getElementById('cart-update-form');
    if (!form) return;

    clearTimeout(autoUpdateTimer);
    autoUpdateTimer = setTimeout(() => form.requestSubmit(), 600);
  }

  document.addEventListener('click', (e) => {
    if (e.target.closest('#cart-update-form .qty [data-act]')) scheduleAutoUpdate();
  });

  document.addEventListener('change', (e) => {
    if (e.target.matches('#cart-update-form .qty input')) scheduleAutoUpdate();
  });

  document.addEventListener('submit', (e) => {
    const form = e.target;
    if (form.id !== 'cart-update-form' && !form.classList.contains('cart-remove-form')) return;

    e.preventDefault();

    fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      headers: { Accept: 'application/json' },
    })
      .then((res) => (res.ok ? res.json() : Promise.reject()))
      .then((data) => {
        if (!data || !data.success) return Promise.reject();
        content.innerHTML = data.html;
        updateCartBadge(data.cartCount);
        if (window.SpryloToast) {
          if (data.warning) {
            window.SpryloToast('warning', data.warning);
          } else {
            window.SpryloToast('success', form.classList.contains('cart-remove-form') ? 'Item removed from your cart' : 'Cart updated');
          }
        }
      })
      .catch(() => form.submit()); // AJAX failed — fall back to a normal submit/reload.
  });
})();
