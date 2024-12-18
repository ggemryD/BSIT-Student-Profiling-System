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
    $query = "SELECT title, content FROM announcements WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param('i', $announcement_id);
    $stmt->execute();
    $stmt->bind_result($title, $content);
    $stmt->fetch();
    $stmt->close();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Update announcement
        $new_title = $_POST['title'];
        $new_content = $_POST['content'];

        $update_query = "UPDATE announcements SET title = ?, content = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        if (!$update_stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $update_stmt->bind_param('ssi', $new_title, $new_content, $announcement_id);
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
    <title>Edit Announcement</title>
</head>
<body>

    <h1>Edit Announcement</h1>

    <form action="editAnnouncement.php?id=<?php echo $announcement_id; ?>" method="POST">
        <label for="title">Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
        
        <label for="content">Content:</label>
        <textarea name="content" required><?php echo htmlspecialchars($content); ?></textarea>
        
        <button type="submit">Update Announcement</button>
    </form>

</body>
</html>
