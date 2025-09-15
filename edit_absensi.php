<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id']) || !isset($_GET['bulan'])) {
    die("Parameter tidak lengkap!");
}

$karyawan_id = (int) $_GET['id'];
$bulan       = preg_match('/^\d{4}-\d{2}$/', $_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

$startDate = $bulan . "-01";
$endDate   = date("Y-m-t", strtotime($startDate));

// ambil data karyawan
$qKaryawan = mysqli_query($conn, "SELECT * FROM karyawan WHERE id=$karyawan_id");
$karyawan  = mysqli_fetch_assoc($qKaryawan);

// ambil absensi karyawan bulan ini
$qAbs = mysqli_query($conn, "
    SELECT * FROM absensi 
    WHERE karyawan_id=$karyawan_id 
      AND tanggal BETWEEN '$startDate' AND '$endDate'
    ORDER BY tanggal ASC
");

// jika update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $absen_id = (int) $_POST['absen_id'];
    $status   = mysqli_real_escape_string($conn, $_POST['status']);
    $menit    = (int) ($_POST['menit_telat'] ?? 0);

    $sql = "UPDATE absensi 
            SET status='$status', menit_telat=$menit 
            WHERE id=$absen_id AND karyawan_id=$karyawan_id";
    if (mysqli_query($conn, $sql)) {
        header("Location: edit_absensi.php?id=$karyawan_id&bulan=$bulan&msg=updated");
        exit;
    } else {
        echo "Gagal update: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Edit Absensi - <?= htmlspecialchars($karyawan['nama']) ?></title>
<link rel="stylesheet" href="assets/style.css">
<style>
body {
    background:#1e1e2f;
    font-family: Arial, sans-serif;
}
.card {
    background:#2c2c44;
    padding:18px;
    border-radius:8px;
    margin:20px auto;
    max-width: 900px;
    box-shadow:0 2px 5px rgba(0,0,0,0.4);
}
.card h2 {
    margin:0 0 12px;
    color:#ffd700;
}
.table-list {
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}
.table-list th, .table-list td {
    padding:8px 10px;
    border-bottom:1px solid #444;
    color:#fff;
    text-align:center;
    font-size:14px;
}
.table-list th {
    background:#3a3a5a;
    color:#ffd700;
}
.table-list tr:hover {
    background:#3a3a5a;
}
select, input[type="number"] {
    padding:5px 6px;
    border-radius:5px;
    border:1px solid #555;
    background:#1e1e2f;
    color:#fff;
    font-size:13px;
}
button {
    background:#4CAF50;
    color:#fff;
    padding:5px 10px;
    border:none;
    border-radius:5px;
    cursor:pointer;
    font-size:13px;
}
button:hover {
    background:#45a049;
}
.btn-back {
    display:inline-block;
    margin-top:14px;
    background:#3498db;
    color:#fff;
    padding:8px 14px;
    border-radius:6px;
    text-decoration:none;
}
.btn-back:hover {
    background:#2980b9;
}
</style>
</head>
<body>
<div class="card">
    <h2>Edit Absensi: <?= htmlspecialchars($karyawan['nama']) ?> <small>(<?= $bulan ?>)</small></h2>

    <table class="table-list">
        <tr>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Menit Telat</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($qAbs)): ?>
        <tr>
            <form method="POST">
                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                <td>
                    <select name="status">
                        <option <?= $row['status']=='Hadir'?'selected':'' ?>>Hadir</option>
                        <option <?= $row['status']=='Sakit'?'selected':'' ?>>Sakit</option>
                        <option <?= $row['status']=='Cuti'?'selected':'' ?>>Cuti</option>
                        <option <?= $row['status']=='Alpa'?'selected':'' ?>>Alpa</option>
                        <option <?= $row['status']=='Terlambat'?'selected':'' ?>>Terlambat</option>
                        <option <?= $row['status']=='Izin Tanpa Bayar'?'selected':'' ?>>Izin Tanpa Bayar</option>
                        <option <?= $row['status']=='Izin Dispensasi'?'selected':'' ?>>Izin Dispensasi</option>
                        <option <?= $row['status']=='Izin Meninggalkan Tempat Kerja'?'selected':'' ?>>Izin Meninggalkan Tempat Kerja</option>
                        <option <?= $row['status']=='Surat Dokter'?'selected':'' ?>>Surat Dokter</option>
                    </select>
                </td>
                <td>
                    <input type="number" name="menit_telat" value="<?= (int)$row['menit_telat'] ?>" min="0" style="width:70px; text-align:center;">
                </td>
                <td>
                    <input type="hidden" name="absen_id" value="<?= $row['id'] ?>">
                    <button type="submit">💾</button>
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>

    <a class="btn-back" href="detail_departemen.php?departemen_id=<?= $karyawan['departemen_id'] ?>&bulan=<?= $bulan ?>">← Kembali</a>
</div>
</body>
</html>
