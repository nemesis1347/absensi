<?php
// Konfigurasi database
$host     = "localhost";   // biasanya "localhost"
$user     = "root";        // username MySQL kamu
$password = "";            // password MySQL (kosong kalau default XAMPP)
$db       = "db_absensi";  // nama database yang sudah kamu buat

// Koneksi ke MySQL
$conn = mysqli_connect($host, $user, $password, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
