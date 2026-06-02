@extends('layouts.app')
@section('title','Edit Barang Keluar')
@section('page-title','Edit Barang Keluar')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('barang-keluar.index') }}">Barang Keluar</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="card card-outline card-warning" style="max-width:700px">
  <div class="card-header"><h3 class="card-title"><i class="fas fa-edit mr-2"></i>Edit Barang Keluar</h3></div>
  <form action="{{ route('barang-keluar.update',$barangKeluar) }}" method="POST">
    @csrf @method('PUT')
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label>Barang <span class="text-danger">*</span></label>
            <select name="barang_id" class="form-control select2" required>
              @foreach($barangs as $b)<option value="{{ $b->id }}" {{ old('barang_id',$barangKeluar->barang_id)==$b->id?'selected':'' }}>{{ $b->kode_barang }} — {{ $b->nama_barang }}</option>@endforeach
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Divisi</label>
            <select name="divisi_id" class="form-control select2">
              <option value="">-- Pilih --</option>
              @foreach($divisis as $d)<option value="{{ $d->id }}" {{ old('divisi_id',$barangKeluar->divisi_id)==$d->id?'selected':'' }}>{{ $d->nama_divisi }}</option>@endforeach
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Jumlah <span class="text-danger">*</span></label>
            <input type="number" name="jumlah" class="form-control" value="{{ old('jumlah',$barangKeluar->jumlah) }}" min="1" required>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Tanggal Keluar</label>
            <input type="date" name="tanggal_keluar" class="form-control" value="{{ old('tanggal_keluar',$barangKeluar->tanggal_keluar->format('Y-m-d')) }}" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Penerima</label>
            <input type="text" name="penerima" class="form-control" value="{{ old('penerima',$barangKeluar->penerima) }}">
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan',$barangKeluar->keterangan) }}</textarea>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Perbarui</button>
      <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary ml-2">Batal</a>
    </div>
  </form>
</div>
@endsection
