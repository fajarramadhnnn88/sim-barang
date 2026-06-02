@extends('layouts.print')
@section('title','Laporan Stock Balance')
@section('subtitle','Laporan Kondisi Stok Barang')
@section('content')
<table>
  <thead>
    <tr><th>No</th><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Satuan</th><th>Masuk</th><th>Keluar</th><th>Tersedia</th><th>Min</th><th>Status</th></tr>
  </thead>
  <tbody>
    @foreach($data as $i=>$s)
    @php $b=$s->barang; @endphp
    <tr style="{{ $s->stok_tersedia<=0?'background:#ffe0e0':($s->stok_tersedia<=$b->stok_minimum?'background:#fff8e0':'') }}">
      <td>{{ $i+1 }}</td><td>{{ $b->kode_barang }}</td><td>{{ $b->nama_barang }}</td>
      <td>{{ $b->kategori->nama_kategori }}</td><td>{{ $b->satuan }}</td>
      <td>{{ $s->stok_masuk }}</td><td>{{ $s->stok_keluar }}</td>
      <td><strong>{{ $s->stok_tersedia }}</strong></td><td>{{ $b->stok_minimum }}</td>
      <td>{{ $s->status_stok }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
<p style="margin-top:10px;font-size:10px">Total {{ $data->count() }} jenis barang | {{ now()->format('d/m/Y H:i') }}</p>
@endsection
