<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }
require '../config/database.php';


$data = $pdo->query(
    "SELECT id, judul, penulis, tahun_terbit, stok, created_at FROM buku ORDER BY created_at DESC"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku - Perpustakaan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="navbar">
        <span class="brand">Perpustakaan Digital</span>
        <nav>
            <a href="../dashboard.php" >Dashboard</a>
            <!-- Menu Users HANYA muncul untuk Admin -->
            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="../user/index.php">Users</a>
            <?php endif; ?>
            <!-- Buku & Peminjaman bisa dilihat Admin & Member -->
            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="../buku/index.php"style="color:white;">Buku</a>
            <?php endif; ?>
            <a href="../peminjaman/index.php">Peminjaman</a>
            <!-- Menu Report PDF HANYA muncul untuk Admin -->
            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="../report/pdf.php"> Report PDF</a>
            <?php endif; ?>
            <a href="../logout.php" style="color:#fc8181;">Logout</a>
        </nav>
    </div>

    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
            <h2>Data Buku</h2>
            <a href="create.php" class="btn btn-success">+ Tambah Buku</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"> <?= htmlspecialchars($_GET['success']) ?> </div>
        <?php endif; ?>

        <div class="card">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Tahun Terbit</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center; color:#718096;">Belum ada data buku.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data as $i => $b): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($b['judul']) ?></td>
                                    <td><?= htmlspecialchars($b['penulis']) ?></td>
                                    <td><?= htmlspecialchars($b['tahun_terbit']) ?></td>
                                    <td>
                                        <span style="background:<?= (int)$b['stok'] > 0 ? '#c6f6d5' : '#fed7d7' ?>;
                                                     color:<?= (int)$b['stok'] > 0 ? '#276749' : '#9b2c2c' ?>;
                                                     padding:4px 10px; border-radius:20px; font-size:13px;">
                                            <?= (int)$b['stok'] ?> buku
                                        </span>
                                    </td>
                                    <td style="display:flex; gap:6px; flex-wrap:wrap;">
                                        <a href="edit.php?id=<?= (int)$b['id'] ?>" class="btn btn-warning">✏️ Edit</a>
                                        <a href="delete.php?id=<?= (int)$b['id'] ?>"
                                           class="btn btn-danger"
                                           onclick="return confirm('Yakin hapus data buku ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

