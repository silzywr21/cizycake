<?php 
include 'koneksi.php';
if(!isset($_SESSION['role']) || $_SESSION['role']!= 'admin'){ 
    header("location:login.php"); 
    exit;
}

// HAPUS PRODUK
if(isset($_GET['hapus_produk'])){
    $id = $_GET['hapus_produk'];
    mysqli_query($conn, "DELETE FROM produk WHERE id=$id");
    header("location:admin.php?tab=produk");
    exit;
}

// UPDATE STATUS PESANAN
if(isset($_POST['update_status'])){
    $id_pesanan = $_POST['id_pesanan'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE pesanan SET status='$status' WHERE id=$id_pesanan");
    header("location:admin.php?tab=pesanan");
    exit;
}

// TAMBAH PRODUK
if(isset($_POST['tambah_produk'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $kategori = $_POST['kategori'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $terlaris = isset($_POST['terlaris'])? 1 : 0;
    
    // Upload foto jadi base64
    $foto = '';
    if($_FILES['foto']['tmp_name']){
        $data = file_get_contents($_FILES['foto']['tmp_name']);
        $type = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = 'data:image/'. $type. ';base64,'. base64_encode($data);
    }
    
    mysqli_query($conn, "INSERT INTO produk (nama_produk, harga, stok, kategori, deskripsi, terlaris, foto) VALUES ('$nama', '$harga', '$stok', '$kategori', '$deskripsi', '$terlaris', '$foto')");
    header("location:admin.php?tab=produk&sukses=tambah");
    exit;
}

// EDIT PRODUK
if(isset($_POST['edit_produk'])){
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $kategori = $_POST['kategori'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $terlaris = isset($_POST['terlaris'])? 1 : 0;
    
    // Cek ada upload foto baru atau ga
    if($_FILES['foto']['tmp_name']){
        $data = file_get_contents($_FILES['foto']['tmp_name']);
        $type = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = 'data:image/'. $type. ';base64,'. base64_encode($data);
        mysqli_query($conn, "UPDATE produk SET nama_produk='$nama', harga='$harga', stok='$stok', kategori='$kategori', deskripsi='$deskripsi', terlaris='$terlaris', foto='$foto' WHERE id=$id");
    } else {
        mysqli_query($conn, "UPDATE produk SET nama_produk='$nama', harga='$harga', stok='$stok', kategori='$kategori', deskripsi='$deskripsi', terlaris='$terlaris' WHERE id=$id");
    }
    header("location:admin.php?tab=produk&sukses=edit");
    exit;
}

$tab = $_GET['tab']?? 'pesanan';
$produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
$pesanan = mysqli_query($conn, "SELECT p.*, u.nama, u.email FROM pesanan p JOIN users u ON p.id_user=u.id ORDER BY p.tanggal DESC");

// Data untuk edit
$edit_data = null;
if(isset($_GET['edit'])){
    $id_edit = $_GET['edit'];
    $q = mysqli_query($conn, "SELECT * FROM produk WHERE id=$id_edit");
    $edit_data = mysqli_fetch_assoc($q);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin - <?= NAMA_TOKO?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --pink:#E91E63; --pink-light:#FCE4EC; --pink-dark:#C2185B; --white:#FFFFFF; --gray:#555; --black:#212121; --green:#4CAF50; --red:#F44336; --orange:#FF9800; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:#f5f5f5; color:var(--black); }
.container { max-width:1400px; margin:auto; padding:20px; }
.navbar { background:var(--white); padding:20px; box-shadow:0 2px 10px rgba(0,0,0,0.05); margin-bottom:30px; border-radius:10px; }
.navbar.container { display:flex; justify-content:space-between; align-items:center; padding:0; }
.logo { font-size:24px; font-weight:800; color:var(--pink); text-decoration:none; }
.btn { background:var(--pink); color:var(--white); border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; transition:0.3s; font-size:14px; }
.btn:hover { background:var(--pink-dark); }
.btn-green { background:var(--green); }
.btn-green:hover { background:#388E3C; }
.btn-red { background:var(--red); }
.btn-red:hover { background:#D32F2F; }
.btn-orange { background:var(--orange); }
.tabs { display:flex; gap:10px; margin-bottom:30px; }
.tab { padding:12px 25px; background:var(--white); border-radius:8px; text-decoration:none; color:var(--black); font-weight:600; box-shadow:0 2px 5px rgba(0,0,0,0.05); }
.tab.active { background:var(--pink); color:var(--white); }
.card { background:var(--white); padding:25px; border-radius:15px; box-shadow:0 5px 15px rgba(0,0,0,0.05); margin-bottom:25px; }
.card h3 { color:var(--pink-dark); margin-bottom:20px; }
table { width:100%; border-collapse:collapse; font-size:14px; }
th, td { padding:12px 8px; text-align:left; border-bottom:1px solid #eee; }
th { background:var(--pink-light); color:var(--pink-dark); font-weight:600; }
tr:hover { background:#fafafa; }
.form-group { margin-bottom:15px; }
.form-group label { display:block; margin-bottom:5px; font-weight:600; font-size:14px; }
.form-group input,.form-group select,.form-group textarea { width:100%; padding:10px; border:2px solid #eee; border-radius:8px; font-size:14px; }
.form-group input:focus,.form-group select:focus,.form-group textarea:focus { border-color:var(--pink); outline:none; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:15px; }
.badge { padding:4px 10px; border-radius:12px; font-size:12px; font-weight:600; }
.badge.pending { background:#FFF3E0; color:#E65100; }
.badge.proses { background:#E3F2FD; color:#0D47A1; }
.badge.selesai { background:#E8F5E9; color:#1B5E20; }
.badge.batal { background:#FFEBEE; color:#B71C1C; }
.img-preview { width:80px; height:80px; object-fit:cover; border-radius:8px; background:var(--pink-light); }
.img-preview-large { width:150px; height:150px; object-fit:cover; border-radius:10px; background:var(--pink-light); margin-top:10px; }
.alert { padding:15px; border-radius:8px; margin-bottom:20px; font-weight:600; }
.alert.sukses { background:#E8F5E9; color:#1B5E20; }
.modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; }
.modal-content { background:var(--white); max-width:600px; margin:50px auto; padding:30px; border-radius:15px; max-height:90vh; overflow-y:auto; }
.close { float:right; font-size:28px; cursor:pointer; color:#999; }
@media (max-width:768px){ 
   .form-grid{ grid-template-columns:1fr; }
    table{ font-size:12px; }
   .tabs{ flex-wrap:wrap; }
}
</style>
</head>
<body>

<div class="container">
    <div class="navbar">
        <div class="container">
            <a href="index.php" class="logo">CiZycake Admin</a>
            <div>
                <span style="margin-right:15px;">Hi, <?= $_SESSION['nama']?></span>
                <a href="logout.php" class="btn btn-red">Logout</a>
            </div>
        </div>
    </div>

    <?php if(isset($_GET['sukses'])):?>
    <div class="alert sukses">
        <?= $_GET['sukses']=='tambah'? 'Produk berhasil ditambahkan!' : 'Produk berhasil diupdate!'?>
    </div>
    <?php endif;?>

    <div class="tabs">
        <a href="admin.php?tab=pesanan" class="tab <?= $tab=='pesanan'? 'active' : ''?>">📦 Kelola Pesanan</a>
        <a href="admin.php?tab=produk" class="tab <?= $tab=='produk'? 'active' : ''?>">🍰 Kelola Produk</a>
    </div>

    <?php if($tab=='pesanan'):?>
    <div class="card">
        <h3>Daftar Pesanan Masuk</h3>
        <div style="overflow-x:auto;">
        <table>
            <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            <?php while($ps = mysqli_fetch_assoc($pesanan)):?>
            <tr>
                <td>#<?= $ps['id']?></td>
                <td><?= date('d/m/Y H:i', strtotime($ps['tanggal']))?></td>
                <td><?= $ps['nama']?><br><small><?= $ps['email']?></small></td>
                <td><b>Rp <?= number_format($ps['total'])?></b></td>
                <td><span class="badge <?= $ps['status']?>"><?= ucfirst($ps['status'])?></span></td>
                <td>
                    <form method="POST" style="display:flex; gap:5px;">
                        <input type="hidden" name="id_pesanan" value="<?= $ps['id']?>">
                        <select name="status" style="padding:5px; border-radius:5px; font-size:12px;">
                            <option value="pending" <?= $ps['status']=='pending'?'selected':''?>>Pending</option>
                            <option value="proses" <?= $ps['status']=='proses'?'selected':''?>>Proses</option>
                            <option value="selesai" <?= $ps['status']=='selesai'?'selected':''?>>Selesai</option>
                            <option value="batal" <?= $ps['status']=='batal'?'selected':''?>>Batal</option>
                        </select>
                        <button type="submit" name="update_status" class="btn" style="padding:5px 10px; font-size:12px;">Update</button>
                    </form>
                </td>
            </tr>
            <?php endwhile;?>
        </table>
        </div>
    </div>

    <?php else:?>
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3>Daftar Produk</h3>
            <button onclick="document.getElementById('modalTambah').style.display='block'" class="btn btn-green">+ Tambah Produk</button>
        </div>
        <div style="overflow-x:auto;">
        <table>
            <tr>
                <th>Foto</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Terlaris</th>
                <th>Aksi</th>
            </tr>
            <?php while($p = mysqli_fetch_assoc($produk)):?>
            <tr>
                <td><img src="<?= $p['foto']?: 'https://via.placeholder.com/80x80/FCE4EC/E91E63?text=No+Img'?>" class="img-preview"></td>
                <td><?= $p['nama_produk']?></td>
                <td><?= $p['kategori']?></td>
                <td>Rp <?= number_format($p['harga'])?></td>
                <td><?= $p['stok']?></td>
                <td><?= $p['terlaris']? '⭐ Ya' : 'Tidak'?></td>
                <td>
                    <a href="admin.php?tab=produk&edit=<?= $p['id']?>" class="btn btn-orange" style="padding:5px 10px; font-size:12px; margin-right:5px;">Edit</a>
                    <a href="admin.php?hapus_produk=<?= $p['id']?>" onclick="return confirm('Yakin hapus produk ini?')" class="btn btn-red" style="padding:5px 10px; font-size:12px;">Hapus</a>
                </td>
            </tr>
            <?php endwhile;?>
        </table>
        </div>
    </div>
    <?php endif;?>
</div>

<!-- MODAL TAMBAH PRODUK -->
<div id="modalTambah" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('modalTambah').style.display='none'">&times;</span>
        <h3 style="color:var(--pink); margin-bottom:20px;">Tambah Produk Baru</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_produk" required>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Roti">Roti</option>
                        <option value="Kue">Kue</option>
                        <option value="Cupcake">Cupcake</option>
                        <option value="Donat">Donat</option>
                        <option value="Cookies">Cookies</option>
                        <option value="Brownies">Brownies</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="harga" required>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" required>
                </div>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Upload Foto Produk</label>
                <input type="file" name="foto" accept="image/*" required onchange="previewImage(this, 'previewTambah')">
                <img id="previewTambah" class="img-preview-large" style="display:none;">
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="terlaris"> Tandai sebagai Produk Terlaris</label>
            </div>
            <button type="submit" name="tambah_produk" class="btn btn-green" style="width:100%;">Simpan Produk</button>
        </form>
    </div>
</div>

<!-- MODAL EDIT PRODUK -->
<?php if($edit_data):?>
<div id="modalEdit" class="modal" style="display:block;">
    <div class="modal-content">
        <span class="close" onclick="window.location='admin.php?tab=produk'">&times;</span>
        <h3 style="color:var(--pink); margin-bottom:20px;">Edit Produk</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $edit_data['id']?>">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_produk" value="<?= $edit_data['nama_produk']?>" required>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" required>
                        <option value="Roti" <?= $edit_data['kategori']=='Roti'?'selected':''?>>Roti</option>
                        <option value="Kue" <?= $edit_data['kategori']=='Kue'?'selected':''?>>Kue</option>
                        <option value="Cupcake" <?= $edit_data['kategori']=='Cupcake'?'selected':''?>>Cupcake</option>
                        <option value="Donat" <?= $edit_data['kategori']=='Donat'?'selected':''?>>Donat</option>
                        <option value="Cookies" <?= $edit_data['kategori']=='Cookies'?'selected':''?>>Cookies</option>
                        <option value="Brownies" <?= $edit_data['kategori']=='Brownies'?'selected':''?>>Brownies</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="harga" value="<?= $edit_data['harga']?>" required>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" value="<?= $edit_data['stok']?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="3"><?= $edit_data['deskripsi']?></textarea>
            </div>
            <div class="form-group">
                <label>Ganti Foto Produk <small>(Kosongkan jika tidak ganti)</small></label>
                <input type="file" name="foto" accept="image/*" onchange="previewImage(this, 'previewEdit')">
                <img src="<?= $edit_data['foto']?>" id="previewEdit" class="img-preview-large" style="<?= $edit_data['foto']? '' : 'display:none;'?>">
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="terlaris" <?= $edit_data['terlaris']?'checked':''?>> Tandai sebagai Produk Terlaris</label>
            </div>
            <button type="submit" name="edit_produk" class="btn btn-orange" style="width:100%;">Update Produk</button>
        </form>
    </div>
</div>
<?php endif;?>

<script>
function previewImage(input, id) {
    const preview = document.getElementById(id);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
// Close modal kalo klik di luar
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
}
</script>

</body>
</html>