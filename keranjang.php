<?php
include 'koneksi.php';
if(!isset($_SESSION['id'])){ header("location:login.php"); exit; }

// Tambah ke keranjang
if(isset($_GET['add'])){
    $id_produk = (int)$_GET['add'];
    
    if(!isset($_SESSION['keranjang'])){
        $_SESSION['keranjang'] = array();
    }
    
    if(isset($_SESSION['keranjang'][$id_produk])){
        $_SESSION['keranjang'][$id_produk]++;
    } else {
        $_SESSION['keranjang'][$id_produk] = 1;
    }
    header("location:keranjang.php"); exit;
}

// Hapus item
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    unset($_SESSION['keranjang'][$id]);
    header("location:keranjang.php"); exit;
}

// Update jumlah
if(isset($_POST['update'])){
    foreach($_POST['qty'] as $id => $jumlah){
        if($jumlah > 0){
            $_SESSION['keranjang'][$id] = (int)$jumlah;
        } else {
            unset($_SESSION['keranjang'][$id]);
        }
    }
    header("location:keranjang.php"); exit;
}

$items = [];
$total = 0;
if(isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0){
    foreach($_SESSION['keranjang'] as $id_produk => $jumlah){
        $q = mysqli_query($conn, "SELECT * FROM produk WHERE id=$id_produk");
        $p = mysqli_fetch_assoc($q);
        $p['jumlah'] = $jumlah;
        $p['subtotal'] = $p['harga'] * $jumlah;
        $total += $p['subtotal'];
        $items[] = $p;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Keranjang - <?= NAMA_TOKO ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --pink:#E91E63; --pink-light:#FCE4EC; --pink-dark:#C2185B; --white:#FFFFFF; --black:#212121; --red:#F44336; --green:#4CAF50; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:#fafafa; color:var(--black); padding:30px 0; }
.container { max-width:900px; margin:auto; padding:0 20px; }
.title { text-align:center; font-size:24px; font-weight:700; color:var(--pink); margin-bottom:30px; }
.card { background:var(--white); padding:30px; border-radius:20px; box-shadow:0 5px 20px rgba(0,0,0,0.08); }
table { width:100%; border-collapse:collapse; }
th, td { padding:15px 10px; text-align:left; border-bottom:1px solid #f0f0f0; }
th { background:var(--pink-light); color:var(--pink-dark); font-size:13px; font-weight:700; }
.img-thumb { width:60px; height:60px; object-fit:cover; border-radius:8px; background:var(--pink-light); }
.qty-input { width:60px; padding:8px; border:2px solid #eee; border-radius:8px; text-align:center; }
.btn { background:var(--pink); color:var(--white); border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; font-size:14px; }
.btn:hover { background:var(--pink-dark); }
.btn-red { background:var(--red); }
.btn-green { background:var(--green); }
.total-box { text-align:right; margin-top:20px; font-size:18px; }
.total-box b { font-size:24px; color:var(--pink-dark); }
.action-bottom { display:flex; justify-content:space-between; margin-top:20px; gap:10px; }
.kosong { text-align:center; padding:60px 0; color:#999; }
</style>
</head>
<body>
<div class="container">
    <h1 class="title">Keranjang Belanja</h1>
    <div class="card">
        <?php if(count($items) > 0): ?>
        <form method="POST">
            <table>
                <tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th>Aksi</th></tr>
                <?php foreach($items as $item): ?>
                <tr>
                    <td style="display:flex; align-items:center; gap:10px;">
                        <img src="<?= $item['foto'] ?: 'https://via.placeholder.com/60x60/FCE4EC/E91E63?text=No' ?>" class="img-thumb">
                        <b><?= $item['nama_produk'] ?></b>
                    </td>
                    <td>Rp <?= number_format($item['harga']) ?></td>
                    <td><input type="number" name="qty[<?= $item['id'] ?>]" value="<?= $item['jumlah'] ?>" min="0" class="qty-input"></td>
                    <td><b>Rp <?= number_format($item['subtotal']) ?></b></td>
                    <td><a href="keranjang.php?hapus=<?= $item['id'] ?>" class="btn btn-red" style="padding:6px 12px; font-size:12px;" onclick="return confirm('Hapus item?')">Hapus</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <div class="total-box">
                Total Belanja: <b>Rp <?= number_format($total) ?></b>
            </div>
            <div class="action-bottom">
                <a href="index.php" class="btn" style="background:#999;">← Lanjut Belanja</a>
                <button type="submit" name="update" class="btn">Update Keranjang</button>
                <a href="checkout.php" class="btn btn-green">Checkout →</a>
            </div>
        </form>
        <?php else: ?>
        <div class="kosong">
            <h3>Keranjang masih kosong 😢</h3>
            <br><a href="index.php" class="btn">Mulai Belanja</a>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>