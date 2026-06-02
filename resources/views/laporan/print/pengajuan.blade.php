@extends('layouts.print')
@section('title','Laporan Pengajuan')
@section('subtitle','Laporan Pengajuan Barang')
@section('content')
<table>
  <thead>
    <tr><th>No</th><th>No. Pengajuan</th><th>Pengaju</th><th>Divisi</th><th>Keperluan</th><th>Total Nilai</th><th>Jalur</th><th>Status</th><th>Tgl</th></tr>
  </thead>
  <tbody>
    @foreach($data as $i=>$p)
    <tr>
      <td>{{ $i+1 }}</td><td>{{ $p->no_pengajuan }}</td>
      <td>{{ $p->user->name }}</td><td>{{ $p->divisi->nama_divisi }}</td>
      <td>{{ Str::limit($p->keperluan,40) }}</td>
      <td>{{ $p->total_nilai_format }}</td>
      <td>{{ $p->jalur_approval?ucfirst(str_replace('_',' ',$p->jalur_approval)):'-' }}</td>
      <td>{{ $p->status_label }}</td>
      <td>{{ $p->tanggal_pengajuan?->format('d/m/Y')??'-' }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr class="total-row"><td colspan="5" style="text-align:right">TOTAL NILAI</td><td>Rp {{ number_format($totalNilai,0,',','.') }}</td><td colspan="3"></td></tr>
  </tfoot>
</table>
@endsection
