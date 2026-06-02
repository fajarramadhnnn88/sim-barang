@extends('layouts.print')
@section('title','Laporan Barang Masuk')
@section('subtitle','Laporan Barang Masuk Periode {{ request("dari","") }} s/d {{ request("sampai","") }}')
@section('content')
<table>
  <thead>
    <tr><th>No</th><th>No. Transaksi</th><th>Tgl Masuk</th><th>Barang</th><th>Supplier</th><th>Jumlah</th><th>Harga Satuan</th><th>Total Harga</th></tr>
  </thead>
  <tbody>
    @foreach($data as $i=>$m)
    <tr>
      <td>{{ $i+1 }}</td><td>{{ $m->no_transaksi }}</td>
      <td>{{ $m->tanggal_masuk->format('d/m/Y') }}</td>
      <td>{{ $m->barang->nama_barang }} ({{ $m->barang->kode_barang }})</td>
      <td>{{ $m->supplier?->nama_supplier??'-' }}</td>
      <td>{{ $m->jumlah }} {{ $m->barang->satuan }}</td>
      <td>Rp {{ number_format($m->harga_satuan,0,',','.') }}</td>
      <td>{{ $m->total_harga_format }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr class="total-row"><td colspan="7" style="text-align:right">TOTAL</td><td>Rp {{ number_format($total,0,',','.') }}</td></tr>
  </tfoot>
</table>
<p style="margin-top:10px;font-size:10px">Total {{ $data->count() }} transaksi</p>
@endsection
