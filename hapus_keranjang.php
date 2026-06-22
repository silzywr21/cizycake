<?php
include 'koneksi.php';
if(!isset($_SESSION['id'])){ header("Location: login.php"); exit; }
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM keranjang WHERE id=$id AND id_user=".$_SESSION['id']);
header("Location: keranjang.php");
?>