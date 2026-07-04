<?php
include 'koneksi.php';
if(!isset($_SESSION['id'])){ header("Location: login.php"); exit; }
$id_user = $_SESSION['id'];
$id_produk = $_GET['id'];
$catatan = $_GET['catatan'] ?? '';

$cek = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_user=$id_user AND id_produk=$id_produk");
if(mysqli_num_rows($cek) > 0){
    mysqli_query($conn, "UPDATE keranjang SET jumlah=jumlah+1 WHERE id_user=$id_user AND id_produk=$id_produk");
} else {
    mysqli_query($conn, "INSERT INTO keranjang (id_user,id_produk,catatan) VALUES ($id_user,$id_produk,'$catatan')");
}
header("Location: keranjang.php");
?>