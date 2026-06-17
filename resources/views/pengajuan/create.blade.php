@extends('layouts.app')
@section('title','Buat Pengajuan')
@section('page-title','Buat Pengajuan')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pengajuan.index') }}">Pengajuan</a></li>
<li class="breadcrumb-item active">Buat</li>
@endsection
@section('content')
<form action="{{ route('pengajuan.store') }}" method="POST" id="formPengajuan">
  @csrf
  <div class="row">
    {{-- Tabel Item --}}
    <div class="col-md-8">
      <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title"><i class="fas fa-boxes mr-2"></i>Item Barang</h3>
          <div>
            <button type="button" class="btn btn-primary btn-sm mr-1" onclick="tambahRow('existing')">
              <i class="fas fa-list mr-1"></i>Pilih dari Daftar
            </button>
            <button type="button" class="btn btn-warning btn-sm" onclick="tambahRow('custom')">
              <i class="fas fa-pencil-alt mr-1"></i>Ketik Barang Baru
            </button>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
              <thead class="thead-light">
                <tr>
                  <th style="width:6%">Tipe</th>
                  <th style="width:34%">Barang</th>
                  <th style="width:12%">Jumlah</th>
                  <th style="width:19%">Harga Est.</th>
                  <th style="width:19%">Subtotal</th>
                  <th style="width:10%"></th>
                </tr>
              </thead>
              <tbody id="tbodyDetail">
                <tr id="emptyRow">
                  <td colspan="6" class="text-center text-muted py-4">
                    <i class="fas fa-inbox d-block mb-2" style="font-size:24px;opacity:.3"></i>
                    Klik tombol di atas untuk menambah barang
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr style="background:#F8FAFC">
                  <td colspan="4" class="text-right font-weight-bold pr-3">TOTAL NILAI</td>
                  <td class="font-weight-bold" id="grandTotal" style="color:#4F46E5">Rp 0</td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <div class="card-footer" style="font-size:11px;color:#64748B;line-height:1.8">
          <span class="badge badge-primary mr-1">Pilih dari Daftar</span> Barang sudah ada di sistem &nbsp;|&nbsp;
          <span class="badge badge-warning mr-1">Ketik Barang Baru</span> Barang belum ada, admin divisi akan menindaklanjuti
          <br>≤ Rp 10.000.000 → Wakil Direktur &nbsp;|&nbsp; > Rp 10.000.000 → Direktur
        </div>
      </div>
    </div>

    {{-- Info Pengajuan --}}
    <div class="col-md-4">
      <div class="card card-outline card-info">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Informasi Pengajuan</h3></div>
        <div class="card-body">
          <div class="form-group">
            <label>Keperluan <span class="text-danger">*</span></label>
            <input type="text" name="keperluan"
              class="form-control @error('keperluan') is-invalid @enderror"
              value="{{ old('keperluan') }}" placeholder="Jelaskan keperluan..." required>
            @error('keperluan')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Tanggal Dibutuhkan</label>
            <input type="date" name="tanggal_dibutuhkan" class="form-control"
              value="{{ old('tanggal_dibutuhkan') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
          </div>
          <div class="form-group">
            <label>Keterangan Tambahan</label>
            <textarea name="keterangan" class="form-control" rows="3"
              placeholder="Opsional...">{{ old('keterangan') }}</textarea>
          </div>
          <div class="callout callout-info p-2 mb-0" style="font-size:12px">
            <strong>Divisi:</strong> {{ auth()->user()->divisi->nama_divisi ?? '-' }}<br>
            <strong>Pengaju:</strong> {{ auth()->user()->name }}
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" name="action" value="draft"
            class="btn btn-secondary btn-block btn-sm mb-1">
            <i class="fas fa-save mr-1"></i>Simpan Draft
          </button>
          <button type="submit" name="action" value="submit"
            class="btn btn-primary btn-block btn-sm">
            <i class="fas fa-paper-plane mr-1"></i>Simpan & Ajukan
          </button>
          <a href="{{ route('pengajuan.index') }}" class="btn btn-light btn-block btn-sm mt-1">Batal</a>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection

@push('scripts')
<script>
const barangs = {!! json_encode($barangs->map(fn($b) => [
    'id'    => $b->id,
    'kode'  => $b->kode_barang,
    'nama'  => $b->nama_barang,
    'satuan'=> $b->satuan,
    'harga' => (float)$b->harga_satuan,
    'stok'  => $b->stok_tersedia,
])) !!};

let idx = 0;

function tambahRow(tipe) {
  document.getElementById('emptyRow')?.remove();
  const i = idx++;
  const tr = document.createElement('tr');
  tr.id = `row${i}`;

  if (tipe === 'existing') {
    const opts = barangs.map(b =>
      `<option value="${b.id}" data-hrg="${b.harga}" data-sat="${b.satuan}">
        ${b.kode} — ${b.nama} (Stok: ${b.stok} ${b.satuan})
      </option>`).join('');
    tr.innerHTML = `
      <td class="text-center pt-2">
        <span class="badge badge-primary" style="font-size:9px">Sistem</span>
        <input type="hidden" name="details[${i}][tipe]" value="existing">
      </td>
      <td>
        <select name="details[${i}][barang_id]" class="form-control form-control-sm sel2row" required
          onchange="  Harga(this,${i})">
          <option value="">-- Pilih Barang --</option>${opts}
        </select>
      </td>
      <td><input type="number" name="details[${i}][jumlah]" class="form-control form-control-sm"
        min="1" value="1" required oninput="sub(${i})"></td>
      <td><input type="number" name="details[${i}][harga_estimasi]" id="h${i}"
        class="form-control form-control-sm" min="0" value="0" oninput="sub(${i})"></td>
      <td id="s${i}" class="font-weight-bold" style="vertical-align:middle;color:#059669">Rp 0</td>
      <td class="text-center" style="vertical-align:middle">
        <button type="button" class="btn btn-xs btn-danger" onclick="hapus(${i})">
          <i class="fas fa-times"></i>
        </button>
      </td>`;
    document.getElementById('tbodyDetail').appendChild(tr);
    $(tr).find('.sel2row').select2({ theme:'bootstrap4', width:'100%' });

  } else {
    tr.innerHTML = `
      <td class="text-center pt-2">
        <span class="badge badge-warning" style="font-size:9px">Baru</span>
        <input type="hidden" name="details[${i}][tipe]" value="custom">
      </td>
      <td>
        <input type="text" name="details[${i}][nama_barang_custom]"
          class="form-control form-control-sm mb-1"
          placeholder="Nama barang yang diinginkan..." required>
        <input type="text" name="details[${i}][spesifikasi_custom]"
          class="form-control form-control-sm"
          placeholder="Spesifikasi: warna, ukuran, tipe... (opsional)">
      </td>
      <td><input type="number" name="details[${i}][jumlah]" class="form-control form-control-sm"
        min="1" value="1" required oninput="sub(${i})"></td>
      <td><input type="number" name="details[${i}][harga_estimasi]" id="h${i}"
        class="form-control form-control-sm" min="0" value="0"
        placeholder="Estimasi harga" oninput="sub(${i})"></td>
      <td id="s${i}" class="font-weight-bold" style="vertical-align:middle;color:#D97706">Rp 0</td>
      <td class="text-center" style="vertical-align:middle">
        <button type="button" class="btn btn-xs btn-danger" onclick="hapus(${i})">
          <i class="fas fa-times"></i>
        </button>
      </td>`;
    document.getElementById('tbodyDetail').appendChild(tr);
  }
  total();
}

function setHarga(sel, i) {
  const o = sel.options[sel.selectedIndex];
  document.getElementById(`h${i}`).value = o.dataset.hrg || 0;
  sub(i);
}

function sub(i) {
  const r = document.getElementById(`row${i}`);
  if (!r) return;
  const j = parseFloat(r.querySelector('[name*="jumlah"]').value) || 0;
  const h = parseFloat(document.getElementById(`h${i}`).value) || 0;
  document.getElementById(`s${i}`).textContent = 'Rp ' + fmt(j * h);
  total();
}

function total() {
  let t = 0;
  document.querySelectorAll('[id^="h"]').forEach(el => {
    const i = el.id.slice(1);
    const r = document.getElementById(`row${i}`);
    if (r) {
      const j = parseFloat(r.querySelector('[name*="jumlah"]').value) || 0;
      t += j * (parseFloat(el.value) || 0);
    }
  });
  const el = document.getElementById('grandTotal');
  el.textContent = 'Rp ' + fmt(t);
  el.style.color = t > 10000000 ? '#DC2626' : '#4F46E5';
}

function hapus(i) {
  document.getElementById(`row${i}`)?.remove();
  if (!document.getElementById('tbodyDetail').children.length) {
    document.getElementById('tbodyDetail').innerHTML =
      `<tr id="emptyRow"><td colspan="6" class="text-center text-muted py-4">
        <i class="fas fa-inbox d-block mb-2" style="font-size:24px;opacity:.3"></i>
        Klik tombol di atas untuk menambah barang
      </td></tr>`;
  }
  total();
}

function fmt(n) { return new Intl.NumberFormat('id-ID').format(n); }
</script>
@endpush