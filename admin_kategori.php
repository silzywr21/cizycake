<?php
include 'koneksi.php';
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){ 
    header("location:login.php"); exit; 
}

// Tambah kategori
if(isset($_POST['tambah'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    $icon = mysqli_real_escape_string($conn, $_POST['icon']);
    mysqli_query($conn, "INSERT INTO kategori (nama_kategori, icon) VALUES ('$nama', '$icon')");
    header("location:admin_kategori.php"); exit;
}

// Hapus kategori
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM kategori WHERE id=$id");
    header("location:admin_kategori.php"); exit;
}

// Edit kategori
if(isset($_POST['edit'])){
    $id = (int)$_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    $icon = mysqli_real_escape_string($conn, $_POST['icon']);
    mysqli_query($conn, "UPDATE kategori SET nama_kategori='$nama', icon='$icon' WHERE id=$id");
    header("location:admin_kategori.php"); exit;
}

$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Kelola Kategori - <?= NAMA_TOKO ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --pink:#E91E63; --pink-light:#FCE4EC; --pink-dark:#C2185B; --white:#FFFFFF; --black:#212121; --red:#F44336; --green:#4CAF50; --blue:#2196F3; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:#fafafa; color:var(--black); }
.container { max-width:1000px; margin:auto; padding:20px; }
.header { background:var(--white); padding:25px; border-radius:15px; box-shadow:0 2px 10px rgba(0,0,0,0.05); margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; }
.header h1 { font-size:24px; font-weight:700; color:var(--pink); margin-bottom:5px; }
.header p { color:#666; font-size:14px; }
.btn { background:var(--pink); color:var(--white); border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; font-size:14px; }
.btn:hover { background:var(--pink-dark); }
.btn-red { background:var(--red); }
.btn-green { background:var(--green); }
.btn-blue { background:var(--blue); }
.btn-tab { background:var(--white); color:var(--black); border:1px solid #eee; padding:12px 20px; border-radius:10px; box-shadow:0 2px 5px rgba(0,0,0,0.05); }
.btn-tab.active { background:var(--pink); color:var(--white); border-color:var(--pink); }
.tabs { display:flex; gap:15px; margin-bottom:20px; }
.card { background:var(--white); padding:25px; border-radius:15px; box-shadow:0 2px 10px rgba(0,0,0,0.05); margin-bottom:20px; }
.card h2 { color:var(--pink-dark); margin-bottom:20px; font-size:18px; font-weight:700; }
.form-group { margin-bottom:15px; }
.form-group label { display:block; margin-bottom:5px; font-weight:600; font-size:14px; }
.form-group input { width:100%; padding:10px; border:2px solid #eee; border-radius:8px; font-size:14px; }
.form-group input:focus { outline:none; border-color:var(--pink); }
table { width:100%; border-collapse:collapse; }
th, td { padding:15px 10px; text-align:left; border-bottom:1px solid #f0f0f0; }
th { background:var(--pink-light); color:var(--pink-dark); font-size:13px; font-weight:700; }
.action { display:flex; gap:8px; align-items:center; }
.form-inline { display:flex; gap:10px; align-items:end; }
.form-inline .form-group { flex:1; margin:0; }
@media (max-width:768px){ 
    .header { flex-direction:column; align-items:start; gap:10px; }
    .form-inline { flex-direction:column; align-items:stretch; }
}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <h1>CiZycake Admin</h1>
            <p>Hi, <?= $_SESSION['nama'] ?></p>
        </div>
        <a href="logout.php" class="btn btn-red">Logout</a>
    </div>

    <div class="tabs">
        <a href="admin_pesanan.php" class="btn-tab">📦 Kelola Pesanan</a>
        <a href="admin.php" class="btn-tab">📋 Kelola Produk</a>
        <a href="admin_kategori.php" class="btn-tab active">📁 Kelola Kategori</a>
    </div>

    <div class="card">
        <h2>Tambah Kategori Baru</h2>
        <form method="POST" class="form-inline">
            <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="nama_kategori" placeholder="Contoh: Pastry" required>
            </div>
            <div class="form-group">
                <label>Icon Emoji</label>
                <input type="text" name="icon" placeholder="🥐" maxlength="2" required>
            </div>
            <button type="submit" name="tambah" class="btn btn-green">+ Tambah</button>
        </form>
    </div>

    <div class="card">
        <h2>Daftar Kategori</h2>
        <table>
            <tr><th>Icon</th><th>Nama Kategori</th><th>Aksi</th></tr>
            <?php while($k = mysqli_fetch_assoc($kategori)): ?>
            <tr>
                <td style="font-size:28px;"><?= $k['icon'] ?></td>
                <td><b><?= $k['nama_kategori'] ?></b></td>
                <td class="action">
                    <form method="POST" style="display:flex; gap:5px;">
                        <input type="hidden" name="id" value="<?= $k['id'] ?>">
                        <input type="text" name="nama_kategori" value="<?= $k['nama_kategori'] ?>" style="width:120px; padding:8px; border:2px solid #eee; border-radius:6px;">
                        <input type="text" name="icon" value="<?= $k['icon'] ?>" style="width:60px; padding:8px; border:2px solid #eee; border-radius:6px;">
                        <button type="submit" name="edit" class="btn btn-blue" style="padding:8px 12px; font-size:12px;">Update</button>
                    </form>
                    <a href="admin_kategori.php?hapus=<?= $k['id'] ?>" class="btn btn-red" style="padding:8px 12px; font-size:12px;" onclick="return confirm('Yakin hapus? Produk dengan kategori ini tetap ada ya')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>