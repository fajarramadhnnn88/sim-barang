@extends('layouts.print')
@section('title','Laporan Barang Keluar')
@section('subtitle','Laporan Barang Keluar')
@section('content')
<table>
  <thead>
    <tr><th>No</th><th>No. Transaksi</th><th>Tgl Keluar</th><th>Barang</th><th>Divisi</th><th>Jumlah</th><th>Penerima</th></tr>
  </thead>
  <tbody>
    @foreach($data as $i=>$k)
    <tr>
      <td>{{ $i+1 }}</td><td>{{ $k->no_transaksi }}</td>
      <td>{{ $k->tanggal_keluar->format('d/m/Y') }}</td>
      <td>{{ $k->barang->nama_barang }}</td>
      <td>{{ $k->divisi?->nama_divisi??'-' }}</td>
      <td>{{ $k->jumlah }} {{ $k->barang->satuan }}</td>
      <td>{{ $k->penerima??'-' }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr class="total-row"><td colspan="5" style="text-align:right">TOTAL KELUAR</td><td>{{ $data->sum('jumlah') }} unit</td><td></td></tr>
  </tfoot>
</table>
@endsection
