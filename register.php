<?php
include 'db_connection.php';

if (isset($_POST['submit'])) {
    // Collect and sanitize form data
    $first_name = trim(mysqli_real_escape_string($conn, $_POST['first_name']));
    $last_name = trim(mysqli_real_escape_string($conn, $_POST['last_name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['cpassword']);

    // Initialize an array for messages
    $messages = [];

    // Validate form inputs
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $messages[] = 'All fields are required!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messages[] = 'Invalid email format!';
    } elseif ($password !== $confirm_password) {
        $messages[] = 'Passwords do not match!';
    } else {
        // Check if email is already registered
        $query = "SELECT * FROM students WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $messages[] = 'This email is already registered!';
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the student data into the database
            $insert_query = "INSERT INTO students (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param('ssss', $first_name, $last_name, $email, $hashed_password);

            if ($insert_stmt->execute()) {
                $messages[] = 'Registration successful! You can now log in.';
            } else {
                $messages[] = 'Something went wrong. Please try again!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/register.css">
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h3>Student Registration</h3>
            
            <!-- Display messages -->
            <?php
            if (!empty($messages)) {
                foreach ($messages as $msg) {
                    $class = strpos($msg, 'successful') !== false ? 'success' : 'error';
                    echo '<div class="message ' . $class . '">' . htmlspecialchars($msg) . '</div>';
                }
            }
            ?>

            <form action="" method="post">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="cpassword" placeholder="Confirm Password" required>
                <input type="submit" name="submit" value="Register" class="btn">
            </form>

            <p class="redirect-text">
                Already have an account? <a href="studentLogin.php">Login here</a>
            </p>
        </div>
    </div>
</body>
</html>
