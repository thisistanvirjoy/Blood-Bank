<?php
require 'db.php';
session_start();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Fetch only admin users by role
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['role'] = 'admin';
        $_SESSION['first_name'] = $admin['first_name'];
        header('Location: admin.php');
        exit;
    } else {
        $message = 'Invalid admin credentials.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Blood Bank Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo">
        <span class="location-icon">📍</span>
        Blood Bank Management System
    </div>
    <div class="nav-links">
        <a href="index.php" class="nav-btn">Home</a>
    </div>
</header>

<div class="login-container">
    <div class="login-card">
        <h2>Admin Login</h2>
        <?php if ($message): ?>
            <p style="color: red; text-align:center;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="username">Email / Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</div>
</body>
</html>
