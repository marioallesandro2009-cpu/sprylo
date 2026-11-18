// Sprylo — checkout courier & shipping cost (Biteship)
(function () {
  const listEl = document.getElementById('checkout-rates-list');
  const btn = document.getElementById('checkout-rates-btn');
  if (!listEl || !btn) return;

  const latInput = document.getElementById('co-latitude');
  const lngInput = document.getElementById('co-longitude');
  const courierCodeInput = document.getElementById('co-courier-code');
  const courierServiceInput = document.getElementById('co-courier-service-code');
  const insuranceCheckbox = document.getElementById('co-insurance');
  // Looked up on each use: the order summary is re-rendered when an option changes.
  const summaryShipping = () => document.getElementById('summary-shipping');
  const summaryTotal = () => document.getElementById('summary-total');
  const summaryInsuranceRow = () => document.getElementById('summary-insurance-row');
  const summaryInsurance = () => document.getElementById('summary-insurance');
  const form = listEl.closest('form');

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  const ratesUrl = form.dataset.ratesUrl;

  // Insurance is an order-wide checkbox picked at checkout (not tied to any product
  // variant), so its fee is folded into the same total the courier price feeds.
  let lastShippingFee = 0;
  // After a failed submit the form comes back with the courier the shopper had chosen; keep it
  // selected when the list is re-fetched instead of silently switching to the cheapest one.
  let preferredCourier = { code: courierCodeInput.value, service: courierServiceInput.value };

  function formatRupiah(amount) {
    return 'Rp ' + Math.round(amount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  function currentInsuranceFee() {
    return insuranceCheckbox && insuranceCheckbox.checked ? parseFloat(insuranceCheckbox.dataset.fee || '0') : 0;
  }

  function refreshBtnAvailability() {
    btn.disabled = !(latInput.value && lngInput.value);
  }

  // Re-applies the insurance row and total without touching the shipping line, so it can also
  // run after the summary is swapped out for a fresh server render (variant change).
  function syncInsuranceAndTotal() {
    const base = parseFloat(summaryTotal().dataset.base || '0');
    const insuranceFee = currentInsuranceFee();

    const row = summaryInsuranceRow();
    if (row) {
      row.style.display = insuranceFee > 0 ? 'flex' : 'none';
      if (insuranceFee > 0) summaryInsurance().textContent = formatRupiah(insuranceFee);
    }

    summaryTotal().textContent = formatRupiah(base + lastShippingFee + insuranceFee);
  }

  function updateTotals(shippingFee) {
    lastShippingFee = shippingFee;
    summaryShipping().textContent = shippingFee === 0 ? 'Free' : formatRupiah(shippingFee);
    syncInsuranceAndTotal();
  }

  document.addEventListener('checkout:summary-replaced', syncInsuranceAndTotal);

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

    // Auto-pick the previously chosen courier if it's still offered, otherwise the cheapest,
    // so the total is immediately accurate.
    const cards = listEl.querySelectorAll('.checkout-rate-option');
    let pickIndex = couriers.findIndex((c) => c.courier_code === preferredCourier.code
      && c.courier_service_code === preferredCourier.service);
    if (pickIndex < 0) pickIndex = 0;
    preferredCourier = { code: '', service: '' };

    const firstCard = cards[pickIndex];
    if (firstCard) {
      firstCard.querySelector('input').checked = true;
      selectCourier(couriers[pickIndex], firstCard);
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
    summaryShipping().textContent = 'Select a courier below';

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

  // Toggling insurance doesn't change the courier, so just recompute the total with
  // whichever shipping fee is already selected.
  insuranceCheckbox && insuranceCheckbox.addEventListener('change', () => updateTotals(lastShippingFee));

  // Fetch automatically as soon as a pin is dropped — placing the order no longer
  // requires the shopper to separately press "Check couriers" first.
  document.addEventListener('checkout:pin-set', () => {
    refreshBtnAvailability();
    checkCouriers();
  });

  refreshBtnAvailability();

  if (latInput.value && lngInput.value) {
    checkCouriers();
  } else {
    syncInsuranceAndTotal();
  }
})();
