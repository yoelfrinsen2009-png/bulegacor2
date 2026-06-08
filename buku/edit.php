<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }
require '../config/database.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM buku WHERE id = ?");
$stmt->execute([$id]);
$b = $stmt->fetch();
if (!$b) { header("Location: index.php"); exit; }

$errors = [];
$judul = $b['judul'];
$penulis = $b['penulis'];
$tahun_terbit = (string)$b['tahun_terbit'];
$stok = (string)$b['stok'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $tahun_terbit = trim($_POST['tahun_terbit'] ?? '');
    $stok = trim($_POST['stok'] ?? '0');

    if (empty($judul)) $errors[] = 'Judul wajib diisi.';
    if (empty($penulis)) $errors[] = 'Penulis wajib diisi.';

    if (empty($tahun_terbit)) {
        $errors[] = 'Tahun terbit wajib diisi.';
    } elseif (!preg_match('/^\d{4}$/', $tahun_terbit)) {
        $errors[] = 'Tahun terbit harus 4 digit (contoh: 2020).';
    }

    if (!preg_match('/^\d+$/', $stok)) {
        $errors[] = 'Stok harus angka.';
    }

    if (empty($errors)) {
        $upd = $pdo->prepare("UPDATE buku SET judul=?, penulis=?, tahun_terbit=?, stok=? WHERE id=?");
        $upd->execute([$judul, $penulis, (int)$tahun_terbit, (int)$stok, $id]);
        header("Location: index.php?success=Buku berhasil diperbarui!");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - Perpustakaan</title>
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
            <a href="../buku/index.php"style="color:white;">Buku</a>
            <a href="../peminjaman/index.php">Peminjaman</a>
            <!-- Menu Report PDF HANYA muncul untuk Admin -->
            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="../report/pdf.php"> Report PDF</a>
            <?php endif; ?>
            <a href="../logout.php" style="color:#fc8181;">Logout</a>
        </nav>
    </div>

    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2>Edit Buku</h2>
            <a href="index.php" class="btn btn-primary">← Kembali</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): ?>
                    <div><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card" style="max-width:500px;">
            <form method="POST">
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="judul" value="<?= htmlspecialchars($judul) ?>" required>
                </div>

                <div class="form-group">
                    <label>Penulis</label>
                    <input type="text" name="penulis" value="<?= htmlspecialchars($penulis) ?>" required>
                </div>

                <div class="form-group">
                    <label>Tahun Terbit</label>
                    <input type="text" name="tahun_terbit" value="<?= htmlspecialchars($tahun_terbit) ?>" required>
                </div>

                <div class="form-group">
                    <label>Stok</label>
                    <input type="text" name="stok" value="<?= htmlspecialchars($stok) ?>" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                </div>

                <button type="submit" class="btn btn-warning" style="width:100%; padding:12px; font-size:15px;">
                    Update Buku
                </button>
            </form>
        </div>
    </div>
</body>
</html>

