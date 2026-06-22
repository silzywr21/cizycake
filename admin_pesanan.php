<?php
include 'koneksi.php';
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){ 
    header("location:login.php"); exit; 
}

if(isset($_POST['update_status'])){
    $id = (int)$_POST['id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE pesanan SET status='$status' WHERE id=$id");
    header("location:admin_pesanan.php"); exit;
}

$pesanan = mysqli_query($conn, "SELECT pesanan.*, users.nama as nama_user FROM pesanan JOIN users ON pesanan.id_user=users.id ORDER BY pesanan.tanggal DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Kelola Pesanan - <?= NAMA_TOKO ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --pink:#E91E63; --pink-light:#FCE4EC; --pink-dark:#C2185B; --white:#FFFFFF; --black:#212121; --red:#F44336; --green:#4CAF50; --orange:#FF9800; --blue:#2196F3; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:#fafafa; color:var(--black); }
.container { max-width:1200px; margin:auto; padding:20px; }
.header { background:var(--white); padding:25px; border-radius:15px; box-shadow:0 2px 10px rgba(0,0,0,0.05); margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; }
.header h1 { font-size:24px; font-weight:700; color:var(--pink); margin-bottom:5px; }
.btn { background:var(--pink); color:var(--white); border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; font-size:14px; }
.btn-tab { background:var(--white); color:var(--black); border:1px solid #eee; padding:12px 20px; border-radius:10px; box-shadow:0 2px 5px rgba(0,0,0,0.05); }
.btn-tab.active { background:var(--pink); color:var(--white); border-color:var(--pink); }
.tabs { display:flex; gap:15px; margin-bottom:20px; }
.card { background:var(--white); padding:25px; border-radius:15px; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
table { width:100%; border-collapse:collapse; }
th, td { padding:12px 10px; text-align:left; border-bottom:1px solid #f0f0f0; font-size:13px; }
th { background:var(--pink-light); color:var(--pink-dark); font-weight:700; }
.badge { padding:5px 10px; border-radius:15px; font-size:11px; font-weight:600; }
.badge-pending { background:#FFF9C4; color:#F57C00; }
.badge-diproses { background:#E3F2FD; color:var(--blue); }
.badge-dikirim { background:#E8F5E9; color:var(--green); }
.badge-selesai { background:#F3E5F5; color:var(--pink-dark); }
select { padding:6px; border:2px solid #eee; border-radius:6px; font-size:12px; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <h1>CiZycake Admin</h1>
            <p>Hi, <?= $_SESSION['nama'] ?></p>
        </div>
        <a href="logout.php" class="btn" style="background:var(--red);">Logout</a>
    </div>

    <div class="tabs">
        <a href="admin_pesanan.php" class="btn-tab active">📦 Kelola Pesanan</a>
        <a href="admin.php" class="btn-tab">📋 Kelola Produk</a>
        <a href="admin_kategori.php" class="btn-tab">📁 Kelola Kategori</a>
    </div>

    <div class="card">
        <h2 style="color:var(--pink); margin-bottom:20px;">Daftar Pesanan Masuk</h2>
        <table>
            <tr><th>ID</th><th>Customer</th><th>Penerima</th><th>Total</th><th>Bayar</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr>
            <?php while($p = mysqli_fetch_assoc($pesanan)): ?>
            <tr>
                <td><b>#<?= $p['id'] ?></b></td>
                <td><?= $p['nama_user'] ?></td>
                <td><?= $p['nama_penerima'] ?><br><small><?= $p['hp'] ?></small></td>
                <td>Rp <?= number_format($p['total']) ?></td>
                <td><?= $p['metode_bayar'] ?></td>
                <td><span class="badge badge-<?= strtolower($p['status']) ?>"><?= $p['status'] ?></span></td>
                <td><?= date('d/m H:i', strtotime($p['tanggal'])) ?></td>
                <td>
                    <form method="POST" style="display:flex; gap:5px;">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <select name="status">
                            <option value="Pending" <?= $p['status']=='Pending'?'selected':'' ?>>Pending</option>
                            <option value="Diproses" <?= $p['status']=='Diproses'?'selected':'' ?>>Diproses</option>
                            <option value="Dikirim" <?= $p['status']=='Dikirim'?'selected':'' ?>>Dikirim</option>
                            <option value="Selesai" <?= $p['status']=='Selesai'?'selected':'' ?>>Selesai</option>
                            <option value="Dibatalkan" <?= $p['status']=='Dibatalkan'?'selected':'' ?>>Dibatalkan</option>
                        </select>
                        <button type="submit" name="update_status" class="btn" style="padding:6px 10px; font-size:11px;">Update</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>