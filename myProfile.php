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

// Process the form submission when the student updates their details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_first_name = $_POST['first_name'];
    $new_last_name = $_POST['last_name'];

    // Update basic info (first_name, last_name)
    $update_query = "UPDATE students SET first_name = ?, last_name = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param('ssi', $new_first_name, $new_last_name, $student_id);
    $stmt->execute();
    $stmt->close();

    // Update additional fields if submitted
    if (!empty($_POST['dynamic_fields'])) {
        foreach ($_POST['dynamic_fields'] as $field_name => $field_value) {
            $update_details_query = "INSERT INTO student_details (student_id, field_name, field_value) 
                                      VALUES (?, ?, ?) 
                                      ON DUPLICATE KEY UPDATE field_value = ?";
            $stmt = $conn->prepare($update_details_query);
            if (!$stmt) {
                die("Error preparing statement: " . $conn->error);
            }
            $stmt->bind_param('isss', $student_id, $field_name, $field_value, $field_value);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Redirect back to profile after update
    header('Location: myProfile.php');
    exit();
}

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="css/myProfile.css"> 
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>

    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="profile-container">
        <h1>My Profile</h1>
        
        <!-- Basic Information -->
        <div class="section">
            <h2>Basic Information</h2>
            <div class="fields-list">
                <div class="field-item">
                    <strong>First Name:</strong>
                    <span><?php echo htmlspecialchars($first_name); ?></span>
                </div>
                <div class="field-item">
                    <strong>Last Name:</strong>
                    <span><?php echo htmlspecialchars($last_name); ?></span>
                </div>
                <div class="field-item">
                    <strong>Email:</strong>
                    <span><?php echo htmlspecialchars($email); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Additional Information -->
        <div class="section">
            <h2>Additional Information</h2>
            <?php if (!empty($dynamic_fields)): ?>
                <div class="fields-list">
                    <?php foreach ($dynamic_fields as $field): ?>
                        <div class="field-item">
                            <strong><?php echo htmlspecialchars($field['field_name']); ?>:</strong>
                            <span><?php echo htmlspecialchars($field['field_value']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
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
