<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: adminLogin.php');
    exit();
}

if (isset($_GET['id'])) {
    $announcement_id = $_GET['id'];

    // Fetch announcement data
    $query = "SELECT title, content, image_path FROM announcements WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param('i', $announcement_id);
    $stmt->execute();
    $stmt->bind_result($title, $content, $image_path);
    $stmt->fetch();
    $stmt->close();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Update announcement
        $new_title = $_POST['title'];
        $new_content = $_POST['content'];
        $new_image_path = $image_path; // Default to the existing image

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES['image']['name']);
            $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validate image file
            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check === false) {
                die("File is not an image.");
            }

            // Allow specific file formats
            if (!in_array($image_file_type, ['jpg', 'png', 'jpeg', 'gif'])) {
                die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
            }

            // Move the uploaded file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $new_image_path = $target_file;
            } else {
                die("Sorry, there was an error uploading your file.");
            }
        }

        $update_query = "UPDATE announcements SET title = ?, content = ?, image_path = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        if (!$update_stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $update_stmt->bind_param('sssi', $new_title, $new_content, $new_image_path, $announcement_id);
        $update_stmt->execute();
        $update_stmt->close();

        header("Location: adminAnnouncement.php");
        exit();
    }
} else {
    echo "Announcement not found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/editAnnouncement.css">
    <title>Edit Announcement</title>
</head>
<body>

    <?php include 'adminSideBar.php'; ?>

    <div class="container">
        <h1>Edit Announcement</h1>

        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="editAnnouncement.php?id=<?php echo $announcement_id; ?>" method="POST" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
            
            <label for="content">Content:</label>
            <textarea name="content" required><?php echo htmlspecialchars($content); ?></textarea>

            <label for="image">Image:</label>
            <?php if (!empty($image_path)): ?>
                <div>
                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Current Image" style="max-width: 200px; max-height: 200px; display: block; margin-bottom: 10px;">
                </div>
            <?php endif; ?>
            <input type="file" name="image" accept="image/*">
            
            <!-- <button type="submit" class="btn">Update Announcement</button> -->

            <div class="btn-container">
                <button type="button" onclick="window.location.href='adminAnnouncement.php'">Back</button>
                <button type="submit">Update Announcement</button>
            </div>
        </form>
    </div>
</body>
</html>
