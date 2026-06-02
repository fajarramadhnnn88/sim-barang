@extends('layouts.app')
@section('title','Edit Divisi')
@section('page-title','Edit Divisi')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('divisi.index') }}">Divisi</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="card card-outline card-warning" style="max-width:600px">
  <div class="card-header"><h3 class="card-title"><i class="fas fa-edit mr-2"></i>Edit: {{ $divisi->nama_divisi }}</h3></div>
  <form action="{{ route('divisi.update',$divisi) }}" method="POST">
    @csrf @method('PUT')
    <div class="card-body">
      <div class="row">
        <div class="col-md-8">
          <div class="form-group">
            <label>Nama Divisi <span class="text-danger">*</span></label>
            <input type="text" name="nama_divisi" class="form-control" value="{{ old('nama_divisi',$divisi->nama_divisi) }}" required>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Kode Divisi <span class="text-danger">*</span></label>
            <input type="text" name="kode_divisi" class="form-control" value="{{ old('kode_divisi',$divisi->kode_divisi) }}" required style="text-transform:uppercase">
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi',$divisi->deskripsi) }}</textarea>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group mb-0">
            <label>Status</label>
            <div class="d-flex mt-1">
              <label style="font-weight:400;text-transform:none;letter-spacing:0;cursor:pointer;display:flex;align-items:center;gap:6px;color:#334155">
                <input type="radio" name="is_active" value="1" {{ old('is_active',$divisi->is_active?'1':'0')==='1'?'checked':'' }}> Aktif
              </label>
              <label style="font-weight:400;text-transform:none;letter-spacing:0;cursor:pointer;display:flex;align-items:center;gap:6px;color:#334155;margin-left:16px">
                <input type="radio" name="is_active" value="0" {{ old('is_active',$divisi->is_active?'1':'0')==='0'?'checked':'' }}> Nonaktif
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Perbarui</button>
      <a href="{{ route('divisi.index') }}" class="btn btn-secondary ml-2">Batal</a>
    </div>
  </form>
</div>
@endsection
