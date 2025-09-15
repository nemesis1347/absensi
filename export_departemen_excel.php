<?php
require 'vendor/autoload.php';
include "config/koneksi.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_GET['departemen_id'])) die("Departemen tidak ditemukan!");
$departemen_id = (int) $_GET['departemen_id'];
$bulan = $_GET['bulan'] ?? date('Y-m');
$startDate = $bulan.'-01';
$endDate   = date('Y-m-t', strtotime($startDate));

// ambil nama departemen
$dep = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_departemen FROM departemen WHERE id=$departemen_id"));
$nama_departemen = $dep['nama_departemen'] ?? '';

// total departemen
$sql_dept_totals = "
    SELECT 
      SUM(a.status='Alpa') AS alpa,
      SUM(a.status='Izin Tanpa Bayar') AS izin_tanpa_bayar,
      SUM(a.status='Sakit') AS sakit,
      SUM(a.status='Izin Dispensasi') AS izin_dispensasi,
      SUM(a.status='Cuti') AS cuti,
      SUM(a.status='Surat Dokter') AS surat_dokter,
      SUM(a.status='Terlambat') AS terlambat,
      SUM(a.status='Izin Meninggalkan Tempat Kerja') AS izin_keluar,
      SUM(a.menit_telat) AS total_menit_telat,
      COUNT(a.id) AS total_absen
    FROM absensi a
    JOIN karyawan k ON a.karyawan_id=k.id
    WHERE k.departemen_id=$departemen_id
      AND a.tanggal BETWEEN '$startDate' AND '$endDate'
";
$dept_totals = mysqli_fetch_assoc(mysqli_query($conn, $sql_dept_totals));

// per karyawan
$sql_summary = "
    SELECT 
      k.nik, k.nama,
      SUM(a.status='Alpa') AS alpa,
      SUM(a.status='Sakit') AS sakit,
      SUM(a.status='Cuti') AS cuti,
      SUM(a.status='Surat Dokter') AS surat_dokter,
      SUM(a.status='Terlambat') AS terlambat,
      SUM(a.menit_telat) AS total_menit_telat,
      SUM(a.status='Izin Tanpa Bayar') AS izin_tanpa_bayar,
      SUM(a.status='Izin Dispensasi') AS izin_dispensasi,
      SUM(a.status='Izin Meninggalkan Tempat Kerja') AS izin_keluar,
      COUNT(a.id) AS total_absen
    FROM karyawan k
    LEFT JOIN absensi a 
      ON a.karyawan_id=k.id AND a.tanggal BETWEEN '$startDate' AND '$endDate'
    WHERE k.departemen_id=$departemen_id
    GROUP BY k.id
    ORDER BY k.nama ASC
";
$res_summary = mysqli_query($conn, $sql_summary);

// === generate excel ===
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// judul
$sheet->setCellValue('A1', "Rekap Absensi Departemen: $nama_departemen");
$sheet->setCellValue('A2', "Periode: ".date('F Y', strtotime($startDate)));

// total departemen
$row = 4;
foreach ($dept_totals as $key => $val) {
    $sheet->setCellValue("A$row", ucfirst(str_replace("_"," ",$key)));
    $sheet->setCellValue("B$row", $val);
    $row++;
}

// header detail per karyawan
$row += 2;
$headers = ["NIK","Nama","Alpa","Sakit","Cuti","Surat Dokter","Terlambat","Total Menit Telat","Izin Tanpa Bayar","Izin Dispensasi","Izin Keluar","Total"];
$col = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($col.$row, $h);
    $col++;
}

// isi data karyawan
$row++;
while ($r = mysqli_fetch_assoc($res_summary)) {
    $col = 'A';
    $sheet->setCellValue($col++.$row, $r['nik']);
    $sheet->setCellValue($col++.$row, $r['nama']);
    $sheet->setCellValue($col++.$row, $r['alpa']);
    $sheet->setCellValue($col++.$row, $r['sakit']);
    $sheet->setCellValue($col++.$row, $r['cuti']);
    $sheet->setCellValue($col++.$row, $r['surat_dokter']);
    $sheet->setCellValue($col++.$row, $r['terlambat']);
    $sheet->setCellValue($col++.$row, $r['total_menit_telat']);
    $sheet->setCellValue($col++.$row, $r['izin_tanpa_bayar']);
    $sheet->setCellValue($col++.$row, $r['izin_dispensasi']);
    $sheet->setCellValue($col++.$row, $r['izin_keluar']);
    $sheet->setCellValue($col++.$row, $r['total_absen']);
    $row++;
}

// output
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=rekap_absensi_{$nama_departemen}_{$bulan}.xlsx");
$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
