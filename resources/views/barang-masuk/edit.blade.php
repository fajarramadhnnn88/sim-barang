@extends('layouts.app')
@section('title','Edit Barang Masuk')
@section('page-title','Edit Barang Masuk')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('barang-masuk.index') }}">Barang Masuk</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="card card-outline card-warning" style="max-width:760px">
  <div class="card-header"><h3 class="card-title"><i class="fas fa-edit mr-2"></i>Edit: {{ $barangMasuk->no_transaksi }}</h3></div>
  <form action="{{ route('barang-masuk.update',$barangMasuk) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="card-body">
      <div class="callout callout-warning py-2 mb-3" style="font-size:12px">
        <i class="fas fa-exclamation-triangle mr-1"></i>
        Mengedit data ini akan menghapus transaksi lama dan membuat baru. Stok otomatis dikoreksi.
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label>Hubungkan ke Pengajuan <small class="text-muted">(opsional)</small></label>
            <select name="pengajuan_id" class="form-control select2">
              <option value="">-- Tanpa Pengajuan --</option>
              @foreach($pengajuanProses as $p)
                <option value="{{ $p->id }}" {{ old('pengajuan_id',$barangMasuk->pengajuan_id)==$p->id?'selected':'' }}>
                  {{ $p->no_pengajuan }} — {{ $p->keperluan }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <label>Barang <span class="text-danger">*</span></label>
            <select name="barang_id" class="form-control select2" required>
              @foreach($barangs as $b)
                <option value="{{ $b->id }}" {{ old('barang_id',$barangMasuk->barang_id)==$b->id?'selected':'' }}>
                  {{ $b->kode_barang }} — {{ $b->nama_barang }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-md-8">
          <div class="form-group">
            <label>Supplier</label>
            <select name="supplier_id" class="form-control select2">
              <option value="">-- Tanpa Supplier --</option>
              @foreach($suppliers as $s)
                <option value="{{ $s->id }}" {{ old('supplier_id',$barangMasuk->supplier_id)==$s->id?'selected':'' }}>{{ $s->nama_supplier }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Nama PIC <span class="text-danger">*</span></label>
            <input type="text" name="pic_name" class="form-control"
              value="{{ old('pic_name',$barangMasuk->pic_name) }}" required>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Jumlah <span class="text-danger">*</span></label>
            <input type="number" name="jumlah" class="form-control" value="{{ old('jumlah',$barangMasuk->jumlah) }}" min="1" required>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Harga Satuan (Rp)</label>
            <input type="number" name="harga_satuan" class="form-control" value="{{ old('harga_satuan',$barangMasuk->harga_satuan) }}" min="0">
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Tanggal Masuk <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_masuk" class="form-control"
              value="{{ old('tanggal_masuk',$barangMasuk->tanggal_masuk->format('Y-m-d')) }}" required>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label>No. Surat Jalan</label>
            <input type="text" name="no_surat_jalan" class="form-control" value="{{ old('no_surat_jalan',$barangMasuk->no_surat_jalan) }}">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>No. PO</label>
            <input type="text" name="no_po" class="form-control" value="{{ old('no_po',$barangMasuk->no_po) }}">
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <label>Foto Dokumentasi</label>
            @if($barangMasuk->foto_dokumentasi_url)
              <div class="mb-2">
                <img src="{{ $barangMasuk->foto_dokumentasi_url }}" style="max-height:140px;border-radius:8px;border:1px solid #E2E8F0">
                <small class="text-muted d-block mt-1">Foto saat ini — upload baru untuk mengganti</small>
              </div>
            @endif
            <div class="custom-file">
              <input type="file" class="custom-file-input" name="foto_dokumentasi" accept="image/*" id="fotoInput" onchange="previewFoto(this)">
              <label class="custom-file-label" for="fotoInput">Pilih foto baru...</label>
            </div>
            <img id="fotoPreview" src="" class="mt-2 rounded" style="max-height:140px;display:none;border:1px solid #E2E8F0">
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan',$barangMasuk->keterangan) }}</textarea>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Perbarui</button>
      <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary ml-2">Batal</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
function previewFoto(i) {
  const p = document.getElementById('fotoPreview');
  if (i.files && i.files[0]) {
    p.src = URL.createObjectURL(i.files[0]); p.style.display = 'block';
    i.nextElementSibling.textContent = i.files[0].name;
  }
}
</script>
@endpush