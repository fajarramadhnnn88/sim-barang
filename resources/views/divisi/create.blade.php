@extends('layouts.app')
@section('title','Tambah Divisi')
@section('page-title','Tambah Divisi')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('divisi.index') }}">Divisi</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection
@section('content')
<div class="card card-outline card-primary" style="max-width:600px">
  <div class="card-header"><h3 class="card-title"><i class="fas fa-plus mr-2"></i>Form Tambah Divisi</h3></div>
  <form action="{{ route('divisi.store') }}" method="POST">
    @csrf
    <div class="card-body">
      <div class="row">
        <div class="col-md-8">
          <div class="form-group">
            <label>Nama Divisi <span class="text-danger">*</span></label>
            <input type="text" name="nama_divisi" class="form-control @error('nama_divisi') is-invalid @enderror" value="{{ old('nama_divisi') }}" placeholder="Teknologi Informasi" required>
            @error('nama_divisi')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Kode Divisi <span class="text-danger">*</span></label>
            <input type="text" name="kode_divisi" class="form-control @error('kode_divisi') is-invalid @enderror" value="{{ old('kode_divisi') }}" placeholder="TI" required style="text-transform:uppercase">
            @error('kode_divisi')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi singkat divisi...">{{ old('deskripsi') }}</textarea>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group mb-0">
            <label>Status</label>
            <div class="d-flex gap-3 mt-1">
              <label style="font-weight:400;text-transform:none;letter-spacing:0;cursor:pointer;display:flex;align-items:center;gap:6px;color:#334155">
                <input type="radio" name="is_active" value="1" {{ old('is_active','1')=='1'?'checked':'' }}> Aktif
              </label>
              <label style="font-weight:400;text-transform:none;letter-spacing:0;cursor:pointer;display:flex;align-items:center;gap:6px;color:#334155;margin-left:16px">
                <input type="radio" name="is_active" value="0" {{ old('is_active')=='0'?'checked':'' }}> Nonaktif
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan</button>
      <a href="{{ route('divisi.index') }}" class="btn btn-secondary ml-2">Batal</a>
    </div>
  </form>
</div>
@endsection
