@extends('layouts.app')
@section('title','Laporan')
@section('page-title','Laporan')
@section('breadcrumb')<li class="breadcrumb-item active">Laporan</li>@endsection
@section('content')
<div class="row">
  @php $cards=[['route'=>'laporan.barang-masuk','icon'=>'fa-arrow-circle-down','color'=>'success','title'=>'Barang Masuk','desc'=>'Rekapitulasi penerimaan barang per periode'],['route'=>'laporan.barang-keluar','icon'=>'fa-arrow-circle-up','color'=>'danger','title'=>'Barang Keluar','desc'=>'Rekapitulasi pengeluaran barang per divisi'],['route'=>'laporan.pengajuan','icon'=>'fa-file-alt','color'=>'primary','title'=>'Pengajuan','desc'=>'Rekap pengajuan beserta status dan nilai'],['route'=>'laporan.stock','icon'=>'fa-layer-group','color'=>'warning','title'=>'Stock Balance','desc'=>'Kondisi stok terkini seluruh barang']]; @endphp
  @foreach($cards as $c)
  <div class="col-md-3">
    <div class="card text-center" style="border:none;border-radius:16px;box-shadow:0 4px 16px rgba(0,0,0,.08);transition:all .2s;overflow:hidden">
      <div class="card-body py-4">
        <div style="width:64px;height:64px;background:{{ ['success'=>'linear-gradient(135deg,#10B981,#059669)','danger'=>'linear-gradient(135deg,#EF4444,#DC2626)','primary'=>'linear-gradient(135deg,#4F46E5,#7C3AED)','warning'=>'linear-gradient(135deg,#F59E0B,#D97706)'][$c['color']] }};border-radius:16px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px">
          <i class="fas {{ $c['icon'] }} text-white" style="font-size:26px"></i>
        </div>
        <h5 style="font-weight:700;color:#1E293B;margin-bottom:6px">{{ $c['title'] }}</h5>
        <p style="color:#94A3B8;font-size:12px;line-height:1.5;margin-bottom:20px">{{ $c['desc'] }}</p>
        <a href="{{ route($c['route']) }}" class="btn btn-{{ $c['color'] }} btn-sm btn-block" style="border-radius:8px">
          <i class="fas fa-chart-bar mr-1"></i>Lihat Laporan
        </a>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endsection
