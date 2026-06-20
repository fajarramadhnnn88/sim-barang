@extends('layouts.app')
@section('title','Data Barang')
@section('page-title','Data Barang')
@section('breadcrumb')<li class="breadcrumb-item active">Data Barang</li>@endsection
@section('content')
@php
  // Hanya admin_divisi & superadmin yang punya akses penuh.
  // Purchasing TIDAK MENDAPATKAN variabel ini bernilai true sama sekali.
  $bolehKelola = auth()->user()->isAdminDivisi() || auth()->user()->isSuperadmin();
@endphp

<div class="card card-outline card-primary">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title">
      <i class="fas fa-box mr-2"></i>Daftar Data Barang
      @unless($bolehKelola)
        <span class="badge badge-warning ml-2" style="font-size:10px"><i class="fas fa-eye mr-1"></i>Mode Lihat</span>
      @endunless
    </h3>

    {{-- Tombol Tambah HANYA dirender untuk role yang boleh kelola.
         Purchasing tidak akan pernah melihat elemen ini di HTML sama sekali. --}}
    @if($bolehKelola)
      <a href="{{ route('barang.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus mr-1"></i>Tambah Barang
      </a>
    @endif
  </div>

  <div class="card-body">
    <form method="GET" class="mb-3">
      <div class="row">
        <div class="col-md-3">
          <input type="text" name="search" class="form-control form-control-sm"
            placeholder="Cari kode / nama barang..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
          <select name="kategori_id" class="form-control form-control-sm">
            <option value="">-- Semua Kategori --</option>
            @foreach($kategoris as $k)
              <option value="{{ $k->id }}" {{ request('kategori_id')==$k->id?'selected':'' }}>{{ $k->nama_kategori }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <button class="btn btn-info btn-sm mr-1"><i class="fas fa-search mr-1"></i>Cari</button>
          <a href="{{ route('barang.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        </div>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead class="thead-light">
          <tr>
            <th style="width:50px">Foto</th>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Satuan</th>
            <th>Harga Satuan</th>
            <th class="text-center">Stok</th>
            <th>Status</th>
            <th style="width:{{ $bolehKelola ? '150px' : '70px' }}">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($barangs as $b)
          <tr>
            <td class="text-center">
              @if($b->foto)
                <img src="{{ asset('storage/'.$b->foto) }}" style="width:36px;height:36px;object-fit:cover;border-radius:6px">
              @else
                <div style="width:36px;height:36px;background:#F1F5F9;border-radius:6px;display:flex;align-items:center;justify-content:center">
                  <i class="fas fa-box text-muted" style="font-size:14px"></i>
                </div>
              @endif
            </td>
            <td><code style="font-size:11px;background:#F1F5F9;padding:2px 6px;border-radius:4px">{{ $b->kode_barang }}</code></td>
            <td>
              <strong style="font-size:13px">{{ $b->nama_barang }}</strong>
              @if($b->merk)<br><small class="text-muted">{{ $b->merk }}</small>@endif
            </td>
            <td>{{ $b->kategori->nama_kategori ?? '-' }}</td>
            <td>{{ $b->satuan }}</td>
            <td>Rp {{ number_format($b->harga_satuan,0,',','.') }}</td>
            <td class="text-center">
              <span class="badge {{ ($b->stok_tersedia ?? 0) <= 0 ? 'badge-danger' : (($b->stok_tersedia ?? 0) <= $b->stok_minimum ? 'badge-warning' : 'badge-success') }}">
                {{ $b->stok_tersedia ?? 0 }} {{ $b->satuan }}
              </span>
            </td>
            <td>
              @if(($b->stok_tersedia ?? 0) <= 0)
                <span class="badge badge-danger">Habis</span>
              @elseif(($b->stok_tersedia ?? 0) <= $b->stok_minimum)
                <span class="badge badge-warning">Menipis</span>
              @else
                <span class="badge badge-success">Aman</span>
              @endif
            </td>
            <td>
              {{-- Lihat selalu tersedia untuk semua role yang punya akses ke halaman ini --}}
              <a href="{{ route('barang.show',$b) }}" class="btn btn-xs btn-info" title="Lihat Detail">
                <i class="fas fa-eye"></i>
              </a>

              {{-- Edit & Hapus HANYA dirender untuk admin_divisi & superadmin.
                   Untuk Purchasing, blok ini SAMA SEKALI TIDAK ADA di HTML
                   (bukan disabled, tapi memang tidak dirender). --}}
              @if($bolehKelola)
                <a href="{{ route('barang.edit',$b) }}" class="btn btn-xs btn-warning" title="Edit">
                  <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('barang.destroy',$b) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Hapus barang {{ $b->nama_barang }}?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-xs btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                </form>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data barang</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{ $barangs->links() }}
  </div>
</div>
@endsection