<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit;
}

$history = $pdo->query("
    SELECT r.id, CONCAT(u.first_name, ' ', u.last_name) AS patient_name, r.blood_group, r.units_required,
           r.request_date, r.status, r.action_date
    FROM blood_requests r
    JOIN users u ON r.patient_id = u.id
    ORDER BY r.request_date DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request History</title>
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
            <li><a href="request-history.php" class="active">📜 Request History</a></li>
            <li><a href="blood-stock.php">🩸 Blood Stock</a></li>
        </ul>
    </nav>

    <main class="content">
        <h2>Request History</h2>
        <table>
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Patient Name</th>
                    <th>Blood Group</th>
                    <th>Units</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Action Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['patient_name']) ?></td>
                    <td><?= $row['blood_group'] ?></td>
                    <td><?= $row['units_required'] ?></td>
                    <td><?= $row['request_date'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= $row['action_date'] ?? 'Pending' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
