@extends('layouts.app')
@section('title','Pengajuan Barang')
@section('page-title','Pengajuan Barang')
@section('breadcrumb')<li class="breadcrumb-item active">Pengajuan</li>@endsection
@section('content')
<div class="card card-outline card-primary">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title"><i class="fas fa-file-alt mr-2"></i>Daftar Pengajuan</h3>
    @if(auth()->user()->isStaff()||auth()->user()->isAdminDivisi()||auth()->user()->isSuperadmin())
    <a href="{{ route('pengajuan.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i>Buat Pengajuan</a>
    @endif
  </div>
  <div class="card-body">
    <form method="GET" class="mb-3">
      <div class="row">
        <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Cari no. pengajuan / keperluan..." value="{{ request('search') }}"></div>
        <div class="col-md-3">
          <select name="status" class="form-control form-control-sm">
            <option value="">Semua Status</option>
            @foreach($statusList as $s)<option value="{{ $s->value }}" {{ request('status')==$s->value?'selected':'' }}>{{ $s->label() }}</option>@endforeach
          </select>
        </div>
        <div class="col-md-3">
          <button class="btn btn-info btn-sm mr-1"><i class="fas fa-search"></i> Filter</button>
          <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        </div>
      </div>
    </form>
    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead class="thead-light">
          <tr><th>No. Pengajuan</th><th>Pengaju</th><th>Divisi</th><th>Keperluan</th><th>Total Nilai</th><th>Jalur</th><th>Status</th><th>Tgl</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          @forelse($pengajuans as $p)
          <tr>
            <td><a href="{{ route('pengajuan.show',$p) }}" style="font-weight:700;color:#4F46E5">{{ $p->no_pengajuan }}</a></td>
            <td style="font-size:12px">{{ $p->user->name }}</td>
            <td><span class="badge badge-secondary">{{ $p->divisi->kode_divisi }}</span></td>
            <td style="font-size:12px">{{ Str::limit($p->keperluan,40) }}</td>
            <td style="font-weight:600;color:{{ $p->isDiatas10Juta()?'#DC2626':'#1E293B' }};font-size:12px">
              {{ $p->total_nilai_format }}
              @if($p->isDiatas10Juta())<i class="fas fa-exclamation-circle text-danger ml-1" title=">10 juta"></i>@endif
            </td>
            <td>
              @if($p->jalur_approval)
              <span class="badge {{ $p->jalur_approval=='direktur'?'badge-danger':'badge-warning' }}" style="font-size:10px">
                {{ $p->jalur_approval=='direktur'?'Direktur':'Wadir' }}
              </span>
              @else<span class="text-muted">-</span>@endif
            </td>
            <td><span class="badge {{ $p->status_badge }}">{{ $p->status_label }}</span></td>
            <td style="font-size:11px">{{ $p->tanggal_pengajuan?->format('d/m/Y')??'-' }}</td>
            <td>
              <a href="{{ route('pengajuan.show',$p) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
              @if($p->status->value==='draft'&&auth()->id()===$p->user_id)
              <a href="{{ route('pengajuan.edit',$p) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
              <form action="{{ route('pengajuan.destroy',$p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengajuan ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
              </form>
              @endif
            </td>
          </tr>
          @empty<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada pengajuan</td></tr>@endforelse
        </tbody>
      </table>
    </div>
    {{ $pengajuans->links() }}
  </div>
</div>
@endsection
