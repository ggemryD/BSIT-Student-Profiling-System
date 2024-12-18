<?php
// Start session and include database connection
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminLogin.php'); // Redirect to login if not logged in
    exit();
}

// Check if form is submitted to add an announcement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_announcement'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $admin_id = $_SESSION['admin_id']; // Admin ID from session

    // Validate inputs
    if (!empty($title) && !empty($content)) {
        // Insert announcement into the database
        $query = "INSERT INTO announcements (admin_id, title, content) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param('iss', $admin_id, $title, $content);
        if ($stmt->execute()) {
            echo "<p>Announcement posted successfully!</p>";
        } else {
            echo "<p>Error posting announcement. Please try again.</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Please fill in both the title and content.</p>";
    }
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
    <title>Admin - Post Announcement</title>
    <link rel="stylesheet" href="css/adminAnnouncement.css"> <!-- Link your CSS file -->
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>

    <!-- Include admin side bar -->
    <?php include 'adminSideBar.php'; ?>

    <div class="announcement-container">
        <h1>Post an Announcement</h1>
        
        <!-- Form to create a new announcement -->
        <form action="adminAnnouncement.php" method="POST">
            <div class="form-group">
                <label for="title">Announcement Title</label>
                <input type="text" name="title" id="title" required>
            </div>
            <div class="form-group">
                <label for="content">Announcement Content</label>
                <textarea name="content" id="content" rows="4" required></textarea>
            </div>
            <button type="submit" name="submit_announcement">Post Announcement</button>
        </form>

        <hr>

        <h2>Existing Announcements</h2>
        <?php if (!empty($announcements)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Posted By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $announcement): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($announcement['title']); ?></td>
                            <td><?php echo htmlspecialchars($announcement['admin_username']); ?></td>
                            <td><?php echo htmlspecialchars($announcement['created_at']); ?></td>
                            <td>
                                <a href="editAnnouncement.php?id=<?php echo $announcement['id']; ?>">Edit</a> | 
                                <a href="deleteAnnouncement.php?id=<?php echo $announcement['id']; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No announcements yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
