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

        {{-- Barang --}}
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

        {{-- Supplier + tombol tambah --}}
        <div class="col-md-8">
          <div class="form-group">
            <label>Supplier</label>
            <div class="input-group">
              <select name="supplier_id" id="selectSupplier" class="form-control">
                <option value="">-- Tanpa Supplier --</option>
                @foreach($suppliers as $s)
                  <option value="{{ $s->id }}" {{ old('supplier_id')==$s->id?'selected':'' }}>
                    {{ $s->nama_supplier }}
                  </option>
                @endforeach
              </select>
              <div class="input-group-append">
                <button type="button" class="btn btn-outline-success" data-toggle="modal"
                  data-target="#modalSupplier" title="Tambah supplier baru">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
            </div>
            <small class="text-muted">Belum ada? Klik <i class="fas fa-plus" style="color:#10B981"></i> untuk tambah supplier baru</small>
          </div>
        </div>

        {{-- Jumlah --}}
        <div class="col-md-4">
          <div class="form-group">
            <label>Jumlah <span class="text-danger">*</span></label>
            <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror"
              value="{{ old('jumlah') }}" min="1" required id="jml" oninput="hitung()">
            @error('jumlah')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        {{-- Harga --}}
        <div class="col-md-4">
          <div class="form-group">
            <label>Harga Satuan (Rp) <span class="text-danger">*</span></label>
            <input type="number" name="harga_satuan" class="form-control"
              value="{{ old('harga_satuan',0) }}" min="0" id="hrg" oninput="hitung()">
          </div>
        </div>

        {{-- Total --}}
        <div class="col-md-4">
          <div class="form-group">
            <label>Total Harga</label>
            <div class="form-control bg-light font-weight-bold text-success" id="total">Rp 0</div>
          </div>
        </div>

        {{-- Tanggal --}}
        <div class="col-md-4">
          <div class="form-group">
            <label>Tanggal Masuk <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_masuk" class="form-control"
              value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
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
        <h5 class="modal-title text-white" style="font-size:14px">
          <i class="fas fa-plus mr-2"></i>Tambah Supplier Baru
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-8">
            <div class="form-group">
              <label>Nama Supplier <span class="text-danger">*</span></label>
              <input type="text" id="inpNamaSupplier" class="form-control"
                placeholder="contoh: PT. Sumber Makmur">
              <div id="errNamaSupplier" class="text-danger mt-1" style="font-size:12px;display:none"></div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Kode <span class="text-danger">*</span></label>
              <input type="text" id="inpKodeSupplier" class="form-control"
                placeholder="SUP004" style="text-transform:uppercase"
                oninput="this.value=this.value.toUpperCase()">
              <div id="errKodeSupplier" class="text-danger mt-1" style="font-size:12px;display:none"></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Kontak Person</label>
              <input type="text" id="inpKontakSupplier" class="form-control" placeholder="Nama PIC">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Telepon</label>
              <input type="text" id="inpTelpSupplier" class="form-control" placeholder="021-xxxxxxx">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group mb-0">
              <label>Email</label>
              <input type="email" id="inpEmailSupplier" class="form-control" placeholder="email@supplier.id">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group mb-0">
              <label>Alamat</label>
              <input type="text" id="inpAlamatSupplier" class="form-control" placeholder="Kota, Provinsi">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="padding:10px 20px">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-success btn-sm" id="btnSimpanSupplier">
          <i class="fas fa-save mr-1"></i>Simpan Supplier
        </button>
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
  document.getElementById('total').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(j * h);
}

document.getElementById('btnSimpanSupplier').addEventListener('click', function () {
  const nama  = document.getElementById('inpNamaSupplier').value.trim();
  const kode  = document.getElementById('inpKodeSupplier').value.trim().toUpperCase();
  const kontak= document.getElementById('inpKontakSupplier').value.trim();
  const telp  = document.getElementById('inpTelpSupplier').value.trim();
  const email = document.getElementById('inpEmailSupplier').value.trim();
  const alamat= document.getElementById('inpAlamatSupplier').value.trim();

  const errNama = document.getElementById('errNamaSupplier');
  const errKode = document.getElementById('errKodeSupplier');
  errNama.style.display = 'none';
  errKode.style.display = 'none';

  if (!nama) { errNama.textContent = 'Nama supplier wajib diisi.'; errNama.style.display = 'block'; return; }
  if (!kode) { errKode.textContent = 'Kode supplier wajib diisi.'; errKode.style.display = 'block'; return; }

  const btn = this;
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...';

  fetch('{{ route("supplier.store") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
    },
    body: JSON.stringify({ nama_supplier: nama, kode_supplier: kode, kontak_person: kontak, telepon: telp, email: email, alamat: alamat })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      // Tambah option baru dan pilih otomatis
      const select = document.getElementById('selectSupplier');
      const option = new Option(data.supplier.nama_supplier, data.supplier.id, true, true);
      select.appendChild(option);
      select.value = data.supplier.id;

      // Reset form modal
      ['inpNamaSupplier','inpKodeSupplier','inpKontakSupplier','inpTelpSupplier','inpEmailSupplier','inpAlamatSupplier']
        .forEach(id => document.getElementById(id).value = '');
      $('#modalSupplier').modal('hide');

      // Notif
      const alert = document.createElement('div');
      alert.className = 'alert alert-success alert-dismissible fade show';
      alert.innerHTML = `<i class="fas fa-check-circle mr-2"></i>Supplier <strong>${data.supplier.nama_supplier}</strong> berhasil ditambahkan dan dipilih otomatis. <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>`;
      document.querySelector('.content .container-fluid').prepend(alert);
      setTimeout(() => alert.remove(), 4000);
    } else {
      if (data.errors?.nama_supplier) { errNama.textContent = data.errors.nama_supplier[0]; errNama.style.display = 'block'; }
      if (data.errors?.kode_supplier) { errKode.textContent = data.errors.kode_supplier[0]; errKode.style.display = 'block'; }
    }
  })
  .catch(() => {
    errNama.textContent = 'Terjadi kesalahan. Coba lagi.';
    errNama.style.display = 'block';
  })
  .finally(() => {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-save mr-1"></i>Simpan Supplier';
  });
});

$('#modalSupplier').on('hidden.bs.modal', function () {
  ['inpNamaSupplier','inpKodeSupplier','inpKontakSupplier','inpTelpSupplier','inpEmailSupplier','inpAlamatSupplier']
    .forEach(id => document.getElementById(id).value = '');
  ['errNamaSupplier','errKodeSupplier'].forEach(id => document.getElementById(id).style.display = 'none');
});
</script>
@endpush