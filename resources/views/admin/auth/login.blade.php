<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Login — {{ $settings['site_name'] ?? 'solis' }}</title>
  @if(!empty($settings['favicon']))
    <link rel="icon" href="{{ $settings['favicon'] }}" />
  @endif
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    :root { --bs-primary: #4F46E5; --bs-primary-rgb: 79, 70, 229; }
    body { font-family: 'Outfit', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px 16px;
      background: radial-gradient(1000px 520px at 15% -10%, rgba(124, 58, 237, .55), transparent 60%), radial-gradient(900px 520px at 100% 110%, rgba(79, 70, 229, .6), transparent 60%), #1E1B4B; }
    .login-card { background: #fff; border-radius: 20px; padding: 36px 36px 32px; width: 100%; max-width: 400px; box-shadow: 0 30px 80px rgba(15, 23, 42, .45); }
    .login-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 26px; }
    .login-brand .bico { width: 46px; height: 46px; border-radius: 13px; box-shadow: 0 8px 18px rgba(79, 70, 229, .35); object-fit: cover; }
    .login-brand .bname { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 1.2rem; letter-spacing: -.03em; line-height: 1.1; }
    .login-brand .bsub { font-size: .66rem; text-transform: uppercase; letter-spacing: .14em; color: #94A3B8; }
    .login-title { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 1.3rem; letter-spacing: -.02em; margin: 0 0 4px; }
    .login-sub { color: #64748B; font-size: .88rem; margin: 0 0 22px; }
    .form-label { font-weight: 600; color: #334155; }
    .form-control { border-color: #CBD5E1; border-radius: 10px; padding: .6rem .8rem; }
    .form-control:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79, 70, 229, .22); }
    .form-check-input:checked { background-color: #4F46E5; border-color: #4F46E5; }
    .pw-wrap { position: relative; }
    .pw-wrap .form-control { padding-right: 4.2rem; }
    .pw-toggle { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); border: 0; background: none; color: #4F46E5; font-size: .78rem; font-weight: 600; padding: 6px 10px; border-radius: 8px; }
    .pw-toggle:hover { background: #EEF2FF; }
    .btn-login { background: linear-gradient(135deg, #4F46E5, #6366F1); color: #fff; border: none; border-radius: 12px; padding: .7rem 1rem; box-shadow: 0 10px 22px rgba(79, 70, 229, .32); }
    .btn-login:hover, .btn-login:focus { filter: brightness(.94); color: #fff; }
    .back-link { display: block; text-align: center; margin-top: 18px; font-size: .82rem; color: #64748B; text-decoration: none; }
    .back-link:hover { color: #4F46E5; }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-brand">
      @if(!empty($settings['logo']))
        <img class="bico" src="{{ $settings['logo'] }}" alt="{{ $settings['site_name'] ?? 'solis' }}" style="object-fit:cover" />
      @else
        <img class="bico" src="{{ asset('assets/img/logo.svg') }}" alt="{{ $settings['site_name'] ?? 'solis' }}" />
      @endif
      <div>
        <div class="bname">{{ $settings['site_name'] ?? 'solis' }}</div>
        <div class="bsub">Admin Panel</div>
      </div>
    </div>

    <h1 class="login-title">Masuk ke dashboard</h1>
    <p class="login-sub">Kelola produk, pesanan, dan konten toko kamu.</p>

    @if($errors->any())
      <div class="alert alert-danger py-2 small">
        @foreach($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}">
      @csrf
      <div class="mb-3">
        <label for="email" class="form-label small">Email</label>
        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" autocomplete="username" required autofocus />
      </div>
      <div class="mb-3">
        <label for="password" class="form-label small">Password</label>
        <div class="pw-wrap">
          <input id="password" type="password" name="password" class="form-control" autocomplete="current-password" required />
          <button type="button" class="pw-toggle" id="pwToggle" aria-label="Tampilkan password" aria-controls="password">Lihat</button>
        </div>
      </div>
      <div class="mb-3 form-check">
        <input type="checkbox" name="remember" class="form-check-input" id="remember" />
        <label class="form-check-label small" for="remember">Ingat saya</label>
      </div>
      <button type="submit" class="btn btn-login w-100 fw-semibold">Masuk →</button>
    </form>
    <a href="{{ route('home') }}" class="back-link">← Kembali ke toko</a>
  </div>
  <script>
    (function () {
      var btn = document.getElementById('pwToggle'), input = document.getElementById('password');
      btn.addEventListener('click', function () {
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.textContent = show ? 'Sembunyi' : 'Lihat';
        btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
      });
    })();
  </script>
</body>
</html>
