<?php
// Start session and include database connection
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminLogin.php'); // Redirect to login if not logged in
    exit();
}

// Fetch counts for dashboard using a single optimized query
$query = "
    SELECT 
        (SELECT COUNT(*) FROM students) AS total_students,
        SUM(CASE WHEN year_level = '1st Year' THEN 1 ELSE 0 END) AS first_year,
        SUM(CASE WHEN year_level = '2nd Year' THEN 1 ELSE 0 END) AS second_year,
        SUM(CASE WHEN year_level = '3rd Year' THEN 1 ELSE 0 END) AS third_year,
        SUM(CASE WHEN year_level = '4th Year' THEN 1 ELSE 0 END) AS fourth_year,
        SUM(CASE WHEN field_name = 'Gender' AND field_value = 'Male' THEN 1 ELSE 0 END) AS male_students,
        SUM(CASE WHEN field_name = 'Gender' AND field_value = 'Female' THEN 1 ELSE 0 END) AS female_students,
        SUM(CASE WHEN field_name = 'Enrollment_Status' AND field_value = 'Regular' THEN 1 ELSE 0 END) AS regular_students,
        SUM(CASE WHEN field_name = 'Enrollment_Status' AND field_value = 'Irregular' THEN 1 ELSE 0 END) AS irregular_students
    FROM students
    LEFT JOIN student_details ON students.id = student_details.student_id
";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching data: " . $conn->error);
}

$data = $result->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/adminDashboard.css"> <!-- Link your CSS file -->
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>

    <!-- Include Navbar -->
    <?php include 'adminNavbar.php'; ?>

    <div class="dashboard-container">
        <h1>Admin Dashboard</h1>
        <div class="stats-grid">
            <div class="stat-card">
                <h2>Total Students</h2>
                <p><?php echo $data['total_students']; ?></p>
            </div>
            <div class="stat-card">
                <h2>1st Year Students</h2>
                <p><?php echo $data['first_year']; ?></p>
            </div>
            <div class="stat-card">
                <h2>2nd Year Students</h2>
                <p><?php echo $data['second_year']; ?></p>
            </div>
            <div class="stat-card">
                <h2>3rd Year Students</h2>
                <p><?php echo $data['third_year']; ?></p>
            </div>
            <div class="stat-card">
                <h2>4th Year Students</h2>
                <p><?php echo $data['fourth_year']; ?></p>
            </div>
            <div class="stat-card">
                <h2>Male Students</h2>
                <p><?php echo $data['male_students']; ?></p>
            </div>
            <div class="stat-card">
                <h2>Female Students</h2>
                <p><?php echo $data['female_students']; ?></p>
            </div>
            <div class="stat-card">
                <h2>Regular Students</h2>
                <p><?php echo $data['regular_students']; ?></p>
            </div>
            <div class="stat-card">
                <h2>Irregular Students</h2>
                <p><?php echo $data['irregular_students']; ?></p>
            </div>
        </div>
    </div>
</body>
</html>
