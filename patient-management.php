<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit;
}

$patients = $pdo->query("
    SELECT u.id, u.first_name, u.last_name, u.email, u.blood_group, u.gender, u.phone, u.date_of_birth,
           p.diagnosis, p.doctor_name, p.hospital_name, p.status
    FROM users u
    JOIN patient_details p ON u.id = p.user_id
    WHERE u.role = 'patient'
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Management - Blood Bank</title>
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
            <li><a href="donor-management.php">👤 Donor</a></li>
            <li><a href="patient-management.php" class="active">🏥 Patient</a></li>
            <li><a href="donations.php">💉 Donations</a></li>
            <li><a href="blood-requests.php">📋 Blood Requests</a></li>
            <li><a href="request-history.php">📜 Request History</a></li>
            <li><a href="blood-stock.php">🩸 Blood Stock</a></li>
        </ul>
    </nav>

    <main class="content">
        <div class="page-header">
            <h2>Patient Management</h2>
        </div>

        <div class="patient-list">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Blood Group</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Diagnosis</th>
                        <th>Doctor</th>
                        <th>Hospital</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($patients as $patient): 
                    $age = date_diff(date_create($patient['date_of_birth']), date_create('today'))->y;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></td>
                        <td><?= htmlspecialchars($patient['email']) ?></td>
                        <td><?= htmlspecialchars($patient['blood_group']) ?></td>
                        <td><?= $age ?></td>
                        <td><?= htmlspecialchars($patient['gender']) ?></td>
                        <td><?= htmlspecialchars($patient['phone']) ?></td>
                        <td><?= htmlspecialchars($patient['diagnosis']) ?></td>
                        <td><?= htmlspecialchars($patient['doctor_name']) ?></td>
                        <td><?= htmlspecialchars($patient['hospital_name']) ?></td>
                        <td><?= htmlspecialchars($patient['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
