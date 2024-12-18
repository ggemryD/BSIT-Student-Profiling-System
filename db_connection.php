<?php
// Database configurations
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_profiling";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

//define('ADMIN_PASSWORD', password_hash('admin', PASSWORD_DEFAULT)); // Hashing the password


// Check connection
// if ($conn->connect_error) {
//   die("Connection failed: " . $conn->connect_error);
// }

// // Predefined admin credentials
// if (!defined('ADMIN_EMAIL')) {
//     define('ADMIN_EMAIL', 'admin');
// }

// if (!defined('ADMIN_PASSWORD')) {
//     define('ADMIN_PASSWORD', password_hash('admin123', PASSWORD_DEFAULT)); // Hashing the password
// }

?>