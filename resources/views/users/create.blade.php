@extends('layouts.app')
@section('title','Tambah Pengguna')
@section('page-title','Tambah Pengguna')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection
@section('content')
<div class="card card-outline card-primary" style="max-width:700px">
  <div class="card-header"><h3 class="card-title"><i class="fas fa-user-plus mr-2"></i>Form Tambah Pengguna</h3></div>
  <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>NIP</label>
            <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip') }}" placeholder="Nomor Induk Pegawai">
            @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>No. HP</label>
            <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Password <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Konfirmasi Password <span class="text-danger">*</span></label>
            <input type="password" name="password_confirmation" class="form-control" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Role <span class="text-danger">*</span></label>
            <select name="role" class="form-control @error('role') is-invalid @enderror" required>
              <option value="">-- Pilih Role --</option>
              @foreach($roles as $r)<option value="{{ $r->value }}" {{ old('role')==$r->value?'selected':'' }}>{{ $r->label() }}</option>@endforeach
            </select>
            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Divisi</label>
            <select name="divisi_id" class="form-control select2">
              <option value="">-- Tanpa Divisi --</option>
              @foreach($divisis as $d)<option value="{{ $d->id }}" {{ old('divisi_id')==$d->id?'selected':'' }}>{{ $d->nama_divisi }}</option>@endforeach
            </select>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label>Foto Profil</label>
            <input type="file" name="foto" class="form-control-file" accept="image/*">
            <small class="text-muted">JPG/PNG maks 2MB</small>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan</button>
      <a href="{{ route('users.index') }}" class="btn btn-secondary ml-2">Batal</a>
    </div>
  </form>
</div>
@endsection
