<?php
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminLogin.php'); // Redirect to admin login if not logged in
    exit();
}

$message = "";

// Reset all student forms to open
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_forms'])) {
    $reset_query = "UPDATE students SET form_locked = 0"; // Unlock all forms
    if ($conn->query($reset_query)) {
        $message = "All student forms have been reopened for the new semester.";
    } else {
        $message = "Error reopening forms. Please try again.";
    }
}

// Close all student forms
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['close_forms'])) {
    $close_query = "UPDATE students SET form_locked = 1"; // Lock all forms
    if ($conn->query($close_query)) {
        $message = "All student forms have been closed.";
    } else {
        $message = "Error closing forms. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Forms</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/createForm.css">
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>
    <!-- Include Sidebar -->
    <?php include 'adminSideBar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>Reset Enrollment Forms</h1>
            <?php if (!empty($message)) : ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <h3>New Semester Reset</h3>
                <p>Click the button below to unlock all student forms for the new semester.</p>
                <button type="submit" name="reset_forms" class="btn reset-btn" onclick="return confirm('Are you sure you want to reopen all forms?')">
                    Reopen All Forms
                </button>
            </form>

            <form action="" method="POST" style="margin-top: 20px;">
                <h3>Close All Forms</h3>
                <p>Click the button below to lock all student forms.</p>
                <button type="submit" name="close_forms" class="btn close-btn" onclick="return confirm('Are you sure you want to close all forms?')">
                    Close All Forms
                </button>
            </form>
        </div>
    </div>
</body>
</html>
