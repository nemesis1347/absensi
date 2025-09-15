<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// ambil departemen_id dan validasi
if (!isset($_GET['departemen_id'])) {
    die("Departemen tidak ditemukan!");
}
$departemen_id = (int) $_GET['departemen_id'];

// ambil bulan filter (format YYYY-MM), default bulan sekarang
$filter_bulan = isset($_GET['bulan']) && preg_match('/^\d{4}-\d{2}$/', $_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$startDate = $filter_bulan . '-01';
$endDate = date('Y-m-t', strtotime($startDate));

// helper: cek apakah kolom ada di tabel
function column_exists($conn, $table, $column) {
    $table = mysqli_real_escape_string($conn, $table);
    $column = mysqli_real_escape_string($conn, $column);
    $q = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $q && mysqli_num_rows($q) > 0;
}

$has_menit = column_exists($conn, 'absensi', 'menit_telat');
$has_file  = column_exists($conn, 'absensi', 'file_surat');

// ambil nama departemen
$depQ = mysqli_query($conn, "SELECT nama_departemen FROM departemen WHERE id = $departemen_id");
if (!$depQ || mysqli_num_rows($depQ) == 0) {
    die("Departemen tidak ditemukan di database.");
}
$depRow = mysqli_fetch_assoc($depQ);
$nama_departemen = htmlspecialchars($depRow['nama_departemen']);

// 1) Summary per karyawan
$sql_summary = "
    SELECT 
      k.id,
      k.nik,
      k.nama,
      COALESCE(SUM(a.status = 'Alpa'),0) AS alpa,
      COALESCE(SUM(a.status = 'Izin Tanpa Bayar'),0) AS izin_tanpa_bayar,
      COALESCE(SUM(a.status = 'Sakit'),0) AS sakit,
      COALESCE(SUM(a.status = 'Izin Dispensasi'),0) AS izin_dispensasi,
      COALESCE(SUM(a.status = 'Cuti'),0) AS cuti,
      COALESCE(SUM(a.status = 'Surat Dokter'),0) AS surat_dokter,
      COALESCE(SUM(a.status = 'Terlambat'),0) AS terlambat,
      COALESCE(SUM(a.status = 'Izin Meninggalkan Tempat Kerja'),0) AS izin_keluar,";
if ($has_menit) {
    $sql_summary .= " COALESCE(SUM(a.menit_telat),0) AS total_menit_telat,";
} else {
    $sql_summary .= " 0 AS total_menit_telat,";
}
$sql_summary .= " COALESCE(COUNT(a.id),0) AS total_absen
    FROM karyawan k
    LEFT JOIN absensi a 
      ON a.karyawan_id = k.id 
      AND a.tanggal BETWEEN '$startDate' AND '$endDate'
    WHERE k.departemen_id = $departemen_id
    GROUP BY k.id
    ORDER BY k.nama ASC
";
$res_summary = mysqli_query($conn, $sql_summary);

// 2) Total pelanggaran / rekap departemen
$sql_dept_totals = "
    SELECT 
      SUM(a.status = 'Alpa') AS alpa,
      SUM(a.status = 'Izin Tanpa Bayar') AS izin_tanpa_bayar,
      SUM(a.status = 'Sakit') AS sakit,
      SUM(a.status = 'Izin Dispensasi') AS izin_dispensasi,
      SUM(a.status = 'Cuti') AS cuti,
      SUM(a.status = 'Surat Dokter') AS surat_dokter,
      SUM(a.status = 'Terlambat') AS terlambat,
      SUM(a.status = 'Izin Meninggalkan Tempat Kerja') AS izin_keluar,";
if ($has_menit) {
    $sql_dept_totals .= " SUM(a.menit_telat) AS total_menit_telat,";
} else {
    $sql_dept_totals .= " 0 AS total_menit_telat,";
}
$sql_dept_totals .= " COUNT(a.id) AS total_absen
    FROM absensi a
    JOIN karyawan k ON a.karyawan_id = k.id
    WHERE k.departemen_id = $departemen_id
      AND a.tanggal BETWEEN '$startDate' AND '$endDate'
";
$res_dept_totals = mysqli_query($conn, $sql_dept_totals);
$dept_totals = $res_dept_totals ? mysqli_fetch_assoc($res_dept_totals) : [];

// 3) List semua entry absensi (dipakai kalau perlu)
$sql_entries = "
    SELECT a.*, k.nik, k.nama 
    FROM absensi a
    JOIN karyawan k ON a.karyawan_id = k.id
    WHERE k.departemen_id = $departemen_id
      AND a.tanggal BETWEEN '$startDate' AND '$endDate'
    ORDER BY a.tanggal ASC, k.nama ASC
";
$res_entries = mysqli_query($conn, $sql_entries);

// helper prev/next month
function prev_month($ym){
    return date('Y-m', strtotime($ym . '-01 -1 month'));
}
function next_month($ym){
    return date('Y-m', strtotime($ym . '-01 +1 month'));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Detail Absensi - <?= htmlspecialchars($nama_departemen) ?></title>
<link rel="stylesheet" href="assets/style.css">
<style>
.row {display:flex;gap:12px;flex-wrap:wrap;margin-bottom:12px;}
.card {background:#3a3a5a;padding:12px;border-radius:8px;min-width:160px;}
.card h4{margin:0 0 6px;color:#ffd700}
.small {font-size:14px;color:#fff}
.filter-inline {display:flex;gap:8px;align-items:center;margin-bottom:12px}
.filter-inline input[type="month"]{width:200px}
.search-box { margin: 10px 0 15px; position: relative; width: 320px; }
.search-box input { width: 100%; padding: 8px 12px 8px 36px; border-radius: 20px; border:1px solid #ccc; outline:none; transition:0.2s; }
.search-box input:focus { border-color:#4CAF50; box-shadow:0 0 5px rgba(76,175,80,0.25); }
.search-box::before { content:"🔍"; position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:14px; opacity:0.6; pointer-events:none; }
.table-list { width:100%; border-collapse:collapse; margin-top:8px; }
.table-list th, .table-list td { padding:8px 10px; border-bottom:1px solid #444; color:#fff; text-align:left; }
.table-list th { background:#2c2c44; color:#ffd700; }
.file-link { display:inline-block; margin-left:6px; font-size:12px; color:#00c3ff; text-decoration:none; }
.calendar { display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; margin-top:12px; }
.day-header { font-weight: bold; text-align: center; background:#2c3e50; color:#fff; padding:5px; }
.day { border:1px solid #ccc; min-height:120px; padding:8px; font-size:13px; position:relative; background:#3a3a5a; color:#fff; overflow:hidden; }
.day-number { font-weight:bold; position:absolute; top:6px; right:8px; font-size:13px; color:#fff; }
.status { display:flex; align-items:center; gap:8px; margin:8px 0; padding:6px; border-radius:6px; font-size:12px; background:rgba(255,255,255,0.03); }
.hadir { border-left:4px solid #2ecc71; }
.sakit { border-left:4px solid #9b59b6; }
.cuti { border-left:4px solid #e67e22; }
.alpa { border-left:4px solid #e74c3c; }
.izin { border-left:4px solid #3498db; }
.suratdokter { border-left:4px solid #8e44ad; }
.terlambat { border-left:4px solid #c0392b; }
.kosong { color:#aaa; background:#2c2c44; }
.status img { width:28px; height:28px; object-fit:cover; border:1px solid #ccc; border-radius:4px; }
.status .doc-icon { display:inline-block; width:28px; height:28px; line-height:28px; text-align:center; border-radius:4px; background:#2c3e50; color:#fff; font-size:14px; }
</style>
</head>
<body>
<div class="container">
    <div class="navbar" style="display:flex; justify-content:space-between; align-items:center;">
        <div>Selamat datang, <?= htmlspecialchars($_SESSION['nama'] ?? '') ?></div>
        <div style="display:flex; gap:10px; align-items:center;">
            <a href="export_departemen_excel.php?departemen_id=<?= $departemen_id ?>&bulan=<?= $filter_bulan ?>" class="btn btn-success">Download Excel</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <h2>Detail Absensi: <?= htmlspecialchars($nama_departemen) ?></h2>

    <!-- Filter bulan + prev/next -->
    <div class="filter-inline">
        <a class="btn" href="detail_departemen.php?departemen_id=<?= $departemen_id ?>&bulan=<?= prev_month($filter_bulan) ?>">&larr; Prev</a>
        <form method="GET" style="display:inline-flex;align-items:center;gap:8px;">
            <input type="hidden" name="departemen_id" value="<?= $departemen_id ?>">
            <input type="month" name="bulan" value="<?= $filter_bulan ?>">
            <button class="btn" type="submit">Tampilkan</button>
        </form>
        <a class="btn" href="detail_departemen.php?departemen_id=<?= $departemen_id ?>&bulan=<?= next_month($filter_bulan) ?>">Next &rarr;</a>

        <div style="margin-left:auto;color:#ffd700;font-weight:bold;">
            Periode: <?= date('F Y', strtotime($startDate)) ?>
        </div>
    </div>

    <!-- Rekap singkat departemen -->
    <div class="row" style="margin-bottom:10px;flex-wrap:wrap;">
        <div class="card"><h4>Total Absen</h4><div class="small"><?= $dept_totals['total_absen'] ?? 0 ?></div></div>
        <div class="card"><h4>Alpa</h4><div class="small"><?= $dept_totals['alpa'] ?? 0 ?></div></div>
        <div class="card"><h4>Sakit</h4><div class="small"><?= $dept_totals['sakit'] ?? 0 ?></div></div>
        <div class="card"><h4>Cuti</h4><div class="small"><?= $dept_totals['cuti'] ?? 0 ?></div></div>
        <div class="card"><h4>Surat Dokter</h4><div class="small"><?= $dept_totals['surat_dokter'] ?? 0 ?></div></div>
        <div class="card"><h4>Terlambat</h4><div class="small"><?= $dept_totals['terlambat'] ?? 0 ?></div></div>
        <div class="card"><h4>Total Menit Telat</h4><div class="small"><?= $dept_totals['total_menit_telat'] ?? 0 ?> menit</div></div>
        <div class="card"><h4>Izin Tanpa Bayar</h4><div class="small"><?= $dept_totals['izin_tanpa_bayar'] ?? 0 ?></div></div>
        <div class="card"><h4>Izin Dispensasi</h4><div class="small"><?= $dept_totals['izin_dispensasi'] ?? 0 ?></div></div>
        <div class="card"><h4>Izin Keluar</h4><div class="small"><?= $dept_totals['izin_keluar'] ?? 0 ?></div></div>
    </div>

    <!-- Search & Table -->
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Cari karyawan (NIK / Nama) — ketik untuk filter...">
    </div>

    <table class="table-list" id="karyawanTable" role="grid" aria-label="Ringkasan karyawan">
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Alpa</th>
                <th>Sakit</th>
                <th>Cuti</th>
                <th>Surat Dokter</th>
                <th>Terlambat</th>
                <th>Total Menit Telat</th>
                <th>Izin Tanpa Bayar</th>
                <th>Izin Dispensasi</th>
                <th>Izin Keluar</th>
                <th>Total</th>
                <th> </th>
                <th>Aksi</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            if ($res_summary && mysqli_num_rows($res_summary) > 0):
                mysqli_data_seek($res_summary, 0);
                while ($r = mysqli_fetch_assoc($res_summary)):
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($r['nik']); ?></td>
                <td><?= htmlspecialchars($r['nama']); ?></td>
                <td><?= (int)$r['alpa']; ?></td>
                <td><?= (int)$r['sakit']; ?></td>
                <td><?= (int)$r['cuti']; ?></td>
                <td><?= (int)$r['surat_dokter']; ?></td>
                <td><?= (int)$r['terlambat']; ?></td>
                <td><?= (int)$r['total_menit_telat']; ?> menit</td>
                <td><?= (int)$r['izin_tanpa_bayar']; ?></td>
                <td><?= (int)$r['izin_dispensasi']; ?></td>
                <td><?= (int)$r['izin_keluar']; ?></td>
                <td><?= (int)$r['total_absen']; ?></td>
                <td>
                    <a class="btn" href="detail_karyawan.php?id=<?= $r['id'] ?>&bulan=<?= $filter_bulan ?>">Detail</a>
                </td>
                <td>
                    <a class="btn btn-warning" href="edit_absensi.php?id=<?= $r['id'] ?>&bulan=<?= $filter_bulan ?>">Edit</a>
                </td>
                <td>
                    <a class="btn btn-danger" 
                    href="hapus_karyawan_absensi.php?id=<?= $r['id'] ?>&departemen_id=<?= $departemen_id ?>&bulan=<?= $filter_bulan ?>" 
                    onclick="return confirm('Yakin ingin menghapus semua absensi karyawan ini di bulan terpilih?')">Delete</a>
                </td>
            </tr>
            <?php
                endwhile;
            else:
            ?>
            <tr><td colspan="14">Belum ada data karyawan / absensi untuk departemen ini di bulan terpilih.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
    // Script untuk filter tabel karyawan (simple & responsive)
    (function(){
        const input = document.getElementById('searchInput');
        const rows = () => document.querySelectorAll('#karyawanTable tbody tr');

        input.addEventListener('input', function(){
            const q = this.value.trim().toLowerCase();
            if (q === '') {
                rows().forEach(r => r.style.display = '');
                return;
            }
            rows().forEach(r => {
                const nik = r.children[1].innerText.toLowerCase();
                const nama = r.children[2].innerText.toLowerCase();
                r.style.display = (nik.includes(q) || nama.includes(q)) ? '' : 'none';
            });
        });
    })();
    </script>

    <!-- Kalender absensi -->
    <h3 style="margin-top:20px;">Kalender Absensi Bulanan</h3>

    <?php
    // Ambil absensi bulan ini (tambahin file_surat bila ada)
    if ($has_file) {
        $abs_q2 = mysqli_query($conn, "
            SELECT a.karyawan_id, a.tanggal, a.status, " . ($has_menit ? "a.menit_telat," : "0 AS menit_telat,") . " a.file_surat, k.nama 
            FROM absensi a 
            JOIN karyawan k ON a.karyawan_id = k.id
            WHERE k.departemen_id = $departemen_id 
            AND a.tanggal BETWEEN '$startDate' AND '$endDate'
        ");
    } else {
        // kalau kolom file_surat nggak ada, ambil tanpa file_surat (beri string kosong)
        $abs_q2 = mysqli_query($conn, "
            SELECT a.karyawan_id, a.tanggal, a.status, " . ($has_menit ? "a.menit_telat," : "0 AS menit_telat,") . " '' AS file_surat, k.nama 
            FROM absensi a 
            JOIN karyawan k ON a.karyawan_id = k.id
            WHERE k.departemen_id = $departemen_id 
            AND a.tanggal BETWEEN '$startDate' AND '$endDate'
        ");
    }

    $abs_data = [];
    if ($abs_q2) {
        while ($rr = mysqli_fetch_assoc($abs_q2)) {
            $abs_data[$rr['tanggal']][] = [
                'nama' => $rr['nama'],
                'status' => $rr['status'],
                'menit_telat' => isset($rr['menit_telat']) ? (int)$rr['menit_telat'] : 0,
                'file_surat' => $rr['file_surat'] ?? ''
            ];
        }
    }

    // setup kalender
    $firstDay = date('N', strtotime($startDate)); // 1 (Senin) - 7 (Minggu)
    $hariDalamBulan = date('t', strtotime($startDate));

    echo "<div class='calendar'>";
    $hari = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
    foreach ($hari as $h) { echo "<div class='day-header'>{$h}</div>"; }

    // kotak kosong sebelum tanggal 1
    for ($i=1; $i<$firstDay; $i++) { echo "<div class='day kosong'></div>"; }

    for ($tgl=1; $tgl<=$hariDalamBulan; $tgl++) {
        $full_date = $filter_bulan . '-' . str_pad($tgl,2,'0',STR_PAD_LEFT);
        echo "<div class='day'>";
        echo "<div class='day-number'>{$tgl}</div>";

        if (isset($abs_data[$full_date])) {
            foreach ($abs_data[$full_date] as $entry) {
                $statusClass = '';
                switch(strtolower($entry['status'])){
                    case 'hadir': $statusClass='hadir'; break;
                    case 'sakit': $statusClass='sakit'; break;
                    case 'cuti': $statusClass='cuti'; break;
                    case 'alpa': $statusClass='alpa'; break;
                    case 'izin tanpa bayar':
                    case 'izin dispensasi':
                    case 'izin meninggalkan tempat kerja': $statusClass='izin'; break;
                    case 'surat dokter': $statusClass='suratdokter'; break;
                    case 'terlambat': $statusClass='terlambat'; break;
                    default: $statusClass=''; break;
                }

                $label = htmlspecialchars($entry['nama']) . ': ' . htmlspecialchars($entry['status']);
                if (strtolower($entry['status']) === 'terlambat' && $entry['menit_telat'] > 0) {
                    $label .= " ({$entry['menit_telat']} mnt)";
                }

                echo "<div class='status {$statusClass}'>";
                echo "<span style='flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>{$label}</span>";

                // tampilkan file surat jika ada (pastikan folder uploads/ ada dan berisi file)
                if (!empty($entry['file_surat'])) {
                    $safeFile = rawurlencode($entry['file_surat']);
                    $filePath = "uploads/" . $safeFile;
                    $ext = strtolower(pathinfo($entry['file_surat'], PATHINFO_EXTENSION));

                    if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                        echo "<a href='{$filePath}' target='_blank' rel='noopener noreferrer'><img src='{$filePath}' alt='surat' /></a>";
                    } else {
                        echo "<a href='{$filePath}' target='_blank' rel='noopener noreferrer' class='doc-icon' title='Buka file'>📄</a>";
                    }
                }

                echo "</div>";
            }
        } else {
            // kalau kosong, boleh tampilkan tanda - atau biarkan kosong
            // echo "<div class='status kosong'>-</div>";
        }

        echo "</div>"; // tutup .day
    }

    // kotak kosong setelah tanggal akhir (agar grid rapi)
    $lastDay = date('N', strtotime($endDate));
    for ($i=$lastDay; $i<7; $i++) { echo "<div class='day kosong'></div>"; }

    echo "</div>"; // tutup .calendar
    ?>

    <!-- Tombol back -->
    <p style="margin-top:15px;">
        <a href="dashboard.php" class="btn">← Kembali ke Dashboard</a>
    </p>

</div>
</body>
</html>
