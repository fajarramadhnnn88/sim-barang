@extends('layouts.app')
@section('title','Edit Barang')
@section('page-title','Edit Barang')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Barang</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="card card-outline card-warning" style="max-width:800px">
  <div class="card-header"><h3 class="card-title"><i class="fas fa-edit mr-2"></i>Edit: {{ $barang->nama_barang }}</h3></div>
  <form action="{{ route('barang.update',$barang) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="card-body">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label>Kode Barang <span class="text-danger">*</span></label>
            <input type="text" name="kode_barang" class="form-control" value="{{ old('kode_barang',$barang->kode_barang) }}" required>
          </div>
        </div>
        <div class="col-md-8">
          <div class="form-group">
            <label>Nama Barang <span class="text-danger">*</span></label>
            <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang',$barang->nama_barang) }}" required>
          </div>
        </div>
        <div class="col-md-5">
          <div class="form-group">
            <label>Kategori <span class="text-danger">*</span></label>
            <select name="kategori_id" class="form-control select2" required>
              @foreach($kategoris as $k)<option value="{{ $k->id }}" {{ old('kategori_id',$barang->kategori_id)==$k->id?'selected':'' }}>{{ $k->nama_kategori }}</option>@endforeach
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Satuan <span class="text-danger">*</span></label>
            <input type="text" name="satuan" class="form-control" value="{{ old('satuan',$barang->satuan) }}" required>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Merk</label>
            <input type="text" name="merk" class="form-control" value="{{ old('merk',$barang->merk) }}">
          </div>
        </div>
        <div class="col-md-5">
          <div class="form-group">
            <label>Harga Satuan (Rp) <span class="text-danger">*</span></label>
            <input type="number" name="harga_satuan" class="form-control" value="{{ old('harga_satuan',$barang->harga_satuan) }}" min="0" required>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Stok Minimum</label>
            <input type="number" name="stok_minimum" class="form-control" value="{{ old('stok_minimum',$barang->stok_minimum) }}" min="0">
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Status</label>
            <select name="is_active" class="form-control">
              <option value="1" {{ old('is_active',$barang->is_active?'1':'0')=='1'?'selected':'' }}>Aktif</option>
              <option value="0" {{ old('is_active',$barang->is_active?'1':'0')=='0'?'selected':'' }}>Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label>Foto Baru</label>
            @if($barang->foto)<img src="{{ $barang->foto_url }}" class="d-block mb-2 rounded" style="max-height:80px">@endif
            <input type="file" name="foto" class="form-control-file" accept="image/*">
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Perbarui</button>
      <a href="{{ route('barang.show',$barang) }}" class="btn btn-secondary ml-2">Batal</a>
    </div>
  </form>
</div>
@endsection
