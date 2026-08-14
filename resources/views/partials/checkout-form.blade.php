<form class="contact-form" action="{{ route('checkout.store') }}" method="POST">
  @csrf
  <h2 style="font-size: var(--text-xl); margin-bottom: var(--s4)">Shipping details</h2>

  @if($errors->any())
    <div style="color: var(--rose); font-size: var(--text-sm); margin-bottom: var(--s4)">
      <ul style="margin:0; padding-left: 18px">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="field">
    <label for="co-name">Full name</label>
    <input id="co-name" name="name" type="text" required value="{{ old('name') }}" />
  </div>

  <div class="field-row">
    <div class="field">
      <label for="co-email">Email</label>
      <input id="co-email" name="email" type="email" required value="{{ old('email') }}" />
    </div>
    <div class="field">
      <label for="co-phone">Phone</label>
      <input id="co-phone" name="phone" type="tel" required value="{{ old('phone') }}" />
    </div>
  </div>

  <div class="field">
    <label for="co-address">Address</label>
    <textarea id="co-address" name="address" required>{{ old('address') }}</textarea>
  </div>

  <div class="field-row">
    <div class="field">
      <label for="co-city">City</label>
      <input id="co-city" name="city" type="text" required value="{{ old('city') }}" />
    </div>
    <div class="field">
      <label for="co-postal">Postal code</label>
      <input id="co-postal" name="postal_code" type="text" required value="{{ old('postal_code') }}" />
    </div>
  </div>

  <div class="field">
    <label for="co-notes">Order notes (optional)</label>
    <textarea id="co-notes" name="notes" placeholder="Delivery instructions, gift note, etc.">{{ old('notes') }}</textarea>
  </div>

  <button class="btn btn--indigo btn--block" type="submit"
    style="padding: 16px; font-size: var(--text-base); margin-top: var(--s2)">Place order →</button>
</form>
