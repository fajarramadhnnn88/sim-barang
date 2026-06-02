@extends('layouts.app')
@section('title','Edit Barang Masuk')
@section('page-title','Edit Barang Masuk')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('barang-masuk.index') }}">Barang Masuk</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="card card-outline card-warning" style="max-width:700px">
  <div class="card-header"><h3 class="card-title"><i class="fas fa-edit mr-2"></i>Edit: {{ $barangMasuk->no_transaksi }}</h3></div>
  <form action="{{ route('barang-masuk.update',$barangMasuk) }}" method="POST">
    @csrf @method('PUT')
    <div class="card-body">
      <div class="callout callout-warning"><i class="fas fa-exclamation-triangle mr-2"></i>Mengedit data ini akan menghapus transaksi lama dan membuat baru, stok otomatis dikoreksi.</div>
      <div class="row mt-3">
        <div class="col-md-12">
          <div class="form-group">
            <label>Barang <span class="text-danger">*</span></label>
            <select name="barang_id" class="form-control select2" required>
              @foreach($barangs as $b)<option value="{{ $b->id }}" {{ old('barang_id',$barangMasuk->barang_id)==$b->id?'selected':'' }}>{{ $b->kode_barang }} — {{ $b->nama_barang }}</option>@endforeach
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Supplier</label>
            <select name="supplier_id" class="form-control select2">
              <option value="">-- Tanpa --</option>
              @foreach($suppliers as $s)<option value="{{ $s->id }}" {{ old('supplier_id',$barangMasuk->supplier_id)==$s->id?'selected':'' }}>{{ $s->nama_supplier }}</option>@endforeach
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Jumlah <span class="text-danger">*</span></label>
            <input type="number" name="jumlah" class="form-control" value="{{ old('jumlah',$barangMasuk->jumlah) }}" min="1" required>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Harga Satuan</label>
            <input type="number" name="harga_satuan" class="form-control" value="{{ old('harga_satuan',$barangMasuk->harga_satuan) }}" min="0">
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Tanggal Masuk <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk',$barangMasuk->tanggal_masuk->format('Y-m-d')) }}" required>
          </div>
        </div>
        <div class="col-md-4"><div class="form-group"><label>No. Surat Jalan</label><input type="text" name="no_surat_jalan" class="form-control" value="{{ old('no_surat_jalan',$barangMasuk->no_surat_jalan) }}"></div></div>
        <div class="col-md-4"><div class="form-group"><label>No. PO</label><input type="text" name="no_po" class="form-control" value="{{ old('no_po',$barangMasuk->no_po) }}"></div></div>
        <div class="col-md-12"><div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan',$barangMasuk->keterangan) }}</textarea></div></div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Perbarui</button>
      <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary ml-2">Batal</a>
    </div>
  </form>
</div>
@endsection
