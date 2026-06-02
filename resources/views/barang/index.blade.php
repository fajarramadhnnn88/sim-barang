@extends('layouts.app')
@section('title','Data Barang')
@section('page-title','Data Barang')
@section('breadcrumb')<li class="breadcrumb-item active">Barang</li>@endsection
@section('content')
<div class="card card-outline card-primary">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title"><i class="fas fa-box mr-2"></i>Daftar Barang</h3>
    <a href="{{ route('barang.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i>Tambah</a>
  </div>
  <div class="card-body">
    <form method="GET" class="mb-3">
      <div class="row">
        <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Cari kode / nama / merk..." value="{{ request('search') }}"></div>
        <div class="col-md-3">
          <select name="kategori_id" class="form-control form-control-sm">
            <option value="">Semua Kategori</option>
            @foreach($kategoris as $k)<option value="{{ $k->id }}" {{ request('kategori_id')==$k->id?'selected':'' }}>{{ $k->nama_kategori }}</option>@endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select name="status" class="form-control form-control-sm">
            <option value="">Semua</option>
            <option value="aktif" {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
            <option value="nonaktif" {{ request('status')=='nonaktif'?'selected':'' }}>Nonaktif</option>
          </select>
        </div>
        <div class="col-md-3">
          <button class="btn btn-info btn-sm mr-1"><i class="fas fa-search mr-1"></i>Filter</button>
          <a href="{{ route('barang.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        </div>
      </div>
    </form>
    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead class="thead-light">
          <tr><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Satuan</th><th>Harga</th><th class="text-center">Stok</th><th class="text-center">Min</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          @forelse($barangs as $b)
          <tr>
            <td><code style="background:#F1F5F9;padding:2px 6px;border-radius:4px;font-size:11px">{{ $b->kode_barang }}</code></td>
            <td><strong style="font-size:13px">{{ $b->nama_barang }}</strong>@if($b->merk)<br><small class="text-muted">{{ $b->merk }}</small>@endif</td>
            <td>{{ $b->kategori->nama_kategori }}</td>
            <td>{{ $b->satuan }}</td>
            <td>{{ $b->harga_format }}</td>
            <td class="text-center">
              <span class="badge {{ $b->stok_tersedia<=0?'badge-danger':($b->is_stok_minimum?'badge-warning':'badge-success') }}">{{ $b->stok_tersedia }}</span>
            </td>
            <td class="text-center">{{ $b->stok_minimum }}</td>
            <td><span class="badge {{ $b->is_active?'badge-success':'badge-secondary' }}">{{ $b->is_active?'Aktif':'Nonaktif' }}</span></td>
            <td>
              <a href="{{ route('barang.show',$b) }}" class="btn btn-xs btn-info" title="Detail"><i class="fas fa-eye"></i></a>
              <a href="{{ route('barang.edit',$b) }}" class="btn btn-xs btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
              <form action="{{ route('barang.destroy',$b) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus barang ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data barang</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-2">
      <small class="text-muted">Total: {{ $barangs->total() }} barang</small>
      {{ $barangs->links() }}
    </div>
  </div>
</div>
@endsection
