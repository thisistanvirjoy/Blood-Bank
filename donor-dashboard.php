<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: donor-login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Donor profile
$stmt = $pdo->prepare("
    SELECT u.first_name, u.last_name, u.email, u.phone, u.gender, u.blood_group, u.date_of_birth, u.weight,
           d.last_donation_date, d.medical_conditions, d.status
    FROM users u
    JOIN donor_details d ON u.id = d.user_id
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$donor = $stmt->fetch();

// Last accepted blood request
$stmt = $pdo->prepare("
    SELECT r.*, CONCAT(p.first_name, ' ', p.last_name) AS patient_name
    FROM blood_requests r
    JOIN users p ON r.patient_id = p.id
    WHERE r.assigned_donor_id = ?
    ORDER BY r.action_date DESC
    LIMIT 1
");
$stmt->execute([$user_id]);
$matched = $stmt->fetch();

// Eligible donation requests
$ninety_days_ago = date('Y-m-d', strtotime('-90 days'));
$stmt = $pdo->prepare("SELECT blood_group FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$blood_group = $stmt->fetchColumn();

$requests = $pdo->prepare("
    SELECT br.*, CONCAT(p.first_name, ' ', p.last_name) AS patient_name
    FROM blood_requests br
    JOIN users p ON br.patient_id = p.id
    WHERE br.status = 'Approved'
    AND br.assigned_donor_id IS NULL
    AND br.blood_group = ?
    AND (
        (SELECT last_donation_date FROM donor_details WHERE user_id = ?) <= ?
        OR (SELECT last_donation_date FROM donor_details WHERE user_id = ?) IS NULL
    )
");
$requests->execute([$blood_group, $user_id, $ninety_days_ago, $user_id]);
$eligible_requests = $requests->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo">📍 Blood Bank - Donor</div>
    <div class="nav-links">
        <a href="donor-dashboard.php" class="nav-btn">Dashboard</a>
        <a href="logout.php" class="nav-btn">Logout</a>
    </div>
</header>

<div class="register-container">
    <div class="profile-header">
        <h2>👋 Hello, <?= htmlspecialchars($donor['first_name']) ?>!</h2>
        <p class="subtext">Here's your profile. You can update anything below.</p>
    </div>

    <form method="POST" action="update-profile.php" class="profile-form">
        <div class="form-row">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($donor['email']) ?>" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($donor['phone']) ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Weight (kg)</label>
                <input type="number" name="weight" value="<?= $donor['weight'] ?>" required>
            </div>
            <div class="form-group">
                <label>Last Donation Date</label>
                <input type="date" name="last_donation_date" value="<?= $donor['last_donation_date'] ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Medical Conditions</label>
            <textarea name="medical_conditions" rows="3"><?= htmlspecialchars($donor['medical_conditions']) ?></textarea>
        </div>

        <div class="form-group">
            <label>New Password (optional)</label>
            <input type="password" name="new_password" placeholder="Leave blank to keep unchanged">
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>

    <?php if ($matched): ?>
    <div class="register-card" style="margin-top: 2rem;">
        <h2>Last Matched Donation</h2>
        <p><strong>Patient:</strong> <?= htmlspecialchars($matched['patient_name']) ?></p>
        <p><strong>Blood Group:</strong> <?= $matched['blood_group'] ?></p>
        <p><strong>Units:</strong> <?= $matched['units_required'] ?></p>
        <p><strong>Required Date:</strong> <?= $matched['required_date'] ?></p>
        <p><strong>Reason:</strong> <?= htmlspecialchars($matched['reason']) ?></p>
        <p><strong>Action Date:</strong> <?= $matched['action_date'] ?></p>
    </div>
    <?php endif; ?>

    <?php if ($eligible_requests): ?>
    <div class="register-card" style="margin-top: 2rem;">
        <h2>Available Blood Requests</h2>
        <table>
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Units</th>
                    <th>Date</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($eligible_requests as $req): ?>
                <tr>
                    <td><?= htmlspecialchars($req['patient_name']) ?></td>
                    <td><?= $req['units_required'] ?></td>
                    <td><?= $req['required_date'] ?></td>
                    <td><?= htmlspecialchars($req['reason']) ?></td>
                    <td>
                        <form method="POST" action="accept-request.php">
                            <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                            <button class="btn btn-primary">Accept</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
