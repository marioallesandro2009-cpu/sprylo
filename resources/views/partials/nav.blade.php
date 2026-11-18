<nav class="nav-bar" aria-label="Primary">
  <div class="container">
    <a href="{{ route('shop.index') }}" class="all-cats">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      {{ $ui('ui_nav_all_categories') }}
    </a>
    <div class="main-nav">
      @foreach($navLinks->whereIn('placement', ['both', 'nav']) as $link)
        <a href="{{ $link->resolvedUrl() }}" @if($link->isCurrent()) aria-current="page" @endif>{{ $link->label }}</a>
      @endforeach
    </div>
    <span class="nav-cta">{{ $settings['nav_cta_prefix'] ?? '' }} <strong>{{ $settings['nav_cta_highlight'] ?? '' }}</strong> {{ $settings['nav_cta_suffix'] ?? '' }}</span>
  </div>
</nav>
