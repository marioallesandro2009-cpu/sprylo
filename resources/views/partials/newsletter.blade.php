<section id="newsletter" style="background: var(--paper)">
  <div class="container">
    <div class="newsletter">
      <div class="newsletter-grid">
        <div>
          <h2>{{ $settings['newsletter_prefix'] ?? 'Get' }} <strong>{{ $settings['newsletter_highlight'] ?? '20% Off' }}</strong> {{ $settings['newsletter_suffix'] ?? 'your first order — straight to your inbox.' }}</h2>
          <p>{{ $settings['newsletter_description'] ?? '' }}</p>
        </div>
        <div>
          <form class="newsletter-form" action="{{ route('newsletter.store') }}" method="POST">
            @csrf
            <input type="email" name="email" required maxlength="255"
                   value="{{ $errors->newsletter->any() ? old('email') : '' }}"
                   placeholder="{{ $settings['newsletter_placeholder'] ?? 'Enter your email' }}" aria-label="Email address" />
            <button class="btn" type="submit">{{ $settings['newsletter_button_label'] ?? 'Subscribe →' }}</button>
          </form>
          @if(session('newsletter_success'))
            <p style="margin-top: var(--s3); font-weight: 600">{{ session('newsletter_success') }}</p>
          @endif
          @if($errors->newsletter->any())
            <p style="margin-top: var(--s3); font-weight: 600">{{ $errors->newsletter->first() }}</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>