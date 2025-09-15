<?php
session_start();
include "config/koneksi.php"; // koneksi ke database

// Cek login admin
if(!isset($_SESSION['admin_id'])){
    header("Location: index.php");
    exit;
}

// Ambil semua departemen untuk dropdown
$departemen = mysqli_query($conn, "SELECT * FROM departemen ORDER BY nama_departemen ASC");

// Proses form input karyawan
if(isset($_POST['submit'])){
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $departemen_id = $_POST['departemen_id'];

    // Cek NIK sudah ada atau belum
    $cek = mysqli_query($conn, "SELECT * FROM karyawan WHERE nik='$nik'");
    if(mysqli_num_rows($cek) > 0){
        $error = "NIK sudah terdaftar!";
    } else {
        mysqli_query($conn, "INSERT INTO karyawan (nik, nama, departemen_id) VALUES ('$nik','$nama','$departemen_id')");
        $success = "Karyawan berhasil ditambahkan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Input Karyawan</title>
<link rel="stylesheet" href="assets/style.css">
<style>
    .btn-back {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background: #3498db;
        color: #fff;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
    }
    .btn-back:hover {
        background: #2980b9;
    }
</style>
</head>
<body>
<div class="container">
    <div class="navbar">
        <div>Selamat datang, <?= $_SESSION['nama']; ?></div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <h2>Input Karyawan</h2>

    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>

    <form method="POST">
        <label>NIK</label>
        <input type="number" name="nik" required oninput="this.value=this.value.replace(/[^0-9]/g,'')">

        <label>Nama</label>
        <input type="text" name="nama" required>

        <label>Departemen</label>
        <select name="departemen_id" required>
            <option value="">-- Pilih Departemen --</option>
            <?php while($d = mysqli_fetch_assoc($departemen)) { ?>
                <option value="<?= $d['id']; ?>"><?= $d['nama_departemen']; ?></option>
            <?php } ?>
        </select>

        <button type="submit" name="submit">Tambah Karyawan</button>
    </form>

    <!-- Tombol balik ke dashboard -->
    <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
</div>
</body>
</html>
