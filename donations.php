<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'donor'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role === 'admin') {
    // Admin sees all matched donations
    $stmt = $pdo->query("
        SELECT r.*, 
               CONCAT(d.first_name, ' ', d.last_name) AS donor_name,
               CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
               r.action_date
        FROM blood_requests r
        JOIN users d ON r.assigned_donor_id = d.id
        JOIN users p ON r.patient_id = p.id
        WHERE r.status = 'Matched'
        ORDER BY r.action_date DESC
    ");
    $donations = $stmt->fetchAll();
} else {
    // Donor sees their own history
    $stmt = $pdo->prepare("
        SELECT r.*, 
               CONCAT(p.first_name, ' ', p.last_name) AS patient_name
        FROM blood_requests r
        JOIN users p ON r.patient_id = p.id
        WHERE r.status = 'Matched' AND r.assigned_donor_id = ?
        ORDER BY r.action_date DESC
    ");
    $stmt->execute([$user_id]);
    $donations = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blood Donations</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo">📍 Blood Bank - <?= ucfirst($role) ?> Donations</div>
    <div class="nav-links">
        <a href="<?= $role === 'admin' ? 'admin.php' : 'donor-dashboard.php' ?>" class="nav-btn">Dashboard</a>
        <a href="logout.php" class="nav-btn">Logout</a>
    </div>
</header>

<div class="register-container">
    <div class="register-card">
        <h2><?= $role === 'admin' ? 'All Matched Donations' : 'Your Donation History' ?></h2>

        <?php if (empty($donations)): ?>
            <p>No donation records found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <?php if ($role === 'admin'): ?>
                            <th>Donor</th>
                        <?php endif; ?>
                        <th>Patient</th>
                        <th>Blood Group</th>
                        <th>Units</th>
                        <th>Request Date</th>
                        <th>Matched Date</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donations as $row): ?>
                        <tr>
                            <?php if ($role === 'admin'): ?>
                                <td><?= htmlspecialchars($row['donor_name']) ?></td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($row['patient_name']) ?></td>
                            <td><?= $row['blood_group'] ?></td>
                            <td><?= $row['units_required'] ?></td>
                            <td><?= $row['required_date'] ?></td>
                            <td><?= $row['action_date'] ?></td>
                            <td><?= htmlspecialchars($row['reason']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
