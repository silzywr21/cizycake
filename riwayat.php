<?php 
include 'koneksi.php';
if(!isset($_SESSION['id'])){ header("Location: login.php"); exit; }
$id_user = $_SESSION['id'];
if(isset($_POST['upload_bukti'])){
    $id_trx = $_POST['id_trx'];
    $file = $_FILES['bukti']['name'];
    $tmp = $_FILES['bukti']['tmp_name'];
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $nama_file = 'bukti_' . $id_trx . '_' . time() . '.' . $ext;
    move_uploaded_file($tmp, "uploads/$nama_file");
    mysqli_query($conn, "UPDATE transaksi SET bukti_bayar='$nama_file', status='dibayar' WHERE id=$id_trx AND id_user=$id_user");
    header("Location: riwayat.php"); exit;
}
$transaksi = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_user=$id_user ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Riwayat Pesanan - <?= NAMA_TOKO ?></title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --pink:#E91E63; --pink-light:#FCE4EC; --white:#FFFFFF; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background: var(--pink-light); }
.container { max-width:1100px; margin:40px auto; padding:0 20px; }
.card { background:var(--white); padding:25px; border-radius:15px; box-shadow:0 5px 20px rgba(0,0,0,0.08); margin-bottom:20px; }
h2 { color:var(--pink); margin-bottom:25px; }
.status { display:inline-block; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600; }
.status-pending { background:#fff3e0; color:#e65100; }
.status-dibayar { background:#e3f2fd; color:#1565c0; }
.status-diproses { background:#f3e5f5; color:#6a1b9a; }
.status-dikirim { background:#e0f2f1; color:#00695c; }
.status-selesai { background:#e8f5e9; color:#2e7d32; }
.status-batal { background:#ffebee; color:#c62828; }
.detail-table { width:100%; margin-top:15px; font-size:14px; }
.detail-table td { padding:8px 0; }
.btn { padding:8px 18px; border:none; border-radius:8px; font-weight:600; cursor:pointer; font-size:13px; text-decoration:none; display:inline-block; }
.btn-pink { background:var(--pink); color:var(--white); }
.sukses { background:#e8f5e9; color:#2e7d32; padding:15px; border-radius:10px; margin-bottom:20px; text-align:center; }
.upload-box { background:var(--pink-light); padding:15px; border-radius:10px; margin-top:15px; }
input[type=file] { margin:10px 0; }
</style>
</head>
<body>
<div class="container">
    <h2>📦 Riwayat Pesanan</h2>
    <?php if(isset($_GET['sukses'])): ?>
    <div class="sukses">✅ Pesanan berhasil dibuat! Kode: <b><?= $_GET['kode'] ?></b></div>
    <?php endif; ?>
    
    <?php if(mysqli_num_rows($transaksi) > 0): ?>
    <?php while($t = mysqli_fetch_assoc($transaksi)): ?>
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom:15px;">
            <div>
                <h3 style="color:#333; margin-bottom:5px;">#<?= $t['kode_trx'] ?></h3>
                <small style="color:#999;"><?= date('d M Y H:i', strtotime($t['created_at'])) ?></small>
            </div>
            <span class="status status-<?= $t['status'] ?>"><?= strtoupper($t['status']) ?></span>
        </div>
        
        <table class="detail-table">
            <tr><td width="30%">Penerima</td><td><b><?= $t['nama_penerima'] ?></b> (<?= $t['no_hp'] ?>)</td></tr>
            <tr><td>Alamat</td><td><?= $t['alamat'] ?></td></tr>
            <tr><td>Metode Bayar</td><td><?= $t['metode_bayar'] ?></td></tr>
            <tr><td>Total</td><td><b style="color:var(--pink); font-size:16px;">Rp <?= number_format($t['total_harga']) ?></b></td></tr>
            <?php if($t['catatan_admin']): ?>
            <tr><td>Catatan Admin</td><td><?= $t['catatan_admin'] ?></td></tr>
            <?php endif; ?>
        </table>
        
        <?php 
        $detail = mysqli_query($conn, "SELECT d.*, p.nama_produk FROM detail_transaksi d JOIN produk p ON d.id_produk=p.id WHERE d.id_transaksi={$t['id']}");
        ?>
        <div style="background:#fafafa; padding:15px; border-radius:10px; margin-top:15px;">
            <b style="font-size:13px; color:#666;">Item Pesanan:</b>
            <?php while($d = mysqli_fetch_assoc($detail)): ?>
            <div style="display:flex; justify-content:space-between; margin-top:8px; font-size:13px;">
                <span><?= $d['nama_produk'] ?> x<?= $d['jumlah'] ?></span>
                <span>Rp <?= number_format($d['harga'] * $d['jumlah']) ?></span>
            </div>
            <?php endwhile; ?>
        </div>
        
        <?php if($t['status']=='pending' && $t['metode_bayar']!='COD' && !$t['bukti_bayar']): ?>
        <div class="upload-box">
            <b style="font-size:14px;">📤 Upload Bukti Pembayaran</b>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_trx" value="<?= $t['id'] ?>">
                <input type="file" name="bukti" accept="image/*" required>
                <button type="submit" name="upload_bukti" class="btn btn-pink">Upload</button>
            </form>
        </div>
        <?php elseif($t['bukti_bayar']): ?>
        <div style="margin-top:15px;">
            <b style="font-size:13px; color:#666;">Bukti Bayar:</b><br>
            <img src="uploads/<?= $t['bukti_bayar'] ?>" style="max-width:200px; border-radius:10px; margin-top:8px;">
        </div>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>
    <?php else: ?>
    <div class="card" style="text-align:center; padding:60px 20px;">
        <div style="font-size:60px; margin-bottom:15px;">📦</div>
        <h3>Belum Ada Pesanan</h3>
        <p style="color:#999; margin:10px 0 20px;">Yuk mulai belanja!</p>
        <a href="index.php" class="btn btn-pink">Mulai Belanja</a>
    </div>
    <?php endif; ?>
</div>
</body>
</html>