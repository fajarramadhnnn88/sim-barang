@extends('layouts.app')
@section('title','Laporan Pengajuan')
@section('page-title','Laporan Pengajuan')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
<li class="breadcrumb-item active">Pengajuan</li>
@endsection
@section('content')
<div class="card card-outline card-primary">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title"><i class="fas fa-file-alt mr-2"></i>Laporan Pengajuan</h3>
    @if(isset($data)&&$data->count())
    <a href="{{ request()->fullUrlWithQuery(['format'=>'print']) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-print mr-1"></i>Cetak</a>
    @endif
  </div>
  <div class="card-body">
    <form method="GET" class="mb-4">
      <div class="row">
        <div class="col-md-2"><label>Dari</label><input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari',now()->startOfMonth()->format('Y-m-d')) }}"></div>
        <div class="col-md-2"><label>Sampai</label><input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai',now()->format('Y-m-d')) }}"></div>
        <div class="col-md-3"><label>Status</label>
          <select name="status" class="form-control form-control-sm">
            <option value="">Semua Status</option>
            @foreach(\App\Enums\StatusPengajuan::cases() as $s)<option value="{{ $s->value }}" {{ request('status')==$s->value?'selected':'' }}>{{ $s->label() }}</option>@endforeach
          </select>
        </div>
        <div class="col-md-3"><label>Divisi</label>
          <select name="divisi_id" class="form-control form-control-sm select2">
            <option value="">Semua</option>
            @foreach(\App\Models\Divisi::orderBy('nama_divisi')->get() as $d)<option value="{{ $d->id }}" {{ request('divisi_id')==$d->id?'selected':'' }}>{{ $d->nama_divisi }}</option>@endforeach
          </select>
        </div>
        <div class="col-md-2 d-flex align-items-end"><button class="btn btn-info btn-sm w-100"><i class="fas fa-search mr-1"></i>Tampilkan</button></div>
      </div>
    </form>
    @isset($data)
    @if($data->count())
    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="tbl">
        <thead class="thead-dark"><tr><th>No</th><th>No. Pengajuan</th><th>Pengaju</th><th>Divisi</th><th>Keperluan</th><th>Total Nilai</th><th>Jalur</th><th>Status</th><th>Tgl</th></tr></thead>
        <tbody>
          @foreach($data as $i=>$p)
          <tr>
            <td>{{ $i+1 }}</td>
            <td><code style="font-size:11px">{{ $p->no_pengajuan }}</code></td>
            <td>{{ $p->user->name }}</td><td>{{ $p->divisi->nama_divisi }}</td>
            <td>{{ Str::limit($p->keperluan,35) }}</td>
            <td style="font-weight:600;color:{{ $p->isDiatas10Juta()?'#DC2626':'#1E293B' }}">{{ $p->total_nilai_format }}</td>
            <td>{{ $p->jalur_approval?ucfirst(str_replace('_',' ',$p->jalur_approval)):'-' }}</td>
            <td><span class="badge {{ $p->status_badge }}">{{ $p->status_label }}</span></td>
            <td>{{ $p->tanggal_pengajuan?->format('d/m/Y')??'-' }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot><tr style="background:#EEF2FF;font-weight:700"><td colspan="5" class="text-right">TOTAL NILAI</td><td style="color:#4F46E5">Rp {{ number_format($totalNilai,0,',','.') }}</td><td colspan="3"></td></tr></tfoot>
      </table>
    </div>
    @else<div class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3 d-block" style="opacity:.3"></i>Tidak ada data</div>@endif
    @endisset
  </div>
</div>
@endsection
@push('scripts')
<script>$('#tbl').DataTable({paging:false,searching:false,info:false,order:[]});</script>
@endpush
