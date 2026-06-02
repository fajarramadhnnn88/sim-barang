@extends('layouts.app')
@section('title','Kelola Divisi')
@section('page-title','Kelola Divisi')
@section('breadcrumb')<li class="breadcrumb-item active">Divisi</li>@endsection
@section('content')
<div class="card card-outline card-primary">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title"><i class="fas fa-sitemap mr-2"></i>Daftar Divisi</h3>
    <a href="{{ route('divisi.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i>Tambah Divisi</a>
  </div>
  <div class="card-body">
    <form method="GET" class="mb-4">
      <div class="row">
        <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama / kode divisi..." value="{{ request('search') }}"></div>
        <div class="col-md-3"><button class="btn btn-info btn-sm mr-1"><i class="fas fa-search"></i></button><a href="{{ route('divisi.index') }}" class="btn btn-secondary btn-sm">Reset</a></div>
      </div>
    </form>
    <div class="row">
      @forelse($divisis as $d)
      <div class="col-md-4 mb-3">
        <div class="card h-100" style="border:1px solid #E2E8F0;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.06);transition:all .2s">
          <div class="card-body p-3">
            <div class="d-flex align-items-center mb-3">
              <div style="width:44px;height:44px;background:linear-gradient(135deg,#4F46E5,#7C3AED);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:12px">
                <span style="color:#fff;font-weight:800;font-size:13px">{{ substr($d->kode_divisi,0,2) }}</span>
              </div>
              <div class="flex-grow-1">
                <div style="font-weight:700;font-size:14px;color:#1E293B">{{ $d->nama_divisi }}</div>
                <code style="font-size:11px;background:#F1F5F9;padding:1px 6px;border-radius:4px;color:#64748B">{{ $d->kode_divisi }}</code>
              </div>
              <span class="badge {{ $d->is_active?'badge-success':'badge-secondary' }}">{{ $d->is_active?'Aktif':'Nonaktif' }}</span>
            </div>
            @if($d->deskripsi)
              <p style="font-size:12px;color:#64748B;margin-bottom:12px;line-height:1.5">{{ Str::limit($d->deskripsi,60) }}</p>
            @endif
            <div class="row text-center mb-3" style="background:#F8FAFC;border-radius:8px;padding:8px 0;margin:0">
              <div class="col-6" style="border-right:1px solid #E2E8F0">
                <div style="font-size:20px;font-weight:700;color:#4F46E5">{{ $d->users_count??0 }}</div>
                <div style="font-size:10px;color:#94A3B8;font-weight:500">Anggota</div>
              </div>
              <div class="col-6">
                <div style="font-size:20px;font-weight:700;color:#10B981">{{ $d->pengajuans_count??0 }}</div>
                <div style="font-size:10px;color:#94A3B8;font-weight:500">Pengajuan</div>
              </div>
            </div>
            <div class="d-flex gap-1">
              <a href="{{ route('divisi.show',$d) }}" class="btn btn-xs btn-info flex-fill"><i class="fas fa-eye mr-1"></i>Detail</a>
              <a href="{{ route('divisi.edit',$d) }}" class="btn btn-xs btn-warning flex-fill"><i class="fas fa-edit mr-1"></i>Edit</a>
              <form action="{{ route('divisi.destroy',$d) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus divisi {{ $d->nama_divisi }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
              </form>
            </div>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12 text-center py-5">
        <i class="fas fa-sitemap fa-3x text-muted mb-3 d-block" style="opacity:.3"></i>
        <p class="text-muted">Belum ada divisi. <a href="{{ route('divisi.create') }}">Tambah sekarang</a></p>
      </div>
      @endforelse
    </div>
    {{ $divisis->links() }}
  </div>
</div>
@endsection
