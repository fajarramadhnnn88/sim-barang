<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Login — SIM Barang Support</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *{font-family:'Inter',sans-serif;box-sizing:border-box;}
    body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#0F172A 0%,#1E3A5F 60%,#0F172A 100%);}
    .dots{position:fixed;inset:0;background-image:radial-gradient(rgba(255,255,255,.05) 1px,transparent 1px);background-size:28px 28px;pointer-events:none;}
    .wrap{width:100%;max-width:440px;padding:20px;}
    .logo-box{width:60px;height:60px;background:linear-gradient(135deg,#4F46E5,#7C3AED);border-radius:16px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 8px 32px rgba(79,70,229,.4);}
    .card{border:none;border-radius:16px;box-shadow:0 24px 64px rgba(0,0,0,.3);overflow:hidden;}
    .card-top{background:linear-gradient(135deg,#4F46E5,#7C3AED);padding:22px;text-align:center;}
    .card-body{padding:28px;background:#fff;}
    label{font-size:11px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;display:block;}
    .form-control{border:1.5px solid #E2E8F0;border-radius:10px;padding:10px 14px;font-size:14px;height:auto;transition:all .2s;}
    .form-control:focus{border-color:#4F46E5;box-shadow:0 0 0 3px rgba(79,70,229,.1);outline:none;}
    .igt{background:#F8FAFC;border:1.5px solid #E2E8F0;border-right:none;border-radius:10px 0 0 10px;color:#94A3B8;}
    .form-control.right-grp{border-radius:0 10px 10px 0;}
    .btn-eye{background:#F8FAFC;border:1.5px solid #E2E8F0;border-left:none;border-radius:0 10px 10px 0;color:#94A3B8;cursor:pointer;padding:0 14px;}
    .btn-login{background:linear-gradient(135deg,#4F46E5,#7C3AED);border:none;border-radius:10px;padding:12px;font-size:14px;font-weight:600;color:#fff;width:100%;transition:all .2s;}
    .btn-login:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(79,70,229,.4);}
    .alert-danger{background:#FEF2F2;color:#991B1B;border:none;border-left:4px solid #EF4444;border-radius:8px;font-size:13px;}
    .demo{background:#F8FAFC;border-radius:10px;padding:14px;margin-top:16px;}
    .demo h6{font-size:10px;font-weight:600;color:#94A3B8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px;}
    .demo-row{display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid #F1F5F9;cursor:pointer;transition:background .1s;border-radius:6px;padding:5px 6px;}
    .demo-row:last-child{border:none;}
    .demo-row:hover{background:#EEF2FF;}
    .demo-email{font-size:12px;color:#475569;font-family:monospace;}
    .rbadge{font-size:10px;font-weight:600;padding:2px 8px;border-radius:5px;}
    .r1{background:#EEF2FF;color:#4F46E5;}.r2{background:#FEF2F2;color:#DC2626;}
    .r3{background:#FFFBEB;color:#D97706;}.r4{background:#ECFEFF;color:#0891B2;}
    .r5{background:#ECFDF5;color:#059669;}.r6{background:#F1F5F9;color:#64748B;}
  </style>
</head>
<body>
<div class="dots"></div>
<div class="wrap">
  <div class="text-center mb-4">
    <div class="logo-box mb-3"><i class="fas fa-boxes text-white" style="font-size:26px"></i></div>
    <h4 style="color:#fff;font-weight:700;margin:0">SIM Barang Support</h4>
    <p style="color:#64748B;font-size:13px;margin:4px 0 0">Sistem Informasi Manajemen Data Barang</p>
  </div>
  <div class="card">
    <div class="card-top">
      <h5 style="color:#fff;font-weight:600;margin:0;font-size:15px">Selamat Datang</h5>
      <p style="color:rgba(255,255,255,.7);font-size:12px;margin:4px 0 0">Masuk untuk melanjutkan</p>
    </div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger mb-3 py-2"><i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}</div>
      @endif
      <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="form-group mb-3">
          <label>Email</label>
          <div class="input-group">
            <div class="input-group-prepend"><span class="input-group-text igt"><i class="fas fa-envelope" style="font-size:12px"></i></span></div>
            <input type="email" name="email" class="form-control right-grp" placeholder="email@perusahaan.id" value="{{ old('email') }}" required autofocus id="eml">
          </div>
        </div>
        <div class="form-group mb-3">
          <label>Password</label>
          <div class="input-group">
            <div class="input-group-prepend"><span class="input-group-text igt"><i class="fas fa-lock" style="font-size:12px"></i></span></div>
            <input type="password" name="password" id="pwd" class="form-control" style="border-radius:0;border-right:none" placeholder="Password" required>
            <div class="input-group-append"><button type="button" class="btn-eye" onclick="togglePwd()"><i class="fas fa-eye" id="eyeI" style="font-size:12px"></i></button></div>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
          <label style="text-transform:none;font-size:13px;color:#475569;cursor:pointer;font-weight:400;display:flex;align-items:center;gap:6px;margin:0">
            <input type="checkbox" name="remember"> Ingat saya
          </label>
        </div>
        <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt mr-2"></i>Masuk ke Sistem</button>
      </form>

      <div class="demo">
        <h6>Akun demo — klik untuk isi otomatis</h6>
        <div class="demo-row" onclick="fl('superadmin@simbarang.id')">
          <span class="demo-email">superadmin@simbarang.id</span><span class="rbadge r1">Super Admin</span>
        </div>
        <div class="demo-row" onclick="fl('direktur@simbarang.id')">
          <span class="demo-email">direktur@simbarang.id</span><span class="rbadge r2">Direktur</span>
        </div>
        <div class="demo-row" onclick="fl('wadir@simbarang.id')">
          <span class="demo-email">wadir@simbarang.id</span><span class="rbadge r3">Wakil Direktur</span>
        </div>
        <div class="demo-row" onclick="fl('purchasing@simbarang.id')">
          <span class="demo-email">purchasing@simbarang.id</span><span class="rbadge r4">Purchasing</span>
        </div>
        <div class="demo-row" onclick="fl('admin.ti@simbarang.id')">
          <span class="demo-email">admin.ti@simbarang.id</span><span class="rbadge r5">Admin Divisi</span>
        </div>
        <div class="demo-row" onclick="fl('staff.ti@simbarang.id')">
          <span class="demo-email">staff.ti@simbarang.id</span><span class="rbadge r6">Staff</span>
        </div>
      </div>
    </div>
  </div>
  <p class="text-center mt-3" style="color:#475569;font-size:11px">&copy; {{ date('Y') }} SIM Barang Support</p>
</div>
<script>
function togglePwd(){const p=document.getElementById('pwd'),e=document.getElementById('eyeI');p.type=p.type==='password'?'text':'password';e.classList.toggle('fa-eye');e.classList.toggle('fa-eye-slash');}
function fl(email){document.getElementById('eml').value=email;document.getElementById('pwd').value='password';}
</script>
</body>
</html>
