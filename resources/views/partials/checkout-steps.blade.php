@php
  $current = $current ?? 1;
  $labels = ['Cart', 'Checkout', 'Confirmation'];
@endphp
<ol class="co-steps" aria-label="Checkout progress">
  @foreach($labels as $i => $label)
    @php $n = $i + 1; @endphp
    <li class="co-step @if($n < $current) is-done @endif @if($n === $current) is-current @endif" @if($n === $current) aria-current="step" @endif>
      <span class="co-step__dot" aria-hidden="true">@if($n < $current)&#10003;@else{{ $n }}@endif</span>
      <span class="co-step__label">{{ $label }}</span>
    </li>
  @endforeach
</ol>
