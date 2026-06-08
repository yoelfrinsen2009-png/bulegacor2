<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }
require '../config/database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("DELETE FROM peminjaman WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?success=Peminjaman berhasil dihapus!");
exit;

