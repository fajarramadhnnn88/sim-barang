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
    <div class="card card-outline card-{{ $pengajuan->isDiatas10Juta()?'danger':'warning' }}">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-{{ $pengajuan->isDiatas10Juta()?'exclamation-triangle':'check-circle' }} mr-2"></i>{{ $pengajuan->no_pengajuan }}
          <span class="badge {{ $pengajuan->isDiatas10Juta()?'badge-danger':'badge-warning' }} ml-2">{{ $pengajuan->isDiatas10Juta()?'Jalur Direktur':'Jalur Wakil Direktur' }}</span>
        </h3>
      </div>
      <div class="card-body">
        <div class="row mb-3" style="font-size:13px">
          <div class="col-6"><strong>Pengaju:</strong> {{ $pengajuan->user->name }}</div>
          <div class="col-6"><strong>Divisi:</strong> {{ $pengajuan->divisi->nama_divisi }}</div>
          <div class="col-6 mt-2"><strong>Keperluan:</strong> {{ $pengajuan->keperluan }}</div>
          <div class="col-6 mt-2"><strong>Dibutuhkan:</strong> {{ $pengajuan->tanggal_dibutuhkan?->format('d/m/Y')??'-' }}</div>
        </div>
        <div class="callout callout-{{ $pengajuan->isDiatas10Juta()?'danger':'warning' }} py-2">
          <h5 class="mb-1">Total Nilai: <strong>{{ $pengajuan->total_nilai_format }}</strong></h5>
          <small>{{ $pengajuan->isDiatas10Juta()?'Nilai >Rp 10 juta → Wewenang Direktur':'Nilai ≤Rp 10 juta → Wewenang Wakil Direktur' }}</small>
        </div>
        <table class="table table-sm table-bordered mt-3">
          <thead class="thead-light"><tr><th>Barang</th><th>Jumlah</th><th>Harga Est.</th><th>Subtotal</th></tr></thead>
          <tbody>
            @foreach($pengajuan->details as $d)
            <tr><td><strong>{{ $d->barang->nama_barang }}</strong></td><td>{{ $d->jumlah_diminta }} {{ $d->barang->satuan }}</td><td>{{ $d->harga_estimasi_format }}</td><td class="font-weight-bold">{{ $d->subtotal_format }}</td></tr>
            @endforeach
          </tbody>
          <tfoot><tr style="background:#F8FAFC"><td colspan="3" class="text-right font-weight-bold">TOTAL</td><td class="font-weight-bold" style="color:#4F46E5">{{ $pengajuan->total_nilai_format }}</td></tr></tfoot>
        </table>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="card card-outline card-success">
          <div class="card-header" style="background:#ECFDF5"><h3 class="card-title text-success"><i class="fas fa-check mr-2"></i>Setujui</h3></div>
          <form action="{{ route('approval.setujui',$pengajuan) }}" method="POST">
            @csrf
            <div class="card-body">
              <div class="form-group mb-0">
                <label>Catatan (opsional)</label>
                <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan persetujuan..."></textarea>
              </div>
            </div>
            <div class="card-footer">
              <button class="btn btn-success btn-block" onclick="return confirm('Setujui pengajuan ini?')"><i class="fas fa-check-circle mr-1"></i>Setujui Pengajuan</button>
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card card-outline card-danger">
          <div class="card-header" style="background:#FEF2F2"><h3 class="card-title text-danger"><i class="fas fa-times mr-2"></i>Tolak</h3></div>
          <form action="{{ route('approval.tolak',$pengajuan) }}" method="POST">
            @csrf
            <div class="card-body">
              <div class="form-group mb-0">
                <label>Alasan Penolakan <span class="text-danger">*</span></label>
                <textarea name="alasan" class="form-control" rows="3" placeholder="Wajib diisi (min 5 karakter)..." required minlength="5"></textarea>
              </div>
            </div>
            <div class="card-footer">
              <button class="btn btn-danger btn-block"><i class="fas fa-times-circle mr-1"></i>Tolak Pengajuan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

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
              <h3 class="timeline-header" style="font-size:11px;padding:5px 10px"><strong>{{ $log->user->name }}</strong> — {{ $log->aksi_label }}</h3>
              @if($log->catatan)<div class="timeline-body" style="padding:4px 10px;font-size:11px;color:#64748B">{{ $log->catatan }}</div>@endif
            </div>
          </div>
          @endforeach
          <div><i class="fas fa-clock text-muted" style="font-size:10px"></i></div>
        </div>
      </div>
    </div>
    <a href="{{ route('pengajuan.show',$pengajuan) }}" class="btn btn-secondary btn-block">
      <i class="fas fa-arrow-left mr-1"></i>Kembali ke Detail
    </a>
  </div>
</div>
@endsection
