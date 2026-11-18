<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">{{ $ui('ui_crumb_home') }}</a> <span class="sep">›</span> <a href="{{ route('cart.index') }}">{{ $ui('ui_checkout_crumb_cart') }}</a> <span class="sep">›</span> <span>{{ $ui('ui_checkout_crumb') }}</span></div>
    @include('partials.checkout-steps', ['current' => 2])
    <h1>{{ $settings['checkout_head_title'] ?? 'Checkout' }}</h1>
    <p>{{ $settings['checkout_head_text'] ?? 'Enter your shipping details to place your order.' }}</p>
  </div>
</section>
