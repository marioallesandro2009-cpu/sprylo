@extends('layouts.admin')

@section('title', 'Suspended Customers')
@section('page-title', 'Suspended Customers')

@push('styles')
  <style>
    @media (max-width: 767px) {
      .table-suspended thead { display: none; }
      .table-suspended, .table-suspended tbody, .table-suspended tr, .table-suspended td { display: block; width: 100%; }
      .table-suspended tr { border: 1px solid #eee; border-radius: 10px; margin-bottom: 14px; padding: 12px 14px; }
      .table-suspended td { border: none; padding: 6px 0; text-align: left !important; }
      .table-suspended td::before { content: attr(data-label); display: block; font-size: .68rem; text-transform: uppercase; letter-spacing: .5px; color: #999; font-weight: 600; margin-bottom: 2px; }
      .table-suspended td.text-end { text-align: left !important; }
    }
  </style>
@endpush

@section('content')

  <div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#suspendCustomerModal">
      <i class="fa-solid fa-hourglass-half"></i> Suspend Customer
    </button>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin table-suspended align-middle">
        <thead>
          <tr><th>Email</th><th>Phone</th><th>Status</th><th>Suspended By</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($suspensions as $suspension)
            <tr>
              <td data-label="Email">{{ $suspension->email ?? '—' }}</td>
              <td data-label="Phone">{{ $suspension->phone ?? '—' }}</td>
              <td data-label="Status">
                @if($suspension->isExpired())
                  <span class="badge bg-secondary">Expired</span>
                @else
                  <span class="badge bg-warning text-dark">
                    Suspended
                    @if($suspension->suspended_until) until {{ $suspension->suspended_until->format('d M Y H:i') }} @endif
                  </span>
                @endif
                @if($suspension->reason)
                  <div class="text-muted small mt-1">{{ $suspension->reason }}</div>
                @endif
              </td>
              <td data-label="Suspended By">{{ $suspension->creator?->name ?? '—' }}</td>
              <td data-label="Actions" class="text-end">
                <form action="{{ route('admin.suspended-customers.destroy', $suspension) }}" method="POST" class="d-inline" onsubmit="return confirm('Lift this suspension?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-success"><i class="fa-solid fa-check"></i> Lift Suspension</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No suspended customers.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="modal fade" id="suspendCustomerModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="{{ route('admin.suspended-customers.store') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Suspend Customer</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted small">Customers have no login on this site — checkout is matched by the email/phone they enter, so fill in at least one of the two below.</p>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" maxlength="255" placeholder="customer@example.com">
              @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control" maxlength="50" placeholder="08123456789">
              @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Reason (optional)</label>
              <input type="text" name="reason" class="form-control" maxlength="255" placeholder="e.g. Repeated fraudulent orders">
            </div>
            <div class="mb-1">
              <label class="form-label">Suspend until (optional)</label>
              <input type="datetime-local" name="suspended_until" class="form-control">
              <div class="form-text">Leave blank to suspend indefinitely until manually lifted.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-warning">Suspend Customer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection
