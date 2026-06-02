@extends('layouts.app')
@section('title','404 Tidak Ditemukan')
@section('page-title','Halaman Tidak Ditemukan')
@section('content')
<div class="text-center py-5">
  <div style="font-size:5rem;line-height:1">🔍</div>
  <h1 style="font-size:4rem;font-weight:800;color:#F59E0B;margin:8px 0">404</h1>
  <h4 style="color:#64748B;font-weight:500">Halaman Tidak Ditemukan</h4>
  <p class="text-muted">Halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
  <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3"><i class="fas fa-home mr-2"></i>Kembali ke Dashboard</a>
</div>
@endsection
