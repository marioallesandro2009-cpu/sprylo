@php
  $bankRows = array_filter([
    'Bank' => trim($settings['payment_bank_name'] ?? ''),
    'Account no.' => trim($settings['payment_bank_account_number'] ?? ''),
    'Account holder' => trim($settings['payment_bank_account_holder'] ?? ''),
  ]);
  $contactEmail = trim($settings['contact_email'] ?? '');
@endphp
@if($bankRows)
  <div class="payment-bank-details">
    @foreach($bankRows as $label => $value)
      <div><span>{{ $label }}</span><strong>{{ $value }}</strong></div>
    @endforeach
  </div>
@else
  <p class="checkout-rates-empty">
    Our bank transfer details haven't been added yet.
    @if($contactEmail)
      Email <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a> with your order number and we'll send them to you.
    @else
      Please contact the store with your order number and we'll send them to you.
    @endif
  </p>
@endif
