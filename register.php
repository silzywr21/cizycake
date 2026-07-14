<?php 
include 'koneksi.php';

$error = '';
if(isset($_POST['daftar'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = MD5($_POST['password']);
    
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($cek) > 0){
        $error = 'Email sudah terdaftar!';
    } else {
        mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', 'user')");
        header("location:login.php?sukses=daftar");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Daftar - <?= NAMA_TOKO ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --pink:#E91E63; --pink-light:#FCE4EC; --pink-dark:#C2185B; --white:#FFFFFF; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:linear-gradient(135deg, #fff 0%, var(--pink-light) 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; }
.login-box { background:var(--white); padding:40px; border-radius:20px; box-shadow:0 10px 40px rgba(233,30,99,0.2); width:100%; max-width:400px; }
.logo { text-align:center; font-size:30px; font-weight:800; color:var(--pink); margin-bottom:30px; }
.logo span { color:var(--pink-dark); }
h2 { text-align:center; margin-bottom:25px; color:var(--pink-dark); }
.form-group { margin-bottom:20px; }
.form-group label { display:block; margin-bottom:8px; font-weight:600; font-size:14px; }
.form-group input { width:100%; padding:12px 15px; border:2px solid #eee; border-radius:10px; font-size:14px; }
.btn-login { width:100%; background:var(--pink); color:var(--white); border:none; padding:14px; border-radius:10px; cursor:pointer; font-weight:700; font-size:16px; }
.error { background:#FFEBEE; color:#C62828; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; font-size:14px; font-weight:600; }
.link-daftar { text-align:center; margin-top:20px; font-size:14px; }
.link-daftar a { color:var(--pink); text-decoration:none; font-weight:600; }
</style>
</head>
<body>
<div class="login-box">
    <div class="logo">CiZy<span>cake</span></div>
    <h2>Daftar Akun Baru</h2>
    <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" placeholder="Masukkan nama" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Masukkan email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>
        </div>
        <button type="submit" name="daftar" class="btn-login">Daftar</button>
    </form>
    <div class="link-daftar">
        Sudah punya akun? <a href="login.php">Login disini</a>
    </div>
</div>
</body>
</html>