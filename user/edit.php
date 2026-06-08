<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}
require '../config/database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

$st = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$st->execute([$id]);
$u = $st->fetch();
if (!$u) {
    header("Location: index.php");
    exit;
}

$errors = [];
$nama = $email = $no_hp = $role = '';
$password = '';

$nama = $u['nama'];
$email = $u['email'];
$no_hp = $u['no_hp'];
$role = $u['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $role = $_POST['role'] ?? 'member';
    $password = $_POST['password'] ?? '';

    if (empty($nama)) $errors[] = 'Nama wajib diisi.';
    if (empty($email)) $errors[] = 'Email wajib diisi.';
    if (empty($no_hp)) $errors[] = 'No HP wajib diisi.';
    if (!empty($no_hp) && !preg_match('/^[0-9]+$/', $no_hp)) $errors[] = 'No HP wajib berisi angka saja.';
    if (!empty($password) && strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';

    if (empty($errors)) {
        $cek = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $cek->execute([$email, $id]);
        if ($cek->fetch()) {
            $errors[] = 'Email sudah digunakan user lain.';
        }
    }

    if (empty($errors)) {
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $up = $pdo->prepare("UPDATE users SET nama=?, email=?, password=?, no_hp=?, role=? WHERE id=?");
            $up->execute([$nama, $email, $hash, $no_hp, $role, $id]);
        } else {
            $up = $pdo->prepare("UPDATE users SET nama=?, email=?, no_hp=?, role=? WHERE id=?");
            $up->execute([$nama, $email, $no_hp, $role, $id]);
        }

        header("Location: index.php?success=User berhasil diperbarui!");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Perpustakaan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="navbar">
    <span class="brand"> Perpustakaan Digital</span>
    <nav>
        <a href="../dashboard.php">Dashboard</a>
            <!-- Menu Users HANYA muncul untuk Admin -->
            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="../user/index.php" style="color:white;">Users</a>
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
        <h2>✏️ Edit User</h2>
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
                <label>Nama Lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="form-group">
                <label>No HP</label>
                <input type="text" name="no_hp" value="<?= htmlspecialchars($no_hp) ?>" required
                       oninput="this.value=this.value.replace(/[^0-9]/g,'')">
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="member" <?= $role === 'member' ? 'selected' : '' ?>>Member</option>
                    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label>Password Baru <small style="color:#718096;">(opsional)</small></label>
                <input type="password" name="password" placeholder="Isi jika ingin ganti password">
            </div>

            <button type="submit" class="btn btn-warning" style="width:100%; padding:12px; font-size:15px;">Update</button>
        </form>
    </div>
</div>
</body>
</html>

