<?php
// Start session and include database connection
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminLogin.php'); // Redirect to login if not logged in
    exit();
}

// Check if 'id' is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Student ID is required.");
}

$student_id = intval($_GET['id']); // Sanitize input

// Fetch student's basic information
$studentQuery = "SELECT first_name, last_name, email, created_at FROM students WHERE id = ?";
$stmt = $conn->prepare($studentQuery);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$studentResult = $stmt->get_result();

if ($studentResult->num_rows === 0) {
    die("Student not found.");
}

$student = $studentResult->fetch_assoc();

// Fetch additional details from student_details table
$detailsQuery = "SELECT field_name, field_value FROM student_details WHERE student_id = ?";
$stmt = $conn->prepare($detailsQuery);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$detailsResult = $stmt->get_result();

// Format details into an associative array
$details = [];
while ($row = $detailsResult->fetch_assoc()) {
    $details[$row['field_name']] = $row['field_value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student</title>
    <link rel="stylesheet" href="css/viewStudent.css"> <!-- Link to updated CSS -->
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>
    <!-- Include Sidebar -->
    <?php include 'adminSideBar.php'; ?>

    <div class="view-student-container">
        <h1>View Student</h1>
        
        <div class="student-info">
            <!-- <h2>Basic Information</h2> -->
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($student['first_name']); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($student['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
            <p><strong>Account Created:</strong> <?php echo htmlspecialchars($student['created_at']); ?></p>
        </div>

        <div class="student-details">
            <h2>Additional Details</h2>
            <?php if (empty($details)): ?>
                <p>No additional details available.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($details as $field => $value): ?>
                        <li><strong><?php echo htmlspecialchars($field); ?>:</strong> <?php echo htmlspecialchars($value); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
        <a href="studentManagement.php" class="btn-back">Back to Student Management</a>
    </div>
</body>
</html>

