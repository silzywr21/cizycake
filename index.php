<?php 
include 'koneksi.php';
$jml_keranjang = 0;
if(isset($_SESSION['id'])){
    $id_user = $_SESSION['id'];
    $q = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM keranjang WHERE id_user=$id_user");
    $jml_keranjang = mysqli_fetch_assoc($q)['total'] ?? 0;
}

$kategori_aktif = $_GET['kategori'] ?? '';
$where = $kategori_aktif ? "WHERE kategori='$kategori_aktif'" : "";
$produk = mysqli_query($conn, "SELECT * FROM produk $where ORDER BY id DESC");
$terlaris = mysqli_query($conn, "SELECT * FROM produk WHERE terlaris=1 LIMIT 4");
?>
<!DOCTYPE html>
<html>
<head>
<title><?= NAMA_TOKO ?> - Toko Roti Terbaik</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
:root { --pink:#E91E63; --pink-light:#FCE4EC; --pink-dark:#C2185B; --white:#FFFFFF; --gray:#555; --black:#212121; --dark:#1a1a1a; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; scroll-behavior:smooth; }
body { background: var(--white); color: var(--black); }
.container { max-width:1200px; margin:auto; padding:0 20px; }
.btn-pink { background:var(--pink); color:var(--white); border:none; padding:12px 30px; border-radius:30px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; transition:0.3s; }
.btn-pink:hover { background:var(--pink-dark); transform:translateY(-2px); box-shadow:0 5px 15px rgba(233,30,99,0.4); }
.navbar { background:var(--white); padding:20px 0; box-shadow:0 2px 10px rgba(0,0,0,0.05); position:sticky; top:0; z-index:100; }
.navbar .container { display:flex; justify-content:space-between; align-items:center; }
.logo { font-size:26px; font-weight:800; color:var(--pink); text-decoration:none; }
.logo span { color:var(--pink-dark); font-weight:600; }
.nav-menu a { margin:0 18px; text-decoration:none; color:var(--black); font-weight:500; transition:0.3s; position:relative; }
.nav-menu a:hover, .nav-menu a.active { color:var(--pink); }
.nav-menu a.active::after { content:''; position:absolute; bottom:-5px; left:0; width:100%; height:2px; background:var(--pink); }
.nav-right { display:flex; align-items:center; gap:15px; }
.cart-icon { position:relative; font-size:24px; text-decoration:none; color:var(--black); }
.cart-badge { position:absolute; top:-8px; right:-8px; background:var(--pink); color:var(--white); border-radius:50%; width:20px; height:20px; font-size:11px; display:flex; align-items:center; justify-content:center; font-weight:700; }
.hero { background:linear-gradient(135deg, #fff 0%, var(--pink-light) 100%); padding:80px 0; position:relative; overflow:hidden; }
.hero-content { display:flex; align-items:center; gap:40px; }
.hero-text { flex:1; z-index:2; }
.hero-text h1 { font-size:50px; line-height:1.1; margin-bottom:10px; font-weight:800; color:var(--pink-dark); }
.hero-text h1 span { color:var(--pink); display:block; font-weight:600; }
.hero-text p { color:var(--gray); margin:20px 0 30px; max-width:450px; font-size:16px; }
.hero-img { flex:1; text-align:center; position:relative; }
.hero-img img { width:380px; filter:drop-shadow(0 20px 30px rgba(233,30,99,0.25)); animation:float 3s ease-in-out infinite; }
@keyframes float { 0%,100%{ transform:translateY(0); } 50%{ transform:translateY(-20px); } }
.strawberry { position:absolute; width:60px; z-index:1; }
.straw1 { top:10%; right:15%; animation:float 4s ease-in-out infinite; }
.straw2 { bottom:20%; left:5%; animation:float 5s ease-in-out infinite; }
.straw3 { bottom:10%; right:20%; width:40px; animation:float 3.5s ease-in-out infinite; }
.straw4 { top:60%; left:45%; width:35px; animation:float 4.5s ease-in-out infinite; }
.section { padding:80px 0; }
.section-title { text-align:center; font-size:34px; margin-bottom:50px; color:var(--pink-dark); font-weight:700; }
.kategori-grid { display:flex; justify-content:center; gap:25px; flex-wrap:wrap; margin-bottom:60px; }
.kat-item { text-align:center; text-decoration:none; color:var(--black); padding:20px; border-radius:15px; transition:0.3s; min-width:120px; background:var(--pink-light); }
.kat-item:hover, .kat-item.active { background:var(--pink); color:var(--white); transform:translateY(-5px); }
.kat-icon { font-size:40px; margin-bottom:10px; }
.produk-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(270px, 1fr)); gap:30px; }
.produk-card { background:var(--white); border-radius:20px; box-shadow:0 5px 20px rgba(0,0,0,0.08); overflow:hidden; transition:0.3s; }
.produk-card:hover { transform:translateY(-8px); box-shadow:0 10px 30px rgba(233,30,99,0.2); }
.produk-card img { width:100%; height:250px; object-fit:cover; background:var(--pink-light); }
.produk-info { padding:20px; }
.produk-info h4 { font-size:18px; margin-bottom:8px; color:var(--black); }
.produk-info .kategori-tag { font-size:12px; color:var(--pink); background:var(--pink-light); display:inline-block; padding:3px 10px; border-radius:12px; margin-bottom:10px; font-weight:600; }
.produk-info .deskripsi { font-size:13px; color:var(--gray); margin-bottom:15px; height:40px; overflow:hidden; }
.produk-bottom { display:flex; justify-content:space-between; align-items:center; margin-top:15px; }
.produk-price { font-size:20px; font-weight:700; color:var(--pink-dark); }
.btn-add { background:var(--pink-light); color:var(--pink); border:none; padding:8px 18px; border-radius:20px; font-size:13px; cursor:pointer; text-decoration:none; font-weight:600; transition:0.3s; }
.btn-add:hover { background:var(--pink); color:var(--white); }
.about-section { background:var(--pink-light); position:relative; overflow:hidden; }
.feature-icon { width:50px; height:50px; background:var(--pink); color:var(--white); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:20px; margin:0 auto 10px; }
.kontak-grid { display:grid; grid-template-columns:1fr 1fr; gap:30px; margin-top:40px; }
.kontak-card { background:var(--white); padding:25px; border-radius:15px; box-shadow:0 5px 15px rgba(0,0,0,0.05); }
.kontak-card h4 { color:var(--pink); margin-bottom:15px; display:flex; align-items:center; gap:8px; }
.kontak-card table { width:100%; font-size:14px; }
.kontak-card td { padding:8px 0; }
.kontak-card td:first-child { color:var(--gray); }
.footer { background:var(--dark); color:var(--white); padding:60px 0 20px; }
.footer-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:40px; margin-bottom:40px; }
.footer h4 { margin-bottom:20px; color:var(--pink-light); }
.footer a, .footer p { color:#ccc; font-size:14px; text-decoration:none; line-height:2; transition:0.3s; }
.footer a:hover { color:var(--pink); }
.sosmed { display:flex; gap:15px; margin-top:15px; }
.sosmed a { width:35px; height:35px; background:var(--pink); border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--white); }
.copyright { text-align:center; padding-top:30px; border-top:1px solid #333; color:#999; font-size:14px; }
@media (max-width:768px){ 
    .hero-content{ flex-direction:column; text-align:center; } 
    .hero-text h1{ font-size:36px; }
    .hero-img img{ width:280px; }
    .kontak-grid, .footer-grid{ grid-template-columns:1fr; }
    .nav-menu{ display:none; }
}
</style>
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="index.php" class="logo">CiZy<span>cake</span></a>
        <div class="nav-menu">
            <a href="index.php" class="<?= !$kategori_aktif ? 'active' : '' ?>">Beranda</a>
            <a href="#kategori">Kategori</a>
            <a href="#terlaris">Produk Terlaris</a>
            <a href="#tentang">Tentang Toko</a>
        </div>
        <div class="nav-right">
            <?php if(isset($_SESSION['role'])): ?>
                <a href="keranjang.php" class="cart-icon">🛒
                    <?php if($jml_keranjang > 0): ?>
                    <span class="cart-badge"><?= $jml_keranjang ?></span>
                    <?php endif; ?>
                </a>
                <span style="font-size:14px;">Hi, <?= $_SESSION['nama'] ?></span>
                <?php if($_SESSION['role'] == 'admin'): ?>
                    <a href="admin.php" class="btn-pink">Admin</a>
                <?php endif; ?>
                <a href="logout.php" style="color:var(--pink); text-decoration:none; font-size:14px; font-weight:600;">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-pink">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<section class="hero">
    <img src="https://cdn-icons-png.flaticon.com/512/590/590685.png" class="strawberry straw1" alt="strawberry">
    <img src="https://cdn-icons-png.flaticon.com/512/590/590685.png" class="strawberry straw2" alt="strawberry">
    <img src="https://cdn-icons-png.flaticon.com/512/590/590685.png" class="strawberry straw3" alt="strawberry">
    <img src="https://cdn-icons-png.flaticon.com/512/590/590685.png" class="strawberry straw4" alt="strawberry">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Toko Roti Terbaik <span>Segar & Lezat</span></h1>
                <p>Nikmati berbagai macam roti dan kue segar buatan tangan. Pesan online dengan mudah dan cepat.</p>
                <a href="#produk" class="btn-pink">Lihat Produk</a>
            </div>
            <div class="hero-img">
                <img src="https://cdn-icons-png.flaticon.com/512/590/590685.png" 
                     alt="Strawberry Fresh">
            </div>
        </div>
    </div>
</section>

<section class="section" id="kategori">
    <div class="container">
        <h2 class="section-title">Produk</h2>
        <div class="kategori-grid">
            <a href="index.php" class="kat-item <?= !$kategori_aktif ? 'active' : '' ?>">
                <div class="kat-icon">🍰</div><p>Semua</p>
            </a>
            <a href="index.php?kategori=Roti" class="kat-item <?= $kategori_aktif=='Roti' ? 'active' : '' ?>">
                <div class="kat-icon">🍞</div><p>Roti</p>
            </a>
            <a href="index.php?kategori=Kue" class="kat-item <?= $kategori_aktif=='Kue' ? 'active' : '' ?>">
                <div class="kat-icon">🎂</div><p>Kue</p>
            </a>
            <a href="index.php?kategori=Cupcake" class="kat-item <?= $kategori_aktif=='Cupcake' ? 'active' : '' ?>">
                <div class="kat-icon">🧁</div><p>Cupcake</p>
            </a>
            <a href="index.php?kategori=Donat" class="kat-item <?= $kategori_aktif=='Donat' ? 'active' : '' ?>">
                <div class="kat-icon">🍩</div><p>Donat</p>
            </a>
            <a href="index.php?kategori=Cookies" class="kat-item <?= $kategori_aktif=='Cookies' ? 'active' : '' ?>">
                <div class="kat-icon">🍪</div><p>Cookies</p>
            </a>
            <a href="index.php?kategori=Brownies" class="kat-item <?= $kategori_aktif=='Brownies' ? 'active' : '' ?>">
                <div class="kat-icon">🍫</div><p>Brownies</p>
            </a>
        </div>
    </div>
</section>

<section class="section" id="terlaris" style="background:var(--pink-light);">
    <div class="container">
        <h2 class="section-title">Produk Terlaris</h2>
        <div class="produk-grid">
            <?php while($t = mysqli_fetch_assoc($terlaris)): ?>
            <div class="produk-card">
                <img src="<?= $t['foto'] ?: 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=400&fit=crop&q=80' ?>" 
                     alt="<?= $t['nama_produk'] ?>"
                     onerror="this.src='https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=400&h=400&fit=crop&q=80'">
                <div class="produk-info">
                    <span class="kategori-tag"><?= $t['kategori'] ?></span>
                    <h4><?= $t['nama_produk'] ?></h4>
                    <div class="produk-bottom">
                        <span class="produk-price">Rp <?= number_format($t['harga']) ?></span>
                        <?php if(isset($_SESSION['role']) && $t['stok'] > 0): ?>
                            <!-- FIX 1: GANTI INI DARI tambah_keranjang.php?id= JADI keranjang.php?add= -->
                            <a href="keranjang.php?add=<?= $t['id'] ?>" class="btn-add">+ Keranjang</a>
                        <?php elseif($t['stok'] == 0): ?>
                            <span style="font-size:12px; color:red; font-weight:600;">Habis</span>
                        <?php else: ?>
                            <a href="login.php" class="btn-add">+ Keranjang</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<section class="section" id="produk">
    <div class="container">
        <h2 class="section-title"><?= $kategori_aktif ? "$kategori_aktif" : "Semua Produk" ?></h2>
        <div class="produk-grid">
            <?php if(mysqli_num_rows($produk) > 0): ?>
            <?php while($p = mysqli_fetch_assoc($produk)): ?>
            <div class="produk-card">
                <img src="<?= $p['foto'] ?: 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=400&fit=crop&q=80' ?>" 
                     alt="<?= $p['nama_produk'] ?>"
                     onerror="this.src='https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=400&h=400&fit=crop&q=80'">
                <div class="produk-info">
                    <span class="kategori-tag"><?= $p['kategori'] ?></span>
                    <h4><?= $p['nama_produk'] ?></h4>
                    <p class="deskripsi"><?= $p['deskripsi'] ?></p>
                    <div class="produk-bottom">
                        <span class="produk-price">Rp <?= number_format($p['harga']) ?></span>
                        <?php if(isset($_SESSION['role']) && $p['stok'] > 0): ?>
                            <!-- FIX 2: GANTI INI DARI tambah_keranjang.php?id= JADI keranjang.php?add= -->
                            <a href="keranjang.php?add=<?= $p['id'] ?>" class="btn-add">+ Keranjang</a>
                        <?php elseif($p['stok'] == 0): ?>
                            <span style="font-size:12px; color:red; font-weight:600;">Habis</span>
                        <?php else: ?>
                            <a href="login.php" class="btn-add">+ Keranjang</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <p style="grid-column:1/-1; text-align:center; color:#666;">Belum ada produk di kategori ini.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section about-section" id="tentang">
    <div class="container" style="text-align:center; max-width:800px; margin:0 auto;">
        <h2 class="section-title">Tentang CiZycake</h2>
        <p style="font-size:16px; color:var(--gray); line-height:1.8; margin-bottom:30px;">
            CiZycake adalah toko roti modern yang menyediakan produk segar buatan tangan. 
            Kami fokus pada kualitas bahan dan rasa yang autentik untuk keluarga Indonesia.
        </p>
        
        <div style="display:flex; justify-content:center; gap:40px; flex-wrap:wrap; margin:40px 0;">
            <div style="text-align:center;">
                <div class="feature-icon">✓</div>
                <b>Bahan Segar Alami</b><br>
                <span style="font-size:14px; color:var(--gray);">Tanpa pengawet berbahaya</span>
            </div>
            <div style="text-align:center;">
                <div class="feature-icon">✓</div>
                <b>Pengiriman Cepat</b><br>
                <span style="font-size:14px; color:var(--gray);">Same day area Bandung</span>
            </div>
            <div style="text-align:center;">
                <div class="feature-icon">✓</div>
                <b>Order Mudah</b><br>
                <span style="font-size:14px; color:var(--gray);">Website & WhatsApp 24 jam</span>
            </div>
        </div>

        <h2 class="section-title" style="margin-top:80px;">Kontak & Lokasi</h2>
        <div class="kontak-grid">
            <div class="kontak-card">
                <h4>🕐 Jam Operasi</h4>
                <table>
                    <tr><td>Senin - Jumat</td><td><b>08:00 - 20:00</b></td></tr>
                    <tr><td>Sabtu - Minggu</td><td><b>08:00 - 22:00</b></td></tr>
                    <tr><td colspan="2" style="padding-top:10px; color:#999; font-size:13px;">ℹ️ Kami buka setiap hari termasuk hari libur</td></tr>
                </table>
            </div>
            <div class="kontak-card">
                <h4>📍 Lokasi Toko</h4>
                <p>Kunjungi toko kami di alamat berikut:</p>
                <p style="margin:10px 0; font-weight:600;">UNIVERSITAS TEKNOLOGI MATARAM</p>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3945.0387027742304!2d116.0921182!3d-8.5922774!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dcdbf7ed05603ab%3A0x6b5771dd5cbe0d20!2sUniversitas%20Teknologi%20Mataram!5e0!3m2!1sid!2sid!4v1782059073564!5m2!1sid!2sid" width="100%" height="200" style="border:0; border-radius:10px; margin-top:10px;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
        <div class="kontak-card" style="margin-top:30px; text-align:left;">
            <h4>💬 Pesan via WhatsApp</h4>
            <p>Langsung chat kami untuk pemesanan atau konsultasi produk</p>
            <div style="display:flex; align-items:center; gap:15px; margin-top:15px;">
                <div style="width:50px; height:50px; background:var(--pink); border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-size:24px;">📱</div>
                <div>
                    <div style="font-size:12px; color:#999;">Nomor WhatsApp</div>
                    <div style="font-size:18px; font-weight:600;">+62 812 3456 7890</div>
                </div>
                <a href="https://wa.me/<?= WA_ADMIN ?>" target="_blank" class="btn-pink" style="margin-left:auto;">Chat Sekarang</a>
            </div>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <h4>CiZycake</h4>
                <p>Roti fresh setiap hari, dibuat dari bahan berkualitas dan rasa terbaik untuk keluarga.</p>
            </div>
            <div>
                <h4>Navigasi</h4>
                <p><a href="index.php">Beranda</a></p>
                <p><a href="#kategori">Kategori</a></p>
                <p><a href="#terlaris">Produk Terlaris</a></p>
                <p><a href="#tentang">Tentang Toko</a></p>
            </div>
            <div>
                <h4>Kontak</h4>
                <p>📍 Mataram</p>
                <p>📞 +62 812 4571 2097</p>
                <p>✉️ cizycake@gmail.com</p>
            </div>
            <div>
                <h4>Ikuti Kami</h4>
                <div class="sosmed">
                    <a href="#">IG</a>
                    <a href="#">FB</a>
                    <a href="#">TT</a>
                </div>
            </div>
        </div>
        <div class="copyright">
            © 2026 CiZycake. All Rights Reserved.
        </div>
    </div>
</footer>

</body>
</html>