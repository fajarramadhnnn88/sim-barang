@extends('layouts.app')
@section('title','Laporan Stock')
@section('page-title','Laporan Stock Balance')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
<li class="breadcrumb-item active">Stock</li>
@endsection
@section('content')
<div class="card card-outline card-warning">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title"><i class="fas fa-layer-group mr-2"></i>Laporan Stock Balance</h3>
    @if(isset($data)&&$data->count())
    <a href="{{ request()->fullUrlWithQuery(['format'=>'print']) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-print mr-1"></i>Cetak</a>
    @endif
  </div>
  <div class="card-body">
    <form method="GET" class="mb-4">
      <div class="row">
        <div class="col-md-3"><label>Filter Stok</label>
          <select name="filter" class="form-control form-control-sm">
            <option value="">Semua Stok</option>
            <option value="menipis" {{ request('filter')=='menipis'?'selected':'' }}>Stok Menipis</option>
            <option value="habis" {{ request('filter')=='habis'?'selected':'' }}>Stok Habis</option>
          </select>
        </div>
        <div class="col-md-2 d-flex align-items-end"><button class="btn btn-info btn-sm w-100"><i class="fas fa-search mr-1"></i>Tampilkan</button></div>
      </div>
    </form>
    @isset($data)
    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="tbl">
        <thead class="thead-dark"><tr><th>No</th><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Satuan</th><th class="text-center">Masuk</th><th class="text-center">Keluar</th><th class="text-center">Tersedia</th><th class="text-center">Minimum</th><th class="text-center">Status</th></tr></thead>
        <tbody>
          @foreach($data as $i=>$s)
          @php $b=$s->barang; @endphp
          <tr class="{{ $s->stok_tersedia<=0?'table-danger':($s->stok_tersedia<=$b->stok_minimum?'table-warning':'') }}">
            <td>{{ $i+1 }}</td><td><code style="font-size:11px">{{ $b->kode_barang }}</code></td>
            <td>{{ $b->nama_barang }}</td><td>{{ $b->kategori->nama_kategori }}</td><td>{{ $b->satuan }}</td>
            <td class="text-center font-weight-bold text-success">{{ $s->stok_masuk }}</td>
            <td class="text-center font-weight-bold text-danger">{{ $s->stok_keluar }}</td>
            <td class="text-center font-weight-bold">{{ $s->stok_tersedia }}</td>
            <td class="text-center">{{ $b->stok_minimum }}</td>
            <td class="text-center"><span class="badge {{ $s->status_badge }}">{{ $s->status_stok }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endisset
  </div>
</div>
@endsection
@push('scripts')
<script>$('#tbl').DataTable({paging:false,searching:true,info:true,order:[]});</script>
@endpush
