<?php
include 'koneksi.php';
if(!isset($_SESSION['id'])){ header("location:login.php"); exit; }
if(empty($_SESSION['keranjang'])){ header("location:keranjang.php"); exit; }

$items = [];
$total = 0;
foreach($_SESSION['keranjang'] as $id_produk => $jumlah){
    $q = mysqli_query($conn, "SELECT * FROM produk WHERE id=$id_produk");
    $p = mysqli_fetch_assoc($q);
    $p['jumlah'] = $jumlah;
    $p['subtotal'] = $p['harga'] * $jumlah;
    $total += $p['subtotal'];
    $items[] = $p;
}

if(isset($_POST['checkout'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $hp = mysqli_real_escape_string($conn, $_POST['hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $metode = mysqli_real_escape_string($conn, $_POST['metode']);
    
    mysqli_query($conn, "INSERT INTO pesanan (id_user, nama_penerima, hp, alamat, total, metode_bayar, status) VALUES ({$_SESSION['id']}, '$nama', '$hp', '$alamat', $total, '$metode', 'Pending')");
    $id_pesanan = mysqli_insert_id($conn);
    
    foreach($items as $item){
        mysqli_query($conn, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga) VALUES ($id_pesanan, {$item['id']}, {$item['jumlah']}, {$item['harga']})");
        mysqli_query($conn, "UPDATE produk SET stok = stok - {$item['jumlah']} WHERE id={$item['id']}");
    }
    
    unset($_SESSION['keranjang']);
    header("location:pesanan_sukses.php?id=$id_pesanan"); exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Checkout - <?= NAMA_TOKO ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --pink:#E91E63; --pink-light:#FCE4EC; --pink-dark:#C2185B; --white:#FFFFFF; --black:#212121; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:var(--pink-light); color:var(--black); padding:30px 0; }
.container { max-width:600px; margin:auto; padding:0 20px; }
.title { text-align:center; font-size:24px; font-weight:700; color:var(--pink); margin-bottom:30px; }
.card { background:var(--white); padding:30px; border-radius:20px; box-shadow:0 5px 20px rgba(0,0,0,0.08); }
.section-title { font-size:16px; font-weight:700; color:var(--pink); margin-bottom:15px; }
.form-group { margin-bottom:18px; }
.form-group label { display:block; margin-bottom:6px; font-weight:600; font-size:14px; }
.form-group input, .form-group textarea { width:100%; padding:12px; border:2px solid #eee; border-radius:10px; font-size:14px; }
.form-group input:focus, .form-group textarea:focus { outline:none; border-color:var(--pink); }
.metode-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:20px; }
.radio-card { border:2px solid #eee; border-radius:12px; padding:15px; cursor:pointer; display:flex; align-items:center; gap:10px; transition:0.3s; }
.radio-card:hover { border-color:var(--pink); }
.radio-card input { width:auto; }
.radio-card.active { border-color:var(--pink); background:var(--pink-light); }
.info-rek { background:#FFF9C4; padding:15px; border-radius:10px; font-size:14px; margin-bottom:20px; display:none; }
.ringkasan { background:#f9f9f9; padding:15px; border-radius:10px; margin-bottom:20px; }
.ringkasan-item { display:flex; justify-content:space-between; margin-bottom:8px; font-size:14px; }
.total { font-weight:700; font-size:18px; color:var(--pink-dark); border-top:2px solid #eee; padding-top:10px; }
.btn-checkout { background:var(--pink); color:var(--white); border:none; padding:15px; border-radius:12px; width:100%; font-weight:700; font-size:16px; cursor:pointer; }
.btn-checkout:hover { background:var(--pink-dark); }
</style>
</head>
<body>
<div class="container">
    <h1 class="title">Checkout Pesanan</h1>
    <div class="card">
        <form method="POST">
            <div class="section-title">1. Data Penerima</div>
            <div class="form-group">
                <label>Nama Penerima *</label>
                <input type="text" name="nama" placeholder="Nama lengkap penerima" value="<?= $_SESSION['nama'] ?>" required>
            </div>
            <div class="form-group">
                <label>No. HP / WhatsApp *</label>
                <input type="text" name="hp" placeholder="08xxxxxxxxxx" required>
            </div>
            <div class="form-group">
                <label>Alamat Lengkap *</label>
                <textarea name="alamat" rows="3" placeholder="Jl. Nama Jalan No. Rumah, Kelurahan, Kecamatan, Kota" required></textarea>
            </div>

            <div class="section-title">2. Metode Pembayaran</div>
            <div class="metode-grid">
                <label class="radio-card active">
                    <input type="radio" name="metode" value="COD" checked onchange="ubahMetode(this)"> 🏠 COD
                </label>
                <label class="radio-card">
                    <input type="radio" name="metode" value="Transfer BCA" onchange="ubahMetode(this)"> 🏦 Transfer BCA
                </label>
                <label class="radio-card">
                    <input type="radio" name="metode" value="QRIS" onchange="ubahMetode(this)"> 📱 QRIS
                </label>
                <label class="radio-card">
                    <input type="radio" name="metode" value="DANA" onchange="ubahMetode(this)"> 💙 DANA
                </label>
                <label class="radio-card">
                    <input type="radio" name="metode" value="OVO" onchange="ubahMetode(this)"> 💜 OVO
                </label>
                <label class="radio-card">
                    <input type="radio" name="metode" value="Gopay" onchange="ubahMetode(this)"> 💚 Gopay
                </label>
            </div>

            <div class="info-rek" id="infoRek">
                <b>Info Rekening:</b><br>
                <?= defined('NO_REK') ? NO_REK : 'Silakan hubungi admin' ?>
            </div>

            <div class="section-title">3. Ringkasan Pesanan</div>
            <div class="ringkasan">
                <?php foreach($items as $item): ?>
                <div class="ringkasan-item">
                    <span><?= $item['nama_produk'] ?> x<?= $item['jumlah'] ?></span>
                    <span>Rp <?= number_format($item['subtotal']) ?></span>
                </div>
                <?php endforeach; ?>
                <div class="ringkasan-item total">
                    <span>Total Bayar</span>
                    <span>Rp <?= number_format($total) ?></span>
                </div>
            </div>

            <button type="submit" name="checkout" class="btn-checkout">Buat Pesanan Sekarang</button>
        </form>
    </div>
</div>

<script>
function ubahMetode(el){
    document.querySelectorAll('.radio-card').forEach(e => e.classList.remove('active'));
    el.parentElement.classList.add('active');
    document.getElementById('infoRek').style.display = el.value.includes('Transfer') ? 'block' : 'none';
}
</script>
</body>
</html>