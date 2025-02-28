<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: adminLogin.php');
    exit();
}

// Unlock all student forms
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = "UPDATE students SET form_locked = FALSE";
    if ($conn->query($query)) {
        $message = "Forms unlocked for all students!";
    } else {
        $message = "Error unlocking forms.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Control</title>
</head>
<body>
    <h1>Admin Panel</h1>
    <?php if (!empty($message)) : ?>
        <div><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST">
        <button type="submit">Unlock Forms for All Students</button>
    </form>
</body>
</html>
