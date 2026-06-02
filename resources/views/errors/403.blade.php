@extends('layouts.app')
@section('title','403 Akses Ditolak')
@section('page-title','Akses Ditolak')
@section('content')
<div class="text-center py-5">
  <div style="font-size:5rem;line-height:1">🚫</div>
  <h1 style="font-size:4rem;font-weight:800;color:#EF4444;margin:8px 0">403</h1>
  <h4 style="color:#64748B;font-weight:500">Akses Ditolak</h4>
  <p class="text-muted">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
  <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3"><i class="fas fa-home mr-2"></i>Kembali ke Dashboard</a>
</div>
@endsection
