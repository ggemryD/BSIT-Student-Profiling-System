
<?php
include 'db_connection.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['submit'])) {
    $emailOrUsername = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    // Check if the input matches the admin username
    if ($emailOrUsername === 'admin') {
        // Query admin table
        $stmt = $conn->prepare("SELECT * FROM `admin` WHERE username = ?");
        $stmt->bind_param("s", $emailOrUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();

            // Verify the admin password
            if ($pass === $admin['password']) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['username'];

                header('location: adminDashboard.php'); // Redirect to admin dashboard
                exit;
            } else {
                $message[] = 'Incorrect admin password!';
            }
        } else {
            $message[] = 'Admin account not found!';
        }
    } else {
        // Check for student login
        $stmt = $conn->prepare("SELECT * FROM `students` WHERE email = ?");
        $stmt->bind_param("s", $emailOrUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verify the student's password
            if (password_verify($pass, $row['password'])) {
                $_SESSION['student_firstName'] = $row['first_name'];
                $_SESSION['student_lastName'] = $row['last_name'];
                $_SESSION['student_email'] = $row['email'];
                $_SESSION['student_id'] = $row['id'];

                // Check if the form is locked for this student
                if ($row['form_locked'] == 0) {
                    header('location: enroll.php'); // Redirect to enrollment page
                } else {
                    header('location: home.php'); // Redirect to home page
                }
                exit;
            } else {
                $message[] = 'Incorrect email or password!';
            }
        } else {
            $message[] = 'Incorrect email or password!';
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
    <title>Login</title>
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

    <!-- Loading Screen -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <!-- <p style="color: white; margin-top: 10px;">Loading, please wait...</p> -->
    </div>

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
            <h3>Login</h3>
            <!-- <input type="email" name="email" placeholder="Enter your email" required class="box"> -->
            <input type="text" name="email" placeholder="Enter your email" required class="box">
            <input type="password" name="password" placeholder="Enter your password" required class="box">
            <input type="submit" name="submit" value="Login Now" class="btn">
            <p>Don't have an account? <a href="register.php">Register now</a></p>
            <!-- <a href="index.php" class="back-home">Back to Home</a> -->
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

    const form = document.querySelector('form');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Show the loading screen for 2 seconds, then submit the form
    form.addEventListener('submit', function () {
        loadingOverlay.classList.add('show'); // Show loading spinner

        // Delay for 2 seconds before redirecting
        setTimeout(() => {
            loadingOverlay.classList.remove('show'); // Hide loading spinner
        }, 2000); // 2 seconds delay
    });

</script>

