<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'Dashboard') — {{ $settings['site_name'] ?? 'Sprylo' }} Admin</title>
  @if(!empty($settings['favicon']))
    <link rel="icon" href="{{ $settings['favicon'] }}" />
  @endif
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <style>
    :root { --indigo: #4F46E5; --indigo-dark: #3730A3; --ink: #0F172A; }
    body { font-family: 'Outfit', sans-serif; background: #F5F6FA; }
    .admin-sidebar { width: 260px; min-height: 100vh; background: var(--ink); position: fixed; top: 0; left: 0; overflow-y: auto; z-index: 1040; }
    .admin-brand { padding: 20px 22px; border-bottom: 1px solid rgba(255,255,255,.08); display: flex; align-items: center; gap: 10px; }
    .admin-brand .bico { width: 38px; height: 38px; border-radius: 10px; background: var(--indigo); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif; }
    .admin-brand .bname { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.05rem; font-weight: 800; color: #fff; }
    .admin-brand .bsub { font-size: .65rem; text-transform: uppercase; letter-spacing: 1.2px; color: rgba(255,255,255,.4); }
    .admin-nav-section { color: rgba(255,255,255,.32); font-size: .68rem; text-transform: uppercase; letter-spacing: 1px; padding: 18px 22px 6px; font-weight: 600; }
    .admin-nav-link { color: rgba(255,255,255,.7); padding: 10px 22px; font-size: .85rem; display: flex; align-items: center; gap: 10px; transition: .2s; border-left: 3px solid transparent; text-decoration: none; }
    .admin-nav-link:hover { background: rgba(79,70,229,.15); color: #fff; }
    .admin-nav-link.active { background: rgba(79,70,229,.22); color: #fff; border-left-color: var(--indigo); font-weight: 600; }
    .admin-nav-link i { width: 16px; text-align: center; font-size: .8rem; }
    .admin-nav-link.is-disabled { color: rgba(255,255,255,.32); cursor: default; }
    .admin-nav-link .soon { margin-left: auto; font-size: .6rem; background: rgba(255,255,255,.08); padding: 2px 7px; border-radius: 999px; letter-spacing: .5px; }
    .admin-main { margin-left: 260px; min-height: 100vh; }
    .admin-topbar { background: #fff; padding: 14px 26px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 12px rgba(0,0,0,.05); position: sticky; top: 0; z-index: 1030; }
    .admin-topbar h5 { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; margin: 0; color: var(--ink); }
    .admin-content { padding: 26px; }
    .card-admin { background: #fff; border-radius: 14px; padding: 22px; box-shadow: 0 4px 16px rgba(0,0,0,.05); border: none; }
    .btn-admin-primary { background: var(--indigo); color: #fff; border: none; border-radius: 8px; padding: 9px 18px; font-weight: 600; font-size: .85rem; }
    .btn-admin-primary:hover { background: var(--indigo-dark); color: #fff; }
    .table-admin thead { background: #F5F6FA; }
    .table-admin th { font-size: .72rem; text-transform: uppercase; letter-spacing: .5px; color: #888; font-weight: 600; }
    @media (max-width: 991px) {
      .admin-sidebar { transform: translateX(-100%); transition: .3s; }
      .admin-sidebar.show { transform: translateX(0); }
      .admin-main { margin-left: 0; }
    }
  </style>
  @stack('styles')
</head>
<body>

  @php $siteName = $settings['site_name'] ?? 'Sprylo'; @endphp

  <div class="admin-sidebar" id="adminSidebar">
    <div class="admin-brand">
      @if(!empty($settings['logo']))
        <img class="bico" src="{{ $settings['logo'] }}" alt="{{ $siteName }}" style="object-fit:cover" />
      @else
        <div class="bico">{{ strtoupper(substr($siteName, 0, 1)) }}</div>
      @endif
      <div>
        <div class="bname">{{ $siteName }}</div>
        <div class="bsub">Admin Panel</div>
      </div>
    </div>
    <nav class="py-2">
      <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge"></i> Dashboard
      </a>

      <div class="admin-nav-section">Catalog</div>
      <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"><i class="fa-solid fa-box"></i> Products</a>
      <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i class="fa-solid fa-tags"></i> Categories</a>
      <a href="{{ route('admin.brands.index') }}" class="admin-nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"><i class="fa-solid fa-copyright"></i> Brands</a>
      <a href="{{ route('admin.reviews.index') }}" class="admin-nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}"><i class="fa-solid fa-star"></i> Reviews</a>
      <a href="{{ route('admin.promo-codes.index') }}" class="admin-nav-link {{ request()->routeIs('admin.promo-codes.*') ? 'active' : '' }}"><i class="fa-solid fa-percent"></i> Promo Codes</a>

      <div class="admin-nav-section">Content</div>
      <a href="{{ route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"><i class="fa-solid fa-receipt"></i> Orders</a>
      <a href="{{ route('admin.contact-messages.index') }}" class="admin-nav-link {{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }}">
        <i class="fa-solid fa-envelope"></i> Contact Messages
        @if(($unreadMessagesCount ?? 0) > 0)
          <span class="soon" style="background:#4F46E5">{{ $unreadMessagesCount }}</span>
        @endif
      </a>
      <a href="{{ route('admin.faqs.index') }}" class="admin-nav-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}"><i class="fa-solid fa-circle-question"></i> FAQs</a>
      <a href="{{ route('admin.footer-links.index') }}" class="admin-nav-link {{ request()->routeIs('admin.footer-links.*') ? 'active' : '' }}"><i class="fa-solid fa-link"></i> Footer Links</a>
      <a href="{{ route('admin.hero-slides.index') }}" class="admin-nav-link {{ request()->routeIs('admin.hero-slides.*') ? 'active' : '' }}"><i class="fa-solid fa-images"></i> Hero Slides</a>
      <a href="{{ route('admin.promo-banners.index') }}" class="admin-nav-link {{ request()->routeIs('admin.promo-banners.*') ? 'active' : '' }}"><i class="fa-solid fa-tag"></i> Promo Banners</a>

      <div class="admin-nav-section">Customers</div>
      <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Users</a>
      <a href="{{ route('admin.suspended-customers.index') }}" class="admin-nav-link {{ request()->routeIs('admin.suspended-customers.*') ? 'active' : '' }}"><i class="fa-solid fa-user-slash"></i> Suspended Customers</a>
      <a href="{{ route('admin.newsletter-subscribers.index') }}" class="admin-nav-link {{ request()->routeIs('admin.newsletter-subscribers.*') ? 'active' : '' }}"><i class="fa-solid fa-paper-plane"></i> Newsletter</a>

      <div class="admin-nav-section">Configuration</div>
      <a href="{{ route('admin.content.index') }}" class="admin-nav-link {{ request()->routeIs('admin.content.*') ? 'active' : '' }}"><i class="fa-solid fa-pen-to-square"></i> Page Content</a>
      <a href="{{ route('admin.settings.index') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"><i class="fa-solid fa-gear"></i> Site Settings</a>
      <a href="{{ route('admin.page-meta.index') }}" class="admin-nav-link {{ request()->routeIs('admin.page-meta.*') ? 'active' : '' }}"><i class="fa-solid fa-globe"></i> SEO / Page Meta</a>
      <a href="{{ route('admin.account.edit') }}" class="admin-nav-link {{ request()->routeIs('admin.account.*') ? 'active' : '' }}"><i class="fa-solid fa-user"></i> My Account</a>
    </nav>
  </div>

  <div class="admin-main">
    <div class="admin-topbar">
      <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
        <i class="fa-solid fa-bars"></i>
      </button>
      <h5>@yield('page-title', 'Dashboard')</h5>
      <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.account.edit') }}" class="text-muted small text-decoration-none">{{ auth()->user()->name }}</a>
        <form method="POST" action="{{ route('admin.logout') }}">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-right-from-bracket"></i></button>
        </form>
      </div>
    </div>

    <div class="admin-content">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
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
  @stack('scripts')
</body>
</html>
