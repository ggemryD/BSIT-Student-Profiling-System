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

$upload_error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
    $new_last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
    $new_bio = isset($_POST['bio']) ? $_POST['bio'] : '';

    $update_query = "UPDATE students SET first_name = ?, last_name = ?, bio = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('sssi', $new_first_name, $new_last_name, $new_bio, $student_id);
    $stmt->execute();
    $stmt->close();

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
        $upload_error = 'Failed to create upload directory.';
    }

    if (!$upload_error && !empty($_FILES['profile_picture']['name'])) {
        $file_name = basename($_FILES['profile_picture']['name']);
        $target_path = $upload_dir . $student_id . '_' . time() . '_' . $file_name;
        $file_type = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
        $file_size = $_FILES['profile_picture']['size'];
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_type, $allowed_types, true) && $file_size > 0 && $file_size <= 2 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_path)) {
                $profile_picture = $target_path;
                $update_picture_query = "UPDATE students SET profile_picture = ? WHERE id = ?";
                $stmt = $conn->prepare($update_picture_query);
                $stmt->bind_param('si', $profile_picture, $student_id);
                $stmt->execute();
                $stmt->close();
            } else {
                $upload_error = 'Failed to upload profile picture. Please try again.';
            }
        } else {
            $upload_error = 'Invalid file. Only JPG, JPEG, PNG, and GIF under 2MB are allowed.';
        }
    }

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

    <div class="edit-profile-page">
        <main class="edit-profile-layout" aria-labelledby="edit-profile-title">
            <section class="edit-profile-header">
                <div class="edit-profile-heading">
                    <h1 id="edit-profile-title">Edit profile</h1>
                    <p class="edit-profile-subtitle">Update your photo, bio, and personal information.</p>
                </div>
                <a href="myProfile.php" class="edit-profile-link">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    <span>Back to profile</span>
                </a>
            </section>

            <form method="POST" enctype="multipart/form-data" class="edit-profile-form" aria-describedby="edit-profile-description">
                <p id="edit-profile-description" class="visually-hidden">
                    Use this form to update your profile picture, bio, and personal details.
                </p>

                <?php if ($upload_error): ?>
                    <div class="edit-profile-alert" role="alert">
                        <?php echo htmlspecialchars($upload_error); ?>
                    </div>
                <?php endif; ?>

                <section class="edit-profile-section edit-profile-section-primary" aria-label="Profile picture and bio">
                    <div class="edit-profile-avatar-block">
                        <div class="edit-profile-avatar-wrapper">
                            <div class="edit-profile-avatar">
                                <img src="<?php echo htmlspecialchars($profile_picture ? $profile_picture : 'uploads/default.png'); ?>"
                                     alt="Current profile picture"
                                     class="edit-profile-avatar-image">
                            </div>
                            <label for="profile_picture" class="edit-profile-avatar-button">
                                <i class="fas fa-camera" aria-hidden="true"></i>
                                <span>Change photo</span>
                            </label>
                            <input
                                type="file"
                                name="profile_picture"
                                id="profile_picture"
                                accept="image/*"
                                class="edit-profile-avatar-input"
                                aria-label="Choose a new profile picture">
                            <p class="edit-profile-avatar-hint">JPG, PNG, or GIF up to 2 MB.</p>
                        </div>
                        <div class="edit-profile-name-block">
                            <p class="edit-profile-label">Name</p>
                            <p class="edit-profile-name-value">
                                <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>
                            </p>
                            <p class="edit-profile-email-value">
                                <?php echo htmlspecialchars($email); ?>
                            </p>
                        </div>
                    </div>

                    <div class="edit-profile-field-group">
                        <label for="bio" class="edit-profile-label">Bio</label>
                        <textarea
                            id="bio"
                            name="bio"
                            rows="4"
                            placeholder="Write a short introduction..."
                            class="edit-profile-textarea"
                        ><?php echo htmlspecialchars($bio); ?></textarea>
                    </div>
                </section>

                <section class="edit-profile-section" aria-label="Basic information">
                    <h2 class="edit-profile-section-title">Basic information</h2>
                    <div class="edit-profile-two-column">
                        <div class="edit-profile-field-group">
                            <label for="first_name" class="edit-profile-label">First name</label>
                            <input
                                type="text"
                                id="first_name"
                                name="first_name"
                                value="<?php echo htmlspecialchars($first_name); ?>"
                                required
                                class="edit-profile-input">
                        </div>
                        <div class="edit-profile-field-group">
                            <label for="last_name" class="edit-profile-label">Last name</label>
                            <input
                                type="text"
                                id="last_name"
                                name="last_name"
                                value="<?php echo htmlspecialchars($last_name); ?>"
                                required
                                class="edit-profile-input">
                        </div>
                    </div>
                    <div class="edit-profile-field-group">
                        <label class="edit-profile-label">Email</label>
                        <div class="edit-profile-email-display">
                            <?php echo htmlspecialchars($email); ?>
                        </div>
                    </div>
                </section>

                <section class="edit-profile-section" aria-label="Additional information">
                    <h2 class="edit-profile-section-title">Additional information</h2>
                    <?php if (!empty($dynamic_fields)): ?>
                        <div class="edit-profile-dynamic-grid">
                            <?php foreach ($dynamic_fields as $field): ?>
                                <div class="edit-profile-field-group">
                                    <label
                                        for="dynamic_<?php echo htmlspecialchars($field['field_name']); ?>"
                                        class="edit-profile-label"
                                    >
                                        <?php echo htmlspecialchars($field['field_name']); ?>
                                    </label>
                                    <input
                                        type="text"
                                        id="dynamic_<?php echo htmlspecialchars($field['field_name']); ?>"
                                        name="dynamic_fields[<?php echo htmlspecialchars($field['field_name']); ?>]"
                                        value="<?php echo htmlspecialchars($field['field_value']); ?>"
                                        class="edit-profile-input">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="edit-profile-empty-text">
                            No additional fields have been added to your profile yet.
                        </p>
                    <?php endif; ?>
                </section>

                <div class="edit-profile-actions">
                    <button type="submit" class="edit-profile-submit">
                        <span>Save changes</span>
                    </button>
                    <a href="myProfile.php" class="edit-profile-cancel">
                        Cancel
                    </a>
                </div>
            </form>
        </main>
    </div>

    <script>
    (function () {
        var fileInput = document.getElementById('profile_picture');
        var avatarPreview = document.querySelector('.edit-profile-avatar-image');

        if (fileInput && avatarPreview) {
            fileInput.addEventListener('change', function () {
                if (!fileInput.files || !fileInput.files[0]) {
                    return;
                }
                var file = fileInput.files[0];
                if (!file.type || !file.type.match(/^image\\//i)) {
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (event) {
                    if (event.target && typeof event.target.result === 'string') {
                        avatarPreview.src = event.target.result;
                    }
                };
                reader.readAsDataURL(file);
            });
        }
    }());
    </script>
</body>
</html>
