<section class="section">
  <div class="container" style="max-width: 860px">
    @forelse($faqs as $faq)
      <article style="border-bottom: 1px solid var(--rule); padding: var(--s5) 0">
        <h3 style="font-size: var(--text-md); margin-bottom: var(--s2)">{{ $faq->question }}</h3>
        <p style="color: var(--fg-soft); font-size: var(--text-sm); line-height: 1.6">{{ $faq->answer }}</p>
      </article>
    @empty
      <p style="color: var(--fg-soft)">No help articles yet.</p>
    @endforelse
  </div>
</section>
