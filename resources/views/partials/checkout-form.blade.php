<form class="contact-form" action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data" data-rates-url="{{ route('checkout.rates') }}">
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

  <div class="field">
    <label for="checkout-map">Pin delivery location on map</label>
    <div id="checkout-map" class="checkout-map" data-lat="{{ old('latitude') }}" data-lng="{{ old('longitude') }}"></div>
    <p id="checkout-map-status" class="checkout-map-status" data-state="{{ old('latitude') ? 'picked' : 'empty' }}">
      @if(old('latitude'))
        Pinned at {{ old('latitude') }}, {{ old('longitude') }}
      @else
        Click on the map (or use "Use my location") to drop a pin at your delivery address.
      @endif
    </p>
    <button type="button" id="checkout-locate-btn" class="btn btn--ghost" style="margin-top: var(--s2)">Use my location</button>
    <input type="hidden" id="co-latitude" name="latitude" value="{{ old('latitude') }}" />
    <input type="hidden" id="co-longitude" name="longitude" value="{{ old('longitude') }}" />
    @error('latitude')
      <p style="color: var(--rose); font-size: var(--text-sm); margin-top: var(--s2)">{{ $message }}</p>
    @enderror
  </div>

  <div class="field">
    <div style="display:flex; align-items:center; justify-content:space-between; gap: var(--s3)">
      <label for="checkout-rates-btn" style="margin:0">Courier & shipping cost</label>
      <button type="button" id="checkout-rates-btn" class="btn btn--ghost" disabled
        style="padding: 8px 14px; font-size: var(--text-sm)">Check couriers →</button>
    </div>
    <div id="checkout-rates-list" class="checkout-rates" data-state="idle">
      <p class="checkout-rates-empty">Pin your location above, then check available couriers.</p>
    </div>
    <input type="hidden" id="co-courier-code" name="courier_code" value="{{ old('courier_code') }}" />
    <input type="hidden" id="co-courier-service-code" name="courier_service_code" value="{{ old('courier_service_code') }}" />
    @error('courier_code')
      <p style="color: var(--rose); font-size: var(--text-sm); margin-top: var(--s2)">{{ $message }}</p>
    @enderror
  </div>

  <div class="field">
    <label>Payment method</label>
    <div class="payment-methods">
      <label class="payment-method-option">
        <input type="radio" name="payment_method" value="bank_transfer" @checked(old('payment_method', 'bank_transfer') === 'bank_transfer') />
        <span>Bank transfer (manual)</span>
      </label>
      <label class="payment-method-option">
        <input type="radio" name="payment_method" value="qris" @checked(old('payment_method') === 'qris') />
        <span>QRIS</span>
      </label>
    </div>
    @error('payment_method')
      <p style="color: var(--rose); font-size: var(--text-sm); margin-top: var(--s2)">{{ $message }}</p>
    @enderror

    <div id="payment-bank-transfer" class="payment-panel">
      <div class="payment-bank-details">
        <div><span>Bank</span><strong>{{ $settings['payment_bank_name'] ?? '—' }}</strong></div>
        <div><span>Account no.</span><strong>{{ $settings['payment_bank_account_number'] ?? '—' }}</strong></div>
        <div><span>Account holder</span><strong>{{ $settings['payment_bank_account_holder'] ?? '—' }}</strong></div>
      </div>
      <label for="co-payment-proof" style="margin-top: var(--s4); display:block">Upload transfer proof</label>
      <input id="co-payment-proof" name="payment_proof" type="file" accept="image/*" />
      @error('payment_proof')
        <p style="color: var(--rose); font-size: var(--text-sm); margin-top: var(--s2)">{{ $message }}</p>
      @enderror
    </div>

    <div id="payment-qris" class="payment-panel" style="display:none">
      @if($settings['payment_qris_image'] ?? null)
        <img src="{{ $settings['payment_qris_image'] }}" alt="QRIS payment code" class="payment-qris-image" />
      @else
        <p class="checkout-rates-empty">QRIS code hasn't been set up yet — contact the store.</p>
      @endif
      <p class="payment-qris-note">Scan this code with any QRIS-supported app to pay.</p>
    </div>
  </div>

  <button class="btn btn--indigo btn--block" type="submit" id="checkout-submit-btn"
    style="padding: 16px; font-size: var(--text-base); margin-top: var(--s2)">Place order →</button>
</form>
