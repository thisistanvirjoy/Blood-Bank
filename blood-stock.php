<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit;
}

$stock = $pdo->query("SELECT blood_group, quantity FROM blood_stock")->fetchAll();
$history = $pdo->query("
    SELECT s.updated_at, s.blood_group, s.previous_stock, s.updated_stock, s.change_amount,
           CONCAT(u.first_name, ' ', u.last_name) AS admin_name
    FROM stock_updates s
    JOIN users u ON s.updated_by = u.id
    ORDER BY s.updated_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blood Stock</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo">📍 Blood Bank Management System</div>
    <div class="logout"><a href="logout.php">Logout ➜</a></div>
</header>

<div class="container">
    <nav class="sidebar">
        <ul>
            <li><a href="admin.php">🏠 Home</a></li>
            <li><a href="donor-management.php">👤 Donor</a></li>
            <li><a href="patient-management.php">🏥 Patient</a></li>
            <li><a href="donations.php">💉 Donations</a></li>
            <li><a href="blood-requests.php">📋 Blood Requests</a></li>
            <li><a href="request-history.php">📜 Request History</a></li>
            <li><a href="blood-stock.php" class="active">🩸 Blood Stock</a></li>
        </ul>
    </nav>

    <main class="content">
        <h2>Blood Stock Management</h2>
        <div class="blood-groups">
            <?php foreach ($stock as $item): ?>
            <div class="blood-card">
                <h2><?= $item['blood_group'] ?> <span class="blood-drop">🩸</span></h2>
                <p><?= $item['quantity'] ?> units</p>
            </div>
            <?php endforeach; ?>
        </div>

        <h3>Stock Update History</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Blood Group</th>
                    <th>Previous Stock</th>
                    <th>Updated Stock</th>
                    <th>Change</th>
                    <th>Updated By</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $row): ?>
                <tr>
                    <td><?= $row['updated_at'] ?></td>
                    <td><?= $row['blood_group'] ?></td>
                    <td><?= $row['previous_stock'] ?></td>
                    <td><?= $row['updated_stock'] ?></td>
                    <td><?= $row['change_amount'] ?></td>
                    <td><?= $row['admin_name'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
