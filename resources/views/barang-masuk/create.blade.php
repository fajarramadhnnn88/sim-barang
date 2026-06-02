@extends('layouts.app')
@section('title','Tambah Barang Masuk')
@section('page-title','Tambah Barang Masuk')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('barang-masuk.index') }}">Barang Masuk</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection
@section('content')
<div class="card card-outline card-success" style="max-width:700px">
  <div class="card-header"><h3 class="card-title"><i class="fas fa-plus mr-2"></i>Form Barang Masuk</h3></div>
  <form action="{{ route('barang-masuk.store') }}" method="POST">
    @csrf
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label>Barang <span class="text-danger">*</span></label>
            <select name="barang_id" class="form-control select2 @error('barang_id') is-invalid @enderror" required>
              <option value="">-- Pilih Barang --</option>
              @foreach($barangs as $b)<option value="{{ $b->id }}" {{ old('barang_id')==$b->id?'selected':'' }}>{{ $b->kode_barang }} — {{ $b->nama_barang }} (Stok: {{ $b->stok_tersedia }} {{ $b->satuan }})</option>@endforeach
            </select>
            @error('barang_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Supplier</label>
            <select name="supplier_id" class="form-control select2">
              <option value="">-- Tanpa Supplier --</option>
              @foreach($suppliers as $s)<option value="{{ $s->id }}" {{ old('supplier_id')==$s->id?'selected':'' }}>{{ $s->nama_supplier }}</option>@endforeach
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Jumlah <span class="text-danger">*</span></label>
            <input type="number" name="jumlah" class="form-control" value="{{ old('jumlah') }}" min="1" required id="jml" oninput="hitung()">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Harga Satuan <span class="text-danger">*</span></label>
            <input type="number" name="harga_satuan" class="form-control" value="{{ old('harga_satuan',0) }}" min="0" id="hrg" oninput="hitung()">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Total Harga</label>
            <div class="form-control bg-light font-weight-bold text-success" id="total">Rp 0</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Tanggal Masuk <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk',date('Y-m-d')) }}" required>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>No. Surat Jalan</label>
            <input type="text" name="no_surat_jalan" class="form-control" value="{{ old('no_surat_jalan') }}">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>No. PO</label>
            <input type="text" name="no_po" class="form-control" value="{{ old('no_po') }}">
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
      <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i>Simpan</button>
      <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary ml-2">Batal</a>
    </div>
  </form>
</div>
@endsection
@push('scripts')
<script>
function hitung(){const j=parseFloat(document.getElementById('jml').value)||0;const h=parseFloat(document.getElementById('hrg').value)||0;document.getElementById('total').textContent='Rp '+new Intl.NumberFormat('id-ID').format(j*h);}
</script>
@endpush
