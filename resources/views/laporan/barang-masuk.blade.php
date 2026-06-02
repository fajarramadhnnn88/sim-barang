@extends('layouts.app')
@section('title','Laporan Barang Masuk')
@section('page-title','Laporan Barang Masuk')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
<li class="breadcrumb-item active">Barang Masuk</li>
@endsection
@section('content')
<div class="card card-outline card-success">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title"><i class="fas fa-arrow-circle-down mr-2"></i>Laporan Barang Masuk</h3>
    @if(isset($data)&&$data->count())
    <a href="{{ request()->fullUrlWithQuery(['format'=>'print']) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-print mr-1"></i>Cetak</a>
    @endif
  </div>
  <div class="card-body">
    <form method="GET" class="mb-4">
      <div class="row">
        <div class="col-md-3"><label>Dari Tanggal</label><input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari',now()->startOfMonth()->format('Y-m-d')) }}"></div>
        <div class="col-md-3"><label>Sampai Tanggal</label><input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai',now()->format('Y-m-d')) }}"></div>
        <div class="col-md-4"><label>Barang</label>
          <select name="barang_id" class="form-control form-control-sm select2">
            <option value="">Semua Barang</option>
            @foreach(\App\Models\Barang::orderBy('nama_barang')->get() as $b)<option value="{{ $b->id }}" {{ request('barang_id')==$b->id?'selected':'' }}>{{ $b->nama_barang }}</option>@endforeach
          </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button class="btn btn-info btn-sm mr-1 w-100"><i class="fas fa-search mr-1"></i>Tampilkan</button>
        </div>
      </div>
    </form>
    @isset($data)
    @if($data->count())
    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="tbl">
        <thead class="thead-dark">
          <tr><th>No</th><th>No. Transaksi</th><th>Tgl Masuk</th><th>Barang</th><th>Supplier</th><th>Jumlah</th><th>Harga Satuan</th><th>Total</th></tr>
        </thead>
        <tbody>
          @foreach($data as $i=>$m)
          <tr>
            <td>{{ $i+1 }}</td>
            <td><code style="font-size:11px">{{ $m->no_transaksi }}</code></td>
            <td>{{ $m->tanggal_masuk->format('d/m/Y') }}</td>
            <td><strong>{{ $m->barang->nama_barang }}</strong></td>
            <td>{{ $m->supplier?->nama_supplier??'-' }}</td>
            <td>{{ $m->jumlah }} {{ $m->barang->satuan }}</td>
            <td>Rp {{ number_format($m->harga_satuan,0,',','.') }}</td>
            <td style="font-weight:600;color:#059669">{{ $m->total_harga_format }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr style="background:#ECFDF5;font-weight:700">
            <td colspan="7" class="text-right">TOTAL</td>
            <td style="color:#059669">Rp {{ number_format($total,0,',','.') }}</td>
          </tr>
        </tfoot>
      </table>
    </div>
    <small class="text-muted">Total {{ $data->count() }} transaksi</small>
    @else<div class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3 d-block" style="opacity:.3"></i>Tidak ada data</div>@endif
    @endisset
  </div>
</div>
@endsection
@push('scripts')
<script>$('#tbl').DataTable({paging:false,searching:false,info:false,order:[]});</script>
@endpush
