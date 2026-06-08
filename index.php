<?php
session_start();
require 'config/database.php';

$error = '';
$login_attempts = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] : 0;

// Kalau sudah salah 3x, tutup program
if ($login_attempts >= 3) {
    die("
    <script>
        alert(' Anda telah salah login 3 kali! Program ditutup.');
        window.close();
    </script>
    <h2 style='color:red; text-align:center; margin-top:100px;'>
        Program dikunci. Silakan restart browser.
    </h2>
    ");
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Login berhasil
        $_SESSION['user']           = $user;
        $_SESSION['login_attempts'] = 0;
        header("Location: dashboard.php");
        exit;
    } else {
        // Login gagal
        $_SESSION['login_attempts'] = $login_attempts + 1;
        $sisa = 3 - $_SESSION['login_attempts'];
        $error = "Email atau password salah! Sisa percobaan: $sisa kali.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2> Perpustakaan Digital</h2>
            <p>Silakan login untuk melanjutkan</p>

            <?php if ($error): ?>
                <script>
                    // PopUp Message kalau login salah
                    alert("<?= htmlspecialchars($error) ?>");
                </script>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="email@contoh.com" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>
        </div>
    </div>
</body>
</html>