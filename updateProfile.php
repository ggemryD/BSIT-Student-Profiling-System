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

    // Update dynamic fields (additional fields) if submitted
    if (!empty($_POST['dynamic_fields'])) {
        foreach ($_POST['dynamic_fields'] as $field_name => $field_value) {
            // Skip core fields that are directly stored in students table
            if (in_array($field_name, ['first_name', 'last_name'])) {
                continue; // These are already handled
            }

            // Use INSERT ON DUPLICATE KEY UPDATE to prevent duplication in student_details table
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
    <title>Update Profile</title>
    <link rel="stylesheet" href="css/myProfile.css"> <!-- Link your CSS file -->
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>

    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="profile-container">
        <h1>Update Profile</h1>
        
        <!-- Display basic information -->
        <form method="POST" action="updateProfile.php">
            <div class="basic-info">
                <h2>Basic Information</h2>
                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>

                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>

                <label for="email">Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" disabled>
            </div>

            <!-- Dynamic Fields -->
            <div class="dynamic-info">
                <h2>Additional Information</h2>
                <?php foreach ($dynamic_fields as $field): ?>
                    <label for="<?php echo $field['field_name']; ?>"><?php echo $field['field_name']; ?>:</label>
                    <input type="text" name="dynamic_fields[<?php echo $field['field_name']; ?>]" 
                           value="<?php echo htmlspecialchars($field['field_value']); ?>">
                <?php endforeach; ?>
            </div>

            <!-- Update Button -->
            <div class="update-btn-container">
                <button type="submit">Update Profile</button>
            </div>
        </form>
    </div>

</body>
</html>
