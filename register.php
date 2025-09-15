<?php
session_start();
include "config/koneksi.php";

if(isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nomor_hp = mysqli_real_escape_string($conn, $_POST['nomor_hp']);
    $password = md5($_POST['password']); // bisa ganti dengan password_hash() untuk lebih aman

    // Cek apakah nomor HP sudah terdaftar
    $cek = mysqli_query($conn, "SELECT * FROM admin WHERE nomor_hp='$nomor_hp'");
    if(mysqli_num_rows($cek) > 0){
        $error = "Nomor HP sudah terdaftar!";
    } else {
        mysqli_query($conn, "INSERT INTO admin (nama, nomor_hp, password) VALUES ('$nama','$nomor_hp','$password')");
        $success = "Admin berhasil didaftarkan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Register Admin</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="register-container">
    <h2>Register Admin</h2>

    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>

    <form method="POST">
        <label>Nama</label>
        <input type="text" name="nama" required>

        <label>Nomor HP</label>
        <input type="text" name="nomor_hp" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" name="register">Register</button>
    </form>

    <p>Sudah punya akun? <a href="index.php">Login di sini</a></p>
</div>
</body>
</html>
