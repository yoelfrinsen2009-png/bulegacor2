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

// jangan hapus akun sendiri
if (isset($_SESSION['user']['id']) && (string)$id === (string)$_SESSION['user']['id']) {
    header("Location: index.php?success=Tidak bisa menghapus akun sendiri!");
    exit;
}

$st = $pdo->prepare("DELETE FROM users WHERE id = ?");
$st->execute([$id]);

header("Location: index.php?success=User berhasil dihapus!");
exit;

