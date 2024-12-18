<?php
include 'db_connection.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['submit'])) {
    // Get username and password from POST request
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Query to check admin credentials in the database
    $query = "SELECT * FROM admin WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        
        // Check if the password matches directly
        if ($password == $admin['password']) {
            // Set session variables for the admin
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['username']; // Assuming the admin table has a 'username' field
            
            // Redirect to admin home page
            header('location:adminHome.php');
            exit;
        } else {
            $message[] = 'Incorrect username or password!';
        }
    } else {
        $message[] = 'Admin account not found!';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/adminLogin.css">
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>

    <?php
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
        <img src="image/bsitLogo2.png" alt="Logo">
        <h3>Admin Login</h3>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Enter your username" required class="box">
            <input type="password" name="password" placeholder="Enter your password" required class="box">
            <input type="submit" name="submit" value="Login Now" class="btn">
            <p><a href="index.php" class="back-home">Back to Home</a></p>
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

