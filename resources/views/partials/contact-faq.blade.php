@if($faqs->isNotEmpty())
  <section class="section" style="background: var(--bg)">
    <div class="container">
      <div class="section-head">
        <h2>Quick answers</h2>
        <a href="{{ route('help.index') }}" class="view-all">Full help centre →</a>
      </div>
      <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--s5)">
        @foreach($faqs as $faq)
          <article style="background: var(--paper); border: 1px solid var(--rule); border-radius: var(--r); padding: var(--s5)">
            <h3 style="font-size: var(--text-md); margin-bottom: var(--s2)">{{ $faq->question }}</h3>
            <p style="color: var(--fg-soft); font-size: var(--text-sm); line-height: 1.6">{{ $faq->answer }}</p>
          </article>
        @endforeach
      </div>
    </div>
  </section>
@endif
