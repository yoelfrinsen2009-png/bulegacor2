<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }
require '../config/database.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit; }

// Proteksi sederhana: pastikan id valid
if (!$id) {
    header("Location: index.php?success=ID tidak valid!");
    exit;
}


$stmt = $pdo->prepare("DELETE FROM buku WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?success=Buku berhasil dihapus!");
exit;
