<?php
require 'db.php';
session_start();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'donor'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name'];
        header("Location: donor-dashboard.php");
        exit;
    } else {
        $message = "Invalid donor credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Login - Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo">📍 Blood Bank Management System</div>
    <div class="nav-links">
        <a href="index.php" class="nav-btn">Home</a>
    </div>
</header>

<div class="login-container">
    <div class="login-card">
        <h2>Donor Login</h2>
        <?php if ($message): ?>
            <p style="color: red;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="POST" class="login-form">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <div class="login-footer">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</div>
</body>
</html>
