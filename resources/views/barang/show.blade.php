@extends('layouts.app')
@section('title',$barang->nama_barang)
@section('page-title','Detail Barang')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Barang</a></li>
<li class="breadcrumb-item active">{{ $barang->nama_barang }}</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-4">
    <div class="card card-outline card-primary">
      <div class="card-body text-center">
        <img src="{{ $barang->foto_url }}" class="img-fluid rounded mb-3" style="max-height:160px;object-fit:contain">
        <h5 style="font-weight:700;color:#1E293B">{{ $barang->nama_barang }}</h5>
        <code style="background:#F1F5F9;padding:3px 10px;border-radius:6px;font-size:12px">{{ $barang->kode_barang }}</code>
        <div class="mt-2">
          <span class="badge badge-info">{{ $barang->kategori->nama_kategori }}</span>
          <span class="badge {{ $barang->is_active?'badge-success':'badge-secondary' }} ml-1">{{ $barang->is_active?'Aktif':'Nonaktif' }}</span>
        </div>
        <hr>
        <dl class="row text-left mb-0" style="font-size:13px">
          <dt class="col-5">Satuan</dt><dd class="col-7">{{ $barang->satuan }}</dd>
          <dt class="col-5">Merk</dt><dd class="col-7">{{ $barang->merk??'-' }}</dd>
          <dt class="col-5">Harga</dt><dd class="col-7 font-weight-bold">{{ $barang->harga_format }}</dd>
          <dt class="col-5">Lokasi</dt><dd class="col-7">{{ $barang->lokasi_penyimpanan??'-' }}</dd>
        </dl>
      </div>
      <div class="card-footer">
        <a href="{{ route('barang.edit',$barang) }}" class="btn btn-warning btn-block btn-sm"><i class="fas fa-edit mr-1"></i>Edit</a>
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <div class="row mb-3">
      <div class="col-4">
        <div class="info-box mb-0"><span class="info-box-icon bg-success"><i class="fas fa-arrow-down"></i></span>
          <div class="info-box-content"><span class="info-box-text">Total Masuk</span><span class="info-box-number">{{ $barang->stockBalance?->stok_masuk??0 }}</span></div></div>
      </div>
      <div class="col-4">
        <div class="info-box mb-0"><span class="info-box-icon bg-danger"><i class="fas fa-arrow-up"></i></span>
          <div class="info-box-content"><span class="info-box-text">Total Keluar</span><span class="info-box-number">{{ $barang->stockBalance?->stok_keluar??0 }}</span></div></div>
      </div>
      <div class="col-4">
        <div class="info-box mb-0"><span class="info-box-icon {{ $barang->is_stok_minimum?'bg-warning':'bg-primary' }}"><i class="fas fa-layer-group"></i></span>
          <div class="info-box-content"><span class="info-box-text">Tersedia</span><span class="info-box-number">{{ $barang->stok_tersedia }}</span></div></div>
      </div>
    </div>
    <div class="card card-outline card-success">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-arrow-down mr-2"></i>Riwayat Masuk Terbaru</h3></div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <thead class="thead-light"><tr><th>No. Transaksi</th><th>Tgl</th><th>Supplier</th><th>Jumlah</th><th>Total</th></tr></thead>
          <tbody>
            @forelse($barang->barangMasuks as $m)
            <tr><td><code style="font-size:11px">{{ $m->no_transaksi }}</code></td><td>{{ $m->tanggal_masuk->format('d/m/Y') }}</td>
              <td>{{ $m->supplier?->nama_supplier??'-' }}</td><td>{{ $m->jumlah }}</td><td>{{ $m->total_harga_format }}</td></tr>
            @empty<tr><td colspan="5" class="text-center text-muted py-2">Belum ada data</td></tr>@endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card card-outline card-danger">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-arrow-up mr-2"></i>Riwayat Keluar Terbaru</h3></div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <thead class="thead-light"><tr><th>No. Transaksi</th><th>Tgl</th><th>Divisi</th><th>Jumlah</th><th>Penerima</th></tr></thead>
          <tbody>
            @forelse($barang->barangKeluars as $k)
            <tr><td><code style="font-size:11px">{{ $k->no_transaksi }}</code></td><td>{{ $k->tanggal_keluar->format('d/m/Y') }}</td>
              <td>{{ $k->divisi?->nama_divisi??'-' }}</td><td>{{ $k->jumlah }}</td><td>{{ $k->penerima??'-' }}</td></tr>
            @empty<tr><td colspan="5" class="text-center text-muted py-2">Belum ada data</td></tr>@endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
