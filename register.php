<?php
require 'db.php';
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $dob = $_POST['dateOfBirth'];
    $gender = $_POST['gender'];
    $bloodGroup = $_POST['bloodGroup'];
    $weight = $_POST['weight'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        $message = "Passwords do not match!";
    } else {
        try {
            $pdo->beginTransaction();

            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (role, email, phone, password, first_name, last_name, date_of_birth, gender, blood_group, weight)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$role, $email, $phone, $hash, $firstName, $lastName, $dob, $gender, $bloodGroup, $weight]);
            $userId = $pdo->lastInsertId();

            if ($role === 'donor') {
                $lastDonation = $_POST['lastDonation'] ?? null;
                $medicalConditions = $_POST['diseases'] ?? 'None';
                $stmt = $pdo->prepare("INSERT INTO donor_details (user_id, last_donation_date, medical_conditions) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $lastDonation, $medicalConditions]);
            }

            if ($role === 'patient') {
                $diagnosis = $_POST['diagnosis'];
                $doctor = $_POST['doctorName'] ?? null;
                $hospital = $_POST['hospital'] ?? null;
                $stmt = $pdo->prepare("INSERT INTO patient_details (user_id, diagnosis, doctor_name, hospital_name) VALUES (?, ?, ?, ?)");
                $stmt->execute([$userId, $diagnosis, $doctor, $hospital]);
            }

            $pdo->commit();

            // Auto-login
            $_SESSION['user_id'] = $userId;
            $_SESSION['role'] = $role;
            $_SESSION['first_name'] = $firstName;

            // Redirect based on role
            header("Location: " . ($role === 'donor' ? "donor-dashboard.php" : "patient-dashboard.php"));
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "Registration failed: " . $e->getMessage();
        }
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Blood Bank</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function toggleFields() {
            const role = document.getElementById('role').value;
            document.getElementById('donorFields').style.display = role === 'donor' ? 'block' : 'none';
            document.getElementById('patientFields').style.display = role === 'patient' ? 'block' : 'none';
        }
    </script>
</head>
<body>
<header>
    <div class="logo">📍 Blood Bank Management System</div>
    <div class="nav-links"><a href="index.php" class="nav-btn">Home</a></div>
</header>

<div class="register-container">
    <div class="register-card">
        <h2>Registration</h2>
        <?php if ($message): ?>
            <p style="color:red;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST" class="register-form">
            <div class="form-group">
                <label for="role">Register as</label>
                <select id="role" name="role" required onchange="toggleFields()">
                    <option value="">Select Role</option>
                    <option value="donor">Donor</option>
                    <option value="patient">Patient</option>
                </select>
            </div>

            <div class="form-row">
                <input type="text" name="firstName" placeholder="First Name" required>
                <input type="text" name="lastName" placeholder="Last Name" required>
            </div>

            <div class="form-row">
                <input type="email" name="email" placeholder="Email" required>
                <input type="tel" name="phone" placeholder="Phone" required>
            </div>

            <div class="form-row">
                <input type="date" name="dateOfBirth" required>
                <select name="bloodGroup" required>
                    <option value="">Blood Group</option>
                    <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                    <option>O+</option><option>O-</option><option>AB+</option><option>AB-</option>
                </select>
            </div>

            <div class="form-row">
                <select name="gender" required>
                    <option value="">Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                <input type="number" name="weight" placeholder="Weight (kg)" min="45" required>
            </div>

            <!-- Donor-specific -->
            <div id="donorFields" style="display:none;">
                <label>Last Donation Date</label>
                <input type="date" name="lastDonation">
                <label>Any Medical Conditions</label>
                <textarea name="diseases" rows="3" placeholder="e.g. None or list conditions"></textarea>
            </div>

            <!-- Patient-specific -->
            <div id="patientFields" style="display:none;">
                <label>Diagnosis/Reason for Blood Request</label>
                <textarea name="diagnosis" rows="3"></textarea>
                <input type="text" name="doctorName" placeholder="Doctor's Name">
                <input type="text" name="hospital" placeholder="Hospital Name">
            </div>

            <div class="form-row">
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
            </div>

            <div class="form-group">
                <label><input type="checkbox" required> I agree to the terms and conditions</label>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</div>
</body>
</html>
