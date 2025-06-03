<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit;
}

// Approve logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_request_id'])) {
    $request_id = $_POST['approve_request_id'];

    // Get request details
    $stmt = $pdo->prepare("SELECT * FROM blood_requests WHERE id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch();

    if ($request && $request['status'] === 'Pending') {
        $blood_group = $request['blood_group'];

        // Find eligible donors
        $donors = $pdo->prepare("
            SELECT u.id
            FROM users u
            JOIN donor_details d ON u.id = d.user_id
            WHERE u.role = 'donor'
              AND u.blood_group = ?
              AND (d.last_donation_date IS NULL OR d.last_donation_date <= DATE_SUB(CURDATE(), INTERVAL 90 DAY))
        ");
        $donors->execute([$blood_group]);
        $eligible_donors = $donors->fetchAll();

        // Notify each eligible donor (store pending donations)
        $insert = $pdo->prepare("INSERT INTO pending_donations (donor_id, request_id) VALUES (?, ?)");
        foreach ($eligible_donors as $donor) {
            $insert->execute([$donor['id'], $request_id]);
        }

        // Mark request as approved
        $pdo->prepare("UPDATE blood_requests SET status = 'Approved', action_date = NOW() WHERE id = ?")
            ->execute([$request_id]);

        $message = "Request #$request_id approved and " . count($eligible_donors) . " eligible donors notified.";
    }
}

// Get all blood requests
$requests = $pdo->query("
    SELECT r.*, CONCAT(u.first_name, ' ', u.last_name) AS patient_name
    FROM blood_requests r
    JOIN users u ON r.patient_id = u.id
    ORDER BY r.request_date DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blood Requests - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo">📍 Blood Bank Management System</div>
    <div class="nav-links">
        <a href="admin.php" class="nav-btn">Dashboard</a>
        <a href="logout.php" class="nav-btn">Logout</a>
    </div>
</header>

<div class="register-container">
    <div class="register-card">
        <h2>All Blood Requests</h2>

        <?php if (!empty($message)): ?>
            <p style="color: green;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Blood Group</th>
                    <th>Units</th>
                    <th>Need Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $req): ?>
                <tr>
                    <td><?= $req['id'] ?></td>
                    <td><?= htmlspecialchars($req['patient_name']) ?></td>
                    <td><?= $req['blood_group'] ?></td>
                    <td><?= $req['units_required'] ?></td>
                    <td><?= $req['required_date'] ?></td>
                    <td><?= $req['status'] ?></td>
                    <td>
                        <?php if ($req['status'] === 'Pending'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="approve_request_id" value="<?= $req['id'] ?>">
                                <button type="submit" class="btn btn-primary">Approve</button>
                            </form>
                        <?php else: ?>
                            <?= $req['status'] ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
