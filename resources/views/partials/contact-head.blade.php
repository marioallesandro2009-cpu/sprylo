<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>Contact &amp; support</span></div>
    <h1>{{ $settings['contact_head_title'] ?? 'Talk to us — we ship gear, but we answer messages too.' }}</h1>
    <p>{{ $settings['contact_head_text'] ?? '' }}</p>
  </div>
</section>