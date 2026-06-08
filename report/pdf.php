<?php
// Mulai session agar halaman ini aman (hanya bisa diakses jika sudah login)
session_start();
if (!isset($_SESSION['user'])) { 
    header("Location: ../index.php"); 
    exit; 
}

// 1. Panggil file koneksi database milikmu
require '../config/database.php';

// 2. Ambil data dari database (sesuaikan nama tabel jika perlu, asusmsi: tabel 'buku')
$stmt = $pdo->query("SELECT * FROM buku");
$data_buku = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Buku</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        
        /* CSS KHUSUS PRINT: Sembunyikan tombol saat file disimpan menjadi PDF */
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 15px; background: #28a745; color: white; border: none; cursor: pointer; font-size: 16px;">
            🖨️ Cetak / Simpan sebagai PDF
        </button>
        <a href="../buku/index.php" style="margin-left: 10px; padding: 10px 15px; background: #dc3545; color: white; text-decoration: none; font-size: 16px;">Kembali</a>
    </div>

    <h2>Laporan Data Buku Perpustakaan</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Judul Buku</th>
                <th>Pengarang</th>
                <th>Tahun Terbit</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            // 3. Looping (tampilkan) data dari database yang ditarik tadi
            foreach ($data_buku as $row): 
            ?>
            <tr>
                <td style="text-align: center;"><?= $no++; ?></td>
                
                <td><?= htmlspecialchars($row['judul'] ?? $row['judul_buku'] ?? '-'); ?></td>
                <td><?= htmlspecialchars($row['pengarang'] ?? '-'); ?></td>
                <td style="text-align: center;"><?= htmlspecialchars($row['tahun_terbit'] ?? $row['tahun'] ?? '-'); ?></td>
            </tr>
            <?php endforeach; ?>

            <?php if(empty($data_buku)): ?>
            <tr>
                <td colspan="4" style="text-align: center;">Tidak ada data buku di database</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        // Membuka dialog print secara otomatis ketika tombol "Cetak Laporan" di aplikasi diklik
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>