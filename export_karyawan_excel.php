<?php
require 'vendor/autoload.php';
include "config/koneksi.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_GET['karyawan_id'])) {
    die("Karyawan tidak ditemukan!");
}

$karyawan_id = $_GET['karyawan_id'];

// ambil rekap absensi per karyawan
$query = "
    SELECT 
        k.nik,
        k.nama,
        SUM(CASE WHEN a.status = 'Alpa' THEN 1 ELSE 0 END) AS Alpa,
        SUM(CASE WHEN a.status = 'Sakit' THEN 1 ELSE 0 END) AS Sakit,
        SUM(CASE WHEN a.status = 'Cuti' THEN 1 ELSE 0 END) AS Cuti,
        SUM(CASE WHEN a.status = 'Surat Dokter' THEN 1 ELSE 0 END) AS Surat_Dokter,
        SUM(CASE WHEN a.status = 'Terlambat' THEN 1 ELSE 0 END) AS Terlambat,
        SUM(CASE WHEN a.status = 'Terlambat' THEN a.menit_telat ELSE 0 END) AS Total_Menit_Telat,
        SUM(CASE WHEN a.status = 'Izin Tanpa Bayar' THEN 1 ELSE 0 END) AS Izin_Tanpa_Bayar,
        SUM(CASE WHEN a.status = 'Izin Dispensasi' THEN 1 ELSE 0 END) AS Izin_Dispensasi,
        SUM(CASE WHEN a.status = 'Izin Keluar' THEN 1 ELSE 0 END) AS Izin_Keluar,
        COUNT(a.id) AS Total_Absen
    FROM karyawan k
    LEFT JOIN absensi a ON a.karyawan_id = k.id 
        AND MONTH(a.tanggal) = MONTH(CURDATE()) 
        AND YEAR(a.tanggal) = YEAR(CURDATE())
    WHERE k.id = '$karyawan_id'
    GROUP BY k.id, k.nik, k.nama
";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// buat spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// header kolom
$headers = [
    'A1' => 'NIK',
    'B1' => 'Nama',
    'C1' => 'Alpa',
    'D1' => 'Sakit',
    'E1' => 'Cuti',
    'F1' => 'Surat Dokter',
    'G1' => 'Terlambat',
    'H1' => 'Total Menit Telat',
    'I1' => 'Izin Tanpa Bayar',
    'J1' => 'Izin Dispensasi',
    'K1' => 'Izin Keluar',
    'L1' => 'Total Absen',
];
foreach ($headers as $col => $val) {
    $sheet->setCellValue($col, $val);
}

// isi data karyawan
if ($data) {
    $sheet->setCellValue('A2', $data['nik']);
    $sheet->setCellValue('B2', $data['nama']);
    $sheet->setCellValue('C2', $data['Alpa']);
    $sheet->setCellValue('D2', $data['Sakit']);
    $sheet->setCellValue('E2', $data['Cuti']);
    $sheet->setCellValue('F2', $data['Surat_Dokter']);
    $sheet->setCellValue('G2', $data['Terlambat']);
    $sheet->setCellValue('H2', $data['Total_Menit_Telat']);
    $sheet->setCellValue('I2', $data['Izin_Tanpa_Bayar']);
    $sheet->setCellValue('J2', $data['Izin_Dispensasi']);
    $sheet->setCellValue('K2', $data['Izin_Keluar']);
    $sheet->setCellValue('L2', $data['Total_Absen']);
}

// set nama file
$filename = "Rekap_Absensi_Karyawan_" . $karyawan_id . "_" . date('F_Y') . ".xlsx";

// download file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
