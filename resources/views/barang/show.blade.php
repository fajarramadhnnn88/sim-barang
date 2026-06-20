@extends('layouts.app')
@section('title',$barang->nama_barang)
@section('page-title','Detail Barang')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Data Barang</a></li>
<li class="breadcrumb-item active">{{ $barang->nama_barang }}</li>
@endsection
@section('content')
@php
  $bolehKelola = auth()->user()->isAdminDivisi() || auth()->user()->isSuperadmin();
@endphp

<div class="row">
  <div class="col-md-4">
    <div class="card card-outline card-primary">
      <div class="card-body text-center">
        @if($barang->foto)
          <img src="{{ asset('storage/'.$barang->foto) }}" class="img-fluid rounded mb-3" style="max-height:220px">
        @else
          <div style="height:220px;background:#F1F5F9;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:16px">
            <i class="fas fa-box text-muted" style="font-size:48px;opacity:.4"></i>
          </div>
        @endif

        <h5 style="font-weight:700">{{ $barang->nama_barang }}</h5>
        <code style="font-size:12px;background:#F1F5F9;padding:3px 8px;border-radius:5px">{{ $barang->kode_barang }}</code>

        @unless($bolehKelola)
          <div class="mt-3">
            <span class="badge badge-warning" style="font-size:11px;padding:5px 12px">
              <i class="fas fa-eye mr-1"></i>Mode Lihat — Tidak Dapat Diubah
            </span>
          </div>
        @endunless

        {{-- Edit & Hapus HANYA muncul untuk admin_divisi & superadmin --}}
        @if($bolehKelola)
          <div class="mt-3">
            <a href="{{ route('barang.edit',$barang) }}" class="btn btn-warning btn-sm">
              <i class="fas fa-edit mr-1"></i>Edit
            </a>
            <form action="{{ route('barang.destroy',$barang) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Hapus barang ini?')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm"><i class="fas fa-trash mr-1"></i>Hapus</button>
            </form>
          </div>
        @endif
      </div>
    </div>

    {{-- Status Stok --}}
    <div class="card card-outline card-secondary">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-layer-group mr-2"></i>Status Stok</h3></div>
      <div class="card-body text-center">
        <div style="font-size:32px;font-weight:800;color:{{ ($barang->stok_tersedia ?? 0) <= 0 ? '#DC2626' : (($barang->stok_tersedia ?? 0) <= $barang->stok_minimum ? '#D97706' : '#059669') }}">
          {{ $barang->stok_tersedia ?? 0 }}
        </div>
        <div class="text-muted" style="font-size:12px">{{ $barang->satuan }} tersedia</div>
        <hr>
        <div class="row text-center" style="font-size:12px">
          <div class="col-6">
            <div class="text-muted">Stok Minimum</div>
            <strong>{{ $barang->stok_minimum }} {{ $barang->satuan }}</strong>
          </div>
          <div class="col-6">
            <div class="text-muted">Status</div>
            @if(($barang->stok_tersedia ?? 0) <= 0)
              <span class="badge badge-danger">Habis</span>
            @elseif(($barang->stok_tersedia ?? 0) <= $barang->stok_minimum)
              <span class="badge badge-warning">Menipis</span>
            @else
              <span class="badge badge-success">Aman</span>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card card-outline card-primary">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Informasi Barang</h3></div>
      <div class="card-body">
        <dl class="row mb-0" style="font-size:13px">
          <dt class="col-4">Kode Barang</dt><dd class="col-8">{{ $barang->kode_barang }}</dd>
          <dt class="col-4">Nama Barang</dt><dd class="col-8">{{ $barang->nama_barang }}</dd>
          <dt class="col-4">Kategori</dt><dd class="col-8">{{ $barang->kategori->nama_kategori ?? '-' }}</dd>
          <dt class="col-4">Satuan</dt><dd class="col-8">{{ $barang->satuan }}</dd>
          <dt class="col-4">Merk</dt><dd class="col-8">{{ $barang->merk ?? '-' }}</dd>
          <dt class="col-4">Harga Satuan</dt><dd class="col-8 font-weight-bold">Rp {{ number_format($barang->harga_satuan,0,',','.') }}</dd>
          <dt class="col-4">Lokasi Penyimpanan</dt><dd class="col-8">{{ $barang->lokasi_penyimpanan ?? '-' }}</dd>
          <dt class="col-4">Spesifikasi</dt><dd class="col-8">{{ $barang->spesifikasi ?? '-' }}</dd>
        </dl>
      </div>
    </div>

    {{-- Riwayat transaksi terkait barang ini (opsional, jika relasi tersedia) --}}
    @if(($barang->barangMasuks ?? null)?->count())
    <div class="card card-outline card-success">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-truck-loading mr-2"></i>Riwayat Barang Masuk Terakhir</h3></div>
      <div class="card-body p-0">
        <table class="table table-sm table-bordered mb-0">
          <thead class="thead-light">
            <tr><th>No. Transaksi</th><th>Tanggal</th><th>Jumlah</th><th>Supplier</th></tr>
          </thead>
          <tbody>
            @foreach($barang->barangMasuks->take(5) as $bm)
            <tr>
              <td><code style="font-size:11px">{{ $bm->no_transaksi }}</code></td>
              <td>{{ $bm->tanggal_masuk->format('d/m/Y') }}</td>
              <td>{{ $bm->jumlah }} {{ $barang->satuan }}</td>
              <td>{{ $bm->supplier?->nama_supplier ?? '-' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif
  </div>
</div>
@endsection