<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

// Ambil daftar departemen
$departemen_list = mysqli_query($conn, "SELECT * FROM departemen ORDER BY nama_departemen");

// Proses input absensi
if (isset($_POST['submit'])) {
    $departemen_id = intval($_POST['departemen']);
    $id_karyawan   = intval($_POST['karyawan_id']);
    $tanggal       = $_POST['tanggal'];
    $status        = $_POST['status'];
    $keterangan    = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $menit_telat   = isset($_POST['menit_telat']) && $_POST['menit_telat'] !== "" ? intval($_POST['menit_telat']) : null;

    // handle upload file
    $file_bukti = null;
    if (!empty($_FILES['file_bukti']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES['file_bukti']['name']);
        $targetFile = $targetDir . $fileName;
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];

        if (in_array($_FILES['file_bukti']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['file_bukti']['tmp_name'], $targetFile)) {
                $file_bukti = $fileName;
            }
        }
    }

    // Cek absensi duplikat
    $cek = mysqli_query($conn, "SELECT * FROM absensi WHERE karyawan_id='$id_karyawan' AND tanggal='$tanggal'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Absensi untuk karyawan ini pada tanggal tersebut sudah ada!";
    } else {
        $result = mysqli_query($conn, "
            INSERT INTO absensi (karyawan_id, tanggal, status, keterangan, menit_telat, file_surat)
            VALUES ('$id_karyawan', '$tanggal', '$status', '$keterangan', " . ($menit_telat !== null ? $menit_telat : "NULL") . ", " . ($file_bukti ? "'$file_bukti'" : "NULL") . ")
        ");
        if (!$result) {
            $error = "Error input absensi: " . mysqli_error($conn);
        } else {
            $success = "Absensi berhasil ditambahkan!";
        }
    }
}

// Ambil semua karyawan (untuk JS)
$karyawan_all = mysqli_query($conn, "SELECT k.id, k.nama, k.nik, k.departemen_id FROM karyawan k ORDER BY k.nama");
$karyawan_js = [];
while ($row = mysqli_fetch_assoc($karyawan_all)) {
    $karyawan_js[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Input Absensi</title>
<link rel="stylesheet" href="assets/style.css">
<style>
.hidden { display:none; }
</style>
</head>
<body>
<div class="container">
    <h2>Input Absensi Karyawan</h2>
    <a href="dashboard.php" class="btn">⬅ Kembali ke Dashboard</a>
    <br><br>

    <?php if($error != ""): ?>
        <div class="error"><?= $error; ?></div>
    <?php endif; ?>
    <?php if($success != ""): ?>
        <div class="success"><?= $success; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Departemen</label>
            <select name="departemen" id="departemen" required>
                <option value="">-- Pilih Departemen --</option>
                <?php while($row = mysqli_fetch_assoc($departemen_list)): ?>
                    <option value="<?= $row['id']; ?>"><?= $row['nama_departemen']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Karyawan (NIK)</label>
            <select name="karyawan_id" id="karyawan_id" required>
                <option value="">-- Pilih Karyawan --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" required style="width:130px;">
        </div>


        <div class="form-group">
            <label>Status Absensi</label>
            <select name="status" id="status" required>
                <option value="">-- Pilih Status --</option>
                <option value="Alpa">Alpa</option>
                <option value="Izin Tanpa Bayar">Izin Tanpa Bayar</option>
                <option value="Sakit">Sakit</option>
                <option value="Izin Dispensasi">Izin Dispensasi</option>
                <option value="Cuti">Cuti</option>
                <option value="Surat Dokter">Surat Dokter</option>
                <option value="Terlambat">Terlambat</option>
                <option value="Izin Meninggalkan Tempat Kerja">Izin Meninggalkan Tempat Kerja</option>
            </select>
        </div>

        <!-- Upload bukti -->
        <div class="form-group">
            <label for="file_bukti">Upload Surat / Bukti (PDF/JPG/PNG)</label>
            <input type="file" name="file_bukti" accept=".pdf,.jpg,.jpeg,.png">
        </div>

        <!-- Input menit telat, hidden default -->
        <div class="form-group hidden" id="telat-group">
            <label>Jumlah Menit Terlambat</label>
            <input type="number" name="menit_telat" min="1" placeholder="contoh: 15">
        </div>

        <div class="form-group">
            <label>Keterangan (Opsional)</label>
            <textarea name="keterangan" rows="3"></textarea>
        </div>

        <button type="submit" name="submit">Simpan Absensi</button>
    </form>
</div>

<script>
const karyawanData = <?= json_encode($karyawan_js); ?>;
const departemenSelect = document.getElementById('departemen');
const karyawanSelect = document.getElementById('karyawan_id');
const statusSelect = document.getElementById('status');
const telatGroup = document.getElementById('telat-group');

departemenSelect.addEventListener('change', function() {
    const depId = this.value;
    karyawanSelect.innerHTML = '<option value="">-- Pilih Karyawan --</option>';
    karyawanData.forEach(k => {
        if(k.departemen_id == depId) {
            const option = document.createElement('option');
            option.value = k.id;
            option.text = k.nama + ' (' + k.nik + ')';
            karyawanSelect.appendChild(option);
        }
    });
});

// Tampilkan input telat kalau status "Terlambat"
statusSelect.addEventListener('change', function() {
    if (this.value === "Terlambat") {
        telatGroup.classList.remove('hidden');
    } else {
        telatGroup.classList.add('hidden');
    }
});
</script>
</body>
</html>
