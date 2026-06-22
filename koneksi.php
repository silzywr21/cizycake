<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$conn = mysqli_connect("localhost","root","","cizycake");
if (!$conn) { die("Koneksi gagal: " . mysqli_connect_error()); }
define('NAMA_TOKO', 'CiZycake');
define('WA_ADMIN', '6281234567890');
?>