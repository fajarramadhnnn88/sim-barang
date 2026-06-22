@extends('layouts.app')
@section('title','Stock Balance')
@section('page-title','Stock Balance')
@section('breadcrumb')<li class="breadcrumb-item active">Stock Balance</li>@endsection
@section('content')

@if($barangMenipis->count() > 0)
<div class="callout callout-warning">
  <h5><i class="fas fa-exclamation-triangle mr-2"></i>{{ $barangMenipis->count() }} Barang Perlu Perhatian</h5>
  <p class="mb-0" style="font-size:13px">
    @foreach($barangMenipis->take(5) as $b)
      <span class="badge badge-warning mr-1">{{ $b->nama_barang }} ({{ $b->stockBalance->stok_tersedia ?? 0 }} {{ $b->satuan }})</span>
    @endforeach
    @if($barangMenipis->count() > 5)
      <span class="text-muted">+{{ $barangMenipis->count() - 5 }} lainnya</span>
    @endif
  </p>
</div>
@endif

<div class="card card-outline card-primary">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title"><i class="fas fa-layer-group mr-2"></i>Stock Balance</h3>
    @canRole(['superadmin'])
    <form action="{{ route('stock.rekalkuasi.semua') }}" method="POST"
      onsubmit="return confirm('Hitung ulang semua stok berdasarkan data riil barang masuk & keluar? Data stok yatim juga akan dibersihkan.')">
      @csrf
      <button class="btn btn-secondary btn-sm">
        <i class="fas fa-sync-alt mr-1"></i>Rekalkulasi Semua
      </button>
    </form>
    @endCanRole
  </div>

  <div class="card-body">
    <form method="GET" class="mb-3">
      <div class="row">
        <div class="col-md-4">
          <input type="text" name="search" class="form-control form-control-sm"
            placeholder="Cari kode / nama barang..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
          <select name="kategori_id" class="form-control form-control-sm">
            <option value="">-- Semua Kategori --</option>
            @foreach($kategoris as $k)
              <option value="{{ $k->id }}" {{ request('kategori_id')==$k->id?'selected':'' }}>{{ $k->nama_kategori }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <button class="btn btn-info btn-sm mr-1"><i class="fas fa-search mr-1"></i>Cari</button>
          <a href="{{ route('stock.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        </div>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead class="thead-light">
          <tr>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Satuan</th>
            <th class="text-center">Stok Masuk</th>
            <th class="text-center">Stok Keluar</th>
            <th class="text-center">Stok Tersedia</th>
            <th class="text-center">Min</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($stockBalances as $s)
            {{--
              Defense-in-depth: meski controller sudah menyaring dengan
              whereHas('barang'), kita tetap cek null di sini supaya
              halaman TIDAK PERNAH crash walau suatu saat ada data
              tidak konsisten lagi. Baris dengan barang null dilewati
              (tidak ditampilkan) alih-alih menyebabkan error.
            --}}
            @continue(!$s->barang)
            @php $b = $s->barang; @endphp
            <tr>
              <td><code style="font-size:11px;background:#F1F5F9;padding:2px 6px;border-radius:4px">{{ $b->kode_barang }}</code></td>
              <td>
                <strong style="font-size:13px">{{ $b->nama_barang }}</strong>
                @if($b->merk)<br><small class="text-muted">{{ $b->merk }}</small>@endif
              </td>
              <td>{{ $b->kategori->nama_kategori ?? '-' }}</td>
              <td>{{ $b->satuan }}</td>
              <td class="text-center">{{ $s->stok_masuk }}</td>
              <td class="text-center">{{ $s->stok_keluar }}</td>
              <td class="text-center">
                <strong style="font-size:14px;color:{{ $s->stok_tersedia <= 0 ? '#DC2626' : ($s->stok_tersedia <= $b->stok_minimum ? '#D97706' : '#059669') }}">
                  {{ $s->stok_tersedia }}
                </strong>
              </td>
              <td class="text-center">{{ $b->stok_minimum }}</td>
              <td>
                @if($s->stok_tersedia <= 0)
                  <span class="badge badge-danger">Habis</span>
                @elseif($s->stok_tersedia <= $b->stok_minimum)
                  <span class="badge badge-warning">Menipis</span>
                @else
                  <span class="badge badge-success">Aman</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data stok</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{ $stockBalances->links() }}
  </div>
</div>
@endsection