<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['donor', 'patient'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Common fields
$email = $_POST['email'];
$phone = $_POST['phone'];
$weight = $_POST['weight'];

// Update common info
$pdo->prepare("UPDATE users SET email = ?, phone = ?, weight = ? WHERE id = ?")
    ->execute([$email, $phone, $weight, $user_id]);

// Optional password update
if (!empty($_POST['new_password'])) {
    $hashed = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hashed, $user_id]);
}

// Role-specific updates
if ($role === 'donor') {
    $last_donation = $_POST['last_donation_date'] ?: null;
    $conditions = $_POST['medical_conditions'] ?? null;

    $pdo->prepare("UPDATE donor_details SET last_donation_date = ?, medical_conditions = ? WHERE user_id = ?")
        ->execute([$last_donation, $conditions, $user_id]);

    header("Location: donor-dashboard.php?updated=1");
    exit;

} elseif ($role === 'patient') {
    $dob = $_POST['dob'];
    $diagnosis = $_POST['diagnosis'] ?? null;
    $doctor = $_POST['doctor_name'] ?? null;
    $hospital = $_POST['hospital_name'] ?? null;

    // Update patient-specific info
    $pdo->prepare("UPDATE users SET date_of_birth = ? WHERE id = ?")->execute([$dob, $user_id]);
    $pdo->prepare("UPDATE patient_details SET diagnosis = ?, doctor_name = ?, hospital_name = ? WHERE user_id = ?")
        ->execute([$diagnosis, $doctor, $hospital, $user_id]);

    header("Location: patient-dashboard.php?updated=1");
    exit;
}
