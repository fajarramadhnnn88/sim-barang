@extends('layouts.app')
@section('title','Tambah Barang Masuk')
@section('page-title','Tambah Barang Masuk')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('barang-masuk.index') }}">Barang Masuk</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection
@section('content')
<div class="card card-outline card-success" style="max-width:760px">
  <div class="card-header"><h3 class="card-title"><i class="fas fa-plus mr-2"></i>Form Barang Masuk</h3></div>
  <form action="{{ route('barang-masuk.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card-body">

      {{-- Link ke Pengajuan (kalau ada proses pembelian berjalan) --}}
      @if($pengajuan)
        <div class="callout callout-info py-2 mb-3">
          <strong><i class="fas fa-link mr-1"></i>Terhubung ke Pengajuan:</strong>
          {{ $pengajuan->no_pengajuan }} — {{ $pengajuan->keperluan }}
          <input type="hidden" name="pengajuan_id" value="{{ $pengajuan->id }}">
        </div>
      @else
        <div class="form-group">
          <label>Hubungkan ke Pengajuan <small class="text-muted">(opsional)</small></label>
          <select name="pengajuan_id" class="form-control select2">
            <option value="">-- Tanpa Pengajuan (Input Manual) --</option>
            @foreach($pengajuanProses as $p)
              <option value="{{ $p->id }}" {{ old('pengajuan_id')==$p->id?'selected':'' }}>
                {{ $p->no_pengajuan }} — {{ $p->keperluan }} ({{ $p->divisi->nama_divisi }})
              </option>
            @endforeach
          </select>
          <small class="text-muted">Pilih jika barang ini hasil pembelian dari pengajuan yang disetujui</small>
        </div>
      @endif

      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label>Barang <span class="text-danger">*</span></label>
            <select name="barang_id" class="form-control select2 @error('barang_id') is-invalid @enderror" required>
              <option value="">-- Pilih Barang --</option>
              @foreach($barangs as $b)
                <option value="{{ $b->id }}" {{ old('barang_id')==$b->id?'selected':'' }}>
                  {{ $b->kode_barang }} — {{ $b->nama_barang }} (Stok: {{ $b->stok_tersedia }} {{ $b->satuan }})
                </option>
              @endforeach
            </select>
            @error('barang_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="col-md-8">
          <div class="form-group">
            <label>Supplier</label>
            <div class="input-group">
              <select name="supplier_id" id="selectSupplier" class="form-control">
                <option value="">-- Tanpa Supplier --</option>
                @foreach($suppliers as $s)
                  <option value="{{ $s->id }}" {{ old('supplier_id')==$s->id?'selected':'' }}>{{ $s->nama_supplier }}</option>
                @endforeach
              </select>
              <div class="input-group-append">
                <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#modalSupplier">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Nama PIC <span class="text-danger">*</span></label>
            <input type="text" name="pic_name" class="form-control @error('pic_name') is-invalid @enderror"
              value="{{ old('pic_name', auth()->user()->name) }}" placeholder="Nama yang melakukan pembelian" required>
            @error('pic_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Jumlah <span class="text-danger">*</span></label>
            <input type="number" name="jumlah" class="form-control" value="{{ old('jumlah') }}" min="1" required
              id="jml" oninput="hitung()">
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Harga Satuan (Rp) <span class="text-danger">*</span></label>
            <input type="number" name="harga_satuan" class="form-control" value="{{ old('harga_satuan',0) }}"
              min="0" id="hrg" oninput="hitung()">
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>Total Harga</label>
            <div class="form-control bg-light font-weight-bold text-success" id="total">Rp 0</div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Tanggal Masuk <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>No. Surat Jalan</label>
            <input type="text" name="no_surat_jalan" class="form-control" value="{{ old('no_surat_jalan') }}">
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>No. PO</label>
            <input type="text" name="no_po" class="form-control" value="{{ old('no_po') }}">
          </div>
        </div>

        {{-- Foto dokumentasi --}}
        <div class="col-md-12">
          <div class="form-group">
            <label>Foto Dokumentasi Barang</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" name="foto_dokumentasi" accept="image/*"
                id="fotoInput" onchange="previewFoto(this)">
              <label class="custom-file-label" for="fotoInput">Pilih foto sebagai bukti...</label>
            </div>
            <img id="fotoPreview" src="" class="mt-2 rounded" style="max-height:140px;display:none;border:1px solid #E2E8F0">
            <small class="text-muted d-block mt-1">Foto fisik barang sebagai bukti dokumentasi penerimaan. JPG/PNG/WEBP maks 3MB</small>
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

{{-- Modal Tambah Supplier --}}
<div class="modal fade" id="modalSupplier" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:linear-gradient(135deg,#10B981,#059669);padding:14px 20px">
        <h5 class="modal-title text-white" style="font-size:14px"><i class="fas fa-plus mr-2"></i>Tambah Supplier Baru</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-8">
            <div class="form-group">
              <label>Nama Supplier <span class="text-danger">*</span></label>
              <input type="text" id="inpNamaSupplier" class="form-control" placeholder="contoh: PT. Sumber Makmur">
              <div id="errNamaSupplier" class="text-danger mt-1" style="font-size:12px;display:none"></div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Kode <span class="text-danger">*</span></label>
              <input type="text" id="inpKodeSupplier" class="form-control" placeholder="SUP004"
                style="text-transform:uppercase" oninput="this.value=this.value.toUpperCase()">
              <div id="errKodeSupplier" class="text-danger mt-1" style="font-size:12px;display:none"></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group"><label>Kontak Person</label><input type="text" id="inpKontakSupplier" class="form-control"></div>
          </div>
          <div class="col-md-6">
            <div class="form-group"><label>Telepon</label><input type="text" id="inpTelpSupplier" class="form-control"></div>
          </div>
          <div class="col-md-6">
            <div class="form-group mb-0"><label>Email</label><input type="email" id="inpEmailSupplier" class="form-control"></div>
          </div>
          <div class="col-md-6">
            <div class="form-group mb-0"><label>Alamat</label><input type="text" id="inpAlamatSupplier" class="form-control"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="padding:10px 20px">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-success btn-sm" id="btnSimpanSupplier"><i class="fas fa-save mr-1"></i>Simpan</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function hitung() {
  const j = parseFloat(document.getElementById('jml').value) || 0;
  const h = parseFloat(document.getElementById('hrg').value) || 0;
  document.getElementById('total').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(j*h);
}
function previewFoto(i) {
  const p = document.getElementById('fotoPreview');
  if (i.files && i.files[0]) {
    p.src = URL.createObjectURL(i.files[0]); p.style.display = 'block';
    i.nextElementSibling.textContent = i.files[0].name;
  }
}
document.getElementById('btnSimpanSupplier').addEventListener('click', function () {
  const nama=document.getElementById('inpNamaSupplier').value.trim();
  const kode=document.getElementById('inpKodeSupplier').value.trim().toUpperCase();
  const errN=document.getElementById('errNamaSupplier'), errK=document.getElementById('errKodeSupplier');
  errN.style.display='none'; errK.style.display='none';
  if(!nama){errN.textContent='Nama supplier wajib diisi.';errN.style.display='block';return;}
  if(!kode){errK.textContent='Kode supplier wajib diisi.';errK.style.display='block';return;}
  const btn=this; btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...';
  fetch('{{ route("supplier.store") }}',{
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
    body:JSON.stringify({
      nama_supplier:nama, kode_supplier:kode,
      kontak_person:document.getElementById('inpKontakSupplier').value.trim(),
      telepon:document.getElementById('inpTelpSupplier').value.trim(),
      email:document.getElementById('inpEmailSupplier').value.trim(),
      alamat:document.getElementById('inpAlamatSupplier').value.trim(),
    })
  }).then(r=>r.json()).then(data=>{
    if(data.success){
      const sel=document.getElementById('selectSupplier');
      sel.appendChild(new Option(data.supplier.nama_supplier, data.supplier.id, true, true));
      sel.value=data.supplier.id;
      $('#modalSupplier').modal('hide');
    } else {
      if(data.errors?.nama_supplier){errN.textContent=data.errors.nama_supplier[0];errN.style.display='block';}
      if(data.errors?.kode_supplier){errK.textContent=data.errors.kode_supplier[0];errK.style.display='block';}
    }
  }).finally(()=>{btn.disabled=false;btn.innerHTML='<i class="fas fa-save mr-1"></i>Simpan';});
});
</script>
@endpush