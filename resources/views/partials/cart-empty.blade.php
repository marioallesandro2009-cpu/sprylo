<section class="section">
  <div class="container">
    <div class="cart-empty">
      <div class="cart-empty__icon" aria-hidden="true">
        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6h15l-1.6 8.2a2 2 0 0 1-2 1.6H8.2a2 2 0 0 1-2-1.6L4.4 3H2"/><circle cx="9" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/></svg>
      </div>
      <h2>Your cart is empty</h2>
      <p>{{ $settings['cart_empty_text'] ?? "Looks like you haven't added anything to your cart yet." }}</p>
      <a href="{{ route('shop.index') }}" class="btn btn--indigo">{{ $ui('ui_cart_empty_cta') }}</a>
    </div>
  </div>
</section>
