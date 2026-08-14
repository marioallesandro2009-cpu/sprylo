<div class="contact-info">
  <div class="info-block">
    <div class="ic">✉</div>
    <div>
      <div class="label">EMAIL US</div>
      <div class="value"><a href="mailto:{{ $settings['contact_email'] ?? '' }}">{{ $settings['contact_email'] ?? '' }}</a></div>
    </div>
  </div>
  <div class="info-block">
    <div class="ic">☏</div>
    <div>
      <div class="label">CALL US · 24/7</div>
      <div class="value"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings['contact_phone'] ?? '') }}">{{ $settings['contact_phone'] ?? '' }}</a></div>
    </div>
  </div>
  <div class="info-block">
    <div class="ic">💬</div>
    <div>
      <div class="label">{{ $settings['contact_livechat_label'] ?? 'LIVE CHAT' }}</div>
      <div class="value"><a href="{{ route('contact.index') }}#c-msg">{{ $settings['contact_livechat_status'] ?? '' }}</a></div>
    </div>
  </div>
  <div class="info-block">
    <div class="ic">⌖</div>
    <div>
      <div class="label">VISIT OUR FLAGSHIP</div>
      <div class="value">{!! nl2br(e($settings['contact_address'] ?? '')) !!}</div>
    </div>
  </div>
  <div class="info-block">
    <div class="ic">⏱</div>
    <div>
      <div class="label">SUPPORT HOURS</div>
      <div class="value">{{ $settings['contact_hours_weekday'] ?? '' }}<br />{{ $settings['contact_hours_weekend'] ?? '' }}</div>
    </div>
  </div>

  <div style="margin-top: var(--s7); padding: var(--s6); background: linear-gradient(135deg, var(--indigo), var(--card-purple)); color: var(--paper); border-radius: var(--r-lg); position: relative; overflow: hidden">
    <div style="position: absolute; inset: 0; background-image: radial-gradient(circle at 80% 20%, rgba(255,255,255,0.18) 0, transparent 40%); pointer-events: none"></div>
    <div style="position: relative">
      <h3 style="color: var(--paper); font-size: var(--text-xl); margin-bottom: var(--s3)">{{ $settings['contact_trade_heading'] ?? '' }}</h3>
      <p style="color: rgba(255,255,255,0.85); font-size: var(--text-sm); line-height: 1.6; margin-bottom: var(--s4)">
        {{ $settings['contact_trade_description'] ?? '' }}</p>
      <a href="mailto:{{ $settings['contact_email'] ?? '' }}" class="btn btn--paper">{{ $settings['contact_trade_cta_label'] ?? 'Email the trade desk' }} →</a>
    </div>
  </div>
</div>
