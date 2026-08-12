@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Contact — '.($settings['site_name'] ?? 'Sprylo')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')

  <section class="page-head">
    <div class="container">
      <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>Contact &amp; support</span></div>
      <h1>Talk to us — we ship gear, but we answer messages too.</h1>
      <p>Three ways to reach our team: live chat (fastest), email, or this contact form for anything that needs an attachment. We typically reply within 4 working hours and cover support in EN, ES, FR, and DE.</p>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="contact-grid">

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
              <div class="value"><a href="#">{{ $settings['contact_livechat_status'] ?? '' }}</a></div>
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

        <form class="contact-form" action="{{ route('contact.store') }}" method="POST">
          @csrf
          <h2 style="font-size: var(--text-xl); margin-bottom: var(--s2)">Send us a message</h2>

          @if(session('success'))
            <p style="color: var(--emerald); font-weight: 600; font-size: var(--text-sm); margin-bottom: var(--s4)">{{ session('success') }}</p>
          @endif
          @if($errors->any())
            <div style="color: var(--rose); font-size: var(--text-sm); margin-bottom: var(--s4)">
              <ul style="margin:0; padding-left: 18px">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <p style="color: var(--fg-soft); font-size: var(--text-sm); margin-bottom: var(--s5)">No bots, no templated
            replies — a real person at our SF or Berlin desks will pick this up.</p>

          <div class="field-row">
            <div class="field">
              <label for="c-first">First name</label>
              <input id="c-first" name="first_name" type="text" required placeholder="Mira" value="{{ old('first_name') }}" />
            </div>
            <div class="field">
              <label for="c-last">Last name</label>
              <input id="c-last" name="last_name" type="text" required placeholder="Kapoor" value="{{ old('last_name') }}" />
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label for="c-email">Email</label>
              <input id="c-email" name="email" type="email" required placeholder="you@example.com" value="{{ old('email') }}" />
            </div>
            <div class="field">
              <label for="c-phone">Phone (optional)</label>
              <input id="c-phone" name="phone" type="tel" placeholder="+1 (415) 555 0123" value="{{ old('phone') }}" />
            </div>
          </div>

          <div class="field">
            <label for="c-topic">What is this about?</label>
            <select id="c-topic" name="topic">
              @foreach(['Order — tracking, change, or cancellation', 'Returns or refunds', 'Warranty claim or product issue', 'Trade / wholesale inquiry', 'Press, partnership, or affiliate', 'Something else'] as $topic)
                <option @selected(old('topic') === $topic)>{{ $topic }}</option>
              @endforeach
            </select>
          </div>

          <div class="field">
            <label for="c-order">Order number (if applicable)</label>
            <input id="c-order" name="order_number" type="text" placeholder="e.g. SPR-204418" value="{{ old('order_number') }}" />
          </div>

          <div class="field">
            <label for="c-msg">Your message</label>
            <textarea id="c-msg" name="message" required
              placeholder="Tell us what's going on. Screenshots welcome — drag and drop after sending.">{{ old('message') }}</textarea>
          </div>

          <button class="btn btn--indigo btn--block" type="submit"
            style="padding: 16px; font-size: var(--text-base); margin-top: var(--s2)">Send message →</button>

          <p style="font-family: var(--ff-mono); font-size: 11px; color: var(--fg-mute); margin-top: var(--s4); text-align: center; line-height: 1.6">
            By submitting, you agree to our <a href="#" style="color: var(--indigo)">privacy policy</a>. We never sell
            your data and never share with third parties.</p>
        </form>

      </div>
    </div>
  </section>

  <!-- FAQ short -->
  @if($faqs->isNotEmpty())
    <section class="section" style="background: var(--bg)">
      <div class="container">
        <div class="section-head">
          <h2>Quick answers</h2>
          <a href="#" class="view-all">Full help centre →</a>
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

@endsection
