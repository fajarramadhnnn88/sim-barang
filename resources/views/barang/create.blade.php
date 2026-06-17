@extends('layouts.app')
@section('title','Tambah Barang')
@section('page-title','Tambah Barang')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Barang</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="card card-outline card-primary" style="max-width:800px">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-plus mr-2"></i>Form Tambah Barang</h3>
  </div>
  
  <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data" id="formBarang">
    @csrf
    <div class="card-body">
      <div class="row">
        
        {{-- KODE BARANG --}}
        <div class="col-md-4">
          <div class="form-group">
            <label>Kode Barang <span class="text-danger">*</span></label>
            <input type="text" name="kode_barang"
              class="form-control @error('kode_barang') is-invalid @enderror"
              value="{{ old('kode_barang') }}" placeholder="BRG00001" required>
            @error('kode_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        {{-- NAMA BARANG --}}
        <div class="col-md-8">
          <div class="form-group">
            <label>Nama Barang <span class="text-danger">*</span></label>
            <input type="text" name="nama_barang"
              class="form-control @error('nama_barang') is-invalid @enderror"
              value="{{ old('nama_barang') }}" required>
            @error('nama_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        {{-- KATEGORI — 1 FIELD DINAMIS (DENGAN FIX KODE KATEGORI OTOMATIS) --}}
        <div class="col-md-5">
          <div class="form-group">
            <label>Kategori <span class="text-danger">*</span></label>
            <input type="hidden" name="kategori_id" id="inputKategoriId" value="{{ old('kategori_id') }}">

            <div class="d-flex" style="gap: 4px;">
              {{-- Tampilan Default: Mode Pilih Dropdown --}}
              <select id="selectKategori" class="form-control" onchange="pilihKategori(this)">
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoris as $k)
                  <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>
                    {{ $k->nama_kategori }}
                  </option>
                @endforeach
              </select>
              <button type="button" id="btnKeModeBaru" class="btn btn-outline-primary" onclick="setMode('baru')" title="Ketik Kategori Baru">
                <i class="fas fa-plus"></i>
              </button>

              {{-- Tampilan Alternatif: Mode Ketik Kategori Baru --}}
              <input type="text" id="inputNamaKategoriBaru" class="form-control" placeholder="Ketik kategori baru...">
              <button type="button" id="btnSimpanKatBaru" class="btn btn-success" onclick="simpanKategoriBaru()" title="Simpan Kategori">
                <i class="fas fa-save"></i>
              </button>
              <button type="button" id="btnBatalKatBaru" class="btn btn-outline-secondary" onclick="setMode('pilih')" title="Batal">
                <i class="fas fa-times"></i>
              </button>
            </div>

            {{-- Pesan Informasi AJAX --}}
            <div id="errKategoriBaru" class="text-danger mt-1" style="font-size:12px; display:none"></div>
            <div id="successKategoriBaru" class="text-success mt-1" style="font-size:12px; display:none"></div>
            @error('kategori_id')<div class="text-danger mt-1" style="font-size:12px">{{ $message }}</div>@enderror
          </div>
        </div>

        {{-- SATUAN --}}
        <div class="col-md-3">
          <div class="form-group">
            <label>Satuan <span class="text-danger">*</span></label>
            <input type="text" name="satuan" class="form-control"
              value="{{ old('satuan') }}" placeholder="Pcs / Rim / Box" required>
          </div>
        </div>

        {{-- MERK --}}
        <div class="col-md-4">
          <div class="form-group">
            <label>Merk</label>
            <input type="text" name="merk" class="form-control" value="{{ old('merk') }}">
          </div>
        </div>

        {{-- HARGA SATUAN --}}
        <div class="col-md-5">
          <div class="form-group">
            <label>Harga Satuan (Rp) <span class="text-danger">*</span></label>
            <input type="number" name="harga_satuan" class="form-control"
              value="{{ old('harga_satuan', 0) }}" min="0" required>
          </div>
        </div>

        {{-- STOK MINIMUM --}}
        <div class="col-md-3">
          <div class="form-group">
            <label>Stok Minimum</label>
            <input type="number" name="stok_minimum" class="form-control"
              value="{{ old('stok_minimum', 0) }}" min="0">
          </div>
        </div>

        {{-- LOKASI PENYIMPANAN --}}
        <div class="col-md-4">
          <div class="form-group">
            <label>Lokasi</label>
            <input type="text" name="lokasi_penyimpanan" class="form-control"
              value="{{ old('lokasi_penyimpanan') }}" placeholder="Rak A-1">
          </div>
        </div>

        {{-- SPESIFIKASI --}}
        <div class="col-md-12">
          <div class="form-group">
            <label>Spesifikasi</label>
            <input type="text" name="spesifikasi" class="form-control" value="{{ old('spesifikasi') }}">
          </div>
        </div>

        {{-- FOTO BARANG --}}
        <div class="col-md-12">
          <div class="form-group">
            <label>Foto</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" name="foto" accept="image/*"
                id="fotoInput" onchange="previewFoto(this)">
              <label class="custom-file-label" for="fotoInput">Pilih gambar...</label>
            </div>
            <img id="fotoPreview" src="" class="mt-2 rounded" style="max-height:100px; display:none">
            <small class="text-muted d-block mt-1">JPG/PNG/WEBP maks 2MB</small>
          </div>
        </div>

      </div>
    </div>

    {{-- FOOTER FORM --}}
    <div class="card-footer">
      <button type="submit" class="btn btn-primary" id="btnSubmit">
        <i class="fas fa-save mr-1"></i>Simpan
      </button>
      <a href="{{ route('barang.index') }}" class="btn btn-secondary ml-2">Batal</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
// Mengatur inisialisasi awal tampilan
document.addEventListener('DOMContentLoaded', function() {
  setMode('pilih');
  const sel = document.getElementById('selectKategori');
  if (sel.value) document.getElementById('inputKategoriId').value = sel.value;
});

function setMode(mode) {
  const selectKategori = document.getElementById('selectKategori');
  const btnKeModeBaru  = document.getElementById('btnKeModeBaru');
  
  const inputBaru      = document.getElementById('inputNamaKategoriBaru');
  const btnSimpan      = document.getElementById('btnSimpanKatBaru');
  const btnBatal       = document.getElementById('btnBatalKatBaru');
  
  const err            = document.getElementById('errKategoriBaru');
  const ok             = document.getElementById('successKategoriBaru');

  err.style.display = 'none';
  ok.style.display  = 'none';

  if (mode === 'pilih') {
    selectKategori.style.display = 'block';
    btnKeModeBaru.style.display  = 'block';
    
    inputBaru.style.display      = 'none';
    btnSimpan.style.display      = 'none';
    btnBatal.style.display       = 'none';
    
    document.getElementById('inputKategoriId').value = selectKategori.value;
  } else {
    selectKategori.style.display = 'none';
    btnKeModeBaru.style.display  = 'none';
    
    inputBaru.style.display      = 'block';
    btnSimpan.style.display      = 'block';
    btnBatal.style.display       = 'block';
    
    document.getElementById('inputKategoriId').value = '';
    
    inputBaru.disabled = false;
    inputBaru.value = '';
    btnSimpan.disabled = false;
    btnSimpan.innerHTML = '<i class="fas fa-save"></i>';
    btnSimpan.className = 'btn btn-success';
    inputBaru.focus();
  }
}

function pilihKategori(sel) {
  document.getElementById('inputKategoriId').value = sel.value;
}

// Handler Simpan Kategori Baru via AJAX
function simpanKategoriBaru() {
  const nama = document.getElementById('inputNamaKategoriBaru').value.trim();
  const err  = document.getElementById('errKategoriBaru');
  const ok   = document.getElementById('successKategoriBaru');
  err.style.display = 'none';
  ok.style.display  = 'none';

  if (!nama) { 
    err.textContent = 'Nama kategori wajib diisi.'; 
    err.style.display = 'block'; 
    return; 
  }

  // GENERATE KODE KATEGORI OTOMATIS AGAR VALIDASI BACKEND LOLOS
  // Mengambil 3 huruf pertama kapital + angka acak (Contoh: KAB-482)
  const prefix = nama.substring(0, 3).toUpperCase().replace(/[^A-Z]/g, 'KTG');
  const kodeOtomatis = prefix + '-' + Math.floor(100 + Math.random() * 900);

  const btn = document.getElementById('btnSimpanKatBaru');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

  fetch('{{ route("kategori.store") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
    },
    // Mengirimkan nama_kategori SEKALIGUS kode_kategori yang diminta backend
    body: JSON.stringify({ 
      nama_kategori: nama,
      kode_kategori: kodeOtomatis
    })
  })
  .then(async res => {
    if (!res.ok) {
      const errorData = await res.json().catch(() => ({}));
      throw new Error(errorData.message || `Server bermasalah (Status: ${res.status})`);
    }
    return res.json();
  })
  .then(data => {
    if (data.success || data.id || data.kategori) {
      // Menyesuaikan struktur return data dari controller Anda
      const kategori = data.kategori || data;
      
      document.getElementById('inputKategoriId').value = kategori.id;

      const sel = document.getElementById('selectKategori');
      const opt = new Option(kategori.nama_kategori, kategori.id);
      sel.appendChild(opt);
      sel.value = kategori.id; 

      ok.textContent = `✓ Kategori "${kategori.nama_kategori}" berhasil dibuat!`;
      ok.style.display = 'block';

      document.getElementById('inputNamaKategoriBaru').disabled = true;
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-check"></i>';
      btn.className = 'btn btn-secondary';
      
      setTimeout(() => {
         setMode('pilih');
      }, 1000);

    } else {
      if (data.errors) {
        // Gabungkan jika ada banyak error dari Laravel Validation
        err.textContent = Object.values(data.errors).flat().join(', ');
      } else { 
        err.textContent = data.message || 'Gagal menyimpan data kategori.'; 
      }
      err.style.display = 'block';
      resetTombolSimpan(btn);
    }
  })
  .catch((error) => { 
    err.textContent = error.message || 'Terjadi kesalahan sistem. Coba lagi.'; 
    err.style.display = 'block'; 
    resetTombolSimpan(btn);
  });
}

function resetTombolSimpan(btn) {
  btn.disabled = false;
  btn.innerHTML = '<i class="fas fa-save"></i>';
}

// Validasi Kelengkapan Utama Form Sebelum dikirim ke Controller
document.getElementById('formBarang').addEventListener('submit', function(e) {
  const katId = document.getElementById('inputKategoriId').value;
  if (!katId) {
    e.preventDefault();
    const err = document.getElementById('errKategoriBaru');
    err.textContent = 'Silakan pilih kategori atau simpan kategori baru terlebih dahulu.';
    err.style.display = 'block';
  }
});

// Preview Gambar Realtime
function previewFoto(i) {
  const p = document.getElementById('fotoPreview');
  if (i.files && i.files[0]) {
    p.src = URL.createObjectURL(i.files[0]);
    p.style.display = 'block';
    i.nextElementSibling.textContent = i.files[0].name;
  }
}
</script>
@endpush