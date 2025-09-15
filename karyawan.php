<?php
session_start();
include "config/koneksi.php";

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Tambah karyawan baru
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $departemen_id = $_POST['departemen_id'];

    $query = "INSERT INTO karyawan (nama, departemen_id) VALUES ('$nama', '$departemen_id')";
    mysqli_query($conn, $query);

    header("Location: karyawan.php");
    exit;
}

// Hapus karyawan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM karyawan WHERE id='$id'");
    header("Location: karyawan.php");
    exit;
}

// Ambil data departemen
$departemen = mysqli_query($conn, "SELECT * FROM departemen ORDER BY nama_departemen ASC");

// Ambil data karyawan
$karyawan = mysqli_query($conn, "
    SELECT k.id, k.nama, d.nama_departemen 
    FROM karyawan k
    JOIN departemen d ON k.departemen_id = d.id
    ORDER BY d.nama_departemen, k.nama
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Karyawan</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h2>Tambah Karyawan</h2>
        <form method="POST">
            <div class="form-group">
                <label>Nama Karyawan</label>
                <input type="text" name="nama" required>
            </div>
            <div class="form-group">
                <label>Departemen</label>
                <select name="departemen_id" required>
                    <option value="">-- Pilih Departemen --</option>
                    <?php while ($row = mysqli_fetch_assoc($departemen)) { ?>
                        <option value="<?= $row['id']; ?>"><?= $row['nama_departemen']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" name="
