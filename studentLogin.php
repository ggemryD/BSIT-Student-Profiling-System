<?php
include 'db_connection.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['submit'])) {
    // Get user input and sanitize it
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password']; // Get the password from POST

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM `students` WHERE email = ?");
    $stmt->bind_param("s", $email);  // "s" means string
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any student matches the provided email
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password using password_verify
        if (password_verify($pass, $row['password'])) {
            // Password is correct, set session variables
            $_SESSION['student_firstName'] = $row['firstName'];
            $_SESSION['student_lastName'] = $row['lastName'];
            $_SESSION['student_email'] = $row['email'];
            $_SESSION['student_id'] = $row['id'];
            
            // Redirect to the home page after successful login
            header('location: home.php');
            exit;
        } else {
            // Password is incorrect
            $message[] = 'Incorrect email or password!';
        }
    } else {
        // No user found with the provided email
        $message[] = 'Incorrect email or password!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/studentLogin.css">
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">

    <style>
        .back-home {
            display: inline-block;
            margin-top: 1.5rem;
            color: #23374b;
            font-size: 1.2rem;
            text-decoration: underline;
            cursor: pointer;
        }

        .back-home:hover {
            color: #3c6186;
        }
    </style>
</head>
<body>

<img src="image/bsitLogo2.png" alt="Logo">

<?php
// Display error message if any
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="modal-overlay">
            <div class="modal">
                <span>' . htmlspecialchars($msg) . '</span>
                <button class="close-modal" onclick="closeModal()">Close</button>
            </div>
        </div>
        ';
    }
}
?>

<div class="form-container">
    <form action="" method="post">
        <h3>Student Login</h3>
        <input type="email" name="email" placeholder="Enter your email" required class="box">
        <input type="password" name="password" placeholder="Enter your password" required class="box">
        <input type="submit" name="submit" value="Login Now" class="btn">
        <p>Don't have an account? <a href="register.php">Register now</a></p>
        <a href="index.php" class="back-home">Back to Home</a>
    </form>
</div>

</body>
</html>

<script>
function closeModal() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
        modal.remove();
    }
}
</script>
