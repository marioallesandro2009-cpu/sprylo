<form class="co-form" action="{{ route('checkout.store') }}" method="POST" data-rates-url="{{ route('checkout.rates') }}" data-postal-lookup-url="{{ route('checkout.postal-lookup') }}">
  @csrf

  @if($errors->any())
    <div class="co-errors" role="alert">
      <strong>Please fix the following:</strong>
      <ul>
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- 1 · Contact --}}
  <section class="co-section">
    <header class="co-section__head">
      <span class="co-num" aria-hidden="true">1</span>
      <div>
        <h2>Contact</h2>
        <p>Your order confirmation and shipping updates go here.</p>
      </div>
    </header>

    <div class="field">
      <label for="co-name">Full name</label>
      <input id="co-name" name="name" type="text" required autocomplete="name" value="{{ old('name') }}" />
    </div>

    <div class="field-row">
      <div class="field">
        <label for="co-email">Email</label>
        <input id="co-email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}" />
      </div>
      <div class="field">
        <label for="co-phone">Phone</label>
        <input id="co-phone" name="phone" type="tel" required autocomplete="tel" value="{{ old('phone') }}" />
      </div>
    </div>
  </section>

  {{-- 2 · Delivery location (pin first: it fills the address, city and postal code below) --}}
  <section class="co-section">
    <header class="co-section__head">
      <span class="co-num" aria-hidden="true">2</span>
      <div>
        <h2>Delivery address</h2>
        <p>Search or pin your location — we'll fill in the address, city and postal code for you.</p>
      </div>
    </header>

    <div class="field">
      <label for="checkout-map-search">Find your location</label>
      <div class="checkout-map-search">
        <input type="text" id="checkout-map-search" autocomplete="off"
          placeholder="Search your address, e.g. Jl. Sudirman, Jakarta" />
        <ul id="checkout-map-suggestions" class="checkout-map-suggestions" hidden></ul>
      </div>
      <div id="checkout-map" class="checkout-map" data-lat="{{ old('latitude') }}" data-lng="{{ old('longitude') }}"></div>
      <div class="co-map-foot">
        <p id="checkout-map-status" class="checkout-map-status" data-state="{{ old('latitude') ? 'picked' : 'empty' }}">
          @if(old('latitude'))
            Pinned at {{ old('latitude') }}, {{ old('longitude') }}
          @else
            Click on the map (or use "Use my location") to drop a pin at your delivery address.
          @endif
        </p>
        <button type="button" id="checkout-locate-btn" class="btn btn--ghost btn--sm">Use my location</button>
      </div>
      <input type="hidden" id="co-latitude" name="latitude" value="{{ old('latitude') }}" />
      <input type="hidden" id="co-longitude" name="longitude" value="{{ old('longitude') }}" />
      @error('latitude')
        <p class="co-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="field">
      <label for="co-address">Street address</label>
      <textarea id="co-address" name="address" required autocomplete="street-address">{{ old('address') }}</textarea>
    </div>

    <div class="field-row">
      <div class="field">
        <label for="co-city">City</label>
        <input id="co-city" name="city" type="text" required autocomplete="address-level2" value="{{ old('city') }}" />
      </div>
      <div class="field">
        <label for="co-postal">Postal code</label>
        <input id="co-postal" name="postal_code" type="text" required autocomplete="postal-code" inputmode="numeric" value="{{ old('postal_code') }}" />
      </div>
    </div>

    <div class="field">
      <label for="co-notes">Order notes (optional)</label>
      <textarea id="co-notes" name="notes" placeholder="Delivery instructions, gift note, etc.">{{ old('notes') }}</textarea>
    </div>
  </section>

  {{-- 3 · Delivery options --}}
  <section class="co-section">
    <header class="co-section__head">
      <span class="co-num" aria-hidden="true">3</span>
      <div>
        <h2>Delivery options</h2>
        <p>Choose a courier, a delivery date, and whether to insure the parcel.</p>
      </div>
    </header>

    <div class="field">
      <div class="co-label-row">
        <label for="checkout-rates-btn">Courier &amp; shipping cost</label>
        <button type="button" id="checkout-rates-btn" class="btn btn--ghost btn--sm" disabled>Check couriers →</button>
      </div>
      <div id="checkout-rates-list" class="checkout-rates" data-state="idle">
        <p class="checkout-rates-empty">Pin your location above, then check available couriers.</p>
      </div>
      <input type="hidden" id="co-courier-code" name="courier_code" value="{{ old('courier_code') }}" />
      <input type="hidden" id="co-courier-service-code" name="courier_service_code" value="{{ old('courier_service_code') }}" />
      @error('courier_code')
        <p class="co-error">{{ $message }}</p>
      @enderror
    </div>

    <div class="field">
      <label for="co-delivery-date">Delivery schedule (optional)</label>
      <input id="co-delivery-date" name="requested_delivery_date" type="date"
        min="{{ now()->addDay()->toDateString() }}" max="{{ now()->addDays(90)->toDateString() }}"
        value="{{ old('requested_delivery_date') }}" />
      <p class="co-hint">
        Timing it for a birthday, a launch event, or just making sure someone's home to sign for it? Pick a date, then a
        time window. We'll aim to deliver in that window — best-effort, as it depends on the courier's own schedule.</p>
      @error('requested_delivery_date')
        <p class="co-error">{{ $message }}</p>
      @enderror

      <div id="co-slots" class="co-slots" data-url="{{ route('checkout.delivery-slots') }}" data-old="{{ old('requested_delivery_slot') }}" hidden>
        <div class="co-slots__title" id="co-slots-title">Choose a time window</div>
        <div class="co-slots__grid" role="radiogroup" aria-labelledby="co-slots-title"></div>
        <p class="co-slots__note" aria-live="polite"></p>
      </div>
      @error('requested_delivery_slot')
        <p class="co-error">{{ $message }}</p>
      @enderror
    </div>

    <label class="co-option" for="co-insurance">
      <input type="checkbox" id="co-insurance" name="has_insurance" value="1" data-fee="{{ (int) $insuranceFee }}" @checked(old('has_insurance')) />
      <span class="co-option__body">
        <strong>Add shipping insurance</strong>
        <span>Covers your order if it's lost or damaged in transit. Applies to the whole order.</span>
      </span>
      <span class="co-option__price">+ {{ rupiah($insuranceFee) }}</span>
    </label>
  </section>

  {{-- 4 · Payment --}}
  <section class="co-section">
    <header class="co-section__head">
      <span class="co-num" aria-hidden="true">4</span>
      <div>
        <h2>Payment</h2>
        <p>You'll complete payment right after placing the order.</p>
      </div>
    </header>

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
      <p class="co-error">{{ $message }}</p>
    @enderror

    <div id="payment-bank-transfer" class="payment-panel">
      @include('partials.bank-details')
      <p class="co-hint" style="margin-top: var(--s4)">You'll upload your transfer proof after placing the order.</p>
    </div>

    <div id="payment-qris" class="payment-panel" style="display:none">
      @if($settings['payment_qris_image'] ?? null)
        <img src="{{ $settings['payment_qris_image'] }}" alt="QRIS payment code" class="payment-qris-image" />
      @else
        <p class="checkout-rates-empty">QRIS code hasn't been set up yet — contact the store.</p>
      @endif
      <p class="payment-qris-note">Scan this code with any QRIS-supported app to pay.</p>
    </div>
  </section>

  <button class="btn btn--indigo btn--block co-submit" type="submit" id="checkout-submit-btn">Place order →</button>
</form>
