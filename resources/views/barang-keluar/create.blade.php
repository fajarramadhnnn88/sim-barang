@extends('layouts.app')
@section('title','Tambah Barang Keluar')
@section('page-title','Tambah Barang Keluar')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('barang-keluar.index') }}">Barang Keluar</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection
@section('content')
<div class="card card-outline card-danger" style="max-width:700px">
  <div class="card-header"><h3 class="card-title"><i class="fas fa-plus mr-2"></i>Form Barang Keluar</h3></div>
  <form action="{{ route('barang-keluar.store') }}" method="POST">
    @csrf
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label>Barang <span class="text-danger">*</span></label>
            <select name="barang_id" class="form-control select2 @error('barang_id') is-invalid @enderror" required id="selBarang">
              <option value="">-- Pilih Barang --</option>
              @foreach($barangs as $b)<option value="{{ $b->id }}" data-stok="{{ $b->stok_tersedia }}" data-sat="{{ $b->satuan }}" {{ old('barang_id')==$b->id?'selected':'' }}>{{ $b->kode_barang }} — {{ $b->nama_barang }} (Stok: {{ $b->stok_tersedia }} {{ $b->satuan }})</option>@endforeach
            </select>
            @error('barang_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small id="infoStok" class="text-muted mt-1 d-block"></small>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Divisi Penerima</label>
            <select name="divisi_id" class="form-control select2">
              <option value="">-- Pilih Divisi --</option>
              @foreach($divisis as $d)<option value="{{ $d->id }}" {{ old('divisi_id')==$d->id?'selected':'' }}>{{ $d->nama_divisi }}</option>@endforeach
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Jumlah <span class="text-danger">*</span></label>
            <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah') }}" min="1" required>
            @error('jumlah')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Tanggal Keluar <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_keluar" class="form-control" value="{{ old('tanggal_keluar',date('Y-m-d')) }}" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Nama Penerima</label>
            <input type="text" name="penerima" class="form-control" value="{{ old('penerima') }}" placeholder="Nama lengkap penerima">
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-danger"><i class="fas fa-save mr-1"></i>Simpan</button>
      <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary ml-2">Batal</a>
    </div>
  </form>
</div>
@endsection
@push('scripts')
<script>
$('#selBarang').on('change',function(){const o=this.options[this.selectedIndex];const s=o.dataset.stok,t=o.dataset.sat;document.getElementById('infoStok').textContent=s?`Stok tersedia: ${s} ${t}`:'';});
</script>
@endpush
