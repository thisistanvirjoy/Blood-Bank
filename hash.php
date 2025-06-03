<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Hasher</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <h2>Generate Password Hash</h2>
        <form method="POST">
            <div class="form-group">
                <label for="password">Enter Plain Password</label>
                <input type="text" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Hash Password</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])): ?>
            <div class="form-group">
                <label>Hashed Password:</label>
                <textarea readonly rows="2"><?= htmlspecialchars(password_hash($_POST['password'], PASSWORD_BCRYPT)) ?></textarea>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
