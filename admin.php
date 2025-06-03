<?php
require 'db.php';
session_start();

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit;
}

// Fetch stats
$totalDonors = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'donor'")->fetchColumn();
$totalRequests = $pdo->query("SELECT COUNT(*) FROM blood_requests")->fetchColumn();
$approvedRequests = $pdo->query("SELECT COUNT(*) FROM blood_requests WHERE status = 'Approved'")->fetchColumn();
$totalUnits = $pdo->query("SELECT SUM(quantity) FROM blood_stock")->fetchColumn() ?? 0;

// Fetch blood stock by group
$stock = $pdo->query("SELECT blood_group, quantity FROM blood_stock")->fetchAll(PDO::FETCH_KEY_PAIR);

// Fetch matched donations
$donations = $pdo->query("
    SELECT u.first_name, u.last_name, u.date_of_birth, u.blood_group, d.medical_conditions, br.units_required, br.request_date
    FROM blood_requests br
    JOIN users u ON br.assigned_donor_id = u.id
    JOIN donor_details d ON u.id = d.user_id
    WHERE br.status = 'Matched'
    ORDER BY br.request_date DESC
    LIMIT 10
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Blood Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo">📍 Blood Bank Management System</div>
    <div class="logout">
        <a href="logout.php">Logout ➜</a>
    </div>
</header>

<div class="container">
    <nav class="sidebar">
        <ul>
            <li><a href="admin.php" class="active">🏠 Home</a></li>
            <li><a href="donor-management.php">👤 Donor</a></li>
            <li><a href="patient-management.php">🏥 Patient</a></li>
            <li><a href="donations.php">💉 Donations</a></li>
            <li><a href="blood-requests.php">📋 Blood Requests</a></li>
            <li><a href="request-history.php">📜 Request History</a></li>
            <li><a href="blood-stock.php">🩸 Blood Stock</a></li>
        </ul>
    </nav>

    <main class="content">
        <!-- Blood Group Cards -->
        <div class="blood-groups">
            <?php
            $groups = ['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-'];
            foreach ($groups as $group):
                $qty = $stock[$group] ?? 0;
            ?>
            <div class="blood-card">
                <h2><?= htmlspecialchars($group) ?> <span class="blood-drop">🩸</span></h2>
                <p><?= $qty ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Summary Cards -->
        <div class="features">
    <div class="feature-card">
        <div class="feature-icon">👥</div>
        <h3>Total Donors</h3>
        <p><?= $totalDonors ?></p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">📄</div>
        <h3>Total Requests</h3>
        <p><?= $totalRequests ?></p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">✅</div>
        <h3>Approved Requests</h3>
        <p><?= $approvedRequests ?></p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">🩸</div>
        <h3>Total Blood Unit (ml)</h3>
        <p><?= $totalUnits * 450 ?></p>
    </div>
</div>
>


        <!-- Donation Table -->
        <div class="donation-details">
            <h2>BLOOD DONATION DETAILS</h2>
            <table>
                <thead>
                    <tr>
                        <th>Donor Name</th>
                        <th>Disease</th>
                        <th>Age</th>
                        <th>Blood Group</th>
                        <th>Unit</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($donations) === 0): ?>
                        <tr><td colspan="8" style="text-align:center;">No matched donations found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($donations as $donation): ?>
                            <tr>
                                <td><?= htmlspecialchars($donation['first_name'] . ' ' . $donation['last_name']) ?></td>
                                <td><?= htmlspecialchars($donation['medical_conditions']) ?></td>
                                <td><?= date_diff(date_create($donation['date_of_birth']), date_create('today'))->y ?></td>
                                <td><?= htmlspecialchars($donation['blood_group']) ?></td>
                                <td><?= $donation['units_required'] * 450 ?> ml</td>
                                <td><?= $donation['request_date'] ?></td>
                                <td>Matched</td>
                                <td>-</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
