<?php
include 'db_connection.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <!-- Link to External CSS File -->
    <link rel="stylesheet" href="css/adminHome.css">
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
    
    <!-- Font Awesome for icons -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> -->
</head>
<body>
    <!-- Include Sidebar -->
    <?php include 'adminSideBar.php'; ?>

    <main>
        <header>
            <h1>Welcome Home, Admin!</h1>
            <p class="welcome-text">You have full control over the system, manage students, and view analytics right here.</p>
        </header>

    </main>
</body>
</html>
