@extends('layouts.admin')

@section('title', 'Automatic Vouchers')
@section('page-title', 'Automatic Vouchers')

@section('content')
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <p class="text-muted small mb-0" style="max-width:660px">
      When one of these events happens, the customer is handed a personal, single-use voucher automatically — emailed to them and shown on screen.
      Issued vouchers appear in <a href="{{ route('admin.promo-codes.index') }}">Promo Codes</a> marked “Auto”.
    </p>
    <a href="{{ route('admin.voucher-rules.create') }}" class="btn btn-admin-primary"><i class="fa-solid fa-plus me-1"></i> Add Rule</a>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead><tr><th>Name</th><th>When</th><th>Voucher</th><th>Valid for</th><th>Issued / used</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
          @forelse($rules as $rule)
            <tr>
              <td class="fw-semibold">{{ $rule->name }}</td>
              <td class="small">{{ $rule->trigger_label }}</td>
              <td class="small"><span class="fw-semibold">{{ $rule->value_label }}</span>@if($rule->min_subtotal)<div class="text-muted">min. {{ rupiah($rule->min_subtotal) }}</div>@endif</td>
              <td class="small">{{ $rule->valid_days }} days</td>
              <td class="small">{{ $rule->issued_count }} / {{ $rule->used_vouchers }}</td>
              <td><span class="badge bg-{{ $rule->is_active ? 'success' : 'secondary' }}">{{ $rule->is_active ? 'Active' : 'Off' }}</span></td>
              <td class="text-end text-nowrap">
                <a href="{{ route('admin.voucher-rules.edit', $rule) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
                <form action="{{ route('admin.voucher-rules.destroy', $rule) }}" method="POST" class="d-inline" data-confirm="Delete this rule? Vouchers already issued keep working.">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No rules yet — add one to start rewarding customers automatically.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
