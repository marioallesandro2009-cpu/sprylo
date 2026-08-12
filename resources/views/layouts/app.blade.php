<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', $settings['site_name'] ?? 'Sprylo')</title>
  <meta name="description" content="@yield('meta_description', $settings['site_description'] ?? '')" />
  <meta name="theme-color" content="#4F46E5" />
  @if(!empty($settings['favicon']))
    <link rel="icon" href="{{ $settings['favicon'] }}" />
  @endif
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Outfit:wght@400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" />
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
  @stack('styles')
</head>
<body>
  <a class="skip-link" href="#main">Skip to content</a>

  @include('partials.utility')
  @include('partials.header')
  @include('partials.nav')
  @include('partials.drawer')

  <main id="main">
    @yield('content')
  </main>

  @include('partials.footer')

  <script src="{{ asset('assets/js/main.js') }}" defer></script>
  @stack('scripts')
</body>
</html>
