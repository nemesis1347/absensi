<?php
include "config/koneksi.php";

if (isset($_POST['simpan'])) {
    $karyawan_id = $_POST['karyawan_id'];
    $tanggal     = $_POST['tanggal'];
    $status      = $_POST['status'];
    $menit_telat = $_POST['menit_telat'] ?? 0;

    $file_bukti = null;

    // cek apakah ada file
    if (!empty($_FILES['file_bukti']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $ext = pathinfo($_FILES['file_bukti']['name'], PATHINFO_EXTENSION);
        $file_bukti = time() . "_" . $karyawan_id . "." . $ext;
        $target_file = $target_dir . $file_bukti;

        // validasi tipe file
        $allowed = ['pdf','jpg','jpeg','png'];
        if (in_array(strtolower($ext), $allowed)) {
            move_uploaded_file($_FILES['file_bukti']['tmp_name'], $target_file);
        } else {
            die("Format file tidak diizinkan!");
        }
    }

    // simpan ke DB
    $sql = "INSERT INTO absensi (karyawan_id, tanggal, status, menit_telat, file_bukti) 
            VALUES ('$karyawan_id', '$tanggal', '$status', '$menit_telat', ".($file_bukti ? "'$file_bukti'" : "NULL").")";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));

    header("Location: dashboard.php");
}
?>
