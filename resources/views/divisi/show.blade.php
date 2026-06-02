@extends('layouts.app')
@section('title',$divisi->nama_divisi)
@section('page-title','Detail Divisi')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('divisi.index') }}">Divisi</a></li>
<li class="breadcrumb-item active">{{ $divisi->nama_divisi }}</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-4">
    <div class="card card-outline card-primary">
      <div class="card-body text-center py-4">
        <div style="width:72px;height:72px;background:linear-gradient(135deg,#4F46E5,#7C3AED);border-radius:20px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px">
          <span style="color:#fff;font-weight:800;font-size:22px">{{ substr($divisi->kode_divisi,0,2) }}</span>
        </div>
        <h4 style="font-weight:700;color:#1E293B;margin-bottom:4px">{{ $divisi->nama_divisi }}</h4>
        <code style="background:#F1F5F9;padding:3px 10px;border-radius:6px;font-size:13px;color:#64748B">{{ $divisi->kode_divisi }}</code>
        <div class="mt-2"><span class="badge {{ $divisi->is_active?'badge-success':'badge-secondary' }}" style="font-size:12px">{{ $divisi->is_active?'Aktif':'Nonaktif' }}</span></div>
        @if($divisi->deskripsi)<p class="mt-3 mb-0" style="font-size:13px;color:#64748B;line-height:1.6">{{ $divisi->deskripsi }}</p>@endif
        <hr>
        <div class="row text-center">
          <div class="col-6"><div style="font-size:28px;font-weight:700;color:#4F46E5">{{ $divisi->users_count }}</div><div style="font-size:11px;color:#94A3B8">Anggota</div></div>
          <div class="col-6"><div style="font-size:28px;font-weight:700;color:#10B981">{{ $divisi->pengajuans_count }}</div><div style="font-size:11px;color:#94A3B8">Pengajuan</div></div>
        </div>
      </div>
      <div class="card-footer">
        <a href="{{ route('divisi.edit',$divisi) }}" class="btn btn-warning btn-block btn-sm"><i class="fas fa-edit mr-1"></i>Edit Divisi</a>
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card card-outline card-secondary">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-users mr-2"></i>Anggota Divisi</h3></div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <thead class="thead-light"><tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th></tr></thead>
          <tbody>
            @forelse($divisi->users as $u)
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <img src="{{ $u->foto_url }}" class="rounded-circle mr-2" style="width:30px;height:30px;object-fit:cover">
                  <strong style="font-size:13px">{{ $u->name }}</strong>
                </div>
              </td>
              <td style="font-size:12px;color:#64748B">{{ $u->email }}</td>
              <td><span class="badge badge-primary">{{ $u->role->label() }}</span></td>
              <td><span class="badge {{ $u->is_active?'badge-success':'badge-secondary' }}">{{ $u->is_active?'Aktif':'Nonaktif' }}</span></td>
            </tr>
            @empty<tr><td colspan="4" class="text-center text-muted py-3">Belum ada anggota</td></tr>@endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
