// Sprylo — checkout payment method toggle
(function () {
  const radios = document.querySelectorAll('input[name="payment_method"]');
  const bankPanel = document.getElementById('payment-bank-transfer');
  const qrisPanel = document.getElementById('payment-qris');
  if (!radios.length || !bankPanel || !qrisPanel) return;

  const optionEls = document.querySelectorAll('.payment-method-option');

  function syncPanels() {
    const selected = document.querySelector('input[name="payment_method"]:checked')?.value;

    optionEls.forEach((el) => el.classList.toggle('is-selected', el.querySelector('input').checked));

    if (selected === 'qris') {
      bankPanel.style.display = 'none';
      qrisPanel.style.display = '';
    } else {
      bankPanel.style.display = '';
      qrisPanel.style.display = 'none';
    }
  }

  radios.forEach((r) => r.addEventListener('change', syncPanels));
  syncPanels();
})();
