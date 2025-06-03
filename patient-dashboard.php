<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: patient-login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch patient info
$stmt = $pdo->prepare("
    SELECT u.first_name, u.last_name, u.email, u.phone, u.gender, u.blood_group, u.date_of_birth, u.weight,
           p.diagnosis, p.doctor_name, p.hospital_name, p.status
    FROM users u
    JOIN patient_details p ON u.id = p.user_id
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$patient = $stmt->fetch();

// Matched donor info
$donation_stmt = $pdo->prepare("
    SELECT r.*, CONCAT(d.first_name, ' ', d.last_name) AS donor_name, d.phone AS donor_phone, d.email AS donor_email
    FROM blood_requests r
    JOIN users d ON r.assigned_donor_id = d.id
    WHERE r.patient_id = ? AND r.status = 'Matched'
    ORDER BY r.action_date DESC
    LIMIT 1
");
$donation_stmt->execute([$user_id]);
$matched_donation = $donation_stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo">📍 Blood Bank - Patient</div>
    <div class="nav-links">
        <a href="request-blood.php" class="nav-btn">Request Blood</a>
        <a href="logout.php" class="nav-btn">Logout</a>
    </div>
</header>

<div class="register-container">
    <div class="profile-header">
        <h2>👋 Hello, <?= htmlspecialchars($patient['first_name']) ?>!</h2>
        <p class="subtext">You can view and update your medical profile here.</p>
    </div>

    <form method="POST" action="update-profile.php" class="profile-form">
        <div class="form-row">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($patient['email']) ?>" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($patient['phone']) ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Weight (kg)</label>
                <input type="number" name="weight" value="<?= $patient['weight'] ?>" required>
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?= $patient['date_of_birth'] ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Diagnosis / Blood Issue</label>
            <textarea name="diagnosis" rows="3"><?= htmlspecialchars($patient['diagnosis']) ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Doctor's Name</label>
                <input type="text" name="doctor_name" value="<?= htmlspecialchars($patient['doctor_name']) ?>">
            </div>
            <div class="form-group">
                <label>Hospital</label>
                <input type="text" name="hospital_name" value="<?= htmlspecialchars($patient['hospital_name']) ?>">
            </div>
        </div>

        <div class="form-group">
            <label>New Password (optional)</label>
            <input type="password" name="new_password" placeholder="Leave blank to keep unchanged">
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>

    <?php if ($matched_donation): ?>
    <div class="register-card" style="margin-top: 2rem;">
        <h2>Your Matched Donor</h2>
        <p><strong>Donor Name:</strong> <?= htmlspecialchars($matched_donation['donor_name']) ?></p>
        <p><strong>Contact Email:</strong> <?= htmlspecialchars($matched_donation['donor_email']) ?></p>
        <p><strong>Contact Phone:</strong> <?= htmlspecialchars($matched_donation['donor_phone']) ?></p>
        <p><strong>Units:</strong> <?= $matched_donation['units_required'] ?></p>
        <p><strong>Required Date:</strong> <?= $matched_donation['required_date'] ?></p>
        <p><strong>Reason:</strong> <?= htmlspecialchars($matched_donation['reason']) ?></p>
        <p><strong>Matched On:</strong> <?= $matched_donation['action_date'] ?></p>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
