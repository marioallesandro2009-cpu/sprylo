// Sprylo — checkout payment method toggle
(function () {
  const radios = document.querySelectorAll('input[name="payment_method"]');
  const bankPanel = document.getElementById('payment-bank-transfer');
  const qrisPanel = document.getElementById('payment-qris');
  const proofInput = document.getElementById('co-payment-proof');
  if (!radios.length || !bankPanel || !qrisPanel) return;

  const form = bankPanel.closest('form');
  const optionEls = document.querySelectorAll('.payment-method-option');

  function syncPanels() {
    const selected = document.querySelector('input[name="payment_method"]:checked')?.value;

    optionEls.forEach((el) => el.classList.toggle('is-selected', el.querySelector('input').checked));

    if (selected === 'qris') {
      bankPanel.style.display = 'none';
      qrisPanel.style.display = '';
      if (proofInput) proofInput.required = false;
    } else {
      bankPanel.style.display = '';
      qrisPanel.style.display = 'none';
      if (proofInput) proofInput.required = true;
    }
  }

  radios.forEach((r) => r.addEventListener('change', syncPanels));
  syncPanels();

  form && form.addEventListener('submit', (e) => {
    const selected = document.querySelector('input[name="payment_method"]:checked')?.value;

    if (selected === 'bank_transfer' && proofInput && proofInput.files.length === 0) {
      e.preventDefault();
      proofInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
      proofInput.reportValidity();
    }
  });
})();
