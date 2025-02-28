<?php
session_start();
include 'db_connection.php';

// Fetch all announcements
$query = "SELECT a.id, a.title, a.content, a.created_at, a.image_path, s.username AS admin_username 
          FROM announcements a 
          JOIN admin s ON a.admin_id = s.id 
          ORDER BY a.created_at DESC LIMIT 3"; // Limit to 3 latest announcements
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
    <title>Home</title>
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="background-image">
            <h1>Welcome to BSIT Student Profiling System</h1>
            <div class="scroll-down" onclick="scrollToAnnouncements()">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
    </div>

    <div class="announcements">
        <h2>Latest Announcements</h2>
        <?php if (!empty($announcements)): ?>
            <?php foreach ($announcements as $index => $announcement): ?>
                <div class="announcement-item" style="--animation-order: <?php echo $index + 1; ?>">
                    <?php if (!empty($announcement['image_path'])): ?>
                        <div class="announcement-image">
                            <img src="<?php echo htmlspecialchars($announcement['image_path']); ?>" alt="Announcement Image">
                        </div>
                    <?php endif; ?>
                    <div class="announcement-content">
                        <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                        <div class="announcement-meta">
                            <p><i class="fas fa-user"></i> <?php echo htmlspecialchars($announcement['admin_username']); ?></p>
                            <p><i class="fas fa-calendar"></i> <?php echo (new DateTime($announcement['created_at']))->format('F j, Y'); ?></p>
                            <p><i class="fas fa-clock"></i> <?php echo (new DateTime($announcement['created_at']))->format('g:i A'); ?></p>
                        </div>
                        <p><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="announcement-item">
                <div class="announcement-content">
                    <p class="text-center">No announcements available at the moment.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<script>
    
    function scrollToAnnouncements() {
        document.querySelector('.announcements').scrollIntoView({ behavior: 'smooth' });
    }

</script>