@extends('layouts.admin')

@section('title', 'Audit Log')
@section('page-title', 'Audit Log')

@push('styles')
  <style>
    .aud-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 14px; margin-bottom: 18px; }
    .aud-card { display: block; background: var(--paper); border: 1px solid var(--line); border-radius: 14px; padding: 16px; text-decoration: none; color: inherit; transition: border-color .15s, box-shadow .15s; }
    .aud-card:hover { border-color: var(--brand-line); box-shadow: 0 8px 20px rgba(79, 70, 229, .08); color: inherit; }
    .aud-card.is-active { border-color: var(--brand); box-shadow: var(--ring); }
    .aud-card .who { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .aud-card .av { width: 36px; height: 36px; border-radius: 50%; background: var(--brand-soft); color: var(--brand-deep); display: flex; align-items: center; justify-content: center; font-family: var(--ff-display); font-weight: 700; flex-shrink: 0; }
    .aud-card .nm { font-weight: 700; font-size: .88rem; line-height: 1.2; }
    .aud-card .em { font-size: .72rem; color: var(--muted); word-break: break-all; }
    .aud-card dl { margin: 0; display: grid; grid-template-columns: auto 1fr; gap: 4px 10px; font-size: .78rem; }
    .aud-card dt { color: var(--muted); font-weight: 500; }
    .aud-card dd { margin: 0; text-align: right; font-weight: 600; }
    .aud-card dd small { display: block; color: var(--muted); font-weight: 400; }
    .aud-alert { display: flex; align-items: center; gap: 10px; background: var(--warn-soft); color: var(--warn-ink); border-radius: 12px; padding: 10px 14px; font-size: .85rem; margin-bottom: 14px; }
    .aud-filters .form-control, .aud-filters .form-select { font-size: .84rem; }
    .aud-desc { max-width: 560px; }
    .aud-type { display: inline-block; font-size: .68rem; font-weight: 600; color: var(--brand-deep); background: var(--brand-soft); border-radius: 999px; padding: 1px 8px; margin-right: 6px; white-space: nowrap; }
    .aud-changes { margin-top: 6px; font-size: .78rem; }
    .aud-changes summary { cursor: pointer; color: var(--brand); font-weight: 600; }
    .aud-changes table { margin-top: 6px; width: 100%; border: 1px solid var(--line); border-radius: 8px; overflow: hidden; }
    .aud-changes td { padding: 4px 10px; border-top: 1px solid var(--line); vertical-align: top; word-break: break-word; }
    .aud-changes tr:first-child td { border-top: 0; }
    .aud-changes .old { color: var(--bad-ink); text-decoration: line-through; }
    .aud-changes .new { color: var(--ok-ink); font-weight: 600; }
  </style>
@endpush

@section('content')
  @php
    $ago = fn ($time) => $time ? $time->diffForHumans() : null;
    $stamp = fn ($time) => $time ? $time->format('d M Y, H:i') : null;
  @endphp

  <p class="text-muted small" style="max-width:680px">
    A read-only record of every sign-in and every change made in the admin panel. Entries can't be edited or deleted. Times are in WIB.
  </p>

  @if($failedToday > 0)
    <div class="aud-alert"><i class="fa-solid fa-triangle-exclamation"></i> {{ $failedToday }} failed admin sign-in {{ \Illuminate\Support\Str::plural('attempt', $failedToday) }} in the last 24 hours.
      <a href="{{ route('admin.audit-logs.index', ['action' => 'login_failed']) }}" class="ms-auto fw-semibold">Review</a></div>
  @endif

  <div class="aud-cards">
    @foreach($admins as $admin)
      <a href="{{ route('admin.audit-logs.index', ['admin' => $admin->id]) }}" class="aud-card {{ (int) request('admin') === $admin->id ? 'is-active' : '' }}">
        <div class="who">
          <div class="av">{{ strtoupper(mb_substr($admin->name, 0, 1)) }}</div>
          <div class="min-w-0">
            <div class="nm">{{ $admin->name }}
              <span class="badge bg-{{ $admin->isSuperAdmin() ? 'primary' : 'secondary' }} ms-1">{{ $admin->isSuperAdmin() ? 'Super admin' : 'Admin' }}</span>
              @unless($admin->isActive())<span class="badge bg-danger ms-1">Suspended</span>@endunless
            </div>
            <div class="em">{{ $admin->email }}</div>
          </div>
        </div>
        <dl>
          <dt>Last login</dt>
          <dd>@if($lastLogin->has($admin->id)){{ $stamp($lastLogin[$admin->id]) }}<small>{{ $ago($lastLogin[$admin->id]) }}</small>@else<span class="text-muted fw-normal">Never</span>@endif</dd>
          <dt>Last change</dt>
          <dd>@if($lastChange->has($admin->id)){{ $stamp($lastChange[$admin->id]) }}<small>{{ $ago($lastChange[$admin->id]) }}</small>@else<span class="text-muted fw-normal">—</span>@endif</dd>
          <dt>Changes (7 days)</dt>
          <dd>{{ $weekChanges[$admin->id] ?? 0 }}</dd>
        </dl>
      </a>
    @endforeach
  </div>

  <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="card-admin aud-filters mb-3">
    <div class="row g-2 align-items-end">
      <div class="col-lg-2 col-md-4">
        <label class="form-label small mb-1">Admin</label>
        <select name="admin" class="form-select form-select-sm">
          <option value="">All admins</option>
          @foreach($admins as $admin)
            <option value="{{ $admin->id }}" @selected((int) request('admin') === $admin->id)>{{ $admin->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-2 col-md-4">
        <label class="form-label small mb-1">Action</label>
        <select name="action" class="form-select form-select-sm">
          <option value="">All actions</option>
          @foreach($actions as $key => $label)
            <option value="{{ $key }}" @selected(request('action') === $key)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-2 col-md-4">
        <label class="form-label small mb-1">Item type</label>
        <select name="type" class="form-select form-select-sm">
          <option value="">All types</option>
          @foreach($types as $type)
            <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-2 col-md-3 col-6">
        <label class="form-label small mb-1">From</label>
        <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm" />
      </div>
      <div class="col-lg-2 col-md-3 col-6">
        <label class="form-label small mb-1">To</label>
        <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm" />
      </div>
      <div class="col-lg-2 col-md-6">
        <label class="form-label small mb-1">Search</label>
        <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Item, text, IP…" />
      </div>
    </div>
    <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
      <button type="submit" class="btn btn-sm btn-admin-primary">Apply filters</button>
      @if(request()->hasAny(['admin', 'action', 'type', 'from', 'to', 'q']) && collect(request()->only(['admin', 'action', 'type', 'from', 'to', 'q']))->filter()->isNotEmpty())
        <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
      @endif
      <span class="text-muted small ms-auto">{{ number_format($logs->total()) }} {{ \Illuminate\Support\Str::plural('entry', $logs->total()) }}</span>
      <a href="{{ route('admin.audit-logs.export', request()->only(['admin', 'action', 'type', 'from', 'to', 'q'])) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-download me-1"></i> Export CSV</a>
    </div>
  </form>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th style="width:150px">When</th><th style="width:200px">Admin</th><th style="width:110px">Action</th><th>What happened</th><th style="width:120px">IP</th></tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
            @php $when = $log->created_at->copy()->timezone($tz); @endphp
            <tr>
              <td class="small text-nowrap">{{ $when->format('d M Y, H:i:s') }}<div class="text-muted">{{ $when->diffForHumans() }}</div></td>
              <td class="small">
                @if($log->user_name || $log->user_email)
                  <div class="fw-semibold">{{ $log->user_name ?? 'Unknown account' }}</div>
                  <div class="text-muted" style="word-break:break-all">{{ $log->user_email }}</div>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td><span class="badge bg-{{ $log->action_color }}">{{ $log->action_label }}</span></td>
              <td class="aud-desc small">
                @if($log->subject_type)<span class="aud-type">{{ $log->subject_type }}</span>@endif{{ $log->description }}
                @if(!empty($log->changes))
                  <details class="aud-changes">
                    <summary>{{ count($log->changes) }} {{ \Illuminate\Support\Str::plural('field', count($log->changes)) }} changed</summary>
                    <table>
                      @foreach($log->changes as $field => $pair)
                        <tr>
                          <td class="text-muted" style="width:150px">{{ \Illuminate\Support\Str::headline($field) }}</td>
                          <td><span class="old">{{ ($pair[0] ?? '') === '' || $pair[0] === null ? '—' : (is_bool($pair[0]) ? ($pair[0] ? 'yes' : 'no') : $pair[0]) }}</span>
                              → <span class="new">{{ ($pair[1] ?? '') === '' || $pair[1] === null ? '—' : (is_bool($pair[1]) ? ($pair[1] ? 'yes' : 'no') : $pair[1]) }}</span></td>
                        </tr>
                      @endforeach
                    </table>
                  </details>
                @endif
              </td>
              <td class="small text-muted" title="{{ $log->user_agent }}">{{ $log->ip_address ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No entries match these filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($logs->hasPages())
    <div class="mt-3">{{ $logs->links('pagination::bootstrap-5') }}</div>
  @endif
@endsection
