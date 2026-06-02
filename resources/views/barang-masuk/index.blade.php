@extends('layouts.app')
@section('title','Barang Masuk')
@section('page-title','Barang Masuk')
@section('breadcrumb')<li class="breadcrumb-item active">Barang Masuk</li>@endsection
@section('content')
<div class="card card-outline card-success">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title"><i class="fas fa-arrow-circle-down mr-2"></i>Daftar Barang Masuk</h3>
    <a href="{{ route('barang-masuk.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus mr-1"></i>Tambah</a>
  </div>
  <div class="card-body">
    <form method="GET" class="mb-3">
      <div class="row">
        <div class="col-md-3"><input type="text" name="search" class="form-control form-control-sm" placeholder="Cari no. transaksi / barang..." value="{{ request('search') }}"></div>
        <div class="col-md-3"><input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}" placeholder="Dari"></div>
        <div class="col-md-3"><input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}" placeholder="Sampai"></div>
        <div class="col-md-3">
          <button class="btn btn-info btn-sm mr-1"><i class="fas fa-search"></i></button>
          <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        </div>
      </div>
    </form>
    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead class="thead-light">
          <tr><th>No. Transaksi</th><th>Tgl Masuk</th><th>Barang</th><th>Supplier</th><th class="text-center">Jumlah</th><th>Harga Satuan</th><th>Total</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          @forelse($items as $m)
          <tr>
            <td><code style="font-size:11px;background:#F1F5F9;padding:2px 6px;border-radius:4px">{{ $m->no_transaksi }}</code></td>
            <td>{{ $m->tanggal_masuk->format('d/m/Y') }}</td>
            <td><strong style="font-size:13px">{{ $m->barang->nama_barang }}</strong><br><small class="text-muted">{{ $m->barang->kode_barang }}</small></td>
            <td>{{ $m->supplier?->nama_supplier??'-' }}</td>
            <td class="text-center"><span class="badge badge-success">{{ $m->jumlah }} {{ $m->barang->satuan }}</span></td>
            <td>Rp {{ number_format($m->harga_satuan,0,',','.') }}</td>
            <td style="font-weight:600;color:#059669">{{ $m->total_harga_format }}</td>
            <td>
              <a href="{{ route('barang-masuk.show',$m) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
              <a href="{{ route('barang-masuk.edit',$m) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
              <form action="{{ route('barang-masuk.destroy',$m) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus? Stok akan dikurangi.')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
          @empty<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data</td></tr>@endforelse
        </tbody>
      </table>
    </div>
    {{ $items->links() }}
  </div>
</div>
@endsection
