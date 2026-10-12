<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <a href="{{ route('cart.index') }}">Cart</a> <span class="sep">›</span> <span>Checkout</span></div>
    <h1>{{ $settings['checkout_head_title'] ?? 'Checkout' }}</h1>
    <p>{{ $settings['checkout_head_text'] ?? 'Enter your shipping details to place your order.' }}</p>
  </div>
</section>