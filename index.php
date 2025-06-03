<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome - Blood Bank Management System</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <div class="logo">
        <span class="location-icon">📍</span>
        Blood Bank Management System
    </div>
    <div class="nav-links">
        <a href="register.php" class="nav-btn">Register</a>
        <a href="donor-login.php" class="nav-btn">Donor Login</a>
        <a href="patient-login.php" class="nav-btn">Patient Login</a>
        <a href="admin-login.php" class="nav-btn">Admin Login</a>
    </div>
</header>

<div class="hero">
    <div class="hero-content">
        <h1>Save Lives Through Blood Donation</h1>
        <p>Your single donation can save up to three lives. Join our mission to help those in need and make a difference in your community.</p>
        <div class="hero-buttons">
            <a href="register.php" class="btn btn-primary">Become a Donor Today</a>
            <a href="#blood-availability" class="btn btn-secondary">Check Blood Availability</a>
        </div>
    </div>
</div>

<div class="features">
    <div class="feature-card">
        <span class="feature-icon">🩸</span>
        <h3>Safe Donation Process</h3>
        <p>Modern equipment and trained professionals ensure a safe donation experience</p>
    </div>
    <div class="feature-card">
        <span class="feature-icon">🏥</span>
        <h3>24/7 Emergency Service</h3>
        <p>Round-the-clock support for urgent blood requirements</p>
    </div>
    <div class="feature-card">
        <span class="feature-icon">📱</span>
        <h3>Digital Management</h3>
        <p>Track donations and requests through our online platform</p>
    </div>
    <div class="feature-card">
        <span class="feature-icon">📋</span>
        <h3>Quick Registration</h3>
        <p>Simple and fast process to register as a donor or patient</p>
    </div>
</div>

<div id="blood-availability" class="blood-availability">
    <h2>Current Blood Availability</h2>
    <div class="blood-groups">
        <div class="blood-card"><h2>A+ <span class="blood-drop">🩸</span></h2><p>Available</p></div>
        <div class="blood-card"><h2>B+ <span class="blood-drop">🩸</span></h2><p>Available</p></div>
        <div class="blood-card"><h2>O+ <span class="blood-drop">🩸</span></h2><p>Limited</p></div>
        <div class="blood-card"><h2>AB+ <span class="blood-drop">🩸</span></h2><p>Available</p></div>
        <div class="blood-card"><h2>A- <span class="blood-drop">🩸</span></h2><p>Limited</p></div>
        <div class="blood-card"><h2>B- <span class="blood-drop">🩸</span></h2><p>Critical</p></div>
        <div class="blood-card"><h2>O- <span class="blood-drop">🩸</span></h2><p>Available</p></div>
        <div class="blood-card"><h2>AB- <span class="blood-drop">🩸</span></h2><p>Limited</p></div>
    </div>
</div>

<div class="why-donate">
    <h2>Why Donate Blood?</h2>
    <div class="reasons-grid">
        <div class="reason-card">
            <span class="reason-icon">❤️</span>
            <h3>Save Lives</h3>
            <p>Your donation can save up to three lives in critical need</p>
        </div>
        <div class="reason-card">
            <span class="reason-icon">🏥</span>
            <h3>High Demand</h3>
            <p>Someone needs blood every two seconds</p>
        </div>
        <div class="reason-card">
            <span class="reason-icon">🎗️</span>
            <h3>Community Impact</h3>
            <p>Make a direct difference in your local community</p>
        </div>
        <div class="reason-card">
            <span class="reason-icon">🩺</span>
            <h3>Health Check</h3>
            <p>Receive a mini health screening with every donation</p>
        </div>
    </div>
</div>

<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>Emergency Contact</h3>
            <p>📞 Hotline: 01812345678</p>
            <p>📧 Email: emergency@bloodbank.com</p>
            <p>🚑 Available 24/7 for emergencies</p>
        </div>
        <div class="footer-section">
            <h3>Quick Links</h3>
            <a href="register.php">Register as Donor</a>
            <a href="donor-login.php">Donor Login</a>
            <a href="patient-login.php">Patient Login</a>
            <a href="#blood-availability">Blood Availability</a>
        </div>
        <div class="footer-section">
            <h3>Location</h3>
            <p>Sugandha Abashik</p>
            <p>Chittagong</p>
            <p>Open Monday - Sunday</p>
            <p>8:00 AM - 8:00 PM</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 Blood Bank Management System. All rights reserved by Sadia Tasnima Shemu.</p>
    </div>
</footer>
</body>
</html>
