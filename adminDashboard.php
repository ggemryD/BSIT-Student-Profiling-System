<?php
// Start session and include database connection
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminLogin.php'); // Redirect to login if not logged in
    exit();
}

// Fetch total students, male and female counts, and year levels
$query = "
    SELECT 
        COUNT(DISTINCT students.id) AS total_students,
        COUNT(DISTINCT CASE 
            WHEN LOWER(TRIM(student_details.field_name)) = 'gender' 
            AND LOWER(TRIM(student_details.field_value)) = 'male' 
            THEN students.id END) AS male_students,
        COUNT(DISTINCT CASE 
            WHEN LOWER(TRIM(student_details.field_name)) = 'gender' 
            AND LOWER(TRIM(student_details.field_value)) = 'female' 
            THEN students.id END) AS female_students,
        COUNT(DISTINCT CASE 
            WHEN LOWER(TRIM(student_details.field_name)) = 'year_level' 
            AND LOWER(TRIM(student_details.field_value)) = '1st year' 
            THEN students.id END) AS first_year_students,
        COUNT(DISTINCT CASE 
            WHEN LOWER(TRIM(student_details.field_name)) = 'year_level' 
            AND LOWER(TRIM(student_details.field_value)) = '2nd year' 
            THEN students.id END) AS second_year_students,
        COUNT(DISTINCT CASE 
            WHEN LOWER(TRIM(student_details.field_name)) = 'year_level' 
            AND LOWER(TRIM(student_details.field_value)) = '3rd year' 
            THEN students.id END) AS third_year_students,
        COUNT(DISTINCT CASE 
            WHEN LOWER(TRIM(student_details.field_name)) = 'year_level' 
            AND LOWER(TRIM(student_details.field_value)) = '4th year' 
            THEN students.id END) AS fourth_year_students
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/adminDashboard.css">
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>

<body>
    <?php include 'adminSideBar.php'; ?>

    <div class="dashboard-container">
        <h1>Admin Dashboard</h1>
        <div class="stats-grid">
            <div class="stat-card" style="--animation-order: 1">
                <h2>Total Students</h2>
                <p><?php echo $data['total_students'] ?? 0; ?></p>
            </div>
            <div class="stat-card" style="--animation-order: 2">
                <h2>Male Students</h2>
                <p><?php echo $data['male_students'] ?? 0; ?></p>
            </div>
            <div class="stat-card" style="--animation-order: 3">
                <h2>Female Students</h2>
                <p><?php echo $data['female_students'] ?? 0; ?></p>
            </div>
            <div class="stat-card" style="--animation-order: 4">
                <h2>1st Year Students</h2>
                <p><?php echo $data['first_year_students'] ?? 0; ?></p>
            </div>
            <div class="stat-card" style="--animation-order: 5">
                <h2>2nd Year Students</h2>
                <p><?php echo $data['second_year_students'] ?? 0; ?></p>
            </div>
            <div class="stat-card" style="--animation-order: 6">
                <h2>3rd Year Students</h2>
                <p><?php echo $data['third_year_students'] ?? 0; ?></p>
            </div>
            <div class="stat-card" style="--animation-order: 7">
                <h2>4th Year Students</h2>
                <p><?php echo $data['fourth_year_students'] ?? 0; ?></p>
            </div>
        </div>
    </div>
</body>
</html>
