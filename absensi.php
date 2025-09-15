<?php
session_start();
include "config/koneksi.php";

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Simpan data absensi
if (isset($_POST['simpan'])) {
    $karyawan_id = $_POST['karyawan_id'];
    $tanggal     = $_POST['tanggal'];
    $jam_masuk   = $_POST['jam_masuk'];
    $jam_keluar  = $_POST['jam_keluar'];
    $status      = $_POST['status'];
    $keterangan  = $_POST['keterangan'];

    $query = "INSERT INTO absensi (karyawan_id, tanggal, jam_masuk, jam_keluar, status, keterangan)
              VALUES ('$karyawan_id', '$tanggal', '$jam_masuk', '$jam_keluar', '$status', '$keterangan')";
    mysqli_query($conn, $query);

    header("Location: absensi.php");
    exit;
}

// Ambil data karyawan
$karyawan = mysqli_query($conn, "SELECT * FROM karyawan ORDER BY nama ASC");

// Ambil data absensi (join ke tabel karyawan + departemen)
$absensi = mysqli_query($conn, "
    SELECT a.*, k.nama AS nama_karyawan, d.nama_departemen 
    FROM absensi a
    JOIN karyawan k ON a.karyawan_id = k.id
    JOIN departemen d ON k.departemen_id = d.id
    ORDER BY a.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Absensi</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h2>Input Absensi</h2>
        <form method="POST">
            <div class="form-group">
                <label>Karyawan</label>
                <select name="karyawan_id" required>
                    <option value="">-- Pilih Karyawan --</option>
                    <?php while ($row = mysqli_fetch_assoc($karyawan)) { ?>
                        <option value="<?= $row['id']; ?>"><?= $row['nama']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tanggal" required>
            </div>
            <div class="form-group">
                <label>Jam Masuk</label>
                <input type="time" name="jam_masuk">
            </div>
            <div class="form-group">
                <label>Jam Keluar</label>
                <input type="time" name="jam_keluar">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="Hadir">Hadir</option>
                    <option value="Alpa">Alpa</option>
                    <option value="Izin Tanpa Bayar">Izin Tanpa Bayar</option>
