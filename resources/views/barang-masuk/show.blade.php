@extends('layouts.app')
@section('title','Detail Barang Masuk')
@section('page-title','Detail Barang Masuk')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('barang-masuk.index') }}">Barang Masuk</a></li>
<li class="breadcrumb-item active">{{ $barangMasuk->no_transaksi }}</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-7">
    <div class="card card-outline card-success">
      <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">{{ $barangMasuk->no_transaksi }}</h3>
        <a href="{{ route('barang-masuk.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Kembali</a>
      </div>
      <div class="card-body">
        <dl class="row">
          <dt class="col-4">Barang</dt><dd class="col-8"><strong>{{ $barangMasuk->barang->nama_barang }}</strong></dd>
          <dt class="col-4">Tanggal</dt><dd class="col-8">{{ $barangMasuk->tanggal_masuk->format('d F Y') }}</dd>
          <dt class="col-4">Supplier</dt><dd class="col-8">{{ $barangMasuk->supplier?->nama_supplier ?? '-' }}</dd>
          <dt class="col-4">Jumlah</dt><dd class="col-8"><span class="badge badge-success" style="font-size:13px">{{ $barangMasuk->jumlah }} {{ $barangMasuk->barang->satuan }}</span></dd>
          <dt class="col-4">Harga Satuan</dt><dd class="col-8">Rp {{ number_format($barangMasuk->harga_satuan,0,',','.') }}</dd>
          <dt class="col-4">Total Harga</dt><dd class="col-8 font-weight-bold text-success">{{ $barangMasuk->total_harga_format }}</dd>
          <dt class="col-4">No. Surat Jalan</dt><dd class="col-8">{{ $barangMasuk->no_surat_jalan ?? '-' }}</dd>
          <dt class="col-4">No. PO</dt><dd class="col-8">{{ $barangMasuk->no_po ?? '-' }}</dd>
          <dt class="col-4">Nama PIC</dt><dd class="col-8"><strong>{{ $barangMasuk->pic_name ?? '-' }}</strong></dd>
          <dt class="col-4">Pengajuan Terkait</dt>
          <dd class="col-8">
            @if($barangMasuk->pengajuan)
              <a href="{{ route('pengajuan.show',$barangMasuk->pengajuan) }}">{{ $barangMasuk->pengajuan->no_pengajuan }}</a>
            @else <span class="text-muted">Input manual (tanpa pengajuan)</span> @endif
          </dd>
          <dt class="col-4">Diinput oleh</dt><dd class="col-8">{{ $barangMasuk->user->name }}</dd>
          @if($barangMasuk->keterangan)
            <dt class="col-4">Keterangan</dt><dd class="col-8">{{ $barangMasuk->keterangan }}</dd>
          @endif
        </dl>
      </div>
    </div>
  </div>

  <div class="col-md-5">
    <div class="card card-outline card-info">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-camera mr-2"></i>Foto Dokumentasi</h3></div>
      <div class="card-body text-center">
        @if($barangMasuk->foto_dokumentasi_url)
          <a href="{{ $barangMasuk->foto_dokumentasi_url }}" target="_blank">
            <img src="{{ $barangMasuk->foto_dokumentasi_url }}" class="img-fluid rounded" style="max-height:320px;border:1px solid #E2E8F0">
          </a>
          <small class="text-muted d-block mt-2">Klik untuk memperbesar</small>
        @else
          <div class="py-5 text-muted">
            <i class="fas fa-image fa-3x mb-2 d-block" style="opacity:.2"></i>
            Tidak ada foto dokumentasi
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection