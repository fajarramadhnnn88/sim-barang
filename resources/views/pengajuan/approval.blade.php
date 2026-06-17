@extends('layouts.app')
@section('title','Approval Pengajuan')
@section('page-title','Approval Pengajuan')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pengajuan.index') }}">Pengajuan</a></li>
<li class="breadcrumb-item active">Approval</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-8">

    {{-- Info Header --}}
    <div class="card card-outline card-{{ $pengajuan->isDiatas10Juta() ? 'danger' : 'warning' }}">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-{{ $pengajuan->isDiatas10Juta() ? 'exclamation-triangle' : 'check-circle' }} mr-2"></i>
          {{ $pengajuan->no_pengajuan }}
          <span class="badge {{ $pengajuan->isDiatas10Juta() ? 'badge-danger' : 'badge-warning' }} ml-2">
            {{ $pengajuan->isDiatas10Juta() ? 'Jalur Direktur' : 'Jalur Wakil Direktur' }}
          </span>
        </h3>
      </div>
      <div class="card-body pb-2">
        <div class="row" style="font-size:13px">
          <div class="col-6"><strong>Pengaju:</strong> {{ $pengajuan->user->name }}</div>
          <div class="col-6"><strong>Divisi:</strong> {{ $pengajuan->divisi->nama_divisi }}</div>
          <div class="col-6 mt-2"><strong>Keperluan:</strong> {{ $pengajuan->keperluan }}</div>
          <div class="col-6 mt-2"><strong>Dibutuhkan:</strong> {{ $pengajuan->tanggal_dibutuhkan?->format('d/m/Y') ?? '-' }}</div>
        </div>
      </div>
    </div>

    {{-- Form Setujui dengan jumlah disetujui --}}
    <div class="card card-outline card-success">
      <div class="card-header" style="background:#ECFDF5">
        <h3 class="card-title text-success">
          <i class="fas fa-check-circle mr-2"></i>Setujui Pengajuan
        </h3>
        <small class="text-muted d-block mt-1">
          Anda dapat mengubah jumlah yang disetujui per item. Kosongkan = setujui semua.
        </small>
      </div>
      <form action="{{ route('approval.setujui', $pengajuan) }}" method="POST">
        @csrf
        <div class="card-body p-0">
          <table class="table table-sm table-bordered mb-0">
            <thead class="thead-light">
              <tr>
                <th>Barang</th>
                <th class="text-center" style="width:12%">Diminta</th>
                <th class="text-center" style="width:18%">
                  Disetujui
                  <i class="fas fa-info-circle text-info ml-1"
                    data-toggle="tooltip"
                    title="Isi jumlah yang disetujui. Kosongkan jika menyetujui semua."></i>
                </th>
                <th style="width:16%">Harga Est.</th>
                <th style="width:18%">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pengajuan->details as $d)
              <tr class="{{ $d->is_custom ? 'table-warning' : '' }}">
                <td>
                  @if($d->is_custom)
                    <span class="badge badge-warning mr-1" style="font-size:9px">BARU</span>
                    <strong style="color:#92400E">{{ $d->nama_barang_custom }}</strong>
                    @if($d->spesifikasi_custom)
                      <br><small class="text-muted">{{ $d->spesifikasi_custom }}</small>
                    @endif
                  @else
                    <strong>{{ $d->barang->nama_barang ?? '-' }}</strong>
                    <br><small class="text-muted">{{ $d->barang->satuan ?? '' }}</small>
                  @endif
                </td>
                <td class="text-center font-weight-bold">{{ $d->jumlah_diminta }}</td>
                <td class="text-center">
                  <input type="number"
                    name="jumlah_disetujui[{{ $d->id }}]"
                    class="form-control form-control-sm text-center jml-setujui"
                    min="0"
                    max="{{ $d->jumlah_diminta }}"
                    value="{{ $d->jumlah_diminta }}"
                    data-harga="{{ $d->harga_estimasi }}"
                    data-id="{{ $d->id }}"
                    oninput="hitungSubtotal(this)"
                    placeholder="{{ $d->jumlah_diminta }}"
                    style="font-weight:600;color:#059669">
                  <small class="text-muted" style="font-size:10px">maks {{ $d->jumlah_diminta }}</small>
                </td>
                <td>{{ $d->harga_estimasi_format }}</td>
                <td class="font-weight-bold" id="subtotal-{{ $d->id }}" style="color:#059669">
                  {{ $d->subtotal_format }}
                </td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr style="background:#ECFDF5">
                <td colspan="4" class="text-right font-weight-bold pr-3">TOTAL DISETUJUI</td>
                <td class="font-weight-bold text-success" id="totalDisetujui">
                  {{ $pengajuan->total_nilai_format }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="card-body pt-3">
          <div class="form-group mb-0">
            <label>Catatan Persetujuan (opsional)</label>
            <textarea name="catatan" class="form-control" rows="2"
              placeholder="Contoh: Disetujui 5 unit karena stok anggaran terbatas..."></textarea>
          </div>
        </div>

        <div class="card-footer">
          <button type="submit" class="btn btn-success"
            onclick="return confirm('Setujui pengajuan {{ $pengajuan->no_pengajuan }}?')">
            <i class="fas fa-check-circle mr-1"></i>Setujui Pengajuan
          </button>
        </div>
      </form>
    </div>

    {{-- Form Tolak --}}
    <div class="card card-outline card-danger">
      <div class="card-header" style="background:#FEF2F2">
        <h3 class="card-title text-danger"><i class="fas fa-times-circle mr-2"></i>Tolak Pengajuan</h3>
      </div>
      <form action="{{ route('approval.tolak', $pengajuan) }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="form-group mb-0">
            <label>Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea name="alasan" class="form-control" rows="3"
              placeholder="Wajib diisi (min 5 karakter)..." required minlength="5"></textarea>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-times-circle mr-1"></i>Tolak Pengajuan
          </button>
        </div>
      </form>
    </div>

    <a href="{{ route('pengajuan.show', $pengajuan) }}" class="btn btn-secondary mb-3">
      <i class="fas fa-arrow-left mr-1"></i>Kembali ke Detail
    </a>
  </div>

  {{-- Timeline --}}
  <div class="col-md-4">
    <div class="card card-outline card-secondary">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-history mr-2"></i>Riwayat Proses</h3></div>
      <div class="card-body" style="padding:12px">
        <div class="timeline timeline-inverse">
          @foreach($pengajuan->approvalLogs as $log)
          <div>
            <i class="fas fa-circle {{ $log->aksi_color }}" style="font-size:10px"></i>
            <div class="timeline-item" style="font-size:11px">
              <span class="time text-muted">{{ $log->created_at->format('d/m H:i') }}</span>
              <h3 class="timeline-header" style="font-size:11px;padding:5px 10px">
                <strong>{{ $log->user->name }}</strong> — {{ $log->aksi_label }}
              </h3>
              @if($log->catatan)
                <div class="timeline-body" style="padding:4px 10px;font-size:11px;color:#64748B">
                  {{ $log->catatan }}
                </div>
              @endif
            </div>
          </div>
          @endforeach
          <div><i class="fas fa-clock text-muted" style="font-size:10px"></i></div>
        </div>
      </div>
    </div>

    {{-- Info Nilai --}}
    <div class="card card-outline card-info">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-calculator mr-2"></i>Ringkasan Nilai</h3></div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush" style="font-size:13px">
          <li class="list-group-item d-flex justify-content-between py-2">
            <span>Nilai Pengajuan Awal</span>
            <strong>{{ $pengajuan->total_nilai_format }}</strong>
          </li>
          <li class="list-group-item d-flex justify-content-between py-2">
            <span>Nilai Disetujui</span>
            <strong class="text-success" id="nilaiDisetujuiInfo">{{ $pengajuan->total_nilai_format }}</strong>
          </li>
          <li class="list-group-item d-flex justify-content-between py-2">
            <span>Jalur Approval</span>
            <span class="badge {{ $pengajuan->isDiatas10Juta() ? 'badge-danger' : 'badge-warning' }}">
              {{ $pengajuan->isDiatas10Juta() ? 'Direktur' : 'Wakil Direktur' }}
            </span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function hitungSubtotal(input) {
  const id    = input.dataset.id;
  const harga = parseFloat(input.dataset.harga) || 0;
  const jml   = parseFloat(input.value) || 0;
  const sub   = jml * harga;

  const elSub = document.getElementById(`subtotal-${id}`);
  if (elSub) {
    elSub.textContent = 'Rp ' + fmt(sub);
  }

  // Hitung total semua
  let total = 0;
  document.querySelectorAll('.jml-setujui').forEach(el => {
    const j = parseFloat(el.value) || 0;
    const h = parseFloat(el.dataset.harga) || 0;
    total += j * h;
  });

  const elTotal = document.getElementById('totalDisetujui');
  const elInfo  = document.getElementById('nilaiDisetujuiInfo');
  if (elTotal) elTotal.textContent = 'Rp ' + fmt(total);
  if (elInfo)  elInfo.textContent  = 'Rp ' + fmt(total);
}

function fmt(n) {
  return new Intl.NumberFormat('id-ID').format(n);
}

// Validasi: jangan lebih dari yang diminta
document.querySelectorAll('.jml-setujui').forEach(input => {
  input.addEventListener('change', function () {
    const max = parseInt(this.getAttribute('max'));
    if (parseInt(this.value) > max) {
      this.value = max;
      hitungSubtotal(this);
    }
    if (parseInt(this.value) < 0) {
      this.value = 0;
      hitungSubtotal(this);
    }
  });
});
</script>
@endpush