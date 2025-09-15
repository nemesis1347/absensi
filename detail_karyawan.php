<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Dapatkan karyawan id dari GET (prioritas id), atau coba cari via nik
$karyawan_id = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $karyawan_id = (int) $_GET['id'];
} elseif (isset($_GET['nik']) && $_GET['nik'] !== '') {
    $nik = mysqli_real_escape_string($conn, $_GET['nik']);
    $q = mysqli_query($conn, "SELECT id FROM karyawan WHERE nik = '$nik' LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $karyawan_id = (int) mysqli_fetch_assoc($q)['id'];
    }
}

if (!$karyawan_id) {
    echo "<p style='color:#ffd700;background:#2c2c44;padding:12px;border-radius:6px;'>Karyawan tidak ditemukan. Pastikan parameter <code>id</code> atau <code>nik</code> diberikan pada URL.</p>";
    exit;
}

// Ambil data karyawan (plus nama departemen)
$kq = mysqli_query($conn, "
    SELECT k.*, d.nama_departemen 
    FROM karyawan k
    LEFT JOIN departemen d ON k.departemen_id = d.id
    WHERE k.id = $karyawan_id
    LIMIT 1
");
if (!$kq || mysqli_num_rows($kq) == 0) {
    echo "<p style='color:#ffd700;background:#2c2c44;padding:12px;border-radius:6px;'>Karyawan tidak ditemukan di database.</p>";
    exit;
}
$karyawan = mysqli_fetch_assoc($kq);

// Filter bulan (format YYYY-MM)
$filter_bulan = (isset($_GET['bulan']) && preg_match('/^\d{4}-\d{2}$/', $_GET['bulan'])) ? $_GET['bulan'] : date('Y-m');
$startDate = $filter_bulan . '-01';
$endDate = date('Y-m-t', strtotime($startDate));

// Ambil absensi karyawan pada bulan terpilih
$absensi_q = mysqli_query($conn, "
    SELECT *
    FROM absensi
    WHERE karyawan_id = $karyawan_id
      AND tanggal BETWEEN '$startDate' AND '$endDate'
    ORDER BY tanggal ASC
");
$absensi = [];
if ($absensi_q) {
    while ($row = mysqli_fetch_assoc($absensi_q)) {
        $absensi[$row['tanggal']] = $row;
    }
}

// Ringkasan counts per status + total menit terlambat
$summary_q = mysqli_query($conn, "
    SELECT 
      SUM(status = 'Hadir') AS hadir,
      SUM(status = 'Alpa') AS alpa,
      SUM(status = 'Sakit') AS sakit,
      SUM(status = 'Cuti') AS cuti,
      SUM(status = 'Surat Dokter') AS surat_dokter,
      SUM(status = 'Terlambat') AS terlambat,
      SUM(status = 'Izin Tanpa Bayar') AS izin_tanpa_bayar,
      SUM(status = 'Izin Dispensasi') AS izin_dispensasi,
      SUM(status = 'Izin Meninggalkan Tempat Kerja') AS izin_keluar,
      SUM(status = 'Dinas Luar') AS dinas_luar,
      SUM(CASE WHEN status='Terlambat' THEN menit_telat ELSE 0 END) AS total_menit_terlambat,
      COUNT(*) AS total
    FROM absensi
    WHERE karyawan_id = $karyawan_id
      AND tanggal BETWEEN '$startDate' AND '$endDate'
");
$summary = $summary_q ? mysqli_fetch_assoc($summary_q) : null;
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Detail Karyawan - <?= htmlspecialchars($karyawan['nama']) ?></title>
<link rel="stylesheet" href="assets/style.css">
<style>
.container { max-width: 1100px; }
.info-row { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
.card { background:#3a3a5a; padding:10px 12px; border-radius:8px; color:#fff; min-width:120px; text-align:center; }
.card h4 { margin:0 0 6px; color:#ffd700; font-size:14px; }
.small { font-size:14px; color:#fff; }
.filter-inline { display:flex; gap:8px; align-items:center; margin-bottom:14px; }
.filter-inline input[type="month"]{ width:200px; }
.calendar { width:100%; border-collapse:collapse; }
.calendar th, .calendar td { border:1px solid #444; padding:6px; text-align:center; vertical-align:top; min-height:80px; }
.calendar th { background:#2c2c44; color:#ffd700; }
.absensi-status { font-size:12px; display:block; margin-top:4px; }
.absensi-hadir { color:lightgreen; }
.absensi-alpa { color:red; }
.absensi-sakit { color:orange; }
.absensi-izin { color:skyblue; }
.absensi-cuti { color:#ffd700; }
.absensi-terlambat { color:#ff69b4; }
.absensi-dinas { color:#00ced1; }
</style>
</head>
<body>
<div class="container">
<div class="navbar" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        Selamat datang, <?= htmlspecialchars($_SESSION['nama'] ?? '') ?>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="export_karyawan_excel.php?karyawan_id=<?= $karyawan_id ?>" class="btn btn-success">Download Excel</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>
    <h2>Detail Absensi: <?= htmlspecialchars($karyawan['nama']) ?> (<?= htmlspecialchars($karyawan['nik']) ?>)</h2>
    <p style="color:#bbb">Departemen: <?= htmlspecialchars($karyawan['nama_departemen'] ?? '-') ?></p>

    <div class="filter-inline">
        <a class="btn" href="detail_karyawan.php?id=<?= $karyawan_id ?>&bulan=<?= date('Y-m', strtotime($startDate . ' -1 month')) ?>">&larr; Prev</a>
        <form method="GET" style="display:inline-flex;align-items:center;gap:8px;">
            <input type="hidden" name="id" value="<?= $karyawan_id ?>">
            <input type="month" name="bulan" value="<?= $filter_bulan ?>">
            <button class="btn" type="submit">Tampilkan</button>
        </form>
        <a class="btn" href="detail_karyawan.php?id=<?= $karyawan_id ?>&bulan=<?= date('Y-m', strtotime($startDate . ' +1 month')) ?>">Next &rarr;</a>
        <div style="margin-left:auto;color:#ffd700;font-weight:bold;">
            Periode: <?= date('F Y', strtotime($startDate)) ?>
        </div>
    </div>

    <!-- Ringkasan absensi -->
    <div class="info-row">
        <div class="card"><h4>Total</h4><div class="small"><?= $summary['total'] ?? 0 ?></div></div>
        <div class="card"><h4>Hadir</h4><div class="small"><?= $summary['hadir'] ?? 0 ?></div></div>
        <div class="card"><h4>Alpa</h4><div class="small"><?= $summary['alpa'] ?? 0 ?></div></div>
        <div class="card"><h4>Sakit</h4><div class="small"><?= $summary['sakit'] ?? 0 ?></div></div>
        <div class="card"><h4>Cuti</h4><div class="small"><?= $summary['cuti'] ?? 0 ?></div></div>
        <div class="card"><h4>Surat Dokter</h4><div class="small"><?= $summary['surat_dokter'] ?? 0 ?></div></div>
        <div class="card"><h4>Terlambat</h4><div class="small">
            <?= $summary['terlambat'] ?? 0 ?><br>
            <?= $summary['total_menit_terlambat'] ?? 0 ?> menit
        </div></div>
        <div class="card"><h4>Izin</h4><div class="small"><?= ($summary['izin_tanpa_bayar']??0)+($summary['izin_dispensasi']??0)+($summary['izin_keluar']??0) ?></div></div>
        <div class="card"><h4>Dinas Luar</h4><div class="small"><?= $summary['dinas_luar'] ?? 0 ?></div></div>
    </div>

    <!-- Kalender absensi -->
    <h3>Kalender Absensi <?= date('F Y', strtotime($startDate)) ?></h3>
    <table class="calendar">
        <thead>
            <tr>
                <th>Min</th><th>Sen</th><th>Sel</th><th>Rab</th><th>Kam</th><th>Jum</th><th>Sab</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $dayCount = date('t', strtotime($startDate));
            $startDayOfWeek = date('w', strtotime($startDate)); // 0=Min
            $currentDay = 1;
            $cells = 0;
            echo "<tr>";
            // Kosong sebelum tanggal 1
            for ($i=0;$i<$startDayOfWeek;$i++) {
                echo "<td></td>";
                $cells++;
            }
            while ($currentDay <= $dayCount) {
            // Dalam loop while kalender, pas lagi generate <td>
            $tanggal = date('Y-m-', strtotime($startDate)) . str_pad($currentDay, 2, '0', STR_PAD_LEFT);
            $status = $absensi[$tanggal]['status'] ?? null;
            $menitTelat = $absensi[$tanggal]['menit_telat'] ?? 0;
            $fileSurat = $absensi[$tanggal]['file_surat'] ?? null;

            echo "<td><strong>$currentDay</strong>";
            if ($status) {
                $class = '';
                $label = $status;
                switch (strtolower($status)) {
                    case 'hadir': $class='absensi-hadir'; break;
                    case 'alpa': $class='absensi-alpa'; break;
                    case 'sakit': $class='absensi-sakit'; break;
                    case 'cuti': $class='absensi-cuti'; break;
                    case 'surat dokter': $class='absensi-sakit'; break;
                    case 'terlambat': $class='absensi-terlambat'; break;
                    case 'izin tanpa bayar':
                    case 'izin dispensasi':
                    case 'izin meninggalkan tempat kerja': $class='absensi-izin'; break;
                    case 'dinas luar': $class='absensi-dinas'; break;
                }
                echo "<span class='absensi-status $class'>$label";
                if (strtolower($status) === 'terlambat' && $menitTelat > 0) {
                    echo " ({$menitTelat}m)";
                }
                echo "</span>";

                // 🔹 Tambahin preview file kalau ada
                if ($fileSurat) {
                    $ext = strtolower(pathinfo($fileSurat, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                        echo "<div style='margin-top:4px;'>
                        <a href='uploads/$fileSurat' target='_blank'>
                            <img src='uploads/$fileSurat' style='max-width:60px;max-height:60px;border:1px solid #555;border-radius:4px;'>
                        </a>
                    </div>";
                } elseif ($ext === 'pdf') {
                    echo "<div style='margin-top:4px;'>
                        <a href='uploads/$fileSurat' target='_blank' style='color:#ffd700;font-size:12px;'>📄 Lihat PDF</a>
                    </div>";
        } else {
            echo "<div style='margin-top:4px;'>
                    <a href='uploads/$fileSurat' target='_blank' style='color:#aaa;font-size:12px;'>📎 Lihat File</a>
                  </div>";
        }
    }
}
echo "</td>";

                $currentDay++;
                $cells++;
                if ($cells % 7 == 0) echo "</tr><tr>";
            }
            // Kosong setelah akhir bulan
            while ($cells % 7 != 0) {
                echo "<td></td>";
                $cells++;
            }
            echo "</tr>";
            ?>
        </tbody>
    </table>

    <p style="margin-top:12px;">
        <a href="dashboard.php" class="btn">⬅ Kembali ke Dashboard</a>
        <a href="detail_departemen.php?departemen_id=<?= (int)$karyawan['departemen_id'] ?>" class="btn">⬅ Kembali ke Departemen</a>
    </p>
</div>
</body>
</html>
