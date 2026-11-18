@extends('layouts.admin')

@section('title', $noun.'s')
@section('page-title', $noun.'s')

@section('content')

  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <p class="text-muted small mb-0" style="max-width:640px">
      @if($flash)
        Limited-time offers with a hard end time and a fixed number of units at the sale price. The price returns to normal when time or quota runs out.
      @else
        Percent or Rupiah discounts that switch on and off by themselves on the schedule you set. Apply them to one product, a category or a brand.
      @endif
    </p>
    <a href="{{ route($routePrefix.'.create') }}" class="btn btn-admin-primary"><i class="fa-solid fa-plus me-1"></i> Add {{ $noun }}</a>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr>
            <th>Name</th><th>Applies to</th><th>Discount</th><th>Schedule (WIB)</th>
            @if($flash)<th>Sold / quota</th>@endif
            <th>Status</th><th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($discounts as $discount)
            @php
              $badge = ['live' => ['success', 'Live'], 'scheduled' => ['primary', 'Scheduled'], 'ended' => ['secondary', 'Ended'], 'sold_out' => ['warning', 'Sold out'], 'inactive' => ['secondary', 'Off']][$discount->status];
              $tz = \App\Models\Discount::DISPLAY_TZ;
            @endphp
            <tr>
              <td class="fw-semibold">{{ $discount->name }}</td>
              <td class="small">{{ $discount->target_label }}</td>
              <td class="fw-semibold">{{ $discount->value_label }}</td>
              <td class="text-muted small">
                {{ $discount->starts_at ? $discount->starts_at->copy()->timezone($tz)->format('d M Y, H:i') : 'Immediately' }}<br>
                → {{ $discount->ends_at ? $discount->ends_at->copy()->timezone($tz)->format('d M Y, H:i') : 'No end' }}
              </td>
              @if($flash)
                <td class="small">
                  <span class="fw-semibold">{{ $discount->sold }}</span> / {{ $discount->quota }}
                  <div class="progress mt-1" style="height:5px;width:90px"><div class="progress-bar" style="width: {{ $discount->quota ? min(100, round($discount->sold / $discount->quota * 100)) : 0 }}%"></div></div>
                </td>
              @endif
              <td><span class="badge bg-{{ $badge[0] }}">{{ $badge[1] }}</span></td>
              <td class="text-end text-nowrap">
                <a href="{{ route($routePrefix.'.edit', $discount) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
                <form action="{{ route($routePrefix.'.destroy', $discount) }}" method="POST" class="d-inline" data-confirm="Delete this {{ strtolower($noun) }}?">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="{{ $flash ? 7 : 6 }}" class="text-center text-muted py-4">No {{ strtolower($noun) }}s yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
