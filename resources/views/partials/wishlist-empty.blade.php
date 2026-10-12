<section class="section">
  <div class="container" style="text-align:center; padding: var(--s8) 0">
    <p style="color: var(--fg-soft); margin-bottom: var(--s5)">{{ $settings['wishlist_empty_text'] ?? 'Nothing here yet — tap the heart on any product to save it.' }}</p>
    <a href="{{ route('shop.index') }}" class="btn btn--indigo">Browse the shop →</a>
  </div>
</section>