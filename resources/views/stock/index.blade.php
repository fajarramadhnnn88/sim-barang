@extends('layouts.app')
@section('title','Stock Balance')
@section('page-title','Stock Balance')
@section('breadcrumb')<li class="breadcrumb-item active">Stock</li>@endsection
@section('content')
@if($alertStok->count())
<div class="alert alert-warning alert-dismissible fade show">
  <i class="fas fa-exclamation-triangle mr-2"></i><strong>{{ $alertStok->count() }} barang</strong> memerlukan perhatian!
  <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif
<div class="card card-outline card-primary">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title"><i class="fas fa-layer-group mr-2"></i>Daftar Stok Barang</h3>
    <div>
      <a href="?filter=menipis" class="btn btn-sm btn-warning mr-1">Menipis</a>
      <a href="?filter=habis" class="btn btn-sm btn-danger mr-1">Habis</a>
      <a href="{{ route('stock.index') }}" class="btn btn-sm btn-secondary">Semua</a>
    </div>
  </div>
  <div class="card-body">
    <form method="GET" class="mb-3">
      <input type="hidden" name="filter" value="{{ request('filter') }}">
      <div class="row">
        <div class="col-md-5"><input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama / kode barang..." value="{{ request('search') }}"></div>
        <div class="col-md-3">
          <button class="btn btn-info btn-sm mr-1"><i class="fas fa-search"></i> Cari</button>
          <a href="{{ route('stock.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        </div>
      </div>
    </form>
    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead class="thead-light">
          <tr><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Satuan</th><th class="text-center">Masuk</th><th class="text-center">Keluar</th><th class="text-center">Tersedia</th><th class="text-center">Minimum</th><th class="text-center">Status</th><th>Update</th></tr>
        </thead>
        <tbody>
          @forelse($stocks as $s)
          @php $b=$s->barang; @endphp
          <tr class="{{ $s->stok_tersedia<=0?'table-danger':($s->stok_tersedia<=$b->stok_minimum?'table-warning':'') }}">
            <td><code style="font-size:11px;background:#F1F5F9;padding:2px 6px;border-radius:4px">{{ $b->kode_barang }}</code></td>
            <td><a href="{{ route('barang.show',$b) }}" style="font-weight:600;color:#1E293B">{{ $b->nama_barang }}</a></td>
            <td><small>{{ $b->kategori->nama_kategori }}</small></td>
            <td>{{ $b->satuan }}</td>
            <td class="text-center font-weight-bold text-success">{{ $s->stok_masuk }}</td>
            <td class="text-center font-weight-bold text-danger">{{ $s->stok_keluar }}</td>
            <td class="text-center"><strong style="font-size:15px">{{ $s->stok_tersedia }}</strong></td>
            <td class="text-center text-muted">{{ $b->stok_minimum }}</td>
            <td class="text-center"><span class="badge {{ $s->status_badge }}">{{ $s->status_stok }}</span></td>
            <td><small class="text-muted">{{ $s->last_updated?$s->last_updated->diffForHumans():'-' }}</small></td>
          </tr>
          @empty<tr><td colspan="10" class="text-center text-muted py-4">Tidak ada data</td></tr>@endforelse
        </tbody>
      </table>
    </div>
    {{ $stocks->links() }}
  </div>
</div>
@endsection
