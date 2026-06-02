@extends('layouts.app')
@section('title','Edit Pengguna')
@section('page-title','Edit Pengguna')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="card card-outline card-warning" style="max-width:700px">
  <div class="card-header"><h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Edit: {{ $user->name }}</h3></div>
  <form action="{{ route('users.update',$user) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="card-body">
      <div class="row">
        @if($user->foto)
        <div class="col-12 mb-3 text-center">
          <img src="{{ $user->foto_url }}" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;border:3px solid #E2E8F0">
        </div>
        @endif
        <div class="col-md-6">
          <div class="form-group">
            <label>Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name',$user->name) }}" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>NIP</label>
            <input type="text" name="nip" class="form-control" value="{{ old('nip',$user->nip) }}">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" value="{{ old('email',$user->email) }}" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>No. HP</label>
            <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp',$user->no_hp) }}">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Password Baru <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
            <input type="password" name="password" class="form-control" minlength="8">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Role <span class="text-danger">*</span></label>
            <select name="role" class="form-control" required>
              @foreach($roles as $r)<option value="{{ $r->value }}" {{ old('role',$user->role->value)==$r->value?'selected':'' }}>{{ $r->label() }}</option>@endforeach
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Divisi</label>
            <select name="divisi_id" class="form-control select2">
              <option value="">-- Tanpa Divisi --</option>
              @foreach($divisis as $d)<option value="{{ $d->id }}" {{ old('divisi_id',$user->divisi_id)==$d->id?'selected':'' }}>{{ $d->nama_divisi }}</option>@endforeach
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Status</label>
            <select name="is_active" class="form-control">
              <option value="1" {{ old('is_active',$user->is_active?'1':'0')==='1'?'selected':'' }}>Aktif</option>
              <option value="0" {{ old('is_active',$user->is_active?'1':'0')==='0'?'selected':'' }}>Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Foto Baru</label>
            <input type="file" name="foto" class="form-control-file" accept="image/*">
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Perbarui</button>
      <a href="{{ route('users.index') }}" class="btn btn-secondary ml-2">Batal</a>
    </div>
  </form>
</div>
@endsection
