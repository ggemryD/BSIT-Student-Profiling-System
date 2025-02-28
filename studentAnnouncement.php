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
$query = "SELECT a.id, a.title, a.content, a.created_at, a.image_path, s.username AS admin_username 
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
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
     <!-- Link your external CSS file -->
    <link rel<link rel="stylesheet" href="css/studentAnnouncement.css">="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="announcement-container">
        <div class="announcement-header">
            <br>
            <h1>📢 Announcements</h1>
        </div>
        
        <!-- Display announcements -->
        <?php if (!empty($announcements)): ?>
            <div class="announcement-list">
                <?php foreach ($announcements as $announcement): ?>
                    <div class="announcement-item">
                        <div class="announcement-title">
                            <h2><?php echo htmlspecialchars($announcement['title']); ?></h2>
                        </div>
                        <div class="announcement-meta">
                            <span><strong>Posted By:</strong> <?php echo htmlspecialchars($announcement['admin_username']); ?></span>
                            <span><strong>Date:</strong> <?php echo htmlspecialchars(date("F j, Y, g:i A", strtotime($announcement['created_at']))); ?></span>
                        </div>
                        <!-- Show Image if available -->
                        <?php if (!empty($announcement['image_path'])): ?>
                            <div class="announcement-image">
                                <img src="<?php echo htmlspecialchars($announcement['image_path']); ?>" alt="Announcement Image">
                            </div>
                        <?php endif; ?>
                        
                        <p class="announcement-content"><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-announcements">No announcements yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
