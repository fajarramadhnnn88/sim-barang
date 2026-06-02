@extends('layouts.app')
@section('title','Tambah Barang')
@section('page-title','Tambah Barang')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Barang</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection
@section('content')
<div class="card card-outline card-primary" style="max-width:800px">
  <div class="card-header"><h3 class="card-title"><i class="fas fa-plus mr-2"></i>Form Tambah Barang</h3></div>
  <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card-body">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label>Kode Barang <span class="text-danger">*</span></label>
            <input type="text" name="kode_barang" class="form-control @error('kode_barang') is-invalid @enderror" value="{{ old('kode_barang') }}" placeholder="BRG00001" required>
            @error('kode_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-8">
          <div class="form-group">
            <label>Nama Barang <span class="text-danger">*</span></label>
            <input type="text" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" value="{{ old('nama_barang') }}" required>
            @error('nama_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-5">
          <div class="form-group">
            <label>Kategori <span class="text-danger">*</span></label>
            <select name="kategori_id" class="form-control select2 @error('kategori_id') is-invalid @enderror" required>
              <option value="">-- Pilih --</option>
              @foreach($kategoris as $k)<option value="{{ $k->id }}" {{ old('kategori_id')==$k->id?'selected':'' }}>{{ $k->nama_kategori }}</option>@endforeach
            </select>
            @error('kategori_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Satuan <span class="text-danger">*</span></label>
            <input type="text" name="satuan" class="form-control" value="{{ old('satuan') }}" placeholder="Pcs / Rim / Box" required>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Merk</label>
            <input type="text" name="merk" class="form-control" value="{{ old('merk') }}">
          </div>
        </div>
        <div class="col-md-5">
          <div class="form-group">
            <label>Harga Satuan (Rp) <span class="text-danger">*</span></label>
            <input type="number" name="harga_satuan" class="form-control" value="{{ old('harga_satuan',0) }}" min="0" required>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Stok Minimum</label>
            <input type="number" name="stok_minimum" class="form-control" value="{{ old('stok_minimum',0) }}" min="0">
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Lokasi</label>
            <input type="text" name="lokasi_penyimpanan" class="form-control" value="{{ old('lokasi_penyimpanan') }}" placeholder="Rak A-1">
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label>Spesifikasi</label>
            <input type="text" name="spesifikasi" class="form-control" value="{{ old('spesifikasi') }}">
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label>Foto</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" name="foto" accept="image/*" id="fotoInput" onchange="previewFoto(this)">
              <label class="custom-file-label" for="fotoInput">Pilih gambar...</label>
            </div>
            <img id="fotoPreview" src="" class="mt-2 rounded" style="max-height:100px;display:none">
            <small class="text-muted d-block mt-1">JPG/PNG/WEBP maks 2MB</small>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan</button>
      <a href="{{ route('barang.index') }}" class="btn btn-secondary ml-2">Batal</a>
    </div>
  </form>
</div>
@endsection
@push('scripts')
<script>
function previewFoto(i){const p=document.getElementById('fotoPreview');if(i.files&&i.files[0]){p.src=URL.createObjectURL(i.files[0]);p.style.display='block';i.nextElementSibling.textContent=i.files[0].name;}}
</script>
@endpush
