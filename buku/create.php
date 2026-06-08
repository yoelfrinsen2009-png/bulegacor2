<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }
require '../config/database.php';

$errors = [];
$judul = $penulis = $tahun_terbit = '';
$stok = '0';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $tahun_terbit = trim($_POST['tahun_terbit'] ?? '');
    $stok = trim($_POST['stok'] ?? '0');

    // Validasi paling simpel
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
        $stmt = $pdo->prepare("INSERT INTO buku (judul, penulis, tahun_terbit, stok) VALUES (?, ?, ?, ?)");
        $stmt->execute([$judul, $penulis, (int)$tahun_terbit, (int)$stok]);
        header("Location: index.php?success=Buku berhasil ditambahkan!");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - Perpustakaan</title>
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
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2>➕ Tambah Buku</h2>
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
                    <input type="text" name="judul" value="<?= htmlspecialchars($judul) ?>" placeholder="Masukkan judul" required>
                </div>

                <div class="form-group">
                    <label>Penulis</label>
                    <input type="text" name="penulis" value="<?= htmlspecialchars($penulis) ?>" placeholder="Masukkan penulis" required>
                </div>

                <div class="form-group">
                    <label>Tahun Terbit</label>
                    <input type="text" name="tahun_terbit" value="<?= htmlspecialchars($tahun_terbit) ?>" placeholder="contoh: 2020" required>
                </div>

                <div class="form-group">
                    <label>Stok</label>
                    <input type="text" name="stok" value="<?= htmlspecialchars($stok) ?>" placeholder="contoh: 5" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                </div>

                <button type="submit" class="btn btn-success" style="width:100%; padding:12px; font-size:15px;">
                    Simpan Buku
                </button>
            </form>
        </div>
    </div>
</body>
</html>

