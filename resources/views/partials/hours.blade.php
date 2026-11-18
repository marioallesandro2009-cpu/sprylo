<section class="section" style="background: var(--bg)">
  <div class="container" style="max-width: 560px">
    <h2 style="font-size: var(--text-2xl); margin-bottom: var(--s5)">{{ $ui('ui_hours_title') }}</h2>
    <div class="info-block">
      <div class="ic">⏱</div>
      <div>
        <div class="label">{{ $settings['contact_label_hours'] ?? 'SUPPORT HOURS' }}</div>
        <div class="value">{{ $settings['contact_hours_weekday'] ?? '' }}<br />{{ $settings['contact_hours_weekend'] ?? '' }}</div>
      </div>
    </div>
  </div>
</section>
