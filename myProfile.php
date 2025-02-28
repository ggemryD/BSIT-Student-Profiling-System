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

// Fetch student basic information
$query = "SELECT first_name, last_name, email, bio, profile_picture FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param('i', $student_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $bio, $profile_picture);
$stmt->fetch();
$stmt->close();

// Fetch dynamic form fields and their values
$query = "
    SELECT field_name, field_value 
    FROM student_details 
    WHERE student_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$dynamic_fields = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Set default profile picture if none exists
if (empty($profile_picture)) {
    $profile_picture = 'uploads/default.png';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/myProfile.css">
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-picture-wrapper">
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-picture">
                </div>
                <div class="profile-info">
                    <h1 class="profile-name"><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></h1>
                    <p class="profile-email">
                        <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($email); ?>
                    </p>
                </div>
                <div class="profile-actions">
                    <a href="updateProfile.php" class="update-btn">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <div class="profile-content">
            <div class="profile-card">
                <h2 class="card-title">
                    <i class="fas fa-user"></i> About Me
                </h2>
                <p class="bio-text"><?php echo nl2br(htmlspecialchars($bio)); ?></p>
            </div>

            <div class="profile-card">
                <h2 class="card-title">
                    <i class="fas fa-info-circle"></i> Additional Information
                </h2>
                <?php if (!empty($dynamic_fields)): ?>
                    <div class="info-list">
                        <?php foreach ($dynamic_fields as $field): ?>
                            <div class="info-item">
                                <span class="info-label"><?php echo htmlspecialchars($field['field_name']); ?></span>
                                <span class="info-value">
                                    <?php echo !empty($field['field_value']) ? htmlspecialchars($field['field_value']) : 'Not Provided'; ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="bio-text">No additional information available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
