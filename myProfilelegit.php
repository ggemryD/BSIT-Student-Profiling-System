<?php
// Start session and include database connection
session_start();
include 'db_connection.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: studentLogin.php'); // Redirect to login if not logged in
    exit();
}

// Get logged-in student's ID
$student_id = $_SESSION['student_id'];

// Fetch student basic information
$query = "SELECT first_name, last_name, email FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param('i', $student_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email);
$stmt->fetch();
$stmt->close();

// Fetch dynamic form fields and their values
$query = "
    SELECT f.field_name, f.field_type, 
           COALESCE(d.field_value, 'Not Provided') AS field_value 
    FROM form_fields f 
    LEFT JOIN student_details d 
    ON f.field_name = d.field_name AND d.student_id = ?
    ORDER BY f.id";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$dynamic_fields = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch any additional dynamic fields that the student submitted
$query = "
    SELECT field_name, field_value
    FROM student_details
    WHERE student_id = ?";  // Ensure we're getting all student-specific data
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param('i', $student_id);
$stmt->execute();
$student_details_result = $stmt->get_result();
$student_details = $student_details_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="css/myProfile.css"> <!-- Link your CSS file -->
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>

    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="profile-container">
        <h1>My Profile</h1>
        
        <!-- Display basic information -->
        <div class="basic-info">
            <h2>Basic Information</h2>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($first_name); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($last_name); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        </div>
        
        <!-- Display dynamic form fields and their values -->
        <div class="dynamic-info">
            <h2>Additional Information</h2>
            <?php if (!empty($student_details)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Field Name</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($student_details as $field): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($field['field_name']); ?></td>
                                <td><?php echo htmlspecialchars($field['field_value']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No additional information available.</p>
            <?php endif; ?>
        </div>
        
        <!-- Update Profile Button -->
        <div class="update-btn-container">
            <a href="updateProfile.php" class="update-btn">Update Profile</a>
        </div>

    </div>
</body>
</html>
