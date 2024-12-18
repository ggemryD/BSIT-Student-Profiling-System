<?php
// Start session and include database connection
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminLogin.php'); // Redirect to login if not logged in
    exit();
}

// Check if required fields exist in `student_details`
$required_fields = [
    'year_level' => false,
    'Gender' => false,
    'Enrollment_Status' => false
];

$field_check_query = "SELECT DISTINCT field_name FROM student_details";
$field_check_result = $conn->query($field_check_query);

if ($field_check_result) {
    while ($row = $field_check_result->fetch_assoc()) {
        $field_name = $row['field_name'];
        if (array_key_exists($field_name, $required_fields)) {
            $required_fields[$field_name] = true;
        }
    }
}

// Construct dynamic query based on available fields
$query = "
    SELECT 
        (SELECT COUNT(*) FROM students) AS total_students
";

if ($required_fields['year_level']) {
    $query .= ",
        SUM(CASE WHEN year_level = '1st Year' THEN 1 ELSE 0 END) AS first_year,
        SUM(CASE WHEN year_level = '2nd Year' THEN 1 ELSE 0 END) AS second_year,
        SUM(CASE WHEN year_level = '3rd Year' THEN 1 ELSE 0 END) AS third_year,
        SUM(CASE WHEN year_level = '4th Year' THEN 1 ELSE 0 END) AS fourth_year
    ";
} else {
    $query .= ",
        0 AS first_year, 
        0 AS second_year, 
        0 AS third_year, 
        0 AS fourth_year
    ";
}

if ($required_fields['Gender']) {
    $query .= ",
        SUM(CASE WHEN field_name = 'Gender' AND field_value = 'Male' THEN 1 ELSE 0 END) AS male_students,
        SUM(CASE WHEN field_name = 'Gender' AND field_value = 'Female' THEN 1 ELSE 0 END) AS female_students
    ";
} else {
    $query .= ",
        0 AS male_students, 
        0 AS female_students
    ";
}

if ($required_fields['Enrollment_Status']) {
    $query .= ",
        SUM(CASE WHEN field_name = 'Enrollment_Status' AND field_value = 'Regular' THEN 1 ELSE 0 END) AS regular_students,
        SUM(CASE WHEN field_name = 'Enrollment_Status' AND field_value = 'Irregular' THEN 1 ELSE 0 END) AS irregular_students
    ";
} else {
    $query .= ",
        0 AS regular_students, 
        0 AS irregular_students
    ";
}

$query .= " FROM students LEFT JOIN student_details ON students.id = student_details.student_id";

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
    <link rel="stylesheet" href="css/adminDashboard.css">
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>
    <?php include 'adminSideBar.php'; ?>

    <div class="dashboard-container">
        <h1>Admin Dashboard</h1>
        <div class="stats-grid">
            <div class="stat-card">
                <h2>Total Students</h2>
                <p><?php echo $data['total_students'] ?? 0; ?></p>
            </div>
            <?php if ($required_fields['year_level']): ?>
                <div class="stat-card">
                    <h2>1st Year Students</h2>
                    <p><?php echo $data['first_year'] ?? 0; ?></p>
                </div>
                <div class="stat-card">
                    <h2>2nd Year Students</h2>
                    <p><?php echo $data['second_year'] ?? 0; ?></p>
                </div>
                <div class="stat-card">
                    <h2>3rd Year Students</h2>
                    <p><?php echo $data['third_year'] ?? 0; ?></p>
                </div>
                <div class="stat-card">
                    <h2>4th Year Students</h2>
                    <p><?php echo $data['fourth_year'] ?? 0; ?></p>
                </div>
            <?php endif; ?>
            <?php if ($required_fields['Gender']): ?>
                <div class="stat-card">
                    <h2>Male Students</h2>
                    <p><?php echo $data['male_students'] ?? 0; ?></p>
                </div>
                <div class="stat-card">
                    <h2>Female Students</h2>
                    <p><?php echo $data['female_students'] ?? 0; ?></p>
                </div>
            <?php endif; ?>
            <?php if ($required_fields['Enrollment_Status']): ?>
                <div class="stat-card">
                    <h2>Regular Students</h2>
                    <p><?php echo $data['regular_students'] ?? 0; ?></p>
                </div>
                <div class="stat-card">
                    <h2>Irregular Students</h2>
                    <p><?php echo $data['irregular_students'] ?? 0; ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

