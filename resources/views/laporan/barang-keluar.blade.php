@extends('layouts.app')
@section('title','Laporan Barang Keluar')
@section('page-title','Laporan Barang Keluar')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
<li class="breadcrumb-item active">Barang Keluar</li>
@endsection
@section('content')
<div class="card card-outline card-danger">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title"><i class="fas fa-arrow-circle-up mr-2"></i>Laporan Barang Keluar</h3>
    @if(isset($data)&&$data->count())
    <a href="{{ request()->fullUrlWithQuery(['format'=>'print']) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-print mr-1"></i>Cetak</a>
    @endif
  </div>
  <div class="card-body">
    <form method="GET" class="mb-4">
      <div class="row">
        <div class="col-md-3"><label>Dari</label><input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari',now()->startOfMonth()->format('Y-m-d')) }}"></div>
        <div class="col-md-3"><label>Sampai</label><input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai',now()->format('Y-m-d')) }}"></div>
        <div class="col-md-4"><label>Divisi</label>
          <select name="divisi_id" class="form-control form-control-sm select2">
            <option value="">Semua Divisi</option>
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
        <thead class="thead-dark"><tr><th>No</th><th>No. Transaksi</th><th>Tgl Keluar</th><th>Barang</th><th>Divisi</th><th>Jumlah</th><th>Penerima</th></tr></thead>
        <tbody>
          @foreach($data as $i=>$k)
          <tr><td>{{ $i+1 }}</td><td><code style="font-size:11px">{{ $k->no_transaksi }}</code></td><td>{{ $k->tanggal_keluar->format('d/m/Y') }}</td>
            <td><strong>{{ $k->barang->nama_barang }}</strong></td><td>{{ $k->divisi?->nama_divisi??'-' }}</td>
            <td><span class="badge badge-danger">{{ $k->jumlah }} {{ $k->barang->satuan }}</span></td><td>{{ $k->penerima??'-' }}</td></tr>
          @endforeach
        </tbody>
        <tfoot><tr style="background:#FEF2F2;font-weight:700"><td colspan="5" class="text-right">TOTAL KELUAR</td><td>{{ $data->sum('jumlah') }} unit</td><td></td></tr></tfoot>
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
