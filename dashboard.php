<?php
session_start();
include "config/koneksi.php";

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Ambil semua departemen
$departemen = mysqli_query($conn, "SELECT * FROM departemen ORDER BY nama_departemen ASC");

// Ambil semua status unik dari tabel absensi
$statusList = mysqli_query($conn, "SELECT DISTINCT status FROM absensi ORDER BY status ASC");
$statuses = [];
while ($row = mysqli_fetch_assoc($statusList)) {
    $statuses[] = $row['status'];
}

// Hitung total pelanggaran absensi per kategori (bulan ini)
$bulan_ini = date("Y-m");
$rekap_pelanggaran = mysqli_query($conn, "
    SELECT status, COUNT(*) as jumlah
    FROM absensi
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulan_ini'
    GROUP BY status
");
$pelanggaran = [];
while ($row = mysqli_fetch_assoc($rekap_pelanggaran)) {
    $pelanggaran[$row['status']] = $row['jumlah'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 15px; }
        table, th, td { border: 1px solid #333; }
        th, td { padding: 8px; text-align: center; }
        .btn { padding: 5px 10px; background: #3498db; color: #fff; border-radius: 5px; text-decoration: none; }
        .btn:hover { background: #2980b9; }
        .logout-btn { background: #e74c3c; padding: 5px 10px; color: #fff; text-decoration: none; border-radius: 5px; }
        .logout-btn:hover { background: #c0392b; }
        .navbar { display:flex; justify-content:space-between; align-items:center; padding:10px; background:#2c3e50; color:white; }
    </style>
</head>
<body>
    <div class="navbar">
        <div>Selamat datang, <?= $_SESSION['nama']; ?></div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
    
    <div class="container">
        <h2>Dashboard Admin</h2>
        <p>Halo, <?= $_SESSION['nama']; ?></p>

        <div style="margin-bottom: 20px;">
            <a href="input_karyawan.php" class="btn">+ Input Karyawan</a>
            <a href="input_absen.php" class="btn">+ Input Absensi</a>
        </div>

        <!-- Chart -->
        <h3>Rekap Pelanggaran Absensi Bulan <?= date("F Y"); ?></h3>
        <canvas id="chartPelanggaran" width="400" height="200"></canvas>

        <!-- Rekap detail per departemen -->
        <h3>Absensi Per Departemen (<?= date("F Y"); ?>)</h3>
        <table>
            <thead>
                <tr>
                    <th>Departemen</th>
                    <th>Total Karyawan</th>
                    <th>Total Absensi</th>
                    <?php foreach ($statuses as $s) { ?>
                        <th><?= $s; ?></th>
                    <?php } ?>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($d = mysqli_fetch_assoc($departemen)) { 
                    $id = $d['id'];

                    // Total karyawan
                    $q1 = mysqli_query($conn, "SELECT COUNT(*) as jml FROM karyawan WHERE departemen_id='$id'");
                    $total_karyawan = mysqli_fetch_assoc($q1)['jml'];

                    // Total absensi bulan ini
                    $q2 = mysqli_query($conn, "
                        SELECT status, COUNT(*) as jml
                        FROM absensi a
                        JOIN karyawan k ON a.karyawan_id = k.id
                        WHERE k.departemen_id='$id'
                        AND DATE_FORMAT(a.tanggal, '%Y-%m') = '$bulan_ini'
                        GROUP BY status
                    ");
                    $rekap = [];
                    while ($row = mysqli_fetch_assoc($q2)) {
                        $rekap[$row['status']] = $row['jml'];
                    }

                    // Hitung total absensi (sum semua status)
                    $total_absen = array_sum($rekap);
                ?>
                <tr>
                    <td><?= $d['nama_departemen']; ?></td>
                    <td><?= $total_karyawan; ?></td>
                    <td><?= $total_absen; ?></td>
                    <?php foreach ($statuses as $s) { ?>
                        <td><?= $rekap[$s] ?? 0; ?></td>
                    <?php } ?>
                    <td><a href="detail_departemen.php?departemen_id=<?= $d['id']; ?>" class="btn">Lihat</a></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        const dataPelanggaran = {
            labels: <?= json_encode(array_keys($pelanggaran)); ?>,
            datasets: [{
                label: 'Jumlah Pelanggaran',
                data: <?= json_encode(array_values($pelanggaran)); ?>,
                backgroundColor: [
                    '#e74c3c', '#f39c12', '#3498db', '#9b59b6',
                    '#1abc9c', '#2ecc71', '#e67e22', '#34495e'
                ]
            }]
        };

        const configPelanggaran = {
            type: 'bar',
            data: dataPelanggaran,
            options: {
                responsive: true,
                plugins: { legend: { display: false } }
            }
        };

        new Chart(document.getElementById('chartPelanggaran'), configPelanggaran);
    </script>
</body>
</html>
