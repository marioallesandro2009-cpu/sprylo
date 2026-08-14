<div style="margin-top: var(--s7); display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--s4); padding: var(--s5); background: var(--bg); border-radius: var(--r)">
  <div style="display:flex; align-items:center; gap:var(--s3)">
    <div style="width:40px; height:40px; background:var(--indigo-soft); color:var(--indigo); border-radius:999px; display:grid; place-items:center; font-size:18px">⚡</div>
    <div>
      <div style="font-family:var(--ff-display); font-weight:700; font-size:var(--text-sm)">{{ $settings['trust_shipping_title'] ?? '' }}</div>
      <div style="font-family:var(--ff-mono); font-size:11px; color:var(--fg-mute)">{{ $settings['trust_shipping_subtitle'] ?? '' }}</div>
    </div>
  </div>
  <div style="display:flex; align-items:center; gap:var(--s3)">
    <div style="width:40px; height:40px; background:var(--indigo-soft); color:var(--indigo); border-radius:999px; display:grid; place-items:center; font-size:18px">↺</div>
    <div>
      <div style="font-family:var(--ff-display); font-weight:700; font-size:var(--text-sm)">{{ $settings['trust_returns_title'] ?? '' }}</div>
      <div style="font-family:var(--ff-mono); font-size:11px; color:var(--fg-mute)">{{ $settings['trust_returns_subtitle'] ?? '' }}</div>
    </div>
  </div>
  <div style="display:flex; align-items:center; gap:var(--s3)">
    <div style="width:40px; height:40px; background:var(--indigo-soft); color:var(--indigo); border-radius:999px; display:grid; place-items:center; font-size:18px">★</div>
    <div>
      <div style="font-family:var(--ff-display); font-weight:700; font-size:var(--text-sm)">{{ $settings['trust_warranty_title'] ?? '' }}</div>
      <div style="font-family:var(--ff-mono); font-size:11px; color:var(--fg-mute)">{{ $settings['trust_warranty_subtitle'] ?? '' }}</div>
    </div>
  </div>
  <div style="display:flex; align-items:center; gap:var(--s3)">
    <div style="width:40px; height:40px; background:var(--indigo-soft); color:var(--indigo); border-radius:999px; display:grid; place-items:center; font-size:18px">✓</div>
    <div>
      <div style="font-family:var(--ff-display); font-weight:700; font-size:var(--text-sm)">{{ $settings['trust_authentic_title'] ?? '' }}</div>
      <div style="font-family:var(--ff-mono); font-size:11px; color:var(--fg-mute)">{{ $settings['trust_authentic_subtitle'] ?? '' }}</div>
    </div>
  </div>
</div>
