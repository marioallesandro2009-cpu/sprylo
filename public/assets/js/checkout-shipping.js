// Sprylo — checkout courier & shipping cost (Biteship)
(function () {
  const listEl = document.getElementById('checkout-rates-list');
  const btn = document.getElementById('checkout-rates-btn');
  if (!listEl || !btn) return;

  const latInput = document.getElementById('co-latitude');
  const lngInput = document.getElementById('co-longitude');
  const courierCodeInput = document.getElementById('co-courier-code');
  const courierServiceInput = document.getElementById('co-courier-service-code');
  const summaryShipping = document.getElementById('summary-shipping');
  const summaryTotal = document.getElementById('summary-total');
  const form = listEl.closest('form');

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  const ratesUrl = form.dataset.ratesUrl;

  function formatRupiah(amount) {
    return 'Rp ' + Math.round(amount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  function refreshBtnAvailability() {
    btn.disabled = !(latInput.value && lngInput.value);
  }

  function updateTotals(shippingFee) {
    const base = parseFloat(summaryTotal.dataset.base || '0');
    summaryShipping.textContent = shippingFee === 0 ? 'Free' : formatRupiah(shippingFee);
    summaryTotal.textContent = formatRupiah(base + shippingFee);
  }

  function selectCourier(option, cardEl) {
    courierCodeInput.value = option.courier_code;
    courierServiceInput.value = option.courier_service_code;

    listEl.querySelectorAll('.checkout-rate-option').forEach((el) => el.classList.remove('is-selected'));
    cardEl.classList.add('is-selected');

    updateTotals(option.price);
  }

  function renderCouriers(couriers) {
    listEl.innerHTML = '';
    listEl.dataset.state = 'loaded';

    couriers.forEach((option) => {
      const card = document.createElement('label');
      card.className = 'checkout-rate-option';

      const durationText = option.duration ? ' · ' + option.duration : '';
      card.innerHTML =
        '<input type="radio" name="courier_choice" />' +
        '<span class="checkout-rate-info">' +
        '<strong>' + option.courier_name + ' — ' + option.service_name + '</strong>' +
        '<span class="checkout-rate-meta">' + formatRupiah(option.price) + durationText + '</span>' +
        '</span>';

      const radio = card.querySelector('input');
      radio.addEventListener('change', () => selectCourier(option, card));
      card.addEventListener('click', () => { radio.checked = true; selectCourier(option, card); });

      listEl.appendChild(card);
    });

    // Auto-pick the cheapest option so the total is immediately accurate.
    const firstCard = listEl.querySelector('.checkout-rate-option');
    if (firstCard) {
      firstCard.querySelector('input').checked = true;
      selectCourier(couriers[0], firstCard);
    }
  }

  function showMessage(text, state) {
    listEl.dataset.state = state;
    listEl.innerHTML = '<p class="checkout-rates-' + state + '">' + text + '</p>';
  }

  async function checkCouriers() {
    if (!latInput.value || !lngInput.value) return;

    btn.disabled = true;
    btn.textContent = 'Checking…';
    showMessage('Checking available couriers…', 'loading');
    courierCodeInput.value = '';
    courierServiceInput.value = '';
    updateTotals(0);
    summaryShipping.textContent = 'Select a courier below';

    try {
      const res = await fetch(ratesUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ latitude: latInput.value, longitude: lngInput.value }),
      });

      const data = await res.json();

      if (!res.ok || !data.success) {
        showMessage(data.message || 'No courier can deliver to this address. Please change your address.', 'empty');
      } else {
        renderCouriers(data.couriers);
      }
    } catch (err) {
      showMessage('Unable to check shipping rates right now. Please try again.', 'empty');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Check couriers →';
      refreshBtnAvailability();
    }
  }

  btn.addEventListener('click', checkCouriers);

  // Fetch automatically as soon as a pin is dropped — placing the order no longer
  // requires the shopper to separately press "Check couriers" first.
  document.addEventListener('checkout:pin-set', () => {
    refreshBtnAvailability();
    checkCouriers();
  });

  refreshBtnAvailability();
})();
