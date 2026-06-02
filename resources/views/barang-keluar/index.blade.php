@extends('layouts.app')
@section('title','Barang Keluar')
@section('page-title','Barang Keluar')
@section('breadcrumb')<li class="breadcrumb-item active">Barang Keluar</li>@endsection
@section('content')
<div class="card card-outline card-danger">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title"><i class="fas fa-arrow-circle-up mr-2"></i>Daftar Barang Keluar</h3>
    <a href="{{ route('barang-keluar.create') }}" class="btn btn-danger btn-sm"><i class="fas fa-plus mr-1"></i>Tambah</a>
  </div>
  <div class="card-body">
    <form method="GET" class="mb-3">
      <div class="row">
        <div class="col-md-3"><input type="text" name="search" class="form-control form-control-sm" placeholder="Cari no. transaksi..." value="{{ request('search') }}"></div>
        <div class="col-md-3"><input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}"></div>
        <div class="col-md-3"><input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}"></div>
        <div class="col-md-3">
          <button class="btn btn-info btn-sm mr-1"><i class="fas fa-search"></i></button>
          <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        </div>
      </div>
    </form>
    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead class="thead-light">
          <tr><th>No. Transaksi</th><th>Tgl Keluar</th><th>Barang</th><th>Divisi</th><th class="text-center">Jumlah</th><th>Penerima</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          @forelse($items as $k)
          <tr>
            <td><code style="font-size:11px;background:#F1F5F9;padding:2px 6px;border-radius:4px">{{ $k->no_transaksi }}</code></td>
            <td>{{ $k->tanggal_keluar->format('d/m/Y') }}</td>
            <td><strong style="font-size:13px">{{ $k->barang->nama_barang }}</strong></td>
            <td>{{ $k->divisi?->nama_divisi??'-' }}</td>
            <td class="text-center"><span class="badge badge-danger">{{ $k->jumlah }} {{ $k->barang->satuan }}</span></td>
            <td>{{ $k->penerima??'-' }}</td>
            <td>
              <a href="{{ route('barang-keluar.show',$k) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
              <a href="{{ route('barang-keluar.edit',$k) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
              <form action="{{ route('barang-keluar.destroy',$k) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
          @empty<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data</td></tr>@endforelse
        </tbody>
      </table>
    </div>
    {{ $items->links() }}
  </div>
</div>
@endsection
