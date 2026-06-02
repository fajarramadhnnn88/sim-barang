@extends('layouts.app')
@section('title','Profil Saya')
@section('page-title','Profil Saya')
@section('breadcrumb')<li class="breadcrumb-item active">Profil</li>@endsection
@section('content')
<div class="row">
  <div class="col-md-4">
    <div class="card card-outline card-primary">
      <div class="card-body text-center py-4">
        <img src="{{ $user->foto_url }}" class="rounded-circle mb-3" style="width:96px;height:96px;object-fit:cover;border:3px solid #E2E8F0;box-shadow:0 4px 12px rgba(0,0,0,.1)">
        <h5 style="font-weight:700;color:#1E293B;margin-bottom:4px">{{ $user->name }}</h5>
        <p style="color:#94A3B8;font-size:13px;margin-bottom:8px">{{ $user->email }}</p>
        @php $rc=['staff'=>'badge-secondary','admin_divisi'=>'badge-info','purchasing'=>'badge-primary','wakil_direktur'=>'badge-warning','direktur'=>'badge-danger','superadmin'=>'badge-dark']; @endphp
        <span class="badge {{ $rc[$user->role->value]??'badge-secondary' }}" style="font-size:12px;padding:5px 14px">{{ $user->role->label() }}</span>
        <hr>
        <dl class="row text-left mb-0" style="font-size:13px">
          <dt class="col-5">NIP</dt><dd class="col-7">{{ $user->nip??'-' }}</dd>
          <dt class="col-5">Divisi</dt><dd class="col-7">{{ $user->divisi?->nama_divisi??'-' }}</dd>
          <dt class="col-5">No. HP</dt><dd class="col-7">{{ $user->no_hp??'-' }}</dd>
          <dt class="col-5">Bergabung</dt><dd class="col-7">{{ $user->created_at->format('d/m/Y') }}</dd>
        </dl>
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card card-outline card-success">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Update Profil</h3></div>
      <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>NIP</label>
                <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip',$user->nip) }}">
                @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                <label>Foto Profil</label>
                <input type="file" name="foto" class="form-control-file" accept="image/*">
                <small class="text-muted">JPG/PNG/WEBP maks 2MB</small>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer"><button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i>Simpan Profil</button></div>
      </form>
    </div>

    <div class="card card-outline card-warning">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-lock mr-2"></i>Ganti Password</h3></div>
      <form action="{{ route('profile.password') }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Password Saat Ini <span class="text-danger">*</span></label>
                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Password Baru <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">Min 8 karakter</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Konfirmasi Password Baru <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer"><button type="submit" class="btn btn-warning"><i class="fas fa-key mr-1"></i>Ganti Password</button></div>
      </form>
    </div>
  </div>
</div>
@endsection
