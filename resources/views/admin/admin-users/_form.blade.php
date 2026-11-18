@php $adminUser = $adminUser ?? null; @endphp

<div class="mb-3">
  <label class="form-label small fw-semibold">Name</label>
  <input type="text" name="name" class="form-control" maxlength="255" value="{{ old('name', $adminUser->name ?? '') }}" required />
  @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">Email (dipakai untuk login)</label>
  <input type="email" name="email" class="form-control" maxlength="255" value="{{ old('email', $adminUser->email ?? '') }}" required />
  @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">
    Password
    @if($adminUser)<span class="text-muted fw-normal">(kosongkan jika tidak diubah)</span>@endif
  </label>
  <input type="password" name="password" class="form-control" minlength="8" autocomplete="new-password" @if(! $adminUser) required @endif />
  @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">Confirm password</label>
  <input type="password" name="password_confirmation" class="form-control" minlength="8" autocomplete="new-password" @if(! $adminUser) required @endif />
</div>