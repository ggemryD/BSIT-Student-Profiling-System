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
    $image_path = null;

    // Validate inputs
    if (!empty($title) && !empty($content)) {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/'; // Directory to store uploaded images
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true); // Create directory if it doesn't exist
            }
            $image_name = basename($_FILES['image']['name']);
            $image_path = $upload_dir . uniqid() . '-' . $image_name;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                echo "<p>Error uploading the image.</p>";
                $image_path = null;
            }
        }

        // Insert announcement into the database
        $query = "INSERT INTO announcements (admin_id, title, content, image_path) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param('isss', $admin_id, $title, $content, $image_path);
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
    <title>Admin - Post Announcement</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
    <link rel="stylesheet" href="css/adminAnnouncement.css">
</head>
<body>
    <?php include 'adminSideBar.php'; ?>

    <div class="announcement-container">
        <div class="dashboard-header">
            <h1 class="page-title">Manage Announcements</h1>
        </div>

        <div class="content-grid">
            <div class="card">
                <h2 class="card-title">
                    <i class="fas fa-bullhorn"></i>
                    Create New Announcement
                </h2>
                <form action="adminAnnouncement.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Announcement Title</label>
                        <input type="text" name="title" id="title" class="form-control" required
                               placeholder="Enter announcement title">
                    </div>
                    <div class="form-group">
                        <label for="content">Announcement Content</label>
                        <textarea name="content" id="content" class="form-control" required
                                  placeholder="Enter announcement content"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Image Upload</label>
                        <div class="file-upload">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div>
                                <div class="upload-text">Click to upload an image</div>
                                <input type="file" name="image" id="image" accept="image/*" 
                                       style="opacity: 0; position: absolute;">
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="submit_announcement" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Post Announcement
                    </button>
                </form>
            </div>

            <div class="card">
                <h2 class="card-title">
                    <i class="fas fa-list"></i>
                    Recent Announcements
                </h2>
                <?php if (!empty($announcements)): ?>
                    <div class="scrollable-announcements">
                        <table class="announcements-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Posted By</th>
                                    <th>Date</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($announcements as $announcement): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 500;">
                                                <?php echo htmlspecialchars($announcement['title']); ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($announcement['admin_username']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($announcement['created_at'])); ?></td>
                                        <td>
                                            <?php if ($announcement['image_path']): ?>
                                                <img src="<?php echo $announcement['image_path']; ?>"
                                                     alt="Announcement Image" class="announcement-image">
                                            <?php else: ?>
                                                <span>No image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="actions">
                                            <a href="editAnnouncement.php?id=<?php echo $announcement['id']; ?>" 
                                               class="btn-icon btn-edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="deleteAnnouncement.php?id=<?php echo $announcement['id']; ?>" 
                                               class="btn-icon btn-delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No announcements available.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
