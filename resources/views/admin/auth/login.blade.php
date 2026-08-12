<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Login — {{ $settings['site_name'] ?? 'Sprylo' }}</title>
  @if(!empty($settings['favicon']))
    <link rel="icon" href="{{ $settings['favicon'] }}" />
  @endif
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { font-family: 'Outfit', sans-serif; background: #0F172A; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-card { background: #fff; border-radius: 16px; padding: 40px; width: 100%; max-width: 380px; box-shadow: 0 20px 60px rgba(0,0,0,.3); }
    .login-brand { display: flex; align-items: center; gap: 10px; margin-bottom: 28px; }
    .login-brand .bico { width: 42px; height: 42px; border-radius: 10px; background: #4F46E5; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif; }
    .login-brand .bname { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 1.1rem; }
    .login-brand .bsub { font-size: .7rem; text-transform: uppercase; letter-spacing: 1px; color: #94A3B8; }
    .btn-login { background: #4F46E5; color: #fff; border: none; }
    .btn-login:hover { background: #3730A3; color: #fff; }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-brand">
      @if(!empty($settings['logo']))
        <img class="bico" src="{{ $settings['logo'] }}" alt="{{ $settings['site_name'] ?? 'Sprylo' }}" style="object-fit:cover" />
      @else
        <div class="bico">{{ strtoupper(substr($settings['site_name'] ?? 'Sprylo', 0, 1)) }}</div>
      @endif
      <div>
        <div class="bname">{{ $settings['site_name'] ?? 'Sprylo' }}</div>
        <div class="bsub">Admin Panel</div>
      </div>
    </div>

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
        <label for="email" class="form-label small fw-semibold">Email</label>
        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus />
      </div>
      <div class="mb-3">
        <label for="password" class="form-label small fw-semibold">Password</label>
        <input id="password" type="password" name="password" class="form-control" required />
      </div>
      <div class="mb-3 form-check">
        <input type="checkbox" name="remember" class="form-check-input" id="remember" />
        <label class="form-check-label small" for="remember">Remember me</label>
      </div>
      <button type="submit" class="btn btn-login w-100 fw-semibold">Sign in →</button>
    </form>
  </div>
</body>
</html>
