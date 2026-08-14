<div>
  <h2 style="font-size: var(--text-2xl); margin-bottom: var(--s5)">Reviews</h2>
  <div style="display:grid; grid-template-columns: auto 1fr; gap: var(--s5); align-items:center; padding: var(--s5); background: var(--bg); border-radius: var(--r); margin-bottom: var(--s5)">
    <div style="text-align:center">
      <div style="font-family: var(--ff-display); font-size: 48px; font-weight: 800; color: var(--ink); line-height: 1">{{ $reviewSummary['average'] }}</div>
      <div style="color: var(--amber); font-size: 18px">{{ str_repeat('★', $product->rating) }}{{ str_repeat('☆', 5 - $product->rating) }}</div>
      <div style="font-family: var(--ff-mono); font-size: 11px; color: var(--fg-mute); margin-top: 4px">{{ $reviewSummary['count'] }} reviews</div>
    </div>
    <div style="display:grid; gap:6px; font-family: var(--ff-mono); font-size: var(--text-xs); color: var(--fg-soft)">
      @foreach($reviewSummary['breakdown'] as $stars => $pct)
        <div style="display:flex; align-items:center; gap:8px"><span style="width: 16px">{{ $stars }}</span><span
            style="flex:1; height:6px; background:var(--rule); border-radius:999px; overflow:hidden"><span
              style="display:block; width:{{ $pct }}%; height:100%; background: var(--amber)"></span></span><span
            style="width: 28px; text-align:right">{{ $pct }}%</span></div>
      @endforeach
    </div>
  </div>

  @forelse($reviews as $review)
    <article style="border-bottom: 1px solid var(--rule); padding-bottom: var(--s5); margin-bottom: var(--s5)">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 8px">
        <strong style="font-family: var(--ff-display); font-weight: 700">{{ $review->name }}</strong>
        <span style="font-family: var(--ff-mono); font-size: 11px; color: var(--fg-mute)">{{ $review->created_at->diffForHumans() }}</span>
      </div>
      <div style="color: var(--amber); font-size: 14px; margin-bottom: 6px">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
      <p style="font-size: var(--text-sm); color: var(--fg-soft); line-height: 1.6">{{ $review->comment }}</p>
    </article>
  @empty
    <p style="color: var(--fg-soft); font-size: var(--text-sm)">No reviews yet — be the first to share your experience.</p>
  @endforelse
</div>
