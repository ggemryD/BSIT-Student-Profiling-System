<?php
// Start session and include database connection
session_start();
include 'db_connection.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: studentLogin.php'); // Redirect to login if not logged in
    exit();
}

// Get logged-in student's ID
$student_id = $_SESSION['student_id'];

// Fetch student details
$query = "SELECT first_name, last_name, email, profile_picture, bio FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

$first_name = $student['first_name'];
$last_name = $student['last_name'];
$email = $student['email'];
$profile_picture = $student['profile_picture'];
$bio = $student['bio']; // Fetch the bio from the database
$stmt->close();

// Fetch dynamic fields
$dynamic_fields = [];
$query = "SELECT field_name, field_value FROM student_details WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $dynamic_fields[] = $row;
}
$stmt->close();

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update basic information
    $new_first_name = $_POST['first_name'];
    $new_last_name = $_POST['last_name'];
    $new_bio = $_POST['bio'];

    $update_query = "UPDATE students SET first_name = ?, last_name = ?, bio = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('sssi', $new_first_name, $new_last_name, $new_bio, $student_id);
    $stmt->execute();
    $stmt->close();

    // Handle Profile Picture Upload
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
        die("Failed to create upload directory.");
    }

    if (!empty($_FILES['profile_picture']['name'])) {
        $file_name = basename($_FILES['profile_picture']['name']);
        $target_path = $upload_dir . $student_id . '_' . time() . '_' . $file_name;
        $file_type = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
        $file_size = $_FILES['profile_picture']['size'];
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_type, $allowed_types) && $file_size <= 2 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_path)) {
                $profile_picture = $target_path;
                $update_picture_query = "UPDATE students SET profile_picture = ? WHERE id = ?";
                $stmt = $conn->prepare($update_picture_query);
                $stmt->bind_param('si', $profile_picture, $student_id);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "<p style='color: red;'>Failed to upload profile picture. Please try again.</p>";
            }
        } else {
            echo "<p style='color: red;'>Invalid file. Only JPG, JPEG, PNG, and GIF under 2MB are allowed.</p>";
        }
    }

    // Handle dynamic fields
    if (!empty($_POST['dynamic_fields'])) {
        foreach ($_POST['dynamic_fields'] as $field_name => $field_value) {
            $update_details_query = "INSERT INTO student_details (student_id, field_name, field_value) 
                                      VALUES (?, ?, ?) 
                                      ON DUPLICATE KEY UPDATE field_value = ?";
            $stmt = $conn->prepare($update_details_query);
            $stmt->bind_param('isss', $student_id, $field_name, $field_value, $field_value);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Redirect back to profile
    header('Location: myProfile.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="css/updateProfile.css">
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="profile-container">
        <h1>Update Profile</h1>
        <form method="POST" enctype="multipart/form-data">
            <!-- Profile Picture -->
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                <?php if ($profile_picture): ?>
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" style="width: 100px; height: 100px;">
                <?php endif; ?>
            </div>

            <div class="bio-section">
                <h2>Bio</h2>
                <textarea name="bio" placeholder="Write something about yourself..." rows="4"><?php echo htmlspecialchars($bio); ?></textarea>
            </div>

            <!-- Basic Information -->
            <div class="basic-info">
                <h2>Basic Information</h2>
                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>

                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>

                <label for="email">Email:</label>
                <p><?php echo htmlspecialchars($email); ?></p>
            </div>

            <!-- Dynamic Fields -->
            <div class="dynamic-info">
                <h2>Additional Information</h2>
                <?php foreach ($dynamic_fields as $field): ?>
                    <label for="<?php echo $field['field_name']; ?>"><?php echo $field['field_name']; ?>:</label>
                    <input type="text" name="dynamic_fields[<?php echo $field['field_name']; ?>]" 
                           value="<?php echo htmlspecialchars($field['field_value']); ?>">
                <?php endforeach; ?>
            </div>

            <!-- Submit Button -->
            <!-- <div class="form-group">
                <button type="submit">Update Profile</button>
                <a href="myProfile.php" class="back-button">Back</a>
            </div> -->

            <div class="form-actions">
                <button type="submit">Update Profile</button>
                <a href="myProfile.php" class="back-button">Back</a>
            </div>
        </form>
    </div>
</body>
</html>
