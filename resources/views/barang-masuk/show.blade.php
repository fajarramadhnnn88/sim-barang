@extends('layouts.app')
@section('title','Detail Barang Masuk')
@section('page-title','Detail Barang Masuk')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('barang-masuk.index') }}">Barang Masuk</a></li>
<li class="breadcrumb-item active">{{ $barangMasuk->no_transaksi }}</li>
@endsection
@section('content')
<div class="card card-outline card-success" style="max-width:600px">
  <div class="card-header d-flex justify-content-between">
    <h3 class="card-title">{{ $barangMasuk->no_transaksi }}</h3>
    <a href="{{ route('barang-masuk.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Kembali</a>
  </div>
  <div class="card-body">
    <dl class="row">
      <dt class="col-4">Barang</dt><dd class="col-8"><strong>{{ $barangMasuk->barang->nama_barang }}</strong></dd>
      <dt class="col-4">Tanggal</dt><dd class="col-8">{{ $barangMasuk->tanggal_masuk->format('d F Y') }}</dd>
      <dt class="col-4">Supplier</dt><dd class="col-8">{{ $barangMasuk->supplier?->nama_supplier??'-' }}</dd>
      <dt class="col-4">Jumlah</dt><dd class="col-8"><span class="badge badge-success" style="font-size:13px">{{ $barangMasuk->jumlah }} {{ $barangMasuk->barang->satuan }}</span></dd>
      <dt class="col-4">Harga Satuan</dt><dd class="col-8">Rp {{ number_format($barangMasuk->harga_satuan,0,',','.') }}</dd>
      <dt class="col-4">Total Harga</dt><dd class="col-8 font-weight-bold text-success">{{ $barangMasuk->total_harga_format }}</dd>
      <dt class="col-4">No. Surat Jalan</dt><dd class="col-8">{{ $barangMasuk->no_surat_jalan??'-' }}</dd>
      <dt class="col-4">No. PO</dt><dd class="col-8">{{ $barangMasuk->no_po??'-' }}</dd>
      <dt class="col-4">Diinput</dt><dd class="col-8">{{ $barangMasuk->user->name }}</dd>
    </dl>
  </div>
</div>
@endsection
