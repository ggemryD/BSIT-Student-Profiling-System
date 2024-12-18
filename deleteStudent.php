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

$student_id = intval($_GET['id']); // Sanitize the student ID

// First, delete the student's related details from student_details table
$deleteDetailsQuery = "DELETE FROM student_details WHERE student_id = ?";
$stmt = $conn->prepare($deleteDetailsQuery);
$stmt->bind_param("i", $student_id);
$stmt->execute();

// Then, delete the student record from students table
$deleteStudentQuery = "DELETE FROM students WHERE id = ?";
$stmt = $conn->prepare($deleteStudentQuery);
$stmt->bind_param("i", $student_id);

if ($stmt->execute()) {
    // Redirect back to the student management page
    header('Location: studentManagement.php');
    exit();
} else {
    die("Error deleting student: " . $conn->error);
}

if ($stmt->execute()) {
    $_SESSION['message'] = "Student deleted successfully!";
    header('Location: studentManagement.php');
    exit();
} else {
    $_SESSION['message'] = "Error deleting student.";
    header('Location: studentManagement.php');
    exit();
}

?>
