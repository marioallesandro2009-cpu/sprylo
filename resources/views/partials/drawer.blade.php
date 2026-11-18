@php
  $siteName = $settings['site_name'] ?? 'solis';
@endphp
<div class="drawer" id="drawer" aria-hidden="true">
  <div class="drawer-head">
    <a href="{{ route('home') }}" class="brand">
      @if(!empty($settings['logo']))
        <img class="brand-mark" src="{{ $settings['logo'] }}" alt="{{ $siteName }}" style="object-fit:cover" />
      @else
        <img class="brand-mark" src="{{ asset('assets/img/logo.svg') }}" alt="{{ $siteName }}" />
      @endif
      {{ $siteName }}
    </a>
    <button class="drawer-close" aria-label="Close menu">{{ $ui('ui_drawer_close') }}</button>
  </div>
  @foreach($navLinks->whereIn('placement', ['both', 'drawer']) as $link)
    <a href="{{ $link->resolvedUrl() }}">{{ $link->label }}</a>
  @endforeach
  <a href="{{ route('cart.index') }}" class="btn btn--indigo" style="margin-top:var(--s5); justify-content:center">{{ $ui('ui_drawer_cta') }}</a>
</div>
