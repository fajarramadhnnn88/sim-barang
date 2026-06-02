@extends('layouts.app')
@section('title','Kelola Pengguna')
@section('page-title','Kelola Pengguna')
@section('breadcrumb')<li class="breadcrumb-item active">Pengguna</li>@endsection
@section('content')
<div class="card card-outline card-primary">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title"><i class="fas fa-users-cog mr-2"></i>Daftar Pengguna</h3>
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i>Tambah Pengguna</a>
  </div>
  <div class="card-body">
    <form method="GET" class="mb-3">
      <div class="row">
        <div class="col-md-3"><input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama / email..." value="{{ request('search') }}"></div>
        <div class="col-md-3">
          <select name="role" class="form-control form-control-sm">
            <option value="">Semua Role</option>
            @foreach($roles as $r)<option value="{{ $r->value }}" {{ request('role')==$r->value?'selected':'' }}>{{ $r->label() }}</option>@endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="divisi_id" class="form-control form-control-sm">
            <option value="">Semua Divisi</option>
            @foreach($divisis as $d)<option value="{{ $d->id }}" {{ request('divisi_id')==$d->id?'selected':'' }}>{{ $d->nama_divisi }}</option>@endforeach
          </select>
        </div>
        <div class="col-md-3">
          <button class="btn btn-info btn-sm mr-1"><i class="fas fa-search"></i></button>
          <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        </div>
      </div>
    </form>
    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead class="thead-light">
          <tr><th>Pengguna</th><th>NIP</th><th>Role</th><th>Divisi</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          @forelse($users as $u)
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <img src="{{ $u->foto_url }}" class="rounded-circle mr-2" style="width:34px;height:34px;object-fit:cover;border:2px solid #E2E8F0">
                <div>
                  <div style="font-weight:600;font-size:13px;color:#1E293B">{{ $u->name }}</div>
                  <div style="font-size:11px;color:#94A3B8">{{ $u->email }}</div>
                </div>
              </div>
            </td>
            <td><code style="font-size:11px">{{ $u->nip??'-' }}</code></td>
            <td>
              @php $roleColors=['staff'=>'badge-secondary','admin_divisi'=>'badge-info','purchasing'=>'badge-primary','wakil_direktur'=>'badge-warning','direktur'=>'badge-danger','superadmin'=>'badge-dark']; @endphp
              <span class="badge {{ $roleColors[$u->role->value]??'badge-secondary' }}">{{ $u->role->label() }}</span>
            </td>
            <td style="font-size:12px">{{ $u->divisi?->nama_divisi??'-' }}</td>
            <td><span class="badge {{ $u->is_active?'badge-success':'badge-secondary' }}">{{ $u->is_active?'Aktif':'Nonaktif' }}</span></td>
            <td>
              <a href="{{ route('users.edit',$u) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
              @if($u->id!==auth()->id())
              <form action="{{ route('users.destroy',$u) }}" method="POST" class="d-inline" onsubmit="return confirm('Nonaktifkan pengguna ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-danger" title="Nonaktifkan"><i class="fas fa-user-slash"></i></button>
              </form>
              @endif
            </td>
          </tr>
          @empty<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada pengguna</td></tr>@endforelse
        </tbody>
      </table>
    </div>
    {{ $users->links() }}
  </div>
</div>
@endsection
