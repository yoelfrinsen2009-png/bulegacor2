<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
require 'config/database.php';

$totalUser    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalBuku    = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
$totalPinjam  = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status='dipinjam'")->fetchColumn();
$totalKembali = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status='dikembalikan'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Perpustakaan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- NAVBAR -->
    <div class="navbar">
        <span class="brand"> Perpustakaan Digital</span>
       <nav>
            <a href="dashboard.php" style="color:white;">Dashboard</a>
            <!-- Menu Users HANYA muncul untuk Admin -->
            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="user/index.php">Users</a>
            <?php endif; ?>
            <!-- Buku & Peminjaman bisa dilihat Admin & Member -->
            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="buku/index.php">Buku</a>
            <?php endif; ?>
            <a href="peminjaman/index.php">Peminjaman</a>
            <!-- Menu Report PDF HANYA muncul untuk Admin -->
            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="report/pdf.php"> Report PDF</a>
            <?php endif; ?>
            <a href="logout.php" style="color:#fc8181;">Logout</a>
</nav>
    </div>

    <div class="container">
        <h2 style="margin-bottom:20px;">Selamat Datang, <?= $_SESSION['user']['nama'] ?>! </h2>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?= $totalUser ?></div>
                <div class="label">Total User</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= $totalBuku ?></div>
                <div class="label">Total Buku</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= $totalPinjam ?></div>
                <div class="label">Sedang Dipinjam</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= $totalKembali ?></div>
                <div class="label">Sudah Dikembalikan</div>
            </div>
        </div>

        <!-- TABEL BUKU TERBARU -->
        <div class="card">
            <h3> Daftar Buku Terbaru</h3>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Tahun Terbit</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $buku = $pdo->query("SELECT * FROM buku ORDER BY created_at DESC LIMIT 5")->fetchAll();
                        foreach ($buku as $i => $b): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($b['judul']) ?></td>
                            <td><?= htmlspecialchars($b['penulis']) ?></td>
                            <td><?= $b['tahun_terbit'] ?></td>
                            <td>
                                <span style="background:<?= $b['stok'] > 0 ? '#c6f6d5' : '#fed7d7' ?>;
                                             color:<?= $b['stok'] > 0 ? '#276749' : '#9b2c2c' ?>;
                                             padding:4px 10px; border-radius:20px; font-size:13px;">
                                    <?= $b['stok'] ?> buku
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>