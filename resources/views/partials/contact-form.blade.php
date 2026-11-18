<form class="contact-form" action="{{ route('contact.store') }}" method="POST">
  @csrf
  <h2 style="font-size: var(--text-xl); margin-bottom: var(--s2)">{{ $ui('ui_contact_form_title') }}</h2>

  @if($errors->any())
    <div style="color: var(--rose); font-size: var(--text-sm); margin-bottom: var(--s4)">
      <ul style="margin:0; padding-left: 18px">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <p style="color: var(--fg-soft); font-size: var(--text-sm); margin-bottom: var(--s5)">{{ $settings['contact_form_note'] ?? 'No bots, no templated replies — a real person on our team will pick this up.' }}</p>

  <div class="field-row">
    <div class="field">
      <label for="c-first">{{ $ui('ui_contact_form_first_name') }}</label>
      <input id="c-first" name="first_name" type="text" required placeholder="{{ $ui('ui_contact_form_first_name_placeholder') }}" value="{{ old('first_name') }}" />
    </div>
    <div class="field">
      <label for="c-last">{{ $ui('ui_contact_form_last_name') }}</label>
      <input id="c-last" name="last_name" type="text" required placeholder="{{ $ui('ui_contact_form_last_name_placeholder') }}" value="{{ old('last_name') }}" />
    </div>
  </div>

  <div class="field-row">
    <div class="field">
      <label for="c-email">{{ $ui('ui_contact_form_email') }}</label>
      <input id="c-email" name="email" type="email" required placeholder="{{ $ui('ui_contact_form_email_placeholder') }}" value="{{ old('email') }}" />
    </div>
    <div class="field">
      <label for="c-phone">{{ $ui('ui_contact_form_phone') }}</label>
      <input id="c-phone" name="phone" type="tel" placeholder="{{ $ui('ui_contact_form_phone_placeholder') }}" value="{{ old('phone') }}" />
    </div>
  </div>

  <div class="field">
    <label for="c-topic">{{ $ui('ui_contact_form_topic') }}</label>
    <select id="c-topic" name="topic">
      @foreach(preg_split('/\r\n|\r|\n/', $ui('ui_contact_form_topics'), -1, PREG_SPLIT_NO_EMPTY) as $topic)
        <option @selected(old('topic') === $topic)>{{ $topic }}</option>
      @endforeach
    </select>
  </div>

  <div class="field">
    <label for="c-order">{{ $ui('ui_contact_form_order') }}</label>
    <input id="c-order" name="order_number" type="text" placeholder="{{ $ui('ui_contact_form_order_placeholder') }}" value="{{ old('order_number') }}" />
  </div>

  <div class="field">
    <label for="c-msg">{{ $ui('ui_contact_form_message') }}</label>
    <textarea id="c-msg" name="message" required
      placeholder="{{ $ui('ui_contact_form_message_placeholder') }}">{{ old('message') }}</textarea>
  </div>

  <button class="btn btn--indigo btn--block" type="submit"
    style="padding: 16px; font-size: var(--text-base); margin-top: var(--s2)">{{ $ui('ui_contact_form_submit') }}</button>

  <p style="font-family: var(--ff-mono); font-size: 11px; color: var(--fg-mute); margin-top: var(--s4); text-align: center; line-height: 1.6">
    {{ $ui('ui_contact_privacy_prefix') }} <a href="{{ route('privacy-policy.index') }}" style="color: var(--indigo)">{{ $ui('ui_contact_privacy_link') }}</a>{{ $ui('ui_contact_privacy_suffix') }}</p>
</form>
