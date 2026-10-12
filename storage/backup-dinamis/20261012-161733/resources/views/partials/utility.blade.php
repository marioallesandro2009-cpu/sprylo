<div class="utility">
  <div class="container">
    <span class="promo">
      <span class="tag">{{ $settings['utility_promo_tag'] ?? 'SALE' }}</span>
      {{ $settings['utility_text'] ?? 'Free shipping on orders over Rp 750.000 · 30-day returns' }}
    </span>
    <span class="links">
      <a href="{{ $settings['utility_link1_url'] ?? '#' }}">{{ $settings['utility_link1_label'] ?? 'Track order' }}</a>
      <a href="{{ $settings['utility_link2_url'] ?? '#' }}">{{ $settings['utility_link2_label'] ?? 'Help' }}</a>
      <a href="{{ $settings['utility_link3_url'] ?? '#' }}">{{ $settings['utility_link3_label'] ?? 'EN · USD' }}</a>
    </span>
  </div>
</div>
