<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: adminLogin.php');
    exit();
}

if (isset($_GET['id'])) {
    $announcement_id = $_GET['id'];

    // Delete the announcement
    $query = "DELETE FROM announcements WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param('i', $announcement_id);
    $stmt->execute();
    $stmt->close();

    header("Location: adminAnnouncement.php");
    exit();
} else {
    echo "Announcement not found.";
}
