@extends('layouts.app')
@section('title','Barang Masuk')
@section('page-title','Barang Masuk')
@section('breadcrumb')<li class="breadcrumb-item active">Barang Masuk</li>@endsection
@section('content')
@php
  // Purchasing & superadmin yang boleh tambah/edit/hapus (tugas inti Purchasing).
  // Admin Divisi hanya bisa melihat di sini.
  $bolehKelola = auth()->user()->isPurchasing() || auth()->user()->isSuperadmin();
@endphp
<div class="card card-outline card-success">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title">
      <i class="fas fa-truck-loading mr-2"></i>Daftar Barang Masuk
      @unless($bolehKelola)
        <span class="badge badge-warning ml-2" style="font-size:10px"><i class="fas fa-eye mr-1"></i>Lihat Saja</span>
      @endunless
    </h3>
    @if($bolehKelola)
      <a href="{{ route('barang-masuk.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus mr-1"></i>Tambah</a>
    @endif
  </div>
  <div class="card-body">
    <form method="GET" class="mb-3">
      <div class="row">
        <div class="col-md-3"><input type="text" name="search" class="form-control form-control-sm" placeholder="Cari no. transaksi / barang..." value="{{ request('search') }}"></div>
        <div class="col-md-3"><input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}"></div>
        <div class="col-md-3"><input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}"></div>
        <div class="col-md-3">
          <button class="btn btn-info btn-sm mr-1"><i class="fas fa-search"></i></button>
          <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        </div>
      </div>
    </form>
    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead class="thead-light">
          <tr>
            <th>No. Transaksi</th><th>Tgl Masuk</th><th>Barang</th><th>Supplier</th>
            <th class="text-center">Jumlah</th><th>PIC</th><th class="text-center">Foto</th>
            <th>Pengajuan</th><th>Total</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $m)
          <tr>
            <td><code style="font-size:11px;background:#F1F5F9;padding:2px 6px;border-radius:4px">{{ $m->no_transaksi }}</code></td>
            <td>{{ $m->tanggal_masuk->format('d/m/Y') }}</td>
            <td><strong style="font-size:13px">{{ $m->barang->nama_barang }}</strong><br><small class="text-muted">{{ $m->barang->kode_barang }}</small></td>
            <td>{{ $m->supplier?->nama_supplier ?? '-' }}</td>
            <td class="text-center"><span class="badge badge-success">{{ $m->jumlah }} {{ $m->barang->satuan }}</span></td>
            <td style="font-size:12px">{{ $m->pic_name ?? '-' }}</td>
            <td class="text-center">
              @if($m->foto_dokumentasi_url)
                <a href="{{ $m->foto_dokumentasi_url }}" target="_blank">
                  <img src="{{ $m->foto_dokumentasi_url }}" style="width:36px;height:36px;object-fit:cover;border-radius:6px;border:1px solid #E2E8F0">
                </a>
              @else <span class="text-muted">-</span> @endif
            </td>
            <td style="font-size:11px">
              @if($m->pengajuan)
                <a href="{{ route('pengajuan.show',$m->pengajuan) }}">{{ $m->pengajuan->no_pengajuan }}</a>
              @else <span class="text-muted">-</span> @endif
            </td>
            <td style="font-weight:600;color:#059669">{{ $m->total_harga_format }}</td>
            <td>
              <a href="{{ route('barang-masuk.show',$m) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
              @if($bolehKelola)
                <a href="{{ route('barang-masuk.edit',$m) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                <form action="{{ route('barang-masuk.destroy',$m) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus? Stok akan dikurangi.')">
                  @csrf @method('DELETE')
                  <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                </form>
              @endif
            </td>
          </tr>
          @empty<tr><td colspan="10" class="text-center text-muted py-4">Tidak ada data</td></tr>@endforelse
        </tbody>
      </table>
    </div>
    {{ $items->links() }}
  </div>
</div>
@endsection