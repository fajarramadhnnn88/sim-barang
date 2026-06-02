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
    <div class="col-md-8">
      <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title"><i class="fas fa-boxes mr-2"></i>Item Barang yang Diminta</h3>
          <button type="button" class="btn btn-success btn-sm" onclick="tambahRow()"><i class="fas fa-plus mr-1"></i>Tambah Barang</button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0" id="tblDetail">
              <thead class="thead-light">
                <tr><th style="width:38%">Barang</th><th style="width:13%">Jumlah</th><th style="width:20%">Harga Estimasi</th><th style="width:20%">Subtotal</th><th style="width:9%"></th></tr>
              </thead>
              <tbody id="tbodyDetail"></tbody>
              <tfoot>
                <tr style="background:#F8FAFC">
                  <td colspan="3" class="text-right font-weight-bold pr-3">TOTAL NILAI</td>
                  <td class="font-weight-bold" id="grandTotal" style="color:#4F46E5">Rp 0</td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <div class="card-footer">
          <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>≤ Rp 10.000.000 → Wakil Direktur &nbsp;|&nbsp; > Rp 10.000.000 → Direktur</small>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-outline card-info">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Informasi Pengajuan</h3></div>
        <div class="card-body">
          <div class="form-group">
            <label>Keperluan <span class="text-danger">*</span></label>
            <input type="text" name="keperluan" class="form-control @error('keperluan') is-invalid @enderror" value="{{ old('keperluan') }}" placeholder="Jelaskan keperluan..." required>
            @error('keperluan')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Tanggal Dibutuhkan</label>
            <input type="date" name="tanggal_dibutuhkan" class="form-control" value="{{ old('tanggal_dibutuhkan') }}" min="{{ date('Y-m-d',strtotime('+1 day')) }}">
          </div>
          <div class="form-group">
            <label>Keterangan Tambahan</label>
            <textarea name="keterangan" class="form-control" rows="3" placeholder="Opsional...">{{ old('keterangan') }}</textarea>
          </div>
          <div class="callout callout-info p-2 mb-0" style="font-size:12px">
            <strong>Divisi:</strong> {{ auth()->user()->divisi->nama_divisi??'-' }}<br>
            <strong>Pengaju:</strong> {{ auth()->user()->name }}
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" name="action" value="draft" class="btn btn-secondary btn-block btn-sm mb-1">
            <i class="fas fa-save mr-1"></i>Simpan Draft
          </button>
          <button type="submit" name="action" value="submit" class="btn btn-primary btn-block btn-sm">
            <i class="fas fa-paper-plane mr-1"></i>Simpan & Ajukan
          </button>
          <a href="{{ route('pengajuan.index') }}" class="btn btn-light btn-block btn-sm mt-1">Batal</a>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
const barangs={!! json_encode($barangs->map(fn($b)=>['id'=>$b->id,'kode'=>$b->kode_barang,'nama'=>$b->nama_barang,'satuan'=>$b->satuan,'harga'=>(float)$b->harga_satuan,'stok'=>$b->stok_tersedia])) !!};
let idx=0;

function tambahRow(bid=null,jml=1,hrg=0){
  const i=idx++;
  const opts=barangs.map(b=>`<option value="${b.id}" data-hrg="${b.harga}" data-sat="${b.satuan}" data-stok="${b.stok}" ${bid==b.id?'selected':''}>${b.kode} — ${b.nama} (Stok:${b.stok} ${b.satuan})</option>`).join('');
  const tr=document.createElement('tr');
  tr.id=`row${i}`;
  tr.innerHTML=`
    <td><select name="details[${i}][barang_id]" class="form-control form-control-sm sel2row" required onchange="setHarga(this,${i})">${opts}</select></td>
    <td><input type="number" name="details[${i}][jumlah]" class="form-control form-control-sm" min="1" value="${jml}" required oninput="sub(${i})"></td>
    <td><input type="number" name="details[${i}][harga_estimasi]" id="h${i}" class="form-control form-control-sm" min="0" value="${hrg}" oninput="sub(${i})"></td>
    <td id="s${i}" class="font-weight-bold" style="vertical-align:middle">Rp ${fmt(jml*hrg)}</td>
    <td style="vertical-align:middle;text-align:center"><button type="button" class="btn btn-xs btn-danger" onclick="hapus(${i})"><i class="fas fa-times"></i></button></td>`;
  document.getElementById('tbodyDetail').appendChild(tr);
  $(tr).find('.sel2row').select2({theme:'bootstrap4',width:'100%'});
  total();
}

function setHarga(sel,i){
  const o=sel.options[sel.selectedIndex];
  document.getElementById(`h${i}`).value=o.dataset.hrg||0;
  sub(i);
}

function sub(i){
  const r=document.getElementById(`row${i}`);
  if(!r)return;
  const j=parseFloat(r.querySelector('[name*="jumlah"]').value)||0;
  const h=parseFloat(document.getElementById(`h${i}`).value)||0;
  document.getElementById(`s${i}`).textContent='Rp '+fmt(j*h);
  total();
}

function total(){
  let t=0;
  document.querySelectorAll('[id^="h"]').forEach(el=>{
    const i=el.id.slice(1);
    const r=document.getElementById(`row${i}`);
    if(r){const j=parseFloat(r.querySelector('[name*="jumlah"]').value)||0;t+=j*(parseFloat(el.value)||0);}
  });
  const el=document.getElementById('grandTotal');
  el.textContent='Rp '+fmt(t);
  el.style.color=t>10000000?'#DC2626':'#4F46E5';
}

function hapus(i){document.getElementById(`row${i}`)?.remove();total();}
function fmt(n){return new Intl.NumberFormat('id-ID').format(n);}

tambahRow();
</script>
@endsection
