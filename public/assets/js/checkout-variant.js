// Sprylo — checkout option picker (swap a cart line's variant, then refresh summary & couriers)
(function () {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  const latInput = document.getElementById('co-latitude');
  const lngInput = document.getElementById('co-longitude');

  function showMessage(text, type) {
    const el = document.getElementById('checkout-variant-message');
    if (el) {
      el.textContent = '';
      el.hidden = true;
    }
    if (text && window.SpryloToast) window.SpryloToast(type || "info", text);
  }

  document.addEventListener('change', async (event) => {
    const select = event.target.closest('.checkout-variant-select');
    if (!select) return;

    const summary = document.getElementById('checkout-summary');
    const previous = select.querySelector('option[selected]')?.value;
    select.disabled = true;

    try {
      const res = await fetch(summary.dataset.variantUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ cart_key: select.dataset.cartKey, variant_id: select.value }),
      });
      const data = await res.json();

      if (!res.ok || !data.success) {
        if (previous) select.value = previous;
        select.disabled = false;
        showMessage(data.message || 'Unable to change this option right now.', 'error');
        return;
      }

      summary.outerHTML = data.html;
      showMessage(data.message, 'warning');
      document.dispatchEvent(new CustomEvent('checkout:summary-replaced'));

      // Price and weight may have changed, so re-quote couriers for the pinned address.
      if (latInput?.value && lngInput?.value) {
        document.dispatchEvent(new CustomEvent('checkout:pin-set'));
      }
    } catch (err) {
      if (previous) select.value = previous;
      select.disabled = false;
      showMessage('Unable to change this option right now. Please try again.', 'error');
    }
  });
})();
