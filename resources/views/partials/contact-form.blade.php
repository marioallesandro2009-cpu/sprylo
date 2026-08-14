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
    By submitting, you agree to our <a href="{{ route('privacy-policy.index') }}" style="color: var(--indigo)">privacy policy</a>. We never sell
    your data and never share with third parties.</p>
</form>
