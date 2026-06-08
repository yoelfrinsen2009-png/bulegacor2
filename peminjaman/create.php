<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }
require '../config/database.php';

$errors = [];
$user_id = $buku_id = '';
$tanggal_pinjam = date('Y-m-d');
$tanggal_kembali = '';
$status = 'dipinjam';

// Ambil data untuk dropdown
$users = $pdo->query("SELECT id, nama FROM users ORDER BY nama ASC")->fetchAll();
$buku  = $pdo->query("SELECT id, judul FROM buku ORDER BY judul ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $buku_id = $_POST['buku_id'] ?? '';
    $tanggal_pinjam = $_POST['tanggal_pinjam'] ?? '';
    $tanggal_kembali = $_POST['tanggal_kembali'] ?? '';
    $status = $_POST['status'] ?? 'dipinjam';

    if (empty($user_id)) $errors[] = 'Peminjam wajib dipilih.';
    if (empty($buku_id)) $errors[] = 'Buku wajib dipilih.';
    if (empty($tanggal_pinjam)) $errors[] = 'Tanggal pinjam wajib diisi.';

    if (!in_array($status, ['dipinjam','dikembalikan'], true)) {
        $errors[] = 'Status tidak valid.';
    }

    if (empty($errors)) {
        // Simpan
        $stmt = $pdo->prepare(
            "INSERT INTO peminjaman (user_id, buku_id, tanggal_pinjam, tanggal_kembali, status) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $user_id,
            $buku_id,
            $tanggal_pinjam,
            empty($tanggal_kembali) ? null : $tanggal_kembali,
            $status
        ]);

        header("Location: index.php?success=Peminjaman berhasil ditambahkan!");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peminjaman - Perpustakaan</title>
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
            <a href="../buku/index.php">Buku</a>
            <?php endif; ?>
            <a href="../peminjaman/index.php"style="color:white;">Peminjaman</a>
            <!-- Menu Report PDF HANYA muncul untuk Admin -->
            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="../report/pdf.php"> Report PDF</a>
            <?php endif; ?>
            <a href="../logout.php" style="color:#fc8181;">Logout</a>
    </nav>
</div>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <h2>➕ Tambah Peminjaman</h2>
        <a href="index.php" class="btn btn-primary">← Kembali</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card" style="max-width:520px;">
        <form method="POST">
            <div class="form-group">
                <label>Peminjam</label>
                <select name="user_id" required>
                    <option value="">-- Pilih Peminjam --</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= (string)$u['id'] === (string)$user_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Buku</label>
                <select name="buku_id" required>
                    <option value="">-- Pilih Buku --</option>
                    <?php foreach ($buku as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= (string)$b['id'] === (string)$buku_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['judul']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tanggal Pinjam</label>
                <input type="date" name="tanggal_pinjam" value="<?= htmlspecialchars($tanggal_pinjam) ?>" required>
            </div>

            <div class="form-group">
                <label>Tanggal Kembali <small style="color:#718096;">(opsional)</small></label>
                <input type="date" name="tanggal_kembali" value="<?= htmlspecialchars($tanggal_kembali) ?>">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="dipinjam" <?= $status === 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                    <option value="dikembalikan" <?= $status === 'dikembalikan' ? 'selected' : '' ?>>Dikembalikan</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success" style="width:100%; padding:12px; font-size:15px;">Simpan Peminjaman</button>
        </form>
    </div>
</div>
</body>
</html>

