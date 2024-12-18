<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing

    // Check if email already exists
    $checkEmailQuery = "SELECT * FROM students WHERE email = '$email'";
    $result = $conn->query($checkEmailQuery);

    if ($result->num_rows > 0) {
        echo "Error: Email already exists!";
    } else {
        // Insert new student
        $query = "INSERT INTO students (first_name, last_name, email, password) VALUES ('$first_name', '$last_name', '$email', '$password')";
        
        if ($conn->query($query)) {
            echo "Student added successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>
