<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: patient-login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blood_group = $_POST['blood_group'];
    $units = $_POST['units'];
    $required_date = $_POST['required_date'];
    $reason = $_POST['reason'];

    try {
        $stmt = $pdo->prepare("INSERT INTO blood_requests (patient_id, blood_group, units_required, required_date, reason, status, request_date)
                               VALUES (?, ?, ?, ?, ?, 'Pending', NOW())");
        $stmt->execute([$user_id, $blood_group, $units, $required_date, $reason]);
        header("Location: request-blood.php?success=1");
        exit;
    } catch (Exception $e) {
        $message = "Error submitting request: " . $e->getMessage();
    }
}

// Fetch current user's requests
$stmt = $pdo->prepare("SELECT * FROM blood_requests WHERE patient_id = ? ORDER BY request_date DESC");
$stmt->execute([$user_id]);
$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Blood</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo">📍 Blood Bank - Patient</div>
    <div class="nav-links">
        <a href="patient-dashboard.php" class="nav-btn">Dashboard</a>
        <a href="logout.php" class="nav-btn">Logout</a>
    </div>
</header>

<div class="register-container">
    <div class="register-card">
        <h2>Request Blood</h2>

        <?php if (isset($_GET['success'])): ?>
            <p style="color: green;">Request submitted successfully!</p>
        <?php elseif ($message): ?>
            <p style="color: red;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST" class="register-form">
            <select name="blood_group" required>
                <option value="">Select Blood Group</option>
                <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                <option>O+</option><option>O-</option><option>AB+</option><option>AB-</option>
            </select>
            <input type="number" name="units" placeholder="Units Required" min="1" required>
            <input type="date" name="required_date" required>
            <textarea name="reason" rows="3" placeholder="Reason for blood request" required></textarea>
            <button type="submit" class="btn btn-primary">Submit Request</button>
        </form>
    </div>

    <div class="register-card" style="margin-top: 2rem;">
        <h2>Your Blood Requests</h2>
        <table>
            <thead>
                <tr>
                    <th>Blood Group</th>
                    <th>Units</th>
                    <th>Date Needed</th>
                    <th>Status</th>
                    <th>Assigned Donor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $req): ?>
                    <tr>
                        <td><?= $req['blood_group'] ?></td>
                        <td><?= $req['units_required'] ?></td>
                        <td><?= $req['required_date'] ?></td>
                        <td><?= $req['status'] ?></td>
                        <td>
                            <?php if ($req['status'] === 'Matched' && $req['assigned_donor_id']): ?>
                                <?php
                                $donor = $pdo->prepare("SELECT first_name, last_name, phone, email FROM users WHERE id = ?");
                                $donor->execute([$req['assigned_donor_id']]);
                                $info = $donor->fetch();
                                ?>
                                <strong><?= htmlspecialchars($info['first_name'] . ' ' . $info['last_name']) ?></strong><br>
                                📞 <?= htmlspecialchars($info['phone']) ?><br>
                                ✉️ <?= htmlspecialchars($info['email']) ?>
                            <?php elseif ($req['status'] === 'Approved'): ?>
                                Searching...
                            <?php elseif ($req['status'] === 'Pending'): ?>
                                Awaiting approval
                            <?php elseif ($req['status'] === 'Rejected'): ?>
                                Rejected by admin
                            <?php else: ?>
                                -
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
