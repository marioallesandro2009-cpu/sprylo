@extends('layouts.admin')

@section('title', 'Delivery Schedule')
@section('page-title', 'Delivery Schedule')

@push('styles')
  <style>
    .ds-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 14px; margin-bottom: 18px; }
    .ds-stat { background: var(--paper); border: 1px solid var(--line); border-radius: 14px; padding: 16px 18px; }
    .ds-stat .n { font-family: var(--ff-display); font-size: 1.7rem; font-weight: 800; line-height: 1.1; }
    .ds-stat .l { font-size: .78rem; color: var(--muted); margin-top: 2px; }
    .ds-stat.is-warn { background: var(--warn-soft); border-color: #FDE68A; }
    .ds-stat.is-warn .n { color: var(--warn-ink); }
    .ds-day { background: var(--paper); border: 1px solid var(--line); border-radius: 16px; margin-bottom: 14px; overflow: hidden; }
    .ds-day.is-today { border-color: var(--brand); box-shadow: var(--ring); }
    .ds-day__head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; padding: 14px 18px; background: var(--brand-soft); border-bottom: 1px solid var(--line); }
    .ds-day__head h2 { font-size: 1rem; font-weight: 800; margin: 0; }
    .ds-day__head .count { margin-left: auto; font-size: .8rem; color: var(--muted); font-weight: 600; }
    .ds-slots { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); }
    .ds-slot { padding: 14px 16px; border-left: 1px solid var(--line); min-width: 0; }
    .ds-slot:first-child { border-left: 0; }
    .ds-slot__head { display: flex; justify-content: space-between; align-items: baseline; gap: 8px; margin-bottom: 6px; }
    .ds-slot__head strong { font-size: .86rem; }
    .ds-slot__head span { font-size: .72rem; color: var(--muted); }
    .ds-bar { height: 5px; border-radius: 99px; background: var(--line); overflow: hidden; margin-bottom: 10px; }
    .ds-bar i { display: block; height: 100%; background: var(--brand); border-radius: 99px; }
    .ds-bar.is-full i { background: #DC2626; }
    .ds-order { display: block; padding: 8px 10px; border: 1px solid var(--line); border-radius: 10px; margin-bottom: 6px; text-decoration: none; color: inherit; transition: border-color .15s, background .15s; }
    .ds-order:hover { border-color: var(--brand-line); background: var(--brand-soft); color: inherit; }
    .ds-order .top { display: flex; justify-content: space-between; gap: 8px; align-items: center; }
    .ds-order .no { font-weight: 700; font-size: .82rem; }
    .ds-order .who { font-size: .78rem; color: var(--ink-soft); }
    .ds-order .meta { font-size: .72rem; color: var(--muted); }
    .ds-empty { font-size: .8rem; color: var(--faint); padding: 4px 0; }
    .ds-late { background: var(--warn-soft); border: 1px solid #FDE68A; border-radius: 14px; padding: 14px 18px; margin-bottom: 18px; }
    .ds-late h2 { font-size: .95rem; font-weight: 800; color: var(--warn-ink); margin: 0 0 8px; }
    .ds-late a { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; padding: 6px 0; border-top: 1px solid rgba(180, 83, 9, .15); text-decoration: none; color: var(--ink); font-size: .84rem; }
    .ds-late a:first-of-type { border-top: 0; }
    .ds-late .when { margin-left: auto; color: var(--warn-ink); font-weight: 600; font-size: .78rem; }
  </style>
@endpush

@section('content')
  @php
    $statusText = fn ($s) => ucwords(str_replace('_', ' ', $s));
    $fmtDay = fn ($d) => $d->translatedFormat('l, d M Y');
  @endphp

  <form method="GET" class="d-flex flex-wrap gap-2 align-items-end mb-3">
    <div>
      <label class="form-label small mb-1">From</label>
      <input type="date" name="from" value="{{ $from->toDateString() }}" class="form-control form-control-sm" />
    </div>
    <div>
      <label class="form-label small mb-1">Range</label>
      <select name="days" class="form-select form-select-sm" onchange="this.form.submit()">
        @foreach([7 => 'Next 7 days', 14 => 'Next 14 days', 30 => 'Next 30 days'] as $value => $label)
          <option value="{{ $value }}" @selected($days === $value)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn btn-sm btn-admin-primary">Show</button>
    @if($from->toDateString() !== $today->toDateString() || $days !== 14)
      <a href="{{ route('admin.delivery-schedule.index') }}" class="btn btn-sm btn-outline-secondary">Today</a>
    @endif
    <span class="text-muted small ms-auto">Times are WIB · each window takes {{ $capacity }} orders a day
      <a href="{{ route('admin.settings.index') }}" class="ms-1">change</a></span>
  </form>

  <div class="ds-stats">
    <div class="ds-stat"><div class="n">{{ $stats['today'] }}</div><div class="l">Deliveries today</div></div>
    <div class="ds-stat"><div class="n">{{ $stats['tomorrow'] }}</div><div class="l">Deliveries tomorrow</div></div>
    <div class="ds-stat"><div class="n">{{ $stats['range'] }}</div><div class="l">Scheduled {{ $from->format('d M') }} – {{ $to->format('d M') }}</div></div>
    <div class="ds-stat {{ $stats['overdue'] > 0 ? 'is-warn' : '' }}"><div class="n">{{ $stats['overdue'] }}</div><div class="l">Running late (date passed, not shipped)</div></div>
  </div>

  @if($overdue->isNotEmpty())
    <div class="ds-late">
      <h2><i class="fa-solid fa-triangle-exclamation me-1"></i> Running late</h2>
      @foreach($overdue as $order)
        <a href="{{ route('admin.orders.show', $order) }}">
          <strong>{{ $order->order_number }}</strong>
          <span>{{ $order->name }} · {{ $order->city }}</span>
          <span class="badge {{ $order->status_badge_class }}">{{ $statusText($order->status) }}</span>
          <span class="when">was due {{ $order->delivery_schedule }}</span>
        </a>
      @endforeach
    </div>
  @endif

  @forelse($scheduled as $date => $orders)
    @php
      $day = \Carbon\Carbon::parse($date, 'Asia/Jakarta');
      $isToday = $day->isSameDay($today);
      $bySlot = $orders->groupBy(fn ($o) => $o->requested_delivery_slot ?: 'any');
    @endphp
    <section class="ds-day {{ $isToday ? 'is-today' : '' }}">
      <div class="ds-day__head">
        <h2>{{ $fmtDay($day) }}</h2>
        @if($isToday)<span class="badge bg-primary">Today</span>@elseif($day->isSameDay($today->copy()->addDay()))<span class="badge bg-info">Tomorrow</span>@endif
        <span class="count">{{ $orders->count() }} {{ \Illuminate\Support\Str::plural('delivery', $orders->count()) }}</span>
      </div>
      <div class="ds-slots">
        @foreach($slots as $key => $slot)
          @php $list = $bySlot[$key] ?? collect(); $pct = min(100, round($list->count() / $capacity * 100)); @endphp
          <div class="ds-slot">
            <div class="ds-slot__head"><strong>{{ $slot['label'] }}</strong><span>{{ $slot['time'] }} · {{ $list->count() }}/{{ $capacity }}</span></div>
            <div class="ds-bar {{ $list->count() >= $capacity ? 'is-full' : '' }}"><i style="width: {{ $pct }}%"></i></div>
            @forelse($list as $order)
              <a href="{{ route('admin.orders.show', $order) }}" class="ds-order">
                <div class="top"><span class="no">{{ $order->order_number }}</span><span class="badge {{ $order->status_badge_class }}">{{ $statusText($order->status) }}</span></div>
                <div class="who">{{ $order->name }}</div>
                <div class="meta">{{ $order->city }}@if($order->courier_name) · {{ $order->courier_name }}@endif</div>
              </a>
            @empty
              <div class="ds-empty">No orders</div>
            @endforelse
          </div>
        @endforeach
        @if(isset($bySlot['any']))
          <div class="ds-slot">
            <div class="ds-slot__head"><strong>No time chosen</strong><span>{{ $bySlot['any']->count() }}</span></div>
            @foreach($bySlot['any'] as $order)
              <a href="{{ route('admin.orders.show', $order) }}" class="ds-order">
                <div class="top"><span class="no">{{ $order->order_number }}</span><span class="badge {{ $order->status_badge_class }}">{{ $statusText($order->status) }}</span></div>
                <div class="who">{{ $order->name }}</div>
                <div class="meta">{{ $order->city }}</div>
              </a>
            @endforeach
          </div>
        @endif
      </div>
    </section>
  @empty
    <div class="card-admin text-center text-muted py-5">No deliveries scheduled between {{ $from->format('d M Y') }} and {{ $to->format('d M Y') }}.</div>
  @endforelse
@endsection
