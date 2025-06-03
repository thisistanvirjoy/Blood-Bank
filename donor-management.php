<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit;
}

$donors = $pdo->query("
    SELECT u.id, u.first_name, u.last_name, u.email, u.blood_group, u.gender, u.phone, u.date_of_birth,
           d.last_donation_date, d.status
    FROM users u
    JOIN donor_details d ON u.id = d.user_id
    WHERE u.role = 'donor'
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Management - Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo">📍 Blood Bank Management System</div>
    <div class="logout">
        <a href="logout.php" id="logoutBtn">Logout <span class="logout-icon">➜</span></a>
    </div>
</header>

<div class="container">
    <nav class="sidebar">
        <ul>
            <li><a href="admin.php">🏠 Home</a></li>
            <li><a href="donor-management.php" class="active">👤 Donor</a></li>
            <li><a href="patient-management.php">🏥 Patient</a></li>
            <li><a href="donations.php">💉 Donations</a></li>
            <li><a href="blood-requests.php">📋 Blood Requests</a></li>
            <li><a href="request-history.php">📜 Request History</a></li>
            <li><a href="blood-stock.php">🩸 Blood Stock</a></li>
        </ul>
    </nav>

    <main class="content">
        <div class="page-header">
            <h2>Donor Management</h2>
        </div>

        <div class="donor-list">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Blood Group</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Last Donation</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($donors as $donor): 
                    $age = date_diff(date_create($donor['date_of_birth']), date_create('today'))->y;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($donor['first_name'] . ' ' . $donor['last_name']) ?></td>
                        <td><?= htmlspecialchars($donor['email']) ?></td>
                        <td><?= htmlspecialchars($donor['blood_group']) ?></td>
                        <td><?= $age ?></td>
                        <td><?= htmlspecialchars($donor['gender']) ?></td>
                        <td><?= htmlspecialchars($donor['phone']) ?></td>
                        <td><?= $donor['last_donation_date'] ?? 'N/A' ?></td>
                        <td><?= htmlspecialchars($donor['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
