<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Ambil data absensi (join karyawan + departemen)
$query_absen = mysqli_query($conn, "
    SELECT a.*, k.nama AS nama_karyawan, k.departemen AS departemen
    FROM absensi a
    JOIN karyawan k ON a.id_karyawan = k.id
    ORDER BY a.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Absensi</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h2>Riwayat Absensi Semua Karyawan</h2>
        <a href="dashboard.php" class="btn">⬅ Kembali ke Dashboard</a>
        <br><br>

        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Karyawan</th>
                    <th>Departemen</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($query_absen) > 0) : ?>
                    <?php while ($row = mysqli_fetch_assoc($query_absen)) : ?>
                        <tr>
                            <td><?php echo date("d-m-Y", strtotime($row['tanggal'])); ?></td>
                            <td><?php echo $row['nama_karyawan']; ?></td>
                            <td><?php echo $row['departemen']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td><?php echo $row['keterangan']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5">Belum ada data absensi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
