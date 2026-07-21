<?php
include 'koneksi.php';
if(!isset($_SESSION['id'])){ header("location:login.php"); exit; }

$id_user = $_SESSION['id'];
$pesanan = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_user=$id_user ORDER BY tanggal DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Riwayat Pesanan - <?= NAMA_TOKO ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --pink:#E91E63; --pink-light:#FCE4EC; --pink-dark:#C2185B; --white:#FFFFFF; --black:#212121; --orange:#FF9800; --green:#4CAF50; --blue:#2196F3; --red:#F44336; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:#fafafa; color:var(--black); padding:20px 0; }
.container { max-width:900px; margin:auto; padding:0 20px; }
.navbar { background:var(--white); padding:20px; border-radius:15px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
.logo { font-size:24px; font-weight:800; color:var(--pink); text-decoration:none; }
.btn { background:var(--pink); color:var(--white); border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; font-size:14px; }
.btn:hover { background:var(--pink-dark); }
.card { background:var(--white); padding:30px; border-radius:15px; box-shadow:0 2px 10px rgba(0,0,0,0.05); margin-bottom:20px; }
.card h1 { color:var(--pink); font-size:22px; margin-bottom:25px; }
.pesanan-item { border:2px solid #f0f0f0; border-radius:12px; padding:20px; margin-bottom:15px; }
.pesanan-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; }
.pesanan-id { font-weight:700; font-size:16px; }
.badge { padding:6px 12px; border-radius:20px; font-size:12px; font-weight:600; }
.badge-pending { background:#FFF9C4; color:#F57C00; }
.badge-diproses { background:#E3F2FD; color:var(--blue); }
.badge-dikirim { background:#E8F5E9; color:var(--green); }
.badge-selesai { background:#F3E5F5; color:var(--pink-dark); }
.badge-dibatalkan { background:#FFEBEE; color:var(--red); }
.detail-row { display:flex; justify-content:space-between; margin-bottom:8px; font-size:14px; }
.detail-row b { color:var(--black); }
.produk-list { margin-top:10px; padding-top:10px; border-top:1px solid #eee; }
.produk-list div { font-size:13px; color:#666; margin-bottom:4px; }
.kosong { text-align:center; padding:60px 0; color:#999; }
</style>
</head>
<body>
<div class="container">
    <div class="navbar">
        <a href="index.php" class="logo">CiZycake</a>
        <a href="index.php" class="btn">← Kembali Belanja</a>
    </div>

    <div class="card">
        <h1>Riwayat Pesanan Saya</h1>
        <?php if(mysqli_num_rows($pesanan) > 0): ?>
            <?php while($p = mysqli_fetch_assoc($pesanan)): 
                $detail = mysqli_query($conn, "SELECT detail_pesanan.*, produk.nama_produk FROM detail_pesanan JOIN produk ON detail_pesanan.id_produk=produk.id WHERE detail_pesanan.id_pesanan={$p['id']}");
                $status_class = strtolower($p['status']);
            ?>
            <div class="pesanan-item">
                <div class="pesanan-header">
                    <div class="pesanan-id">#<?= $p['id'] ?> - <?= date('d M Y, H:i', strtotime($p['tanggal'])) ?></div>
                    <span class="badge badge-<?= $status_class ?>"><?= $p['status'] ?></span>
                </div>
                <div class="detail-row"><span>Penerima:</span> <b><?= $p['nama_penerima'] ?></b></div>
                <div class="detail-row"><span>HP:</span> <b><?= $p['hp'] ?></b></div>
                <div class="detail-row"><span>Total:</span> <b style="color:var(--pink-dark);">Rp <?= number_format($p['total']) ?></b></div>
                <div class="detail-row"><span>Pembayaran:</span> <b><?= $p['metode_bayar'] ?></b></div>
                <div class="detail-row"><span>Alamat:</span> <b><?= $p['alamat'] ?></b></div>
                <div class="produk-list">
                    <b style="font-size:13px;">Detail Produk:</b>
                    <?php while($d = mysqli_fetch_assoc($detail)): ?>
                    <div>• <?= $d['nama_produk'] ?> x<?= $d['jumlah'] ?> - Rp <?= number_format($d['harga'] * $d['jumlah']) ?></div>
                    <?php endwhile; ?>
                </div>
                <?php if($p['status'] == 'Pending'): ?>
                <div style="margin-top:15px;">
                    <a href="https://wa.me/<?= WA_ADMIN ?>?text=Halo kak, konfirmasi pesanan #<?= $p['id'] ?>" class="btn" style="background:#25D366; font-size:12px; padding:8px 15px;" target="_blank">Chat Admin WA</a>
                </div>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="kosong">
                <h3>Belum ada pesanan 😢</h3>
                <br><a href="index.php" class="btn">Mulai Belanja</a>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>