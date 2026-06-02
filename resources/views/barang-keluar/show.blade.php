@extends('layouts.app')
@section('title','Detail Barang Keluar')
@section('page-title','Detail Barang Keluar')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('barang-keluar.index') }}">Barang Keluar</a></li>
<li class="breadcrumb-item active">{{ $barangKeluar->no_transaksi }}</li>
@endsection
@section('content')
<div class="card card-outline card-danger" style="max-width:600px">
  <div class="card-header d-flex justify-content-between">
    <h3 class="card-title">{{ $barangKeluar->no_transaksi }}</h3>
    <a href="{{ route('barang-keluar.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Kembali</a>
  </div>
  <div class="card-body">
    <dl class="row">
      <dt class="col-4">Barang</dt><dd class="col-8"><strong>{{ $barangKeluar->barang->nama_barang }}</strong></dd>
      <dt class="col-4">Tanggal</dt><dd class="col-8">{{ $barangKeluar->tanggal_keluar->format('d F Y') }}</dd>
      <dt class="col-4">Divisi</dt><dd class="col-8">{{ $barangKeluar->divisi?->nama_divisi??'-' }}</dd>
      <dt class="col-4">Jumlah</dt><dd class="col-8"><span class="badge badge-danger" style="font-size:13px">{{ $barangKeluar->jumlah }} {{ $barangKeluar->barang->satuan }}</span></dd>
      <dt class="col-4">Penerima</dt><dd class="col-8">{{ $barangKeluar->penerima??'-' }}</dd>
      <dt class="col-4">No. Pengajuan</dt><dd class="col-8">{{ $barangKeluar->pengajuan?->no_pengajuan??'-' }}</dd>
      <dt class="col-4">Diinput</dt><dd class="col-8">{{ $barangKeluar->user->name }}</dd>
    </dl>
  </div>
</div>
@endsection
