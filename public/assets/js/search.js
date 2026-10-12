// Sprylo - header search: live suggestions + search history
(function () {
  const form = document.querySelector('.search');
  if (!form) return;

  const input = form.querySelector('input[name="q"]');
  const panel = form.querySelector('.search-suggest');
  if (!input || !panel) return;

  const HISTORY_KEY = 'sprylo_search_history';
  const HISTORY_MAX = 8;

  let debounceTimer = null;
  let activeController = null;
  let activeIndex = -1;

  /* ---------- helpers ---------- */

  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (ch) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[ch]));
  }

  function highlight(text, term) {
    const idx = text.toLowerCase().indexOf(term.toLowerCase());
    if (idx === -1) return escapeHtml(text);
    return escapeHtml(text.slice(0, idx))
      + '<mark>' + escapeHtml(text.slice(idx, idx + term.length)) + '</mark>'
      + escapeHtml(text.slice(idx + term.length));
  }

  function searchUrl(term) {
    const url = new URL(form.getAttribute('action'), window.location.href);
    url.searchParams.set('q', term);
    return url.toString();
  }

  /* ---------- history (localStorage) ---------- */

  function loadHistory() {
    try {
      const raw = JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]');
      return Array.isArray(raw) ? raw.filter((t) => typeof t === 'string') : [];
    } catch (e) {
      return [];
    }
  }

  function saveHistory(list) {
    try {
      localStorage.setItem(HISTORY_KEY, JSON.stringify(list));
    } catch (e) { /* storage penuh / diblokir, abaikan */ }
  }

  function addHistory(term) {
    term = String(term || '').trim();
    if (term.length < 2 || term.length > 80) return;
    const list = loadHistory().filter((t) => t.toLowerCase() !== term.toLowerCase());
    list.unshift(term);
    saveHistory(list.slice(0, HISTORY_MAX));
  }

  function removeHistory(term) {
    saveHistory(loadHistory().filter((t) => t !== term));
  }

  function clearHistory() {
    saveHistory([]);
  }

  /* ---------- panel ---------- */

  function closePanel() {
    panel.hidden = true;
    panel.innerHTML = '';
    activeIndex = -1;
  }

  function openPanel(html) {
    panel.innerHTML = html;
    panel.hidden = false;
    activeIndex = -1;
  }

  function historyRow(term, query) {
    return `
      <div class="search-history__row">
        <a class="search-suggest__item search-history__item" data-nav data-term="${escapeHtml(term)}" href="${escapeHtml(searchUrl(term))}">
          <svg class="search-history__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 14"/></svg>
          <span class="search-history__term">${query ? highlight(term, query) : escapeHtml(term)}</span>
        </a>
        <button type="button" class="search-history__remove" data-history-remove="${escapeHtml(term)}" aria-label="Remove ${escapeHtml(term)} from history">&times;</button>
      </div>`;
  }

  function productRow(item, term) {
    return `
      <a class="search-suggest__item" data-nav data-term="${escapeHtml(item.name)}" href="${escapeHtml(item.url)}">
        <span class="search-suggest__pic">${item.image ? `<img src="${escapeHtml(item.image)}" alt="" />` : ''}</span>
        <span class="search-suggest__info">
          <span class="search-suggest__name">${highlight(item.name, term)}</span>
          <span class="search-suggest__price">
            ${escapeHtml(item.price)}
            ${item.original_price ? `<s>${escapeHtml(item.original_price)}</s>` : ''}
          </span>
        </span>
      </a>`;
  }

  function renderHistory() {
    const list = loadHistory();
    if (!list.length) {
      closePanel();
      return;
    }

    openPanel(`
      <div class="search-suggest__head">
        <span>Recent searches</span>
        <button type="button" class="search-history__clear" data-history-clear>Clear all</button>
      </div>
      ${list.map((term) => historyRow(term, '')).join('')}
    `);
  }

  function renderResults(results, term) {
    const q = term.toLowerCase();
    const matches = loadHistory()
      .filter((t) => t.toLowerCase().includes(q) && t.toLowerCase() !== q)
      .slice(0, 3);

    let html = '';

    if (matches.length) {
      html += '<div class="search-suggest__head"><span>Recent searches</span></div>';
      html += matches.map((t) => historyRow(t, term)).join('');
    }

    if (results.length) {
      html += '<div class="search-suggest__head"><span>Products</span></div>';
      html += results.map((item) => productRow(item, term)).join('');
    } else {
      html += '<p class="search-suggest__empty">No products found.</p>';
    }

    html += `<a class="search-suggest__all" data-nav data-term="${escapeHtml(term)}" href="${escapeHtml(searchUrl(term))}">See all results for "${escapeHtml(term)}"</a>`;

    openPanel(html);
  }

  function fetchSuggestions(term) {
    if (activeController) activeController.abort();
    activeController = new AbortController();

    fetch(`/search/suggest?q=${encodeURIComponent(term)}`, {
      signal: activeController.signal,
      headers: { Accept: 'application/json' },
    })
      .then((res) => res.json())
      .then((data) => {
        if (input.value.trim() !== term) return; // input sudah berubah, abaikan
        renderResults(data.results || [], term);
      })
      .catch((err) => {
        if (err.name !== 'AbortError') closePanel();
      });
  }

  function refreshPanel() {
    const term = input.value.trim();
    if (term === '') renderHistory();
    else if (term.length >= 2) fetchSuggestions(term);
    else closePanel();
  }

  /* ---------- events ---------- */

  input.addEventListener('input', () => {
    const term = input.value.trim();
    clearTimeout(debounceTimer);
    if (activeController) activeController.abort();

    if (term === '') {
      renderHistory();
      return;
    }
    if (term.length < 2) {
      closePanel();
      return;
    }

    debounceTimer = setTimeout(() => fetchSuggestions(term), 200);
  });

  function showHistoryIfEmpty() {
    if (input.value.trim() === '') renderHistory();
  }
  input.addEventListener('focus', showHistoryIfEmpty);
  input.addEventListener('click', showHistoryIfEmpty);

  input.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closePanel();
      return;
    }

    const items = panel.querySelectorAll('[data-nav]');
    if (panel.hidden || !items.length) return;

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      activeIndex = Math.min(activeIndex + 1, items.length - 1);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      activeIndex = Math.max(activeIndex - 1, 0);
    } else if (e.key === 'Enter' && activeIndex >= 0) {
      e.preventDefault();
      items[activeIndex].click();
      return;
    } else {
      return;
    }

    items.forEach((item, i) => item.classList.toggle('is-active', i === activeIndex));
  });

  panel.addEventListener('click', (e) => {
    const removeBtn = e.target.closest('[data-history-remove]');
    if (removeBtn) {
      e.preventDefault();
      removeHistory(removeBtn.dataset.historyRemove);
      refreshPanel();
      return;
    }

    if (e.target.closest('[data-history-clear]')) {
      e.preventDefault();
      clearHistory();
      closePanel();
      return;
    }

    const link = e.target.closest('a[data-term]');
    if (link) addHistory(link.dataset.term);
  });

  form.addEventListener('submit', () => addHistory(input.value));

  document.addEventListener('click', (e) => {
    if (!e.composedPath().includes(form)) closePanel();
  });
})();