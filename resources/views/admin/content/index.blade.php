@extends('layouts.admin')

@section('title', 'Page Content')
@section('page-title', 'Page Content')

@section('content')

  <div class="card-admin mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div class="small text-muted">
      Edit teks &amp; gambar di setiap halaman. Kolom kosong = pakai teks bawaan. Habis menambah <code>cms('halaman.bagian.nama', 'Teks')</code> di Blade, klik <strong>Scan view</strong>.
    </div>
    <form action="{{ route('admin.content.scan') }}" method="POST" class="d-flex align-items-center gap-2">
      @csrf
      <input type="hidden" name="page" value="{{ $active }}" />
      <div class="form-check small mb-0">
        <input class="form-check-input" type="checkbox" name="prune" value="1" id="prune" />
        <label class="form-check-label" for="prune">Hapus key yang tidak terpakai</label>
      </div>
      <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-rotate"></i> Scan view</button>
    </form>
  </div>

  @if($groups->isEmpty())
    <div class="card-admin text-center text-muted">Belum ada konten terdaftar. Klik <strong>Scan view</strong> di atas.</div>
  @else
    <ul class="nav nav-pills mb-3 gap-1">
      @foreach($groups as $page => $rows)
        <li class="nav-item">
          <a class="nav-link {{ $page === $active ? 'active' : '' }}" style="{{ $page === $active ? 'background:var(--indigo)' : '' }}"
             href="{{ route('admin.content.index', ['page' => $page]) }}">{{ \Illuminate\Support\Str::headline($page) }} <span class="badge bg-light text-dark ms-1">{{ $rows->count() }}</span></a>
        </li>
      @endforeach
    </ul>

    <form action="{{ route('admin.content.update') }}" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      <input type="hidden" name="page" value="{{ $active }}" />

      <div class="card-admin mb-3">
        <div class="row g-3">
          @foreach($groups[$active] as $row)
            <div class="col-md-{{ $row->type === 'textarea' ? 12 : 6 }}">
              <label class="form-label small fw-semibold">{{ $row->label }} <code class="text-muted ms-1">{{ $row->key }}</code></label>

              @if($row->type === 'textarea')
                <textarea name="content[{{ $row->id }}]" rows="4" class="form-control"
                          placeholder="{{ $row->default_value }}">{{ old('content.'.$row->id, $row->value) }}</textarea>
              @elseif($row->type === 'image')
                @php $preview = $row->value ?: $row->default_value; @endphp
                @if($preview)
                  <div class="mb-2"><img src="{{ $preview }}" alt="" style="max-height:80px; border-radius:8px; border:1px solid #eee" /></div>
                @endif
                <input type="file" name="files[{{ $row->id }}]" accept="image/*" class="form-control mb-2" />
                <input type="text" name="content[{{ $row->id }}]" class="form-control" placeholder="atau tempel URL gambar"
                       value="{{ old('content.'.$row->id, $row->value) }}" />
              @else
                <input type="text" name="content[{{ $row->id }}]" class="form-control"
                       placeholder="{{ $row->default_value }}" value="{{ old('content.'.$row->id, $row->value) }}" />
              @endif

              <div class="form-text d-flex justify-content-between align-items-center">
                <span>Bawaan: {{ \Illuminate\Support\Str::limit($row->default_value, 70) }}</span>
                @if($row->value !== null)
                  <label class="mb-0"><input type="checkbox" name="reset[{{ $row->id }}]" value="1" class="form-check-input me-1" />Reset</label>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <button type="submit" class="btn btn-admin-primary">Simpan {{ \Illuminate\Support\Str::headline($active) }}</button>
    </form>
  @endif

@endsection