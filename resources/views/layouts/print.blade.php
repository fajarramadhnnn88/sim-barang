<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>@yield('title') — SIM Barang Support</title>
  <style>
    *{box-sizing:border-box;font-family:Arial,sans-serif;}
    body{margin:0;padding:16px;font-size:12px;color:#000;}
    .header{text-align:center;border-bottom:2px solid #000;padding-bottom:10px;margin-bottom:14px;}
    .header h2{margin:0;font-size:16px;} .header p{margin:2px 0;font-size:11px;}
    table{width:100%;border-collapse:collapse;margin-top:8px;}
    th,td{border:1px solid #aaa;padding:5px 8px;font-size:11px;}
    th{background:#f0f0f0;font-weight:700;}
    tr:nth-child(even){background:#fafafa;}
    .total-row{font-weight:700;background:#e8e8e8;}
    .no-print{display:none;}
    .badge-s{display:inline-block;padding:2px 8px;border-radius:4px;font-size:10px;font-weight:600;}
    @media print{body{padding:0;}.btn-print{display:none!important;}}
  </style>
</head>
<body>
  <div class="header">
    <h2>SIM Barang Support</h2>
    <p>@yield('subtitle')</p>
    <p>Dicetak: {{ now()->format('d/m/Y H:i') }} oleh {{ auth()->user()->name }}</p>
  </div>
  @yield('content')
  <div style="margin-top:16px;text-align:center" class="no-print">
    <button onclick="window.print()" style="padding:8px 20px;cursor:pointer;margin-right:8px">🖨️ Cetak</button>
    <button onclick="window.close()" style="padding:8px 20px;cursor:pointer">✕ Tutup</button>
  </div>
  <script>window.onload=function(){if(window.location.search.includes('format=print')){}}</script>
</body>
</html>
