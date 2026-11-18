// Sprylo — checkout delivery pin (Leaflet)
(function () {
  const mapEl = document.getElementById('checkout-map');
  if (!mapEl || typeof L === 'undefined') return;

  const statusEl = document.getElementById('checkout-map-status');
  const latInput = document.getElementById('co-latitude');
  const lngInput = document.getElementById('co-longitude');
  const locateBtn = document.getElementById('checkout-locate-btn');
  const form = mapEl.closest('form');
  const searchInput = document.getElementById('checkout-map-search');
  const suggestionsEl = document.getElementById('checkout-map-suggestions');
  const addressInput = document.getElementById('co-address');
  const cityInput = document.getElementById('co-city');
  const postalInput = document.getElementById('co-postal');

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  const postalLookupUrl = form?.dataset.postalLookupUrl;

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

  function setPin(lat, lng, opts) {
    opts = opts || {};
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

    // A search result already carries its own address/city/postcode, so skip the
    // extra lookup — only reverse-geocode pins dropped by click/drag/geolocation.
    if (!opts.skipReverseGeocode) reverseGeocode(lat, lng);
  }

  let postalLookupAbort = null;

  /**
   * Falls back to Biteship's own delivery-area database for a postal code when Nominatim's
   * reverse-geocode didn't return one (common for rural/remote pins) — but only fills a gap,
   * never overwrites a postcode Nominatim already gave for the exact pinned point: a city/
   * district-level lookup like this can span several postal codes (e.g. a district with 3
   * codes), so it's less precise than Nominatim's point-level match when one is available.
   */
  function lookupPostalCode(query) {
    if (!postalLookupUrl || !query) return;
    if (postalLookupAbort) postalLookupAbort.abort();
    postalLookupAbort = new AbortController();

    fetch(postalLookupUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: JSON.stringify({ query }),
      signal: postalLookupAbort.signal,
    })
      .then((res) => (res.ok ? res.json() : null))
      .then((data) => {
        if (data && data.success && data.postal_code && postalInput && !postalInput.value.trim()) {
          postalInput.value = data.postal_code;
        }
      })
      .catch(() => {}); // Best-effort — the geocoder's own postcode guess stays as a fallback.
  }

  /**
   * Nominatim reports the province as "city" for some Indonesian places (e.g. Jakarta), so skip any
   * candidate equal to the state name and fall through to the municipality / county instead.
   */
  function pickCity(address) {
    const state = (address.state || '').trim().toLowerCase();
    const looksLikeProvince = /special (capital )?region|daerah khusus|province|provinsi/i;
    // city_district is where Jakarta's real city ("Central Jakarta") lives when "city" holds the province.
    const candidates = [address.city, address.town, address.municipality, address.county, address.city_district, address.village];

    return candidates.find((c) => c && c.trim().toLowerCase() !== state && !looksLikeProvince.test(c)) || '';
  }

  /**
   * Fills the address/city/postal fields from a Nominatim address payload, but only
   * where the shopper hasn't typed anything yet — a dragged pin shouldn't clobber
   * details they already entered.
   */
  function fillAddressFields(displayName, address) {
    address = address || {};
    const city = pickCity(address);

    if (addressInput && !addressInput.value.trim() && displayName) {
      addressInput.value = displayName;
    }
    if (cityInput && !cityInput.value.trim()) {
      cityInput.value = city;
    }
    if (postalInput && !postalInput.value.trim() && address.postcode) {
      postalInput.value = address.postcode;
    }
    if (city) lookupPostalCode(city);
  }

  let reverseGeocodeAbort = null;

  function reverseGeocode(lat, lng) {
    if (reverseGeocodeAbort) reverseGeocodeAbort.abort();
    reverseGeocodeAbort = new AbortController();

    fetch(
      'https://nominatim.openstreetmap.org/reverse?format=json&addressdetails=1&lat=' + lat + '&lon=' + lng,
      { signal: reverseGeocodeAbort.signal }
    )
      .then((res) => (res.ok ? res.json() : null))
      .then((data) => {
        if (data && data.display_name) fillAddressFields(data.display_name, data.address);
      })
      .catch(() => {}); // Best-effort — the shopper can always fill the fields in by hand.
  }

  if (hasStart) setPin(startLat, startLng, { skipReverseGeocode: true });

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

  // Address search — lets the shopper type an address instead of hunting for it on
  // the map, the same "search then pin" flow used by most Indonesian storefronts.
  if (searchInput && suggestionsEl) {
    const SEARCH_FAILED = Symbol('search-failed');
    let searchAbort = null;
    let searchDebounce = null;
    let activeIndex = -1;

    function hideSuggestions() {
      suggestionsEl.hidden = true;
      suggestionsEl.innerHTML = '';
      activeIndex = -1;
    }

    function selectResult(result) {
      const lat = parseFloat(result.lat);
      const lng = parseFloat(result.lon);
      searchInput.value = result.display_name;
      hideSuggestions();
      map.setView([lat, lng], 17);
      setPin(lat, lng, { skipReverseGeocode: true });
      // A picked search result is the shopper's deliberate choice — override
      // whatever was auto-filled by an earlier pin drop.
      if (addressInput) addressInput.value = result.display_name;
      if (result.address) {
        const city = pickCity(result.address);
        if (cityInput && city) cityInput.value = city;
        if (postalInput && result.address.postcode) postalInput.value = result.address.postcode;
        if (city) lookupPostalCode(city);
      }
    }

    function renderSuggestions(results) {
      if (results === SEARCH_FAILED) {
        suggestionsEl.innerHTML = '<li class="checkout-map-suggestion-empty">Address search is unavailable right now — try clicking the map instead.</li>';
        suggestionsEl.hidden = false;
        return;
      }

      if (!results.length) {
        suggestionsEl.innerHTML = '<li class="checkout-map-suggestion-empty">No matching address found.</li>';
        suggestionsEl.hidden = false;
        return;
      }

      suggestionsEl.innerHTML = '';
      results.forEach((result) => {
        const li = document.createElement('li');
        li.className = 'checkout-map-suggestion';
        li.textContent = result.display_name;
        li.addEventListener('click', () => selectResult(result));
        suggestionsEl.appendChild(li);
      });
      suggestionsEl.hidden = false;
    }

    searchInput.addEventListener('input', () => {
      const query = searchInput.value.trim();
      clearTimeout(searchDebounce);

      if (query.length < 3) {
        hideSuggestions();
        return;
      }

      searchDebounce = setTimeout(() => {
        if (searchAbort) searchAbort.abort();
        searchAbort = new AbortController();

        suggestionsEl.innerHTML = '<li class="checkout-map-suggestion-loading">Searching…</li>';
        suggestionsEl.hidden = false;

        fetch(
          'https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&countrycodes=id&limit=6&q=' + encodeURIComponent(query),
          { signal: searchAbort.signal }
        )
          .then((res) => (res.ok ? res.json() : SEARCH_FAILED))
          .then(renderSuggestions)
          .catch((err) => {
            if (err.name !== 'AbortError') renderSuggestions(SEARCH_FAILED);
          });
      }, 400);
    });

    searchInput.addEventListener('keydown', (e) => {
      const items = suggestionsEl.querySelectorAll('.checkout-map-suggestion');
      if (!items.length || suggestionsEl.hidden) return;

      if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
        activeIndex = e.key === 'ArrowDown'
          ? Math.min(activeIndex + 1, items.length - 1)
          : Math.max(activeIndex - 1, 0);
        items.forEach((item, i) => item.classList.toggle('is-active', i === activeIndex));
        items[activeIndex].scrollIntoView({ block: 'nearest' });
      } else if (e.key === 'Enter') {
        // Suggestions are open — treat Enter as "pick a result", not "submit the form".
        e.preventDefault();
        items[activeIndex >= 0 ? activeIndex : 0].click();
      } else if (e.key === 'Escape') {
        hideSuggestions();
      }
    });

    document.addEventListener('click', (e) => {
      if (e.target !== searchInput && !suggestionsEl.contains(e.target)) hideSuggestions();
    });
  }

  form && form.addEventListener('submit', (e) => {
    if (!latInput.value || !lngInput.value) {
      e.preventDefault();
      if (statusEl) {
        statusEl.dataset.state = 'invalid';
        statusEl.textContent = 'Please pin your delivery location on the map before placing the order.';
      }
      if (window.SpryloAlert) {
        window.SpryloAlert('warning', 'Pin your delivery location', 'Search your address or tap the map so we know where to deliver.')
          .then(() => mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' }));
      } else {
        mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      return;
    }

    // Placing the order takes a couple of seconds (courier rates + email), so lock the
    // button to stop a second click from re-posting the form after the cart was cleared.
    const submitBtn = document.getElementById('checkout-submit-btn');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = 'Placing order…';
    }
  });

  // Leaflet needs a nudge to size correctly when its container was hidden/animated in.
  setTimeout(() => map.invalidateSize(), 200);
})();
