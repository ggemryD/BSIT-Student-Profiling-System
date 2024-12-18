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

// Handle the form submission for editing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated data from the form
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    // Update the student's basic information
    $updateQuery = "UPDATE students SET first_name = ?, last_name = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssi", $first_name, $last_name, $email, $student_id);
    if ($stmt->execute()) {
        echo "Student updated successfully!";
    } else {
        echo "Error updating student: " . $conn->error;
    }

    // Update additional details (if provided)
    foreach ($_POST['details'] as $field_name => $field_value) {
        $updateDetailsQuery = "UPDATE student_details SET field_value = ? WHERE student_id = ? AND field_name = ?";
        $stmt = $conn->prepare($updateDetailsQuery);
        $stmt->bind_param("sis", $field_value, $student_id, $field_name);
        $stmt->execute();
    }
    
    // Redirect back to the student management page
    header("Location: studentManagement.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="css/editStudent.css"> <!-- Link your CSS file -->
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'adminSideBar.php'; ?>

    <div class="edit-student-container">
        <h1>Edit Student</h1>

        <!-- Edit Student Form -->
        <form method="POST" action="editStudent.php?id=<?php echo $student_id; ?>">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>

            <!-- Additional Details -->
            <h3>Additional Details</h3>
            <?php foreach ($details as $field => $value): ?>
                <label for="<?php echo $field; ?>"><?php echo htmlspecialchars($field); ?>:</label>
                <input type="text" name="details[<?php echo $field; ?>]" value="<?php echo htmlspecialchars($value); ?>">
            <?php endforeach; ?>

            <button type="submit">Update Student</button>
        </form>

        <a href="studentManagement.php" class="btn-back">Back to Student Management</a>
    </div>
</body>
</html>
