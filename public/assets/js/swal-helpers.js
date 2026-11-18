// Sprylo — SweetAlert2 helpers: flash toasts, styled confirm dialogs, shared toast API
(function () {
  const hasSwal = typeof Swal !== 'undefined';

  // "Delete this brand?" -> destructive styling; anything else gets the neutral brand style.
  function describe(message) {
    const danger = /^(delete|remove)\b/i.test(message);
    const verb = (message.match(/^(\w+)/) || [])[1] || 'continue';

    return {
      danger,
      confirmText: danger ? 'Yes, ' + verb.toLowerCase() : 'Yes, continue',
      detail: danger ? 'This action cannot be undone.' : '',
    };
  }

  // If the SweetAlert CDN is blocked, forms marked data-confirm must still ask before submitting.
  if (!hasSwal) {
    document.addEventListener('submit', function (e) {
      const form = e.target;
      if (form instanceof HTMLFormElement && form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
        e.preventDefault();
      }
    }, true);

    window.SpryloToast = function () {};
    window.SpryloAlert = function (icon, title, text) { window.alert(text || title); return Promise.resolve(); };
    return;
  }

  const Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3800,
    timerProgressBar: true,
    didOpen: function (toast) {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
  });

  // icon: success | error | warning | info
  window.SpryloToast = function (icon, title) {
    if (!title) return;
    Toast.fire({ icon: icon, title: title });
  };

  window.SpryloAlert = function (icon, title, text) {
    return Swal.fire({ icon: icon, title: title, text: text || '', confirmButtonText: 'Got it' });
  };

  // Server flash messages (session success/error) arrive as data attributes on #flash-data.
  const flash = document.getElementById('flash-data');
  if (flash) {
    if (flash.dataset.success) window.SpryloToast('success', flash.dataset.success);
    if (flash.dataset.error) window.SpryloToast('error', flash.dataset.error);
    if (flash.dataset.warning) window.SpryloToast('warning', flash.dataset.warning);
  }

  // Any <form data-confirm="Delete this brand?"> gets a styled confirmation before it submits.
  document.addEventListener('submit', function (e) {
    const form = e.target;
    if (!(form instanceof HTMLFormElement) || !form.dataset.confirm) return;

    e.preventDefault();
    const message = form.dataset.confirm;
    const info = describe(message);

    Swal.fire({
      icon: info.danger ? 'warning' : 'question',
      title: message,
      text: info.detail,
      showCancelButton: true,
      confirmButtonText: info.confirmText,
      cancelButtonText: 'Cancel',
      reverseButtons: true,
      focusCancel: info.danger,
      customClass: { confirmButton: info.danger ? 'is-danger' : '' },
    }).then(function (result) {
      if (result.isConfirmed) form.submit(); // form.submit() skips this listener, so no loop.
    });
  }, true);
})();
