<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>Help centre</span></div>
    <h1>{{ $settings['help_head_title'] ?? 'Help centre' }}</h1>
    <p>{{ $settings['help_head_text'] ?? "Answers to the questions we get asked most. Can't find what you need?" }} <a href="{{ route('contact.index') }}#c-msg" style="color: var(--indigo)">Send us a message</a> and we'll get back to you.</p>
  </div>
</section>