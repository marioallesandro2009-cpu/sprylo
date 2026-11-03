@php
  // One accent hue per section so the pages aren't all the same colour; neutrals stay identical (see themes.css).
  $pageTheme = 'indigo';
  foreach ([
    'shop.*' => 'violet', 'product.*' => 'violet',
    'categories.*' => 'teal', 'brands.*' => 'sky',
    'cart.*' => 'blue', 'checkout.*' => 'blue',
    'wishlist.*' => 'pink', 'compare.*' => 'cyan',
    'about.*' => 'emerald', 'contact.*' => 'orange',
    'help.*' => 'amber', 'track-order.*' => 'sky', 'invoice-check.*' => 'sky',
    'privacy-policy.*' => 'slate',
  ] as $pattern => $hue) {
    if (request()->routeIs($pattern)) { $pageTheme = $hue; break; }
  }
  $themeColors = ['indigo' => '#4F46E5', 'violet' => '#6D28D9', 'blue' => '#1D4ED8', 'sky' => '#0369A1', 'cyan' => '#0E7490', 'teal' => '#0F766E', 'emerald' => '#047857', 'amber' => '#B45309', 'orange' => '#C2410C', 'pink' => '#BE185D', 'slate' => '#475569'];
@endphp
<!doctype html>
<html lang="en" class="theme-store" data-theme="{{ $pageTheme }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', $settings['site_name'] ?? 'solis')</title>
  <meta name="description" content="@yield('meta_description', $settings['site_description'] ?? '')" />
  <meta name="theme-color" content="{{ $themeColors[$pageTheme] }}" />
  <link rel="icon" type="image/svg+xml" href="{{ $settings['favicon'] ?? asset('favicon.svg') }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Outfit:wght@400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" />
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/themes.css') }}" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" />
  <link rel="stylesheet" href="{{ asset('assets/css/swal-theme.css') }}" />
  @stack('styles')
</head>
<body>
  <a class="skip-link" href="#main">{{ $ui('ui_skip_to_content') }}</a>

  @include('partials.utility')
  @include('partials.header')
  @include('partials.nav')
  @include('partials.drawer')

  <main id="main">
    @yield('content')
  </main>

  @include('partials.footer')
  @include('partials.compare-bar')

  <div id="flash-data" hidden data-success="{{ session('success') }}" data-error="{{ session('error') }}" data-warning="{{ session('warning') }}"></div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" defer></script>
  <script src="{{ asset('assets/js/swal-helpers.js') }}" defer></script>
  <script src="{{ asset('assets/js/main.js') }}" defer></script>
  <script src="{{ asset('assets/js/search.js') }}" defer></script>
  @stack('scripts')
</body>
</html>
