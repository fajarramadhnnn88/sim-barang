@extends('layouts.app')
@section('title',$pengajuan->no_pengajuan)
@section('page-title','Detail Pengajuan')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pengajuan.index') }}">Pengajuan</a></li>
<li class="breadcrumb-item active">{{ $pengajuan->no_pengajuan }}</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-8">

    {{-- Header --}}
    <div class="card card-outline card-primary">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="fas fa-file-alt mr-2"></i>{{ $pengajuan->no_pengajuan }}</h3>
        <span class="badge {{ $pengajuan->status_badge }}" style="font-size:13px;padding:6px 14px">
          {{ $pengajuan->status_label }}
        </span>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <dl class="row mb-0" style="font-size:13px">
              <dt class="col-5">Pengaju</dt><dd class="col-7">{{ $pengajuan->user->name }}</dd>
              <dt class="col-5">Divisi</dt><dd class="col-7">{{ $pengajuan->divisi->nama_divisi }}</dd>
              <dt class="col-5">Keperluan</dt><dd class="col-7">{{ $pengajuan->keperluan }}</dd>
              <dt class="col-5">Tgl Pengajuan</dt><dd class="col-7">{{ $pengajuan->tanggal_pengajuan?->format('d/m/Y') ?? '-' }}</dd>
              <dt class="col-5">Tgl Dibutuhkan</dt><dd class="col-7">{{ $pengajuan->tanggal_dibutuhkan?->format('d/m/Y') ?? '-' }}</dd>
            </dl>
          </div>
          <div class="col-md-6">
            <dl class="row mb-0" style="font-size:13px">
              <dt class="col-5">Total Nilai</dt>
              <dd class="col-7 font-weight-bold" style="color:{{ $pengajuan->isDiatas10Juta()?'#DC2626':'#1E293B' }}">
                {{ $pengajuan->total_nilai_format }}
              </dd>
              <dt class="col-5">Jalur</dt>
              <dd class="col-7">
                @if($pengajuan->jalur_approval)
                  <span class="badge {{ $pengajuan->jalur_approval=='direktur'?'badge-danger':'badge-warning' }}">
                    {{ $pengajuan->jalur_approval=='direktur'?'Direktur':'Wakil Direktur' }}
                  </span>
                @else <span class="text-muted">-</span> @endif
              </dd>
              <dt class="col-5">Keterangan</dt>
              <dd class="col-7">{{ $pengajuan->keterangan ?? '-' }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    {{-- Banner status proses pembelian --}}
    @if($pengajuan->status->value === 'proses_pembelian')
      <div class="callout callout-info">
        <h5><i class="fas fa-shopping-cart mr-2"></i>Sedang Dalam Proses Pembelian</h5>
        <p class="mb-0" style="font-size:13px">
          Purchasing sedang melakukan pembelian barang untuk pengajuan ini.
          @if(auth()->user()->isAdminDivisi() || auth()->user()->isSuperadmin())
            Anda akan menerima notifikasi setelah barang dicatat masuk oleh Purchasing.
          @endif
        </p>
      </div>
    @endif

    {{-- Banner status barang masuk - perlu konfirmasi --}}
    @if($pengajuan->status->value === 'barang_masuk')
      <div class="callout callout-warning">
        <h5><i class="fas fa-box-open mr-2"></i>Barang Sudah Masuk — Mohon Dikonfirmasi</h5>
        <p class="mb-0" style="font-size:13px">
          Purchasing telah mencatat barang masuk. Silakan cek kesesuaian barang di bawah,
          lalu konfirmasi penerimaan jika sudah sesuai.
        </p>
      </div>
    @endif

    {{-- Banner selesai/diterima --}}
    @if($pengajuan->status->value === 'diterima')
      <div class="callout callout-success">
        <h5><i class="fas fa-check-double mr-2"></i>Barang Telah Diterima</h5>
        <p class="mb-0" style="font-size:13px">
          Admin divisi telah mengkonfirmasi penerimaan barang. Pengajuan ini selesai.
        </p>
      </div>
    @endif

    {{-- Detail Barang --}}
    <div class="card card-outline card-secondary">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-boxes mr-2"></i>Detail Barang</h3>
        @if($pengajuan->details->where('is_custom',true)->count() > 0 && (auth()->user()->isAdminDivisi() || auth()->user()->isSuperadmin()))
          <span class="badge badge-warning ml-2">
            {{ $pengajuan->details->where('is_custom',true)->count() }} barang baru perlu ditindaklanjuti
          </span>
        @endif
      </div>
      <div class="card-body p-0">
        <table class="table table-sm table-bordered mb-0">
          <thead class="thead-light">
            <tr>
              <th>Barang</th>
              <th class="text-center">Jml Diminta</th>
              <th class="text-center">Jml Disetujui</th>
              <th>Harga Est.</th>
              <th>Subtotal</th>
              @if(auth()->user()->isAdminDivisi()||auth()->user()->isSuperadmin())
              <th>Aksi Admin</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @foreach($pengajuan->details as $d)
            <tr class="{{ $d->is_custom ? 'table-warning' : '' }}">
              <td>
                @if($d->is_custom)
                  <div class="d-flex align-items-start">
                    <span class="badge badge-warning mr-2 mt-1" style="font-size:9px;flex-shrink:0">BARU</span>
                    <div>
                      <strong style="font-size:13px;color:#92400E">{{ $d->nama_barang_custom }}</strong>
                      @if($d->spesifikasi_custom)
                        <br><small class="text-muted">{{ $d->spesifikasi_custom }}</small>
                      @endif
                      <br><small class="text-warning"><i class="fas fa-exclamation-circle mr-1"></i>Belum ada di sistem</small>
                    </div>
                  </div>
                @else
                  <strong style="font-size:13px">{{ $d->barang->nama_barang ?? '-' }}</strong>
                  <br><small class="text-muted">{{ $d->barang->kode_barang ?? '' }} | {{ $d->barang->satuan ?? '' }}</small>
                @endif
              </td>
              <td class="text-center">{{ $d->jumlah_diminta }}</td>
              <td class="text-center">{{ $d->jumlah_disetujui ?? '-' }}</td>
              <td>{{ $d->harga_estimasi_format }}</td>
              <td class="font-weight-bold">{{ $d->subtotal_format }}</td>

              @if(auth()->user()->isAdminDivisi()||auth()->user()->isSuperadmin())
              <td>
                @if($d->is_custom)
                  <button class="btn btn-xs btn-success" data-toggle="modal"
                    data-target="#modalTambahBarang{{ $d->id }}">
                    <i class="fas fa-plus mr-1"></i>Tambah ke Sistem
                  </button>

                  <div class="modal fade" id="modalTambahBarang{{ $d->id }}">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <form action="{{ route('pengajuan.detail.tambah-barang', $d) }}" method="POST">
                          @csrf
                          <div class="modal-header" style="background:linear-gradient(135deg,#10B981,#059669)">
                            <h5 class="modal-title text-white" style="font-size:14px">
                              <i class="fas fa-plus mr-2"></i>Tambah Barang ke Sistem
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                          </div>
                          <div class="modal-body">
                            <div class="callout callout-warning py-2 mb-3" style="font-size:12px">
                              <strong>Nama Barang dari Staff:</strong> {{ $d->nama_barang_custom }}<br>
                              @if($d->spesifikasi_custom)<strong>Spesifikasi:</strong> {{ $d->spesifikasi_custom }}@endif
                            </div>
                            <div class="form-group">
                              <label>Kode Barang <span class="text-danger">*</span></label>
                              <input type="text" name="kode_barang" class="form-control" placeholder="Contoh: BRG00011" required>
                            </div>
                            <div class="form-group">
                              <label>Kategori <span class="text-danger">*</span></label>
                              <select name="kategori_id" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoris as $k)<option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>@endforeach
                              </select>
                            </div>
                            <div class="row">
                              <div class="col-6">
                                <div class="form-group">
                                  <label>Satuan <span class="text-danger">*</span></label>
                                  <input type="text" name="satuan" class="form-control" placeholder="Pcs / Unit" required>
                                </div>
                              </div>
                              <div class="col-6">
                                <div class="form-group">
                                  <label>Harga Satuan (Rp)</label>
                                  <input type="number" name="harga_satuan" class="form-control" min="0" value="0">
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success btn-sm">
                              <i class="fas fa-save mr-1"></i>Simpan & Hubungkan
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                @else
                  <span class="text-muted" style="font-size:11px"><i class="fas fa-check text-success mr-1"></i>Ada di sistem</span>
                @endif
              </td>
              @endif
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr style="background:#F8FAFC">
              <td colspan="{{ (auth()->user()->isAdminDivisi()||auth()->user()->isSuperadmin()) ? 4 : 3 }}"
                class="text-right font-weight-bold">TOTAL</td>
              <td class="font-weight-bold" style="color:{{ $pengajuan->isDiatas10Juta()?'#DC2626':'#059669' }}">
                {{ $pengajuan->total_nilai_format }}
              </td>
              @if(auth()->user()->isAdminDivisi()||auth()->user()->isSuperadmin())
              <td></td>
              @endif
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    {{-- Riwayat Barang Masuk terkait (kalau ada) --}}
    @if($pengajuan->barangMasuks->count() ?? false)
    <div class="card card-outline card-info">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-truck-loading mr-2"></i>Barang Masuk Terkait</h3></div>
      <div class="card-body p-0">
        <table class="table table-sm table-bordered mb-0">
          <thead class="thead-light">
            <tr><th>No. Transaksi</th><th>Barang</th><th>Jumlah</th><th>PIC</th><th>Foto</th><th>Tanggal</th></tr>
          </thead>
          <tbody>
            @foreach($pengajuan->barangMasuks as $bm)
            <tr>
              <td><code style="font-size:11px">{{ $bm->no_transaksi }}</code></td>
              <td>{{ $bm->barang->nama_barang }}</td>
              <td>{{ $bm->jumlah }} {{ $bm->barang->satuan }}</td>
              <td>{{ $bm->pic_name ?? '-' }}</td>
              <td>
                @if($bm->foto_dokumentasi_url)
                  <a href="{{ $bm->foto_dokumentasi_url }}" target="_blank">
                    <img src="{{ $bm->foto_dokumentasi_url }}" style="width:40px;height:40px;object-fit:cover;border-radius:6px">
                  </a>
                @else <span class="text-muted">-</span> @endif
              </td>
              <td>{{ $bm->tanggal_masuk->format('d/m/Y') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif

    {{-- Aksi --}}
    @php $u = auth()->user(); $s = $pengajuan->status->value; @endphp
    <div class="card card-outline card-warning">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-tasks mr-2"></i>Aksi</h3></div>
      <div class="card-body">
        @if($s==='draft' && $u->id===$pengajuan->user_id)
          <form action="{{ route('pengajuan.submit',$pengajuan) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-success mr-2" onclick="return confirm('Ajukan pengajuan ini?')">
              <i class="fas fa-paper-plane mr-1"></i>Ajukan ke Admin Divisi
            </button>
          </form>
          <a href="{{ route('pengajuan.edit',$pengajuan) }}" class="btn btn-warning mr-2">
            <i class="fas fa-edit mr-1"></i>Edit
          </a>
        @endif

        @if($s==='diajukan' && $u->isAdminDivisi() && $u->divisi_id===$pengajuan->divisi_id)
          <form action="{{ route('approval.review',$pengajuan) }}" method="POST" class="d-inline">
            @csrf<button class="btn btn-info mr-2"><i class="fas fa-eye mr-1"></i>Review</button>
          </form>
        @endif

        @if($s==='review_admin' && $u->isAdminDivisi() && $u->divisi_id===$pengajuan->divisi_id)
          <form action="{{ route('approval.teruskan',$pengajuan) }}" method="POST" class="d-inline">
            @csrf<button class="btn btn-primary mr-2"><i class="fas fa-arrow-right mr-1"></i>Teruskan ke Purchasing</button>
          </form>
        @endif

        @if($s==='diteruskan' && $u->isPurchasing())
          <form action="{{ route('approval.proses',$pengajuan) }}" method="POST" class="d-inline">
            @csrf<button class="btn btn-info mr-2"><i class="fas fa-cog mr-1"></i>Proses</button>
          </form>
        @endif

        @if($s==='proses_purchasing' && $u->isPurchasing())
          <form action="{{ route('approval.ajukan',$pengajuan) }}" method="POST" class="d-inline">
            @csrf<button class="btn btn-primary mr-2"><i class="fas fa-paper-plane mr-1"></i>Ajukan Approval Akhir</button>
          </form>
        @endif

        @if($s==='menunggu_approval' && ($u->isWakilDirektur()||$u->isDirektur()||$u->isSuperadmin()))
          <a href="{{ route('approval.show',$pengajuan) }}" class="btn btn-success mr-2">
            <i class="fas fa-check mr-1"></i>Setujui / Tolak
          </a>
        @endif

        {{-- Purchasing: mulai proses pembelian setelah disetujui --}}
        @if($s==='disetujui' && ($u->isPurchasing()||$u->isSuperadmin()))
          <form action="{{ route('approval.mulai-pembelian',$pengajuan) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-primary mr-2" onclick="return confirm('Mulai proses pembelian untuk pengajuan ini? Admin divisi akan dinotifikasi.')">
              <i class="fas fa-shopping-cart mr-1"></i>Proses Pembelian
            </button>
          </form>
        @endif

        {{-- Purchasing: input barang masuk saat status proses_pembelian --}}
        @if($s==='proses_pembelian' && ($u->isPurchasing()||$u->isSuperadmin()))
          <a href="{{ route('barang-masuk.create', ['pengajuan_id' => $pengajuan->id]) }}" class="btn btn-info mr-2">
            <i class="fas fa-truck-loading mr-1"></i>Input Barang Masuk
          </a>
        @endif

        {{-- Admin divisi: konfirmasi terima saat status barang_masuk --}}
        @if($s==='barang_masuk' && ($u->isAdminDivisi()||$u->isSuperadmin()) && $u->divisi_id===$pengajuan->divisi_id)
          <button class="btn btn-success mr-2" data-toggle="modal" data-target="#modalKonfirmasiTerima">
            <i class="fas fa-check-double mr-1"></i>Konfirmasi Terima Barang
          </button>
        @endif

        @if(!in_array($s,['draft','diterima','ditolak','selesai']) && $u->canApprove())
          <button class="btn btn-danger" data-toggle="modal" data-target="#modalTolak">
            <i class="fas fa-times mr-1"></i>Tolak
          </button>
        @endif
      </div>
    </div>
  </div>

  {{-- Timeline --}}
  <div class="col-md-4">
    <div class="card card-outline card-secondary">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-history mr-2"></i>Timeline Proses</h3></div>
      <div class="card-body" style="padding:12px">
        @if($pengajuan->approvalLogs->count())
        <div class="timeline timeline-inverse">
          @foreach($pengajuan->approvalLogs as $log)
          <div>
            <i class="fas fa-circle {{ $log->aksi_color }}" style="font-size:10px"></i>
            <div class="timeline-item" style="font-size:12px">
              <span class="time text-muted"><i class="fas fa-clock mr-1"></i>{{ $log->created_at->format('d/m H:i') }}</span>
              <h3 class="timeline-header" style="font-size:12px;padding:6px 10px">
                <strong>{{ $log->user->name }}</strong> — {{ $log->aksi_label }}
              </h3>
              @if($log->catatan)
                <div class="timeline-body" style="padding:4px 10px;background:#F8FAFC;border-radius:6px;margin:0 10px 4px;font-size:11px;color:#64748B">
                  {{ $log->catatan }}
                </div>
              @endif
              <div class="timeline-footer" style="padding:4px 10px">
                <span class="badge badge-secondary" style="font-size:10px">{{ $log->status_sesudah }}</span>
              </div>
            </div>
          </div>
          @endforeach
          <div><i class="fas fa-clock text-muted" style="font-size:10px"></i></div>
        </div>
        @else
          <p class="text-center text-muted py-3">Belum ada aktivitas</p>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Modal Konfirmasi Terima --}}
<div class="modal fade" id="modalKonfirmasiTerima">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('approval.konfirmasi-terima',$pengajuan) }}" method="POST">
        @csrf
        <div class="modal-header" style="background:linear-gradient(135deg,#10B981,#059669)">
          <h5 class="modal-title text-white" style="font-size:14px">
            <i class="fas fa-check-double mr-2"></i>Konfirmasi Penerimaan Barang
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="callout callout-info py-2" style="font-size:12px">
            Pastikan barang yang diterima sudah sesuai dengan detail pengajuan sebelum mengkonfirmasi.
          </div>
          <div class="form-group mb-0">
            <label>Catatan (opsional)</label>
            <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: Barang sesuai, kondisi baik..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-check mr-1"></i>Konfirmasi Diterima
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Tolak --}}
<div class="modal fade" id="modalTolak">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('approval.tolak',$pengajuan) }}" method="POST">
        @csrf
        <div class="modal-header" style="background:linear-gradient(135deg,#EF4444,#DC2626)">
          <h5 class="modal-title text-white" style="font-size:14px">
            <i class="fas fa-times mr-2"></i>Tolak Pengajuan
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group mb-0">
            <label>Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea name="alasan" class="form-control" rows="4"
              placeholder="Tulis alasan penolakan (min 5 karakter)..." required minlength="5"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-times mr-1"></i>Tolak Pengajuan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection