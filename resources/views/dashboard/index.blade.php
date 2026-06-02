@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('breadcrumb')<li class="breadcrumb-item active">Dashboard</li>@endsection
@section('content')
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-primary">
      <div class="inner"><h3>{{ $stockSummary['total_jenis_barang'] }}</h3><p>Jenis Barang</p></div>
      <div class="icon"><i class="fas fa-box"></i></div>
      <a href="{{ route('stock.index') }}" class="small-box-footer">Lihat Stock <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-success">
      <div class="inner"><h3>{{ $pengajuanSummary['disetujui'] }}</h3><p>Pengajuan Disetujui</p></div>
      <div class="icon"><i class="fas fa-check-circle"></i></div>
      <a href="{{ route('pengajuan.index') }}?status=disetujui" class="small-box-footer">Lihat <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
      <div class="inner"><h3>{{ $pengajuanSummary['menunggu'] }}</h3><p>Menunggu Proses</p></div>
      <div class="icon"><i class="fas fa-clock"></i></div>
      <a href="{{ route('pengajuan.index') }}" class="small-box-footer">Lihat <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-danger">
      <div class="inner"><h3>{{ $stockSummary['stok_menipis'] + $stockSummary['stok_habis'] }}</h3><p>Stok Perlu Perhatian</p></div>
      <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
      <a href="{{ route('stock.index') }}?filter=menipis" class="small-box-footer">Lihat <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-8">
    <div class="card card-outline card-primary">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Barang Masuk & Keluar (6 Bulan)</h3></div>
      <div class="card-body"><canvas id="chartBarang" height="110"></canvas></div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card card-outline card-warning">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-exclamation-triangle mr-2 text-warning"></i>Alert Stok</h3></div>
      <div class="card-body p-0">
        @forelse($alertStok as $s)
        <div class="d-flex align-items-center px-3 py-2" style="border-bottom:1px solid #F1F5F9">
          <div class="flex-grow-1">
            <div style="font-weight:600;font-size:13px;color:#1E293B">{{ $s->nama_barang }}</div>
            <small class="text-muted">{{ $s->kode_barang }}</small>
          </div>
          <span class="badge {{ $s->stok_tersedia<=0?'badge-danger':'badge-warning' }}">{{ $s->stok_tersedia }} {{ $s->satuan }}</span>
        </div>
        @empty
        <p class="text-center text-muted py-4 mb-0"><i class="fas fa-check-circle text-success d-block mb-1" style="font-size:24px"></i>Semua stok aman</p>
        @endforelse
      </div>
    </div>

    <div class="card card-outline card-info">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Bulan Ini</h3></div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex justify-content-between py-2">
            <span style="font-size:13px"><i class="fas fa-arrow-down text-success mr-2"></i>Barang Masuk</span>
            <strong>{{ $stockSummary['total_masuk_bulan'] }} unit</strong>
          </li>
          <li class="list-group-item d-flex justify-content-between py-2">
            <span style="font-size:13px"><i class="fas fa-arrow-up text-danger mr-2"></i>Barang Keluar</span>
            <strong>{{ $stockSummary['total_keluar_bulan'] }} unit</strong>
          </li>
          <li class="list-group-item d-flex justify-content-between py-2">
            <span style="font-size:13px"><i class="fas fa-file-alt text-warning mr-2"></i>Pengajuan Baru</span>
            <strong>{{ $pengajuanSummary['bulan_ini'] }}</strong>
          </li>
          <li class="list-group-item d-flex justify-content-between py-2">
            <span style="font-size:13px"><i class="fas fa-money-bill text-primary mr-2"></i>Nilai Pengajuan</span>
            <strong style="color:#4F46E5;font-size:12px">Rp {{ number_format($pengajuanSummary['nilai_bulan_ini'],0,',','.') }}</strong>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card card-outline card-secondary">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title"><i class="fas fa-list mr-2"></i>Pengajuan Terbaru</h3>
    <a href="{{ route('pengajuan.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="thead-light">
          <tr><th>No. Pengajuan</th><th>Pengaju</th><th>Divisi</th><th>Keperluan</th><th>Nilai</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
          @forelse($pengajuanTerbaru as $p)
          <tr>
            <td><a href="{{ route('pengajuan.show',$p) }}" style="font-weight:600;color:#4F46E5">{{ $p->no_pengajuan }}</a></td>
            <td>{{ $p->user->name }}</td>
            <td><span class="badge badge-secondary">{{ $p->divisi->kode_divisi }}</span></td>
            <td>{{ Str::limit($p->keperluan,40) }}</td>
            <td style="font-weight:600;color:{{ $p->isDiatas10Juta()?'#DC2626':'#1E293B' }}">{{ $p->total_nilai_format }}</td>
            <td><span class="badge {{ $p->status_badge }}">{{ $p->status_label }}</span></td>
            <td><a href="{{ route('pengajuan.show',$p) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a></td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center text-muted py-4">Belum ada pengajuan</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
const ctx=document.getElementById('chartBarang').getContext('2d');
new Chart(ctx,{
  type:'bar',
  data:{
    labels:{!! json_encode($bulanLabels) !!},
    datasets:[
      {label:'Barang Masuk',data:{!! json_encode($bulanMasuk) !!},backgroundColor:'rgba(16,185,129,.7)',borderRadius:5},
      {label:'Barang Keluar',data:{!! json_encode($bulanKeluar) !!},backgroundColor:'rgba(239,68,68,.7)',borderRadius:5}
    ]
  },
  options:{responsive:true,plugins:{legend:{position:'top'}},scales:{y:{beginAtZero:true,ticks:{stepSize:1}}}}
});
</script>
@endpush
