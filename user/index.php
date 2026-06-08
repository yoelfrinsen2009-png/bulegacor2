<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }
require '../config/database.php';

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Perpustakaan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="navbar">
        <span class="brand"> Perpustakaan Digital</span>
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
    </div>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
            <h2>👥 Manajemen User</h2>
            <a href="create.php" class="btn btn-success">+ Tambah User</a>
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
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th>Role</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                        <tr><td colspan="7" style="text-align:center; color:#718096;">Belum ada user.</td></tr>
                        <?php else: ?>
                        <?php foreach ($users as $i => $u): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($u['nama']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['no_hp']) ?></td>
                            <td>
                                <span style="background:<?= $u['role'] === 'admin' ? '#bee3f8' : '#c6f6d5' ?>;
                                             color:<?= $u['role'] === 'admin' ? '#2b6cb0' : '#276749' ?>;
                                             padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">
                                    <?= ucfirst($u['role']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                            <td style="display:flex; gap:6px; flex-wrap:wrap;">
                                <a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-warning">✏️ Edit</a>
                                <a href="delete.php?id=<?= $u['id'] ?>"
                                   class="btn btn-danger"
                                   onclick="return confirm('Yakin hapus user <?= htmlspecialchars($u['nama']) ?>?')">
                                   Hapus
                                </a>
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