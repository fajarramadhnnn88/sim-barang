@extends('layouts.app')
@section('title','500 Server Error')
@section('page-title','Terjadi Kesalahan')
@section('content')
<div class="text-center py-5">
  <div style="font-size:5rem;line-height:1">⚠️</div>
  <h1 style="font-size:4rem;font-weight:800;color:#EF4444;margin:8px 0">500</h1>
  <h4 style="color:#64748B;font-weight:500">Kesalahan Server</h4>
  <p class="text-muted">Maaf, terjadi kesalahan. Silakan coba lagi.</p>
  <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3"><i class="fas fa-home mr-2"></i>Kembali ke Dashboard</a>
</div>
@endsection
