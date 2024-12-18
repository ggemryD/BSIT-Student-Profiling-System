<?php
// Start session and include database connection
session_start();
include 'db_connection.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: studentLogin.php'); // Redirect to login if not logged in
    exit();
}

// Fetch all announcements
$query = "SELECT a.id, a.title, a.content, a.created_at, s.username AS admin_username 
          FROM announcements a 
          JOIN admin s ON a.admin_id = s.id 
          ORDER BY a.created_at DESC";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();
$announcements = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link rel="stylesheet" href="css/studentAnnouncement.css"> <!-- Link your external CSS file -->
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>

    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="announcement-container">
        <h1>Announcements</h1>
        
        <!-- Display announcements -->
        <?php if (!empty($announcements)): ?>
            <div class="announcement-list">
                <?php foreach ($announcements as $announcement): ?>
                    <div class="announcement-item">
                        <h2><?php echo htmlspecialchars($announcement['title']); ?></h2>
                        <p><strong>Posted By:</strong> <?php echo htmlspecialchars($announcement['admin_username']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($announcement['created_at']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                    </div>
                    <hr>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No announcements yet.</p>
        <?php endif; ?>
    </div>
    
</body>
</html>
