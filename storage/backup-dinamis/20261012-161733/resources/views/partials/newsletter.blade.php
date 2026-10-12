<section style="background: var(--paper)">
  <div class="container">
    <div class="newsletter">
      <div class="newsletter-grid">
        <div>
          <h2>{{ $settings['newsletter_prefix'] ?? 'Get' }} <strong>{{ $settings['newsletter_highlight'] ?? '20% Off' }}</strong> {{ $settings['newsletter_suffix'] ?? 'your first order — straight to your inbox.' }}</h2>
          <p>{{ $settings['newsletter_description'] ?? '' }}</p>
        </div>
        <form class="newsletter-form" onsubmit="event.preventDefault(); this.querySelector('button').textContent='Sent ✓';">
          <input type="email" required placeholder="Enter your email" aria-label="Email address" />
          <button class="btn" type="submit">Subscribe →</button>
        </form>
      </div>
    </div>
  </div>
</section>
