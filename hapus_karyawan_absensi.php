<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id']) || !isset($_GET['departemen_id']) || !isset($_GET['bulan'])) {
    die("Parameter tidak lengkap!");
}

$karyawan_id   = (int) $_GET['id'];
$departemen_id = (int) $_GET['departemen_id'];
$bulan         = preg_match('/^\d{4}-\d{2}$/', $_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

$startDate = $bulan . "-01";
$endDate   = date("Y-m-t", strtotime($startDate));

// Hapus semua absensi karyawan di bulan tsb
$sql = "DELETE FROM absensi WHERE karyawan_id = $karyawan_id AND tanggal BETWEEN '$startDate' AND '$endDate'";
if (mysqli_query($conn, $sql)) {
    header("Location: detail_departemen.php?departemen_id=$departemen_id&bulan=$bulan&msg=deleted");
    exit;
} else {
    echo "Gagal menghapus data absensi: " . mysqli_error($conn);
}
