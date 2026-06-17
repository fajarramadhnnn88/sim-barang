@extends('layouts.app')
@section('title','Edit Pengajuan')
@section('page-title','Edit Pengajuan')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pengajuan.index') }}">Pengajuan</a></li>
<li class="breadcrumb-item"><a href="{{ route('pengajuan.show',$pengajuan) }}">{{ $pengajuan->no_pengajuan }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<form action="{{ route('pengajuan.update',$pengajuan) }}" method="POST" id="formEdit">
  @csrf @method('PUT')
  <div class="row">
    <div class="col-md-8">
      <div class="card card-outline card-warning">
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
              <tbody id="tbodyDetail"></tbody>
              <tfoot>
                <tr style="background:#F8FAFC">
                  <td colspan="4" class="text-right font-weight-bold pr-3">TOTAL</td>
                  <td class="font-weight-bold" id="grandTotal" style="color:#4F46E5">Rp 0</td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card card-outline card-warning">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-edit mr-2"></i>Informasi</h3></div>
        <div class="card-body">
          <div class="form-group">
            <label>Keperluan <span class="text-danger">*</span></label>
            <input type="text" name="keperluan" class="form-control"
              value="{{ old('keperluan',$pengajuan->keperluan) }}" required>
          </div>
          <div class="form-group">
            <label>Tanggal Dibutuhkan</label>
            <input type="date" name="tanggal_dibutuhkan" class="form-control"
              value="{{ old('tanggal_dibutuhkan',$pengajuan->tanggal_dibutuhkan?->format('Y-m-d')) }}">
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan',$pengajuan->keterangan) }}</textarea>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-warning btn-block"><i class="fas fa-save mr-1"></i>Perbarui</button>
          <a href="{{ route('pengajuan.show',$pengajuan) }}" class="btn btn-secondary btn-block mt-1">Batal</a>
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

const existing = {!! json_encode($pengajuan->details->map(fn($d) => [
    'tipe'               => $d->is_custom ? 'custom' : 'existing',
    'barang_id'          => $d->barang_id,
    'nama_barang_custom' => $d->nama_barang_custom,
    'spesifikasi_custom' => $d->spesifikasi_custom,
    'jumlah'             => $d->jumlah_diminta,
    'harga'              => (float)$d->harga_estimasi,
])) !!};

let idx = 0;

function tambahRow(tipe, bid=null, namaCustom='', spekCustom='', jml=1, hrg=0) {
  const i = idx++;
  const tr = document.createElement('tr');
  tr.id = `row${i}`;

  if (tipe === 'existing') {
    const opts = barangs.map(b =>
      `<option value="${b.id}" data-hrg="${b.harga}" ${bid==b.id?'selected':''}>
        ${b.kode} — ${b.nama} (Stok: ${b.stok} ${b.satuan})
      </option>`).join('');
    tr.innerHTML = `
      <td class="text-center pt-2">
        <span class="badge badge-primary" style="font-size:9px">Sistem</span>
        <input type="hidden" name="details[${i}][tipe]" value="existing">
      </td>
      <td>
        <select name="details[${i}][barang_id]" class="form-control form-control-sm sel2row" required
          onchange="setHarga(this,${i})">
          <option value="">-- Pilih --</option>${opts}
        </select>
      </td>
      <td><input type="number" name="details[${i}][jumlah]" class="form-control form-control-sm"
        min="1" value="${jml}" required oninput="sub(${i})"></td>
      <td><input type="number" name="details[${i}][harga_estimasi]" id="h${i}"
        class="form-control form-control-sm" min="0" value="${hrg}" oninput="sub(${i})"></td>
      <td id="s${i}" class="font-weight-bold" style="vertical-align:middle;color:#059669">
        Rp ${fmt(jml*hrg)}
      </td>
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
          placeholder="Nama barang..." required value="${namaCustom}">
        <input type="text" name="details[${i}][spesifikasi_custom]"
          class="form-control form-control-sm"
          placeholder="Spesifikasi (opsional)" value="${spekCustom}">
      </td>
      <td><input type="number" name="details[${i}][jumlah]" class="form-control form-control-sm"
        min="1" value="${jml}" required oninput="sub(${i})"></td>
      <td><input type="number" name="details[${i}][harga_estimasi]" id="h${i}"
        class="form-control form-control-sm" min="0" value="${hrg}" oninput="sub(${i})"></td>
      <td id="s${i}" class="font-weight-bold" style="vertical-align:middle;color:#D97706">
        Rp ${fmt(jml*hrg)}
      </td>
      <td class="text-center" style="vertical-align:middle">
        <button type="button" class="btn btn-xs btn-danger" onclick="hapus(${i})">
          <i class="fas fa-times"></i>
        </button>
      </td>`;
    document.getElementById('tbodyDetail').appendChild(tr);
  }
  total();
}

function setHarga(sel,i){document.getElementById(`h${i}`).value=sel.options[sel.selectedIndex].dataset.hrg||0;sub(i);}
function sub(i){const r=document.getElementById(`row${i}`);if(!r)return;const j=parseFloat(r.querySelector('[name*="jumlah"]').value)||0,h=parseFloat(document.getElementById(`h${i}`).value)||0;document.getElementById(`s${i}`).textContent='Rp '+fmt(j*h);total();}
function total(){let t=0;document.querySelectorAll('[id^="h"]').forEach(el=>{const i=el.id.slice(1),r=document.getElementById(`row${i}`);if(r){const j=parseFloat(r.querySelector('[name*="jumlah"]').value)||0;t+=j*(parseFloat(el.value)||0);}});const el=document.getElementById('grandTotal');el.textContent='Rp '+fmt(t);el.style.color=t>10000000?'#DC2626':'#4F46E5';}
function hapus(i){document.getElementById(`row${i}`)?.remove();total();}
function fmt(n){return new Intl.NumberFormat('id-ID').format(n);}

// Load existing data
existing.forEach(d => {
  if (d.tipe === 'existing') tambahRow('existing', d.barang_id, '', '', d.jumlah, d.harga);
  else tambahRow('custom', null, d.nama_barang_custom||'', d.spesifikasi_custom||'', d.jumlah, d.harga);
});
if (!existing.length) tambahRow('existing');
</script>
@endpush