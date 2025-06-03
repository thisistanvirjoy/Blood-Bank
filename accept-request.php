<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: donor-login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $donor_id = $_SESSION['user_id'];

    // Lock the request to this donor (if not already taken)
    $stmt = $pdo->prepare("
        UPDATE blood_requests
        SET status = 'Matched', assigned_donor_id = ?, action_date = NOW()
        WHERE id = ? AND status = 'Approved' AND assigned_donor_id IS NULL
    ");
    $success = $stmt->execute([$donor_id, $request_id]);

    if ($success && $stmt->rowCount() > 0) {
        // Update last donation date
        $pdo->prepare("UPDATE donor_details SET last_donation_date = NOW() WHERE user_id = ?")
            ->execute([$donor_id]);

        header("Location: donor-dashboard.php?accepted=1");
        exit;
    } else {
        // Request already taken
        header("Location: donor-dashboard.php?error=already_taken");
        exit;
    }
} else {
    // Invalid access
    header("Location: donor-dashboard.php");
    exit;
}
