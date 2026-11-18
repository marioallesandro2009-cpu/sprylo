// solis — checkout delivery schedule: after a date is picked, offer the time windows that still have room.
(function () {
  var dateInput = document.getElementById('co-delivery-date');
  var box = document.getElementById('co-slots');
  if (!dateInput || !box) return;

  var grid = box.querySelector('.co-slots__grid');
  var note = box.querySelector('.co-slots__note');
  var wanted = box.dataset.old || '';
  var request = 0;

  function esc(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function render(slots) {
    var firstFree = null;
    grid.innerHTML = slots.map(function (slot) {
      if (!slot.full && !firstFree) firstFree = slot.key;
      var left = slot.full ? 'Full' : (slot.remaining <= 3 ? 'Only ' + slot.remaining + ' left' : 'Available');

      return '<label class="co-slot' + (slot.full ? ' is-full' : '') + '">' +
        '<input type="radio" name="requested_delivery_slot" value="' + esc(slot.key) + '"' + (slot.full ? ' disabled' : '') + ' />' +
        '<span class="co-slot__body"><strong>' + esc(slot.label) + '</strong><em>' + esc(slot.time) + '</em>' +
        '<small class="' + (slot.full ? 'is-full' : (slot.remaining <= 3 ? 'is-low' : '')) + '">' + left + '</small></span></label>';
    }).join('');

    var pick = slots.some(function (s) { return s.key === wanted && !s.full; }) ? wanted : null;
    var input = pick ? grid.querySelector('input[value="' + pick + '"]') : null;
    if (input) input.checked = true;

    note.textContent = firstFree ? '' : 'Every window is full on this date — please pick another day.';
    wanted = '';
  }

  function load() {
    var date = dateInput.value;

    if (!date) {
      box.hidden = true;
      grid.innerHTML = '';
      return;
    }

    var mine = ++request;
    box.hidden = false;
    grid.innerHTML = '<p class="co-slots__loading">Checking available windows…</p>';

    fetch(box.dataset.url + '?date=' + encodeURIComponent(date), { headers: { 'Accept': 'application/json' } })
      .then(function (res) { return res.ok ? res.json() : Promise.reject(res); })
      .then(function (data) { if (mine === request) render(data.slots); })
      .catch(function () {
        if (mine !== request) return;
        grid.innerHTML = '';
        note.textContent = 'Pick a delivery date between tomorrow and 90 days from now.';
      });
  }

  dateInput.addEventListener('change', load);
  if (dateInput.value) load();
})();
