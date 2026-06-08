<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }
require '../config/database.php';

$data = $pdo->query("
    SELECT p.*, u.nama as nama_user, b.judul, b.penulis
    FROM peminjaman p
    JOIN users u ON p.user_id = u.id
    JOIN buku b ON p.buku_id = b.id
    ORDER BY p.tanggal_pinjam DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman - Perpustakaan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="navbar">
        <span class="brand">Perpustakaan Digital</span>
        <nav>
            <a href="../dashboard.php" style="color:white;">Dashboard</a>
            <!-- Menu Users HANYA muncul untuk Admin -->
            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="../user/index.php">Users</a>
            <?php endif; ?>
            <!-- Buku & Peminjaman bisa dilihat Admin & Member -->
            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="../buku/index.php">Buku</a>
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
            <h2> Data Peminjaman</h2>
            <a href="create.php" class="btn btn-success">+ Tambah Peminjaman</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"> <?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Peminjam</th>
                            <th>Judul Buku</th>
                            <th>Penulis</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                        <tr><td colspan="8" style="text-align:center; color:#718096;">Belum ada data peminjaman.</td></tr>
                        <?php else: ?>
                        <?php foreach ($data as $i => $d): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($d['nama_user']) ?></td>
                            <td><?= htmlspecialchars($d['judul']) ?></td>
                            <td><?= htmlspecialchars($d['penulis']) ?></td>
                            <td><?= date('d/m/Y', strtotime($d['tanggal_pinjam'])) ?></td>
                            <td><?= $d['tanggal_kembali'] ? date('d/m/Y', strtotime($d['tanggal_kembali'])) : '<span style="color:#718096;">-</span>' ?></td>
                            <td>
                                <span style="background:<?= $d['status'] === 'dipinjam' ? '#fefcbf' : '#c6f6d5' ?>;
                                             color:<?= $d['status'] === 'dipinjam' ? '#744210' : '#276749' ?>;
                                             padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">
                                    <?= $d['status'] === 'dipinjam' ? '📤 Dipinjam' : '✅ Dikembalikan' ?>
                                </span>
                            </td>
                             <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                            <td style="display:flex; gap:6px; flex-wrap:wrap;">
                                <a href="edit.php?id=<?= $d['id'] ?>" class="btn btn-warning">✏️ Edit</a>
                                <a href="delete.php?id=<?= $d['id'] ?>"
                                   class="btn btn-danger"
                                   onclick="return confirm('Yakin hapus data peminjaman ini?')">
                                    Hapus
                                </a>
                            </td>
                            <?php endif; ?>
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