@php
  $siteName = $settings['site_name'] ?? 'solis';
  $adminUser = auth()->user();

  // Satu sumber untuk menu sidebar, jadi tidak ada lagi item dobel.
  $menu = [
    ['type' => 'link', 'label' => 'Dashboard', 'icon' => 'fa-house', 'route' => 'admin.dashboard', 'match' => ['admin.dashboard']],
    ['type' => 'group', 'id' => 'pesanan', 'label' => 'Pesanan', 'icon' => 'fa-cart-shopping', 'items' => [
      ['Semua Pesanan', 'admin.orders.index', ['admin.orders.*']],
      ['Jadwal Kirim', 'admin.delivery-schedule.index', ['admin.delivery-schedule.*']],
    ]],
    ['type' => 'group', 'id' => 'produk', 'label' => 'Produk', 'icon' => 'fa-box', 'items' => [
      ['Semua Produk', 'admin.products.index', ['admin.products.*']],
      ['Riwayat Stok', 'admin.stock-movements.index', ['admin.stock-movements.*']], // update 1: menu riwayat stok
      ['Kategori', 'admin.categories.index', ['admin.categories.*']],
      ['Brand', 'admin.brands.index', ['admin.brands.*']],
      ['Ulasan', 'admin.reviews.index', ['admin.reviews.*']],
    ]],
    ['type' => 'group', 'id' => 'promo', 'label' => 'Promo', 'icon' => 'fa-tag', 'items' => [
      ['Kode Promo', 'admin.promo-codes.index', ['admin.promo-codes.*']],
      ['Diskon Terjadwal', 'admin.discounts.index', ['admin.discounts.*']],
      ['Flash Sale', 'admin.flash-sales.index', ['admin.flash-sales.*']],
      ['Voucher Otomatis', 'admin.voucher-rules.index', ['admin.voucher-rules.*']],
      ['Banner Promo', 'admin.promo-banners.index', ['admin.promo-banners.*']],
    ]],
    ['type' => 'group', 'id' => 'pelanggan', 'label' => 'Pelanggan', 'icon' => 'fa-user-group', 'items' => [
      ['Semua Pelanggan', 'admin.users.index', ['admin.users.*']],
      ['Pesan Kontak', 'admin.contact-messages.index', ['admin.contact-messages.*'], $unreadMessagesCount ?? 0],
      ['Ditangguhkan', 'admin.suspended-customers.index', ['admin.suspended-customers.*']],
      ['Newsletter', 'admin.newsletter-subscribers.index', ['admin.newsletter-subscribers.*']],
    ]],
    ['type' => 'link', 'label' => 'Laporan Keuangan', 'icon' => 'fa-chart-line', 'route' => 'admin.reports.finance', 'match' => ['admin.reports.*']],
    ['type' => 'group', 'id' => 'konten', 'label' => 'Konten', 'icon' => 'fa-pen-to-square', 'items' => [
      ['Hero Slide', 'admin.hero-slides.index', ['admin.hero-slides.*']],
      ['Navigasi', 'admin.nav-links.index', ['admin.nav-links.*']],
      ['Footer', 'admin.footer-links.index', ['admin.footer-links.*']],
      ['FAQ', 'admin.faqs.index', ['admin.faqs.*']],
      ['Konten Halaman', 'admin.content.index', ['admin.content.*']],
      ['Teks Halaman', 'admin.ui-texts.index', ['admin.ui-texts.*']],
      ['SEO Halaman', 'admin.page-meta.index', ['admin.page-meta.*']],
    ]],
    ['type' => 'group', 'id' => 'pengaturan', 'label' => 'Pengaturan', 'icon' => 'fa-gear', 'items' => array_values(array_filter([
      ['Pengaturan Toko', 'admin.settings.index', ['admin.settings.*']],
      $adminUser->isSuperAdmin() ? ['Akun Admin', 'admin.admin-users.index', ['admin.admin-users.*']] : null,
      $adminUser->isSuperAdmin() ? ['Log Audit', 'admin.audit-logs.index', ['admin.audit-logs.*']] : null,
    ]))],
  ];
@endphp
<!doctype html>
<html lang="id" class="theme-admin" data-theme="indigo">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'Dashboard') — {{ $siteName }} Admin</title>
  <link rel="icon" href="{{ !empty($settings['favicon']) ? $settings['favicon'] : asset('favicon.svg') }}" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" />
  <link rel="stylesheet" href="{{ asset('assets/css/swal-theme.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/themes.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}" />
  @stack('styles')
</head>
<body>
  <script>try { if (localStorage.getItem('adminSidebarCollapsed') === '1') document.body.classList.add('sb-collapsed'); } catch (e) {}</script>

  <aside class="admin-sidebar" id="adminSidebar">
    <div class="sb-head">
      <a href="{{ route('admin.dashboard') }}" class="sb-brand">
        <img src="{{ !empty($settings['logo']) ? $settings['logo'] : asset('assets/img/logo.svg') }}" alt="" />
        <span class="txt">{{ $siteName }}<small>Admin Panel</small></span>
      </a>
      <button type="button" class="sb-collapse" id="sbCollapse" aria-label="Ciutkan sidebar"><i class="fa-solid fa-angles-left"></i></button>
    </div>

    <nav class="sb-nav" id="sbNav">
      @foreach($menu as $entry)
        @if($entry['type'] === 'link')
          @php $on = request()->routeIs(...$entry['match']); @endphp
          <a href="{{ route($entry['route']) }}" class="sb-link {{ $on ? 'active' : '' }}" title="{{ $entry['label'] }}">
            <i class="fa-solid {{ $entry['icon'] }} ico"></i><span class="sb-txt">{{ $entry['label'] }}</span>
          </a>
        @else
          @php
            $open = collect($entry['items'])->contains(fn ($item) => request()->routeIs(...$item[2]));
            $badge = collect($entry['items'])->sum(fn ($item) => $item[3] ?? 0);
          @endphp
          <button type="button" class="sb-link {{ $open ? 'group-active' : '' }}" data-bs-toggle="collapse" data-bs-target="#grp-{{ $entry['id'] }}" aria-expanded="{{ $open ? 'true' : 'false' }}" title="{{ $entry['label'] }}">
            <i class="fa-solid {{ $entry['icon'] }} ico"></i><span class="sb-txt">{{ $entry['label'] }}</span>
            @if($badge > 0)<span class="sb-count sb-txt ms-auto me-1">{{ $badge }}</span>@endif
            <i class="fa-solid fa-chevron-down sb-chevron" @if($badge > 0) style="margin-left:0" @endif></i>
          </button>
          {{-- data-bs-parent: opening one group closes the others, so the menu stays short. --}}
          <div class="collapse sb-sub {{ $open ? 'show' : '' }}" id="grp-{{ $entry['id'] }}" data-bs-parent="#sbNav">
            @foreach($entry['items'] as $item)
              <a href="{{ route($item[1]) }}" class="{{ request()->routeIs(...$item[2]) ? 'active' : '' }}">
                <span>{{ $item[0] }}</span>
                @if(($item[3] ?? 0) > 0)<span class="sb-count">{{ $item[3] }}</span>@endif
              </a>
            @endforeach
          </div>
        @endif
      @endforeach
    </nav>

    <div class="sb-foot">
      <a href="{{ route('admin.account.edit') }}" class="sb-user" title="Akun saya">
        <div class="sb-avatar">{{ strtoupper(mb_substr($adminUser->name, 0, 1)) }}</div>
        <div class="meta"><div class="nm">{{ $adminUser->name }}</div></div>
      </a>
      <div class="sb-foot-actions">
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="sb-icon-btn" title="Lihat Toko" aria-label="Lihat Toko"><i class="fa-solid fa-store"></i></a>
        <form method="POST" action="{{ route('admin.logout') }}">
          @csrf
          <button type="submit" class="sb-icon-btn" title="Keluar" aria-label="Keluar"><i class="fa-solid fa-right-from-bracket"></i></button>
        </form>
      </div>
    </div>
  </aside>
  <div class="sb-overlay" id="sbOverlay"></div>

  <div class="admin-main">
    <div class="m-topbar">
      <button class="btn btn-sm btn-outline-secondary" type="button" id="sbOpen" aria-label="Buka menu"><i class="fa-solid fa-bars"></i></button>
      <span class="t">@yield('page-title', 'Dashboard')</span>
      <img src="{{ !empty($settings['logo']) ? $settings['logo'] : asset('assets/img/logo.svg') }}" alt="" />
    </div>

    <div class="admin-content">
      @hasSection('custom-header')
        @yield('custom-header')
      @else
        <div class="page-head"><h1>@yield('page-title', 'Dashboard')</h1></div>
      @endif

      <div id="flash-data" hidden data-success="{{ session('success') }}" data-error="{{ session('error') }}" data-warning="{{ session('warning') }}"></div>
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
  <script src="{{ asset('assets/js/swal-helpers.js') }}"></script>
  <script>
    (function () {
      var sidebar = document.getElementById('adminSidebar');
      var overlay = document.getElementById('sbOverlay');
      var close = function () { sidebar.classList.remove('show'); overlay.classList.remove('show'); };

      document.getElementById('sbCollapse').addEventListener('click', function () {
        var collapsed = document.body.classList.toggle('sb-collapsed');
        try { localStorage.setItem('adminSidebarCollapsed', collapsed ? '1' : '0'); } catch (e) {}
      });
      document.getElementById('sbOpen').addEventListener('click', function () {
        sidebar.classList.add('show');
        overlay.classList.add('show');
      });
      overlay.addEventListener('click', close);
    })();
  </script>
  @stack('scripts')
</body>
</html>