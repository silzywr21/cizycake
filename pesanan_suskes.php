<?php
include 'koneksi.php';
if(!isset($_SESSION['id']) || !isset($_GET['id'])){ header("location:index.php"); exit; }

$id_pesanan = (int)$_GET['id'];
$pesanan = mysqli_query($conn, "SELECT * FROM pesanan WHERE id=$id_pesanan AND id_user={$_SESSION['id']}");
$p = mysqli_fetch_assoc($pesanan);
if(!$p){ header("location:index.php"); exit; }

$detail = mysqli_query($conn, "SELECT detail_pesanan.*, produk.nama_produk FROM detail_pesanan JOIN produk ON detail_pesanan.id_produk=produk.id WHERE detail_pesanan.id_pesanan=$id_pesanan");
?>
<!DOCTYPE html>
<html>
<head>
<title>Pesanan Berhasil - <?= NAMA_TOKO ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --pink:#E91E63; --pink-light:#FCE4EC; --pink-dark:#C2185B; --white:#FFFFFF; --black:#212121; --green:#4CAF50; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:var(--pink-light); color:var(--black); padding:30px 0; }
.container { max-width:600px; margin:auto; padding:0 20px; }
.card { background:var(--white); padding:40px; border-radius:20px; box-shadow:0 5px 20px rgba(0,0,0,0.08); text-align:center; }
.icon-sukses { width:80px; height:80px; background:var(--green); color:var(--white); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:40px; margin:0 auto 20px; }
h1 { color:var(--pink); font-size:24px; margin-bottom:10px; }
p { color:#666; margin-bottom:25px; }
.detail { background:#f9f9f9; padding:20px; border-radius:12px; text-align:left; margin-bottom:20px; }
.detail-row { display:flex; justify-content:space-between; margin-bottom:10px; font-size:14px; }
.detail-row b { color:var(--black); }
.btn { background:var(--pink); color:var(--white); border:none; padding:12px 30px; border-radius:10px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; }
.btn:hover { background:var(--pink-dark); }
.item { font-size:13px; color:#666; margin-bottom:5px; }
</style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="icon-sukses">✓</div>
        <h1>Pesanan Berhasil!</h1>
        <p>Terima kasih, pesanan kamu sudah kami terima</p>
        
        <div class="detail">
            <div class="detail-row"><span>No. Pesanan:</span> <b>#<?= $p['id'] ?></b></div>
            <div class="detail-row"><span>Nama:</span> <b><?= $p['nama_penerima'] ?></b></div>
            <div class="detail-row"><span>Total:</span> <b>Rp <?= number_format($p['total']) ?></b></div>
            <div class="detail-row"><span>Pembayaran:</span> <b><?= $p['metode_bayar'] ?></b></div>
            <div class="detail-row"><span>Status:</span> <b style="color:orange;"><?= $p['status'] ?></b></div>
            <hr style="margin:15px 0; border:none; border-top:1px solid #eee;">
            <?php while($d = mysqli_fetch_assoc($detail)): ?>
            <div class="item"><?= $d['nama_produk'] ?> x<?= $d['jumlah'] ?> - Rp <?= number_format($d['harga'] * $d['jumlah']) ?></div>
            <?php endwhile; ?>
        </div>

        <a href="index.php" class="btn">Kembali ke Beranda</a>
        <a href="https://wa.me/<?= WA_ADMIN ?>?text=Halo kak, saya mau konfirmasi pesanan #<?= $p['id'] ?>" class="btn" style="background:#25D366; margin-left:10px;" target="_blank">Chat WhatsApp</a>
    </div>
</div>
</body>
</html>