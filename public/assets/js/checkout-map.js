// Sprylo — checkout delivery pin (Leaflet)
(function () {
  const mapEl = document.getElementById('checkout-map');
  if (!mapEl || typeof L === 'undefined') return;

  const statusEl = document.getElementById('checkout-map-status');
  const latInput = document.getElementById('co-latitude');
  const lngInput = document.getElementById('co-longitude');
  const locateBtn = document.getElementById('checkout-locate-btn');
  const form = mapEl.closest('form');

  const defaultCenter = [-2.5489, 118.0149]; // Indonesia
  const defaultZoom = 5;

  const startLat = parseFloat(mapEl.dataset.lat);
  const startLng = parseFloat(mapEl.dataset.lng);
  const hasStart = Number.isFinite(startLat) && Number.isFinite(startLng);

  const map = L.map(mapEl).setView(hasStart ? [startLat, startLng] : defaultCenter, hasStart ? 15 : defaultZoom);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(map);

  let marker = null;

  function setPin(lat, lng) {
    latInput.value = lat.toFixed(7);
    lngInput.value = lng.toFixed(7);

    if (marker) {
      marker.setLatLng([lat, lng]);
    } else {
      marker = L.marker([lat, lng], { draggable: true }).addTo(map);
      marker.on('dragend', () => {
        const pos = marker.getLatLng();
        setPin(pos.lat, pos.lng);
      });
    }

    if (statusEl) {
      statusEl.dataset.state = 'picked';
      statusEl.textContent = 'Pinned at ' + lat.toFixed(6) + ', ' + lng.toFixed(6) + '. Drag the marker to fine-tune.';
    }

    document.dispatchEvent(new CustomEvent('checkout:pin-set', { detail: { lat, lng } }));
  }

  if (hasStart) setPin(startLat, startLng);

  map.on('click', (e) => setPin(e.latlng.lat, e.latlng.lng));

  locateBtn && locateBtn.addEventListener('click', () => {
    if (!navigator.geolocation) {
      if (statusEl) {
        statusEl.dataset.state = 'invalid';
        statusEl.textContent = 'Geolocation isn\'t available on this browser — click the map to pin manually.';
      }
      return;
    }

    locateBtn.disabled = true;
    locateBtn.textContent = 'Locating…';

    navigator.geolocation.getCurrentPosition(
      (pos) => {
        map.setView([pos.coords.latitude, pos.coords.longitude], 16);
        setPin(pos.coords.latitude, pos.coords.longitude);
        locateBtn.disabled = false;
        locateBtn.textContent = 'Use my location';
      },
      () => {
        if (statusEl) {
          statusEl.dataset.state = 'invalid';
          statusEl.textContent = 'Couldn\'t get your location — click the map to pin manually.';
        }
        locateBtn.disabled = false;
        locateBtn.textContent = 'Use my location';
      },
      { enableHighAccuracy: true, timeout: 10000 }
    );
  });

  form && form.addEventListener('submit', (e) => {
    if (!latInput.value || !lngInput.value) {
      e.preventDefault();
      if (statusEl) {
        statusEl.dataset.state = 'invalid';
        statusEl.textContent = 'Please pin your delivery location on the map before placing the order.';
      }
      mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });

  // Leaflet needs a nudge to size correctly when its container was hidden/animated in.
  setTimeout(() => map.invalidateSize(), 200);
})();
