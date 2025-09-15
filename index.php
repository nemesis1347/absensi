<?php
session_start();
include "config/koneksi.php";

if(isset($_POST['login'])){
    $nomor_hp = mysqli_real_escape_string($conn, $_POST['nomor_hp']);
    $password = md5($_POST['password']); // sesuai dengan register

    // Cek admin
    $cek = mysqli_query($conn, "SELECT * FROM admin WHERE nomor_hp='$nomor_hp' AND password='$password'");
    if(mysqli_num_rows($cek) > 0){
        $admin = mysqli_fetch_assoc($cek);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['nama'] = $admin['nama'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Nomor HP atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Admin</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="login-container">
    <h2>Login Admin</h2>

    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <label>Username</label>
        <input type="text" name="nomor_hp" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" name="login">Login</button>
    </form>
</div>
</body>
</html>
