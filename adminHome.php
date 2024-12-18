<?php
include 'db_connection.php';
session_start();

// Check if the admin is logged in
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
//     header('Location: login.php');
//     exit;
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <!-- Link to External CSS File -->
    <link rel="stylesheet" href="css/adminHome.css">
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Include Sidebar -->
    <?php include 'adminSideBar.php'; ?>

    <main>
        <header>
            <h1>Welcome Home, Admin!</h1>
            <p class="welcome-text">You have full control over the system, manage students, and view analytics right here.</p>
        </header>

        <!-- Overview Section -->
        <!-- <section class="overview">
            <h2>Dashboard Overview</h2>
            <div class="overview-content">
                <div class="stat-box">
                    <h3>Total Students</h3>
                    <p class="stat-value">120</p>
                </div>
                <div class="stat-box">
                    <h3>Active Enrollments</h3>
                    <p class="stat-value">30</p>
                </div>
                <div class="stat-box">
                    <h3>System Status</h3>
                    <p class="stat-value">Running Smoothly</p>
                </div>
            </div>
        </section> -->

        <!-- Quick Links Section -->
        <!-- <section class="quick-links">
            <h2>Quick Links</h2>
            <div class="link-boxes">
                <div class="link-box">
                    <h3>Manage Students</h3>
                    <p>View, edit, and manage student profiles.</p>
                    <a href="studentManagement.php" class="btn">Go to Students</a>
                </div>
                <div class="link-box">
                    <h3>Settings</h3>
                    <p>Update the system configuration and preferences.</p>
                    <a href="settings.php" class="btn">Go to Settings</a>
                </div>
                <div class="link-box">
                    <h3>View Reports</h3>
                    <p>Analyze and generate reports for system data.</p>
                    <a href="reports.php" class="btn">View Reports</a>
                </div>
            </div>
        </section> -->

        <!-- Latest Activity Section -->
        <!-- <section class="latest-activity">
            <h2>Recent Updates</h2>
            <div class="activity-feed">
                <div class="activity-item">
                    <h3>New Enrollment Requests</h3>
                    <p>There are 15 new enrollment requests waiting for your approval.</p>
                    <a href="studentManagement.php" class="btn">Manage Requests</a>
                </div>
                <div class="activity-item">
                    <h3>System Update Completed</h3>
                    <p>The latest system update has been successfully applied.</p>
                    <a href="settings.php" class="btn">View Update Log</a>
                </div>
                <div class="activity-item">
                    <h3>Upcoming Maintenance</h3>
                    <p>Scheduled system maintenance will occur next weekend.</p>
                    <a href="settings.php" class="btn">View Maintenance Schedule</a>
                </div>
            </div>
        </section> -->
    </main>
</body>
</html>
