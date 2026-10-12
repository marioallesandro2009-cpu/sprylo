@extends('layouts.app')

@section('title', 'Log in — '.($settings['site_name'] ?? 'Sprylo'))

@section('content')
  <section class="page-head">
    <div class="container">
      <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>Log in</span></div>
      <h1>Log in</h1>
      <p>Sign in to your account to check out faster and track your orders.</p>
    </div>
  </section>

  <section class="section">
    <div class="container" style="max-width: 480px">

      @if(session('success'))
        <p style="color: var(--emerald); font-weight: 600; margin-bottom: var(--s5)">{{ session('success') }}</p>
      @endif

      <form action="{{ route('login') }}" method="POST" style="background: var(--paper); border: 1px solid var(--rule); border-radius: var(--r); padding: var(--s7)">
        @csrf

        @error('email')
          <p style="color: var(--rose); font-weight: 600; margin-bottom: var(--s4)">{{ $message }}</p>
        @enderror

        <div class="field">
          <label for="login-email">Email</label>
          <input id="login-email" name="email" type="email" required autofocus value="{{ old('email') }}" />
        </div>

        <div class="field">
          <label for="login-password">Password</label>
          <input id="login-password" name="password" type="password" required />
        </div>

        <label style="display:flex; align-items:center; gap: var(--s2); font-size: var(--text-sm); color: var(--fg-soft); margin-bottom: var(--s5)">
          <input type="checkbox" name="remember" value="1" />
          Keep me signed in
        </label>

        <button type="submit" class="btn btn--indigo" style="width:100%">Log in →</button>
      </form>

    </div>
  </section>
@endsection
