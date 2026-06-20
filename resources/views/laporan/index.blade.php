@extends('layouts.app')
@section('title','Laporan')
@section('page-title','Laporan')
@section('breadcrumb')<li class="breadcrumb-item active">Laporan</li>@endsection
@section('content')

<div class="callout callout-info py-2 mb-3">
  <i class="fas fa-download mr-2"></i>
  Klik <strong>Unduh PDF</strong> untuk langsung mengunduh laporan ke perangkat Anda — tidak perlu preview dahulu.
</div>

<div class="row">

  {{-- Barang Masuk --}}
  <div class="col-md-6 col-lg-3 mb-3">
    <div class="card h-100" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.06)">
      <div class="card-body text-center p-4">
        <div style="width:54px;height:54px;background:linear-gradient(135deg,#10B981,#059669);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
          <i class="fas fa-truck-loading text-white" style="font-size:22px"></i>
        </div>
        <h5 style="font-weight:700;font-size:14px">Laporan Barang Masuk</h5>
        <p class="text-muted" style="font-size:12px">Rekap seluruh transaksi pembelian barang</p>
        <form action="{{ route('laporan.barang-masuk.pdf') }}" method="GET">
          <div class="form-group">
            <input type="date" name="dari" class="form-control form-control-sm mb-1" placeholder="Dari tanggal">
            <input type="date" name="sampai" class="form-control form-control-sm" placeholder="Sampai tanggal">
          </div>
          <button type="submit" class="btn btn-success btn-block btn-sm">
            <i class="fas fa-file-pdf mr-1"></i>Unduh PDF
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Barang Keluar --}}
  <div class="col-md-6 col-lg-3 mb-3">
    <div class="card h-100" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.06)">
      <div class="card-body text-center p-4">
        <div style="width:54px;height:54px;background:linear-gradient(135deg,#3B82F6,#2563EB);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
          <i class="fas fa-arrow-circle-up text-white" style="font-size:22px"></i>
        </div>
        <h5 style="font-weight:700;font-size:14px">Laporan Barang Keluar</h5>
        <p class="text-muted" style="font-size:12px">Rekap seluruh barang yang dikeluarkan</p>
        <form action="{{ route('laporan.barang-keluar.pdf') }}" method="GET">
          <div class="form-group">
            <input type="date" name="dari" class="form-control form-control-sm mb-1" placeholder="Dari tanggal">
            <input type="date" name="sampai" class="form-control form-control-sm" placeholder="Sampai tanggal">
          </div>
          <button type="submit" class="btn btn-primary btn-block btn-sm">
            <i class="fas fa-file-pdf mr-1"></i>Unduh PDF
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Pengajuan --}}
  <div class="col-md-6 col-lg-3 mb-3">
    <div class="card h-100" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.06)">
      <div class="card-body text-center p-4">
        <div style="width:54px;height:54px;background:linear-gradient(135deg,#F59E0B,#D97706);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
          <i class="fas fa-file-alt text-white" style="font-size:22px"></i>
        </div>
        <h5 style="font-weight:700;font-size:14px">Laporan Pengajuan</h5>
        <p class="text-muted" style="font-size:12px">Rekap seluruh pengajuan & statusnya</p>
        <form action="{{ route('laporan.pengajuan.pdf') }}" method="GET">
          <div class="form-group">
            <select name="status" class="form-control form-control-sm mb-1">
              <option value="">-- Semua Status --</option>
              @foreach(\App\Enums\StatusPengajuan::cases() as $st)
                <option value="{{ $st->value }}">{{ $st->label() }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="btn btn-warning btn-block btn-sm">
            <i class="fas fa-file-pdf mr-1"></i>Unduh PDF
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Stock --}}
  <div class="col-md-6 col-lg-3 mb-3">
    <div class="card h-100" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.06)">
      <div class="card-body text-center p-4">
        <div style="width:54px;height:54px;background:linear-gradient(135deg,#8B5CF6,#7C3AED);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
          <i class="fas fa-layer-group text-white" style="font-size:22px"></i>
        </div>
        <h5 style="font-weight:700;font-size:14px">Laporan Stock Balance</h5>
        <p class="text-muted" style="font-size:12px">Kondisi stok seluruh barang saat ini</p>
        <form action="{{ route('laporan.stock.pdf') }}" method="GET">
          <div class="form-group" style="min-height:62px">
            <small class="text-muted">Tanpa filter tanggal — data realtime</small>
          </div>
          <button type="submit" class="btn btn-purple btn-block btn-sm" style="background:#8B5CF6;color:white">
            <i class="fas fa-file-pdf mr-1"></i>Unduh PDF
          </button>
        </form>
      </div>
    </div>
  </div>

</div>
@endsection