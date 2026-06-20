<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Dashboard') — SIM Barang Support</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
  </ul>
  <ul class="navbar-nav ml-auto align-items-center">
    <li class="nav-item dropdown mr-1">
      <a class="nav-link position-relative px-3" data-toggle="dropdown" href="#">
        <i class="fas fa-bell" style="font-size:16px;color:#64748B"></i>
        @if(auth()->user()->unreadNotifications->count())
          <span class="badge badge-danger position-absolute" style="top:6px;right:6px;font-size:9px;padding:2px 5px;border-radius:50%">{{ auth()->user()->unreadNotifications->count() }}</span>
        @endif
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="min-width:300px">
        <div class="px-3 py-2 d-flex justify-content-between align-items-center" style="border-bottom:1px solid #F1F5F9">
          <span style="font-weight:600;font-size:13px">Notifikasi</span>
          @if(auth()->user()->unreadNotifications->count())
          <form method="POST" action="{{ route('notifications.read') }}">@csrf
            <button class="btn btn-xs btn-outline-primary">Tandai dibaca</button>
          </form>
          @endif
        </div>
        @forelse(auth()->user()->unreadNotifications->take(5) as $notif)
          <a class="dropdown-item py-2" href="{{ $notif->data['url']??'#' }}" style="white-space:normal;border-bottom:1px solid #F8FAFC">
            <div style="font-weight:600;font-size:12px;color:#1E293B">{{ $notif->data['judul']??'Notif' }}</div>
            <div style="font-size:11px;color:#64748B;margin-top:2px;line-height:1.4">{{ Str::limit($notif->data['pesan']??'',70) }}</div>
            <div style="font-size:10px;color:#94A3B8;margin-top:2px">{{ $notif->created_at->diffForHumans() }}</div>
          </a>
        @empty
          <div class="text-center py-3 text-muted" style="font-size:12px"><i class="fas fa-bell-slash d-block mb-1"></i>Tidak ada notifikasi</div>
        @endforelse
      </div>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#" style="padding:6px 12px;gap:8px">
        <img src="{{ auth()->user()->foto_url }}" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;border:2px solid #E2E8F0">
        <div class="d-none d-sm-block" style="line-height:1.2">
          <div style="font-size:12px;font-weight:600;color:#1E293B">{{ Str::limit(auth()->user()->name,18) }}</div>
          <div style="font-size:10px;color:#94A3B8">{{ auth()->user()->role->label() }}</div>
        </div>
        <i class="fas fa-chevron-down" style="font-size:9px;color:#94A3B8"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <a href="{{ route('profile.edit') }}" class="dropdown-item"><i class="fas fa-user-circle mr-2 text-primary"></i>Profil Saya</a>
        <div class="dropdown-divider"></div>
        <form method="POST" action="{{ route('logout') }}">@csrf
          <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt mr-2"></i>Logout</button>
        </form>
      </div>
    </li>
  </ul>
</nav>

<aside class="main-sidebar sidebar-dark-primary elevation-0">
  <a href="{{ route('dashboard') }}" class="brand-link">
    <div class="d-flex align-items-center">
      <div style="width:34px;height:34px;background:#4F46E5;border-radius:9px;display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0">
        <i class="fas fa-boxes text-white" style="font-size:15px"></i>
      </div>
      <div>
        <div class="brand-text">SIM Barang</div>
        <div style="font-size:10px;color:#475569;font-weight:400">Support System</div>
      </div>
    </div>
  </a>
  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="{{ auth()->user()->foto_url }}" class="img-circle" style="width:34px;height:34px;object-fit:cover;border:2px solid rgba(255,255,255,.1)">
      </div>
      <div class="info">
        <a href="{{ route('profile.edit') }}" class="d-block" style="font-size:13px;font-weight:600">{{ Str::limit(auth()->user()->name,20) }}</a>
        <small style="font-size:11px;color:#475569">{{ auth()->user()->divisi->nama_divisi ?? auth()->user()->role->label() }}</small>
      </div>
    </div>

    <nav class="mt-1">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

        {{-- Dashboard --}}
        <li class="nav-item">
          <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard')?'active':'' }}">
            <i class="nav-icon fas fa-th-large"></i><p>Dashboard</p>
          </a>
        </li>

        {{-- ── INVENTARIS ── --}}
        @if(auth()->user()->isPurchasing())
          {{--
            PURCHASING:
            - Data Barang  → lihat saja
            - Barang Masuk → AKSES PENUH (tambah, edit, hapus) — tugas inti mereka
            - Barang Keluar → tidak ada menu
          --}}
          <li class="nav-header">INVENTARIS</li>
          <li class="nav-item">
            <a href="{{ route('barang.index') }}" class="nav-link {{ request()->routeIs('barang.*')?'active':'' }}">
              <i class="nav-icon fas fa-box"></i><p>Data Barang <small class="text-muted">(lihat)</small></p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('barang-masuk.index') }}" class="nav-link {{ request()->routeIs('barang-masuk.*')?'active':'' }}">
              <i class="nav-icon fas fa-truck-loading"></i><p>Barang Masuk</p>
            </a>
          </li>

        @elseif(auth()->user()->isAdminDivisi())
          {{--
            ADMIN DIVISI:
            - Data Barang   → akses penuh
            - Barang Masuk  → lihat saja
            - Barang Keluar → akses penuh
          --}}
          <li class="nav-header">INVENTARIS</li>
          <li class="nav-item">
            <a href="{{ route('barang.index') }}" class="nav-link {{ request()->routeIs('barang.*')?'active':'' }}">
              <i class="nav-icon fas fa-box"></i><p>Data Barang</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('barang-masuk.index') }}" class="nav-link {{ request()->routeIs('barang-masuk.*')?'active':'' }}">
              <i class="nav-icon fas fa-truck-loading"></i><p>Barang Masuk <small class="text-muted">(lihat)</small></p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('barang-keluar.index') }}" class="nav-link {{ request()->routeIs('barang-keluar.*')?'active':'' }}">
              <i class="nav-icon fas fa-arrow-circle-up"></i><p>Barang Keluar</p>
            </a>
          </li>

        @elseif(auth()->user()->isSuperadmin())
          {{-- SUPERADMIN: akses penuh semua --}}
          <li class="nav-header">INVENTARIS</li>
          <li class="nav-item">
            <a href="{{ route('barang.index') }}" class="nav-link {{ request()->routeIs('barang.*')?'active':'' }}">
              <i class="nav-icon fas fa-box"></i><p>Data Barang</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('barang-masuk.index') }}" class="nav-link {{ request()->routeIs('barang-masuk.*')?'active':'' }}">
              <i class="nav-icon fas fa-truck-loading"></i><p>Barang Masuk</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('barang-keluar.index') }}" class="nav-link {{ request()->routeIs('barang-keluar.*')?'active':'' }}">
              <i class="nav-icon fas fa-arrow-circle-up"></i><p>Barang Keluar</p>
            </a>
          </li>
        @endif

        {{-- Stock — semua role --}}
        <li class="nav-item">
          <a href="{{ route('stock.index') }}" class="nav-link {{ request()->routeIs('stock.*')?'active':'' }}">
            <i class="nav-icon fas fa-layer-group"></i><p>Stock Balance</p>
          </a>
        </li>

        {{-- Pengajuan --}}
        <li class="nav-header">PENGAJUAN</li>
        <li class="nav-item">
          <a href="{{ route('pengajuan.index') }}" class="nav-link {{ request()->routeIs('pengajuan.*')?'active':'' }}">
            <i class="nav-icon fas fa-file-alt"></i>
            <p>Pengajuan
              @php
                $u = auth()->user(); $pc = 0;
                if ($u->isAdminDivisi()) {
                  $pc = \App\Models\Pengajuan::where('divisi_id',$u->divisi_id)
                        ->whereIn('status',['diajukan','review_admin','barang_masuk'])->count();
                } elseif ($u->isPurchasing()) {
                  $pc = \App\Models\Pengajuan::whereIn('status',['diteruskan','disetujui'])->count();
                } elseif ($u->isWakilDirektur()) {
                  $pc = \App\Models\Pengajuan::where('status','menunggu_approval')->where('jalur_approval','wakil_direktur')->count();
                } elseif ($u->isDirektur()) {
                  $pc = \App\Models\Pengajuan::where('status','menunggu_approval')->where('jalur_approval','direktur')->count();
                }
              @endphp
              @if($pc > 0)<span class="badge badge-warning right" style="font-size:10px">{{ $pc }}</span>@endif
            </p>
          </a>
        </li>

        {{-- Laporan --}}
        <li class="nav-header">LAPORAN</li>
        <li class="nav-item">
          <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*')?'active':'' }}">
            <i class="nav-icon fas fa-chart-bar"></i><p>Laporan</p>
          </a>
        </li>

        {{-- Master Data — superadmin --}}
        @canRole(['superadmin'])
        <li class="nav-header">MASTER DATA</li>
        <li class="nav-item">
          <a href="{{ route('divisi.index') }}" class="nav-link {{ request()->routeIs('divisi.*')?'active':'' }}">
            <i class="nav-icon fas fa-sitemap"></i><p>Kelola Divisi</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*')?'active':'' }}">
            <i class="nav-icon fas fa-users-cog"></i><p>Kelola Pengguna</p>
          </a>
        </li>
        @endCanRole

        {{-- Akun --}}
        <li class="nav-header">AKUN</li>
        <li class="nav-item">
          <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*')?'active':'' }}">
            <i class="nav-icon fas fa-user-circle"></i><p>Profil Saya</p>
          </a>
        </li>
        <li class="nav-item">
          <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit" class="nav-link btn btn-link text-left w-100" style="color:rgba(255,255,255,.6)">
              <i class="nav-icon fas fa-sign-out-alt" style="color:#EF4444"></i><p>Logout</p>
            </button>
          </form>
        </li>

      </ul>
    </nav>
  </div>
</aside>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-sm-6"><h1 class="m-0">@yield('page-title','Dashboard')</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            @yield('breadcrumb')
          </ol>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="container-fluid">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
          <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
          <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
          <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
          <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
          <i class="fas fa-exclamation-triangle mr-2"></i><strong>Terdapat kesalahan:</strong>
          <ul class="mb-0 mt-1 pl-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
      @endif
      @yield('content')
    </div>
  </div>
</div>

<footer class="main-footer text-center">
  <strong>SIM Barang Support</strong> &copy; {{ date('Y') }}
</footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
$(function(){
  $('[data-toggle="tooltip"]').tooltip();
  $('.select2').select2({theme:'bootstrap4',width:'100%'});
  setTimeout(()=>$('.alert.fade.show').alert('close'),5000);
});
</script>
@stack('scripts')
</body>
</html>