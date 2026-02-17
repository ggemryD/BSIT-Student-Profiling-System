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
$upload_error = null;
$upload_success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            $upload_error = 'Unable to prepare upload directory.';
        }
    }

    if (!$upload_error && !empty($_FILES['profile_picture']['name'])) {
        $file_name = basename($_FILES['profile_picture']['name']);
        $target_path = $upload_dir . $student_id . '_' . time() . '_' . $file_name;
        $file_type = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
        $file_size = $_FILES['profile_picture']['size'];
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_type, $allowed_types) && $file_size > 0 && $file_size <= 2 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_path)) {
                $profile_picture_path = $target_path;
                $update_picture_query = "UPDATE students SET profile_picture = ? WHERE id = ?";
                $stmt = $conn->prepare($update_picture_query);
                if ($stmt) {
                    $stmt->bind_param('si', $profile_picture_path, $student_id);
                    $stmt->execute();
                    $stmt->close();
                    $upload_success = 'Profile picture updated.';
                } else {
                    $upload_error = 'Unable to update profile picture.';
                }
            } else {
                $upload_error = 'Failed to upload profile picture. Please try again.';
            }
        } else {
            $upload_error = 'Invalid file. Only JPG, JPEG, PNG, and GIF under 2MB are allowed.';
        }
    }
}

// Fetch student basic information
$query = "SELECT first_name, last_name, email, bio, profile_picture, created_at FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param('i', $student_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $bio, $profile_picture, $created_at);
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

$dynamic_fields_count = count($dynamic_fields);
$filled_dynamic_fields = 0;
foreach ($dynamic_fields as $field) {
    if (trim($field['field_value']) !== '') {
        $filled_dynamic_fields++;
    }
}

if ($dynamic_fields_count > 0) {
    $profile_completion = (int) round(($filled_dynamic_fields / $dynamic_fields_count) * 100);
} else {
    $profile_completion = 50;
}

$social_platforms = ['facebook', 'linkedin', 'twitter', 'instagram', 'github', 'website', 'portfolio'];
$social_links = [];
$extra_fields = [];

foreach ($dynamic_fields as $field) {
    $name_lower = strtolower($field['field_name']);
    if (in_array($name_lower, $social_platforms, true)) {
        $social_links[] = $field;
    } else {
        $extra_fields[] = $field;
    }
}

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

    <div class="profile-page">
        <main class="profile-layout" aria-labelledby="profile-title">
            <section class="profile-hero">
                <?php if ($upload_success): ?>
                    <div class="profile-alert profile-alert-success" role="status">
                        <?php echo htmlspecialchars($upload_success); ?>
                    </div>
                <?php endif; ?>
                <?php if ($upload_error): ?>
                    <div class="profile-alert profile-alert-error" role="alert">
                        <?php echo htmlspecialchars($upload_error); ?>
                    </div>
                <?php endif; ?>

                <div class="profile-header">
                    <form class="profile-avatar-form" method="POST" enctype="multipart/form-data">
                        <div class="profile-picture-wrapper">
                            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile picture of <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>" class="profile-picture">
                            <button type="button" class="profile-picture-edit" aria-label="Change profile picture">
                                <i class="fas fa-camera"></i>
                            </button>
                            <input type="file" name="profile_picture" id="profile-picture-input" accept="image/*" aria-label="Choose a new profile picture">
                        </div>
                    </form>
                    <div class="profile-header-main">
                        <div class="profile-text">
                            <p class="profile-label">Student profile</p>
                            <h1 class="profile-name" id="profile-title">
                                <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>
                            </h1>
                            <p class="profile-email">
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                <a href="mailto:<?php echo htmlspecialchars($email); ?>">
                                    <?php echo htmlspecialchars($email); ?>
                                </a>
                            </p>
                            <?php if (!empty($created_at)): ?>
                                <p class="profile-meta">
                                    Member since <?php echo date('F j, Y', strtotime($created_at)); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="profile-metrics" aria-label="Profile activity statistics">
                            <div class="metric-pill">
                                <span class="metric-label">Profile completeness</span>
                                <div class="metric-value-wrapper">
                                    <span class="metric-value"><?php echo $profile_completion; ?>%</span>
                                    <div class="metric-progress" role="img" aria-label="Profile completeness <?php echo $profile_completion; ?> percent">
                                        <div class="metric-progress-bar" style="width: <?php echo $profile_completion; ?>%;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="metric-pill">
                                <span class="metric-label">Details added</span>
                                <span class="metric-value">
                                    <?php echo $filled_dynamic_fields; ?>/<?php echo $dynamic_fields_count; ?>
                                </span>
                            </div>
                        </div>
                        <div class="profile-actions">
                            <a href="updateProfile.php" class="btn primary-action">
                                <i class="fas fa-edit" aria-hidden="true"></i>
                                <span>Edit profile</span>
                            </a>
                            <button type="button" class="btn secondary-action" id="share-profile-button">
                                <i class="fas fa-share-alt" aria-hidden="true"></i>
                                <span>Share</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="profile-main-grid">
                <article class="profile-card profile-about" aria-label="About">
                    <h2 class="card-title">
                        <i class="fas fa-user" aria-hidden="true"></i>
                        <span>About</span>
                    </h2>
                    <p class="bio-text">
                        <?php echo $bio ? nl2br(htmlspecialchars($bio)) : 'Add a short bio so others can get to know you better.'; ?>
                    </p>
                </article>

                <article class="profile-card profile-contact" aria-label="Contact information">
                    <h2 class="card-title">
                        <i class="fas fa-address-book" aria-hidden="true"></i>
                        <span>Contact</span>
                    </h2>
                    <div class="info-list">
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value">
                                <a href="mailto:<?php echo htmlspecialchars($email); ?>">
                                    <?php echo htmlspecialchars($email); ?>
                                </a>
                            </span>
                        </div>
                        <?php foreach ($extra_fields as $field): ?>
                            <?php
                                $label = $field['field_name'];
                                $value = trim($field['field_value']);
                                if (stripos($label, 'phone') === false && stripos($label, 'contact') === false) {
                                    continue;
                                }
                            ?>
                            <div class="info-item">
                                <span class="info-label"><?php echo htmlspecialchars($label); ?></span>
                                <span class="info-value">
                                    <?php echo $value !== '' ? htmlspecialchars($value) : 'Not provided'; ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>

                <article class="profile-card profile-details" aria-label="Additional information">
                    <h2 class="card-title">
                        <i class="fas fa-info-circle" aria-hidden="true"></i>
                        <span>Additional details</span>
                    </h2>
                    <?php if (!empty($dynamic_fields)): ?>
                        <div class="info-list">
                            <?php foreach ($extra_fields as $field): ?>
                                <?php
                                    $label = $field['field_name'];
                                    $value = trim($field['field_value']);
                                    if (stripos($label, 'phone') !== false || stripos($label, 'contact') !== false) {
                                        continue;
                                    }
                                ?>
                                <div class="info-item">
                                    <span class="info-label"><?php echo htmlspecialchars($label); ?></span>
                                    <span class="info-value">
                                        <?php echo $value !== '' ? htmlspecialchars($value) : 'Not provided'; ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($extra_fields)): ?>
                                <p class="bio-text">No additional information available yet.</p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <p class="bio-text">No additional information available yet.</p>
                    <?php endif; ?>
                </article>

                <?php if (!empty($social_links)): ?>
                    <article class="profile-card profile-social" aria-label="Social links">
                        <h2 class="card-title">
                            <i class="fas fa-share-alt" aria-hidden="true"></i>
                            <span>Social</span>
                        </h2>
                        <div class="social-links">
                            <?php foreach ($social_links as $field): ?>
                                <?php
                                    $label = strtolower($field['field_name']);
                                    $value = trim($field['field_value']);
                                    if ($value === '') {
                                        continue;
                                    }
                                    $display_label = ucfirst($label);
                                    $is_url = preg_match('/^https?:\\/\\//i', $value) || preg_match('/^www\\./i', $value);
                                    $href = $is_url ? (preg_match('/^https?:\\/\\//i', $value) ? $value : 'https://' . $value) : '#';
                                    $icon_name = $label === 'website' || $label === 'portfolio' ? 'globe' : $label;
                                ?>
                                <a
                                    class="social-link"
                                    <?php if ($is_url): ?>
                                        href="<?php echo htmlspecialchars($href); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    <?php else: ?>
                                        href="#"
                                    <?php endif; ?>
                                >
                                    <span class="social-icon">
                                        <i class="fab fa-<?php echo htmlspecialchars($icon_name); ?>" aria-hidden="true"></i>
                                    </span>
                                    <span class="social-label"><?php echo htmlspecialchars($display_label); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </article>
                <?php endif; ?>
            </section>
        </main>

        <div class="share-toast" id="share-toast" role="status" aria-live="polite">
            Profile link copied to clipboard
        </div>
    </div>

    <script>
    (function () {
        var fileInput = document.getElementById('profile-picture-input');
        var editButton = document.querySelector('.profile-picture-edit');
        var avatarForm = document.querySelector('.profile-avatar-form');
        var shareButton = document.getElementById('share-profile-button');
        var toast = document.getElementById('share-toast');

        if (editButton && fileInput) {
            editButton.addEventListener('click', function () {
                fileInput.click();
            });
        }

        if (fileInput && avatarForm) {
            fileInput.addEventListener('change', function () {
                if (fileInput.files && fileInput.files[0]) {
                    avatarForm.submit();
                }
            });
        }

        function showToast(message) {
            if (!toast) {
                return;
            }
            toast.textContent = message;
            toast.classList.add('is-visible');
            window.setTimeout(function () {
                toast.classList.remove('is-visible');
            }, 2500);
        }

        if (shareButton) {
            shareButton.addEventListener('click', function () {
                var shareUrl = window.location.href;

                if (navigator.share) {
                    navigator.share({
                        title: document.title,
                        url: shareUrl
                    }).catch(function () {});
                } else if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(shareUrl).then(function () {
                        showToast('Profile link copied to clipboard');
                    }, function () {
                        showToast('Unable to copy link');
                    });
                } else {
                    var tempInput = document.createElement('input');
                    tempInput.type = 'text';
                    tempInput.value = shareUrl;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    try {
                        document.execCommand('copy');
                        showToast('Profile link copied to clipboard');
                    } catch (e) {
                        showToast('Unable to copy link');
                    }
                    document.body.removeChild(tempInput);
                }
            });
        }

        var interactiveElements = document.querySelectorAll('.btn, .social-link, .profile-picture-edit');
        for (var i = 0; i < interactiveElements.length; i++) {
            interactiveElements[i].addEventListener('keyup', function (event) {
                if (event.key === 'Enter' || event.keyCode === 13 || event.key === ' ') {
                    event.target.click();
                }
            });
        }
    }());
    </script>
</body>
</html>
